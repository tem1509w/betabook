CREATE TABLE IF NOT EXISTS orders (
    order_id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_code VARCHAR(32) NOT NULL,
    user_id INT DEFAULT NULL,
    customer_name VARCHAR(191) NOT NULL,
    email VARCHAR(191) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_line VARCHAR(255) NOT NULL,
    ward VARCHAR(191) NOT NULL,
    district VARCHAR(191) NOT NULL,
    city VARCHAR(191) NOT NULL,
    customer_note TEXT DEFAULT NULL,
    payment_method ENUM('cod', 'bank_transfer') NOT NULL DEFAULT 'cod',
    payment_status ENUM('unpaid', 'pending', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
    order_status ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    subtotal DECIMAL(12,2) NOT NULL,
    shipping_fee DECIMAL(12,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_orders_code (order_code),
    INDEX idx_orders_user_created (user_id, created_at),
    INDEX idx_orders_status_created (order_status, created_at),
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    order_item_id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id INT DEFAULT NULL,
    product_title VARCHAR(255) NOT NULL,
    product_code VARCHAR(50) NOT NULL,
    cover_image VARCHAR(255) DEFAULT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    line_total DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_items_order (order_id),
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id)
        REFERENCES orders(order_id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id)
        REFERENCES products(product_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_cart_items (
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, product_id),
    CONSTRAINT fk_user_cart_items_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_user_cart_items_product FOREIGN KEY (product_id)
        REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admin_menu_items
    (menu_key, label, page_key, active_pages, icon_class, is_external, sort_order, is_active)
VALUES
    ('orders', 'Quản lý đơn hàng', 'orders', 'orders', 'bi bi-receipt', 0, 25, 1)
ON DUPLICATE KEY UPDATE
    label = VALUES(label), page_key = VALUES(page_key), active_pages = VALUES(active_pages),
    icon_class = VALUES(icon_class), is_external = VALUES(is_external),
    sort_order = VALUES(sort_order), is_active = VALUES(is_active);

INSERT IGNORE INTO admin_menu_permissions (role_id, menu_id, can_view)
SELECT r.role_id, m.menu_id, 1
FROM roles r
CROSS JOIN admin_menu_items m
WHERE r.role_key = 'admin' AND m.menu_key = 'orders';

INSERT IGNORE INTO admin_menu_permissions (role_id, menu_id, can_view)
SELECT r.role_id, m.menu_id, 1
FROM roles r
CROSS JOIN admin_menu_items m
WHERE r.role_key = 'staff' AND m.menu_key = 'orders';
