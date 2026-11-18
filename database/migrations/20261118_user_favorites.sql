-- Add account and admin support for saved favorite products. Safe to run repeatedly.
CREATE TABLE IF NOT EXISTS user_favorite_items (
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, product_id),
    CONSTRAINT fk_user_favorite_items_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_user_favorite_items_product FOREIGN KEY (product_id)
        REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admin_menu_items (
    menu_key, label, page_key, active_pages, icon_class, is_external, sort_order, is_active
)
VALUES (
    'favorites', 'Sản phẩm yêu thích', 'favorites', 'favorites', 'bi bi-heart', 0, 27, 1
)
ON DUPLICATE KEY UPDATE
    label = VALUES(label),
    page_key = VALUES(page_key),
    active_pages = VALUES(active_pages),
    icon_class = VALUES(icon_class),
    is_external = VALUES(is_external),
    sort_order = VALUES(sort_order),
    is_active = VALUES(is_active);

INSERT INTO admin_menu_permissions (role_id, menu_id, can_view)
SELECT r.role_id, m.menu_id, 1
FROM roles r
CROSS JOIN admin_menu_items m
WHERE r.role_key IN ('admin', 'staff')
  AND m.menu_key = 'favorites'
ON DUPLICATE KEY UPDATE can_view = 1;
