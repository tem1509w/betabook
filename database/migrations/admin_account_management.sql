-- Add the account-management menu for administrators. Safe to run repeatedly.
INSERT INTO admin_menu_items (
    menu_key,
    label,
    page_key,
    active_pages,
    icon_class,
    is_external,
    sort_order,
    is_active
)
VALUES (
    'users',
    'Quản lý tài khoản',
    'users',
    'users,users-form',
    'bi bi-people',
    0,
    5,
    1
)
ON DUPLICATE KEY UPDATE
    label = VALUES(label),
    page_key = VALUES(page_key),
    active_pages = VALUES(active_pages),
    icon_class = VALUES(icon_class),
    is_external = VALUES(is_external),
    sort_order = VALUES(sort_order),
    is_active = VALUES(is_active);

INSERT IGNORE INTO admin_menu_permissions (role_id, menu_id, can_view)
SELECT r.role_id, m.menu_id, 1
FROM roles r
CROSS JOIN admin_menu_items m
WHERE r.role_id = 1
  AND r.role_key = 'admin'
  AND m.menu_key = 'users';