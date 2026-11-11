ALTER TABLE orders
    ADD COLUMN retention_started_at TIMESTAMP NULL DEFAULT NULL AFTER order_status,
    ADD INDEX idx_orders_retention_cleanup
        (retention_started_at, order_status, payment_status);

UPDATE orders
SET retention_started_at = updated_at
WHERE (order_status = 'completed' AND payment_status = 'paid')
   OR payment_status = 'refunded'
   OR order_status = 'cancelled';

INSERT IGNORE INTO admin_menu_permissions (role_id, menu_id, can_view)
SELECT r.role_id, m.menu_id, 1
FROM roles r
CROSS JOIN admin_menu_items m
WHERE r.role_key = 'staff' AND m.menu_key = 'orders';
