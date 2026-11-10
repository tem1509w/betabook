-- Consolidate admin_roles into the shared roles table used by users and menu permissions.
RENAME TABLE admin_roles TO roles;

UPDATE roles
SET role_key = 'admin',
    role_name = 'Quản trị viên'
WHERE role_key = 'manager';

UPDATE roles
SET role_key = 'staff',
    role_name = 'Nhân viên'
WHERE role_key = 'employee';

ALTER TABLE users
    DROP FOREIGN KEY fk_users_role,
    ADD CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(role_id);

ALTER TABLE admin_menu_permissions
    DROP FOREIGN KEY fk_admin_menu_permissions_role,
    ADD CONSTRAINT fk_admin_menu_permissions_roles
        FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE;
