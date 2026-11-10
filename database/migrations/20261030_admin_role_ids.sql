-- One-time migration from role_key-based menu permissions to numeric role IDs.
-- Run against an existing BetaBook database before deploying the updated admin router.

ALTER TABLE admin_menu_permissions
    DROP FOREIGN KEY fk_admin_menu_permissions_role;

ALTER TABLE admin_roles
    ADD COLUMN role_id INT NOT NULL AUTO_INCREMENT UNIQUE FIRST;

ALTER TABLE admin_roles
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (role_id),
    ADD UNIQUE KEY uk_admin_roles_role_key (role_key);

ALTER TABLE admin_menu_permissions
    ADD COLUMN role_id INT NULL AFTER role_key;

UPDATE admin_menu_permissions p
INNER JOIN admin_roles r ON r.role_key = p.role_key
SET p.role_id = r.role_id;

ALTER TABLE admin_menu_permissions
    DROP PRIMARY KEY,
    DROP COLUMN role_key,
    MODIFY role_id INT NOT NULL,
    ADD PRIMARY KEY (role_id, menu_id),
    ADD CONSTRAINT fk_admin_menu_permissions_role
        FOREIGN KEY (role_id) REFERENCES admin_roles(role_id) ON DELETE CASCADE;

UPDATE admin_roles
SET role_key = 'manager',
    role_name = CONVERT(0x5175E1BAA36E206CC3BD USING utf8mb4)
WHERE role_key = 'admin';

UPDATE admin_roles
SET role_key = 'employee',
    role_name = CONVERT(0x4E68C3A26E207669C3AA6E USING utf8mb4)
WHERE role_key = 'staff';

INSERT INTO admin_roles (role_key, role_name, is_active)
VALUES ('user', 'User', 1)
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

INSERT IGNORE INTO admin_menu_permissions (role_id, menu_id)
SELECT r.role_id, m.menu_id
FROM admin_roles r
CROSS JOIN admin_menu_items m
WHERE r.role_key = 'manager'
  AND m.menu_key IN ('dashboard', 'books', 'categories', 'public');

INSERT IGNORE INTO admin_menu_permissions (role_id, menu_id)
SELECT r.role_id, m.menu_id
FROM admin_roles r
CROSS JOIN admin_menu_items m
WHERE r.role_key = 'employee'
  AND m.menu_key IN ('dashboard', 'books', 'public');
