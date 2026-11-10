-- One-time migration from users.role to the shared admin_roles.role_id.
ALTER TABLE users
    ADD COLUMN role_id INT NULL AFTER phone;

UPDATE users u
INNER JOIN admin_roles r
    ON r.role_key = CASE u.role
        WHEN 'admin' THEN 'manager'
        WHEN 'staff' THEN 'employee'
        WHEN 'customer' THEN 'user'
    END
SET u.role_id = r.role_id;

ALTER TABLE users
    DROP COLUMN role,
    MODIFY role_id INT NOT NULL,
    ADD INDEX idx_users_role_id (role_id),
    ADD CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES admin_roles(role_id);
