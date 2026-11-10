-- BetaBook database setup for MySQL/MariaDB.
-- Safe to run more than once: existing tables and user data are not dropped.

CREATE DATABASE IF NOT EXISTS betabook
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE betabook;

CREATE TABLE IF NOT EXISTS categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT DEFAULT NULL,
    slug VARCHAR(191) NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_categories_slug (slug),
    INDEX idx_parent (parent_id),
    CONSTRAINT fk_categories_parent
        FOREIGN KEY (parent_id) REFERENCES categories(category_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS publishers (
    publisher_id INT PRIMARY KEY AUTO_INCREMENT,
    publisher_name VARCHAR(191) NOT NULL,
    slug VARCHAR(191) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_publishers_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_code VARCHAR(50) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    category_id INT DEFAULT NULL,
    publisher_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(191),
    isbn VARCHAR(20),
    cover_type ENUM('hardcover', 'paperback', 'ebook') DEFAULT 'paperback',
    price DECIMAL(12,2) NOT NULL,
    original_price DECIMAL(12,2) DEFAULT NULL,
    stock_quantity INT DEFAULT 0,
    sold_quantity INT DEFAULT 0,
    is_best_seller TINYINT(1) DEFAULT 0,
    is_new_release TINYINT(1) DEFAULT 0,
    is_recommended TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    cover_image VARCHAR(255),
    description TEXT,
    content LONGTEXT,
    publication_year SMALLINT UNSIGNED DEFAULT NULL,
    page_count SMALLINT UNSIGNED DEFAULT NULL,
    dimensions VARCHAR(100) DEFAULT NULL,
    weight DECIMAL(8,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_products_code (product_code),
    UNIQUE KEY uk_products_slug (slug),
    INDEX idx_products_category (category_id),
    INDEX idx_products_publisher (publisher_id),
    INDEX idx_products_active (is_active),
    INDEX idx_products_best_seller (is_best_seller),
    INDEX idx_products_new_release (is_new_release),
    INDEX idx_products_recommended (is_recommended),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    CONSTRAINT fk_products_publisher
        FOREIGN KEY (publisher_id) REFERENCES publishers(publisher_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    alt_text VARCHAR(191),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product_images_product (product_id),
    CONSTRAINT fk_product_images_product
        FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(191) NOT NULL,
    email VARCHAR(191) NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(191),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'staff', 'customer') DEFAULT 'customer',
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_users_username (username),
    UNIQUE KEY uk_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    email VARCHAR(191) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_messages_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(191) NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_settings_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_roles (
    role_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    role_key VARCHAR(50) NOT NULL,
    role_name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uk_admin_roles_role_key (role_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_menu_items (
    menu_id INT PRIMARY KEY AUTO_INCREMENT,
    menu_key VARCHAR(50) NOT NULL,
    label VARCHAR(100) NOT NULL,
    page_key VARCHAR(100) DEFAULT NULL,
    active_pages VARCHAR(255) NOT NULL DEFAULT '',
    icon_class VARCHAR(100) NOT NULL,
    is_external TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uk_admin_menu_key (menu_key),
    INDEX idx_admin_menu_order (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_menu_permissions (
    role_id INT NOT NULL,
    menu_id INT NOT NULL,
    can_view TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (role_id, menu_id),
    CONSTRAINT fk_admin_menu_permissions_role
        FOREIGN KEY (role_id) REFERENCES admin_roles(role_id) ON DELETE CASCADE,
    CONSTRAINT fk_admin_menu_permissions_menu
        FOREIGN KEY (menu_id) REFERENCES admin_menu_items(menu_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admin_roles (role_key, role_name, is_active) VALUES
    ('manager', 'Quản lý', 1),
    ('employee', 'Nhân viên', 1),
    ('user', 'User', 1)
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name), is_active = VALUES(is_active);

INSERT INTO admin_menu_items
    (menu_key, label, page_key, active_pages, icon_class, is_external, sort_order, is_active)
VALUES
    ('dashboard', 'Tổng quan', 'dashboard', 'dashboard', 'bi bi-speedometer2', 0, 10, 1),
    ('books', 'Quản lý sách', 'books', 'books,book-form', 'bi bi-book', 0, 20, 1),
    ('categories', 'Quản lý danh mục', 'categories', 'categories,category-form', 'bi bi-tags', 0, 30, 1),
    ('promotions', 'Mã giảm & Sự kiện', 'promotions', 'promotions', 'bi bi-ticket-perforated', 0, 40, 1),
    ('public', 'Quay lại Public', NULL, '', 'bi bi-box-arrow-up-right', 1, 90, 1)
ON DUPLICATE KEY UPDATE
    label = VALUES(label), page_key = VALUES(page_key), active_pages = VALUES(active_pages),
    icon_class = VALUES(icon_class), is_external = VALUES(is_external),
    sort_order = VALUES(sort_order), is_active = VALUES(is_active);

INSERT IGNORE INTO admin_menu_permissions (role_id, menu_id)
SELECT r.role_id, m.menu_id
FROM admin_roles r CROSS JOIN admin_menu_items m
WHERE r.role_key = 'manager' AND m.menu_key IN ('dashboard', 'books', 'categories', 'promotions', 'public');

INSERT IGNORE INTO admin_menu_permissions (role_id, menu_id)
SELECT r.role_id, m.menu_id
FROM admin_roles r CROSS JOIN admin_menu_items m
WHERE r.role_key = 'employee' AND m.menu_key IN ('dashboard', 'books', 'public');

CREATE TABLE IF NOT EXISTS home_content (
    content_key VARCHAR(100) PRIMARY KEY,
    content_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS home_product_ratings (
    product_id INT PRIMARY KEY,
    rating_average DECIMAL(2,1) NOT NULL,
    rating_count INT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_home_product_ratings_product
        FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_rating_breakdown (
    product_id INT NOT NULL,
    star_rating TINYINT UNSIGNED NOT NULL,
    rating_percentage TINYINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (product_id, star_rating),
    CONSTRAINT chk_product_rating_breakdown_star CHECK (star_rating BETWEEN 1 AND 5),
    CONSTRAINT chk_product_rating_breakdown_percentage CHECK (rating_percentage <= 100),
    CONSTRAINT fk_product_rating_breakdown_product
        FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    reviewer_name VARCHAR(191) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    review_text TEXT NOT NULL,
    reviewed_at DATE NOT NULL,
    avatar_image VARCHAR(255),
    is_sample TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product_reviews_product_date (product_id, reviewed_at),
    UNIQUE KEY uk_product_reviews_sample (product_id, reviewer_name, reviewed_at, is_sample),
    CONSTRAINT chk_product_reviews_rating CHECK (rating BETWEEN 1 AND 5),
    CONSTRAINT fk_product_reviews_product
        FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS home_reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    reviewer_name VARCHAR(191) NOT NULL,
    member_label VARCHAR(100) NOT NULL,
    quote TEXT NOT NULL,
    avatar_image VARCHAR(255),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uk_home_review_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS home_coupons (
    coupon_id INT PRIMARY KEY AUTO_INCREMENT,
    coupon_code VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    expires_at DATE NOT NULL,
    image_path VARCHAR(255),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uk_home_coupon_code (coupon_code),
    UNIQUE KEY uk_home_coupon_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS home_events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    event_slug VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_path VARCHAR(255),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uk_home_event_slug (event_slug),
    UNIQUE KEY uk_home_event_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed six top-level categories and five child categories.
-- Use stable slug lookups so existing category IDs remain valid on re-runs.
INSERT INTO categories (parent_id, slug, name, description, is_active, sort_order) VALUES
(NULL, 'betacards', 'BetaCards', NULL , 1, 1),
(NULL, 'van-hoc-giai-tri', 'Văn học & Giải trí',NULL, 1, 2),
(NULL, 'kinh-doanh-kinh-te', 'Kinh doanh & Kinh tế', NULL, 1, 3),
(NULL, 'tu-duy-phat-trien-ban-than', 'Tư duy & Phát triển bản thân', NULL, 1, 4),
(NULL, 'khoa-hoc-doi-song', 'Khoa học & Đời sống', NULL, 1, 5),
(NULL, 'an-pham-dac-biet', 'Ấn phẩm & Bộ sưu tập đặc biệt', NULL, 1, 6)

ON DUPLICATE KEY UPDATE
    name = VALUES(name), description = VALUES(description), is_active = VALUES(is_active),
    sort_order = VALUES(sort_order);

SET @category_betacards = (SELECT category_id FROM categories WHERE slug = 'betacards');
SET @category_literature = (SELECT category_id FROM categories WHERE slug = 'van-hoc-giai-tri');
SET @category_economy = (SELECT category_id FROM categories WHERE slug = 'kinh-doanh-kinh-te');
SET @category_cknowledge = (SELECT category_id FROM categories WHERE slug = 'tu-duy-phat-trien-ban-than');
SET @category_lifestyle_Science = (SELECT category_id FROM categories WHERE slug = 'khoa-hoc-doi-song');
SET @category_special_editions = (SELECT category_id FROM categories WHERE slug = 'an-pham-dac-biet');

INSERT INTO publishers (publisher_name, slug, description, is_active, sort_order) VALUES
('NXB Văn Học', 'van-hoc', 'Nhà xuất bản Văn Học', 1, 1),
('NXB Trẻ', 'tre', 'Nhà xuất bản Trẻ', 1, 2),
('NXB Kim Đồng', 'kim-dong', 'Nhà xuất bản Kim Đồng', 1, 3),
('NXB Hội Nhà Văn', 'hoi-nha-van', 'Nhà xuất bản Hội Nhà Văn', 1, 4),
('NXB Phong Trào', 'phong-trao', 'Nhà xuất bản Phong Trào', 1, 5)
ON DUPLICATE KEY UPDATE
    publisher_name = VALUES(publisher_name), description = VALUES(description),
    is_active = VALUES(is_active), sort_order = VALUES(sort_order);

-- Clear home-page flags from the old generic demo products without deleting catalog rows.
UPDATE products
SET is_best_seller = 0, is_new_release = 0, is_recommended = 0
WHERE product_code IN (
    'KT001', 'KT002', 'KT003', 'GD001', 'GD002', 'TL001', 'TL002',
    'LS001', 'LS002', 'KN001', 'KN002', 'TE001', 'TE002'
);

-- Seed the books shown in the original home page. Re-running updates these stable sample rows.
INSERT INTO products
    (product_code, slug, category_id, publisher_id, title, author, isbn, cover_type,
     price, original_price, stock_quantity, sold_quantity, is_best_seller, is_new_release,
     is_recommended, is_active, cover_image, description, weight)
VALUES
('HOME-BS-001', 'who-chuyen-ke-danh-nhan-nikola-tesla', @category_lifestyle_Science, NULL, 'WHO? Chuyện kể về danh nhân thế giới - Nikola Tesla', NULL, NULL, 'paperback', 55000, 60000, 0, 0, 1, 0, 0, 1, 'NikolaTesla.webp', NULL, 0.00),
('HOME-BS-002', 'shingeki-no-kyojin-11-attack-on-titan', @category_literature, NULL, '進撃の巨人 11 - Attack On Titan 11', NULL, NULL, 'paperback', 135000, 152000, 0, 0, 1, 0, 0, 1, 'AOT11.webp', NULL, 0.00),
('HOME-BS-003', 'wonka', @category_literature, NULL, 'Wonka', NULL, NULL, 'paperback', 76000, 85000, 0, 0, 1, 0, 0, 1, 'Wonka.webp', NULL, 0.00),
('HOME-BS-004', 'nha-gia-kim-tai-ban-2025', @category_literature, NULL, 'Nhà Giả Kim (Tái Bản 2025)', NULL, NULL, 'paperback', 72000, 95000, 0, 0, 1, 0, 0, 1, 'NhaGiaKim.webp', NULL, 0.00),
('HOME-NR-001', 'mon-qua-mua-xuan', @category_literature, NULL, 'Món quà mùa xuân', 'Makiko Toyofuku', NULL, 'paperback', 45000, 50000, 0, 0, 0, 1, 0, 1, 'món-quà-mùa-xuân.webp', '"Một câu chuyện dịu dàng và sâu sắc, giúp ta biến những điều từng không thích ở bản thân thành niềm yêu thương."', 0.00),
('HOME-RC-001', 'arya-ban-ben-tieng-nga-vol-10', @category_literature, NULL, 'Arya bàn bên thỉnh thoảng lại trêu ghẹo tôi bằng Tiếng Nga(Vol.10)', NULL, NULL, 'paperback', 85000, 100000, 0, 0, 0, 0, 1, 1, 'arya-ban-ben-10-bia.webp', NULL, 0.00),
('HOME-RC-002', 'mong-vuot-qua-den-2', @category_literature, NULL, 'Móng vuốt quạ đen (II)', NULL, NULL, 'paperback', 90000, 110000, 0, 0, 0, 0, 1, 1, 'móng-vuốt-quạ-đen(II).webp', NULL, 0.00),
('HOME-RC-003', 'tieu-thuyet-doraemon-nobita-lau-dai-duoi-day-bien', @category_literature, NULL, 'Tiểu thuyết Doraemon - Nobita và lâu đài dưới đáy bể', NULL, NULL, 'paperback', 54000, 60000, 0, 0, 0, 0, 1, 1, 'doraemon-và-lâu-đài-dưới-đáy-biển.webp', NULL, 0.00),
('HOME-RC-004', 'kheo-khon-voi-tien-tap-1', @category_lifestyle_Science, NULL, 'Khéo khôn với tiền - Tránh những ưu phiền (Tập 1)', NULL, NULL, 'paperback', 100000, 150000, 0, 0, 0, 0, 1, 1, 'khéo-khôn-với-tiền.webp', NULL, 0.00)
ON DUPLICATE KEY UPDATE
    slug = VALUES(slug), category_id = VALUES(category_id), publisher_id = VALUES(publisher_id),
    title = VALUES(title), author = VALUES(author), isbn = VALUES(isbn), cover_type = VALUES(cover_type),
    price = VALUES(price), original_price = VALUES(original_price), stock_quantity = VALUES(stock_quantity),
    sold_quantity = VALUES(sold_quantity), is_best_seller = VALUES(is_best_seller),
    is_new_release = VALUES(is_new_release), is_recommended = VALUES(is_recommended),
    is_active = VALUES(is_active), cover_image = VALUES(cover_image),
    description = VALUES(description), weight = VALUES(weight);


INSERT INTO product_images (product_id, image_url, is_primary, sort_order, alt_text)
SELECT p.product_id, images.image_url, images.is_primary, images.sort_order, images.alt_text
FROM (
    SELECT 'HOME-BS-001' AS product_code, 'images/products/NikolaTesla.webp' AS image_url, 1 AS is_primary, 1 AS sort_order, 'Bìa sách WHO? Chuyện kể về danh nhân thế giới - Nikola Tesla' AS alt_text
    UNION ALL SELECT 'HOME-BS-002', 'images/products/AOT11.webp', 1, 1, 'Bìa sách 進撃の巨人 11 - Attack On Titan 11'
    UNION ALL SELECT 'HOME-BS-003', 'images/products/Wonka.webp', 1, 1, 'Bìa sách Wonka'
    UNION ALL SELECT 'HOME-BS-004', 'images/products/NhaGiaKim.webp', 1, 1, 'Bìa sách Nhà Giả Kim (Tái Bản 2025)'
    UNION ALL SELECT 'HOME-NR-001', 'images/products/món-quà-mùa-xuân.webp', 1, 1, 'Bìa sách Món quà mùa xuân'
    UNION ALL SELECT 'HOME-RC-001', 'images/products/arya-ban-ben-10-bia.webp', 1, 1, 'Bìa sách Arya bàn bên Tiếng Nga (Vol.10)'
    UNION ALL SELECT 'HOME-RC-002', 'images/products/móng-vuốt-quạ-đen(II).webp', 1, 1, 'Bìa sách Móng vuốt quạ đen (II)'
    UNION ALL SELECT 'HOME-RC-003', 'images/products/doraemon-và-lâu-đài-dưới-đáy-biển.webp', 1, 1, 'Bìa sách Tiểu thuyết Doraemon - Nobita và lâu đài dưới đáy bể'
    UNION ALL SELECT 'HOME-RC-004', 'images/products/khéo-khôn-với-tiền.webp', 1, 1, 'Bìa sách Khéo khôn với tiền - Tránh những ưu phiền (Tập 1)'
) AS images
INNER JOIN products p ON p.product_code = images.product_code
WHERE NOT EXISTS (
    SELECT 1
    FROM product_images existing
    WHERE existing.product_id = p.product_id
      AND existing.image_url = images.image_url
);
    
INSERT INTO home_product_ratings (product_id, rating_average, rating_count)
SELECT p.product_id, ratings.rating_average, ratings.rating_count
FROM (
    SELECT 'HOME-BS-001' AS product_code, 4.2 AS rating_average, 360 AS rating_count
    UNION ALL SELECT 'HOME-BS-002', 4.3, 311
    UNION ALL SELECT 'HOME-BS-003', 4.4, 296
    UNION ALL SELECT 'HOME-BS-004', 4.7, 441
    UNION ALL SELECT 'HOME-RC-001', 4.5, 120
    UNION ALL SELECT 'HOME-RC-002', 4.2, 95
    UNION ALL SELECT 'HOME-RC-003', 4.0, 76
    UNION ALL SELECT 'HOME-RC-004', 5.0, 17
) AS ratings
INNER JOIN products p ON p.product_code = ratings.product_code
ON DUPLICATE KEY UPDATE
    rating_average = VALUES(rating_average),
    rating_count = VALUES(rating_count);

INSERT INTO product_rating_breakdown (product_id, star_rating, rating_percentage)
SELECT p.product_id, breakdown.star_rating, breakdown.rating_percentage
FROM (
    SELECT 'HOME-BS-001' AS product_code, 5 AS star_rating, 62 AS rating_percentage
    UNION ALL SELECT 'HOME-BS-001', 4, 20
    UNION ALL SELECT 'HOME-BS-001', 3, 10
    UNION ALL SELECT 'HOME-BS-001', 2, 5
    UNION ALL SELECT 'HOME-BS-001', 1, 3
) AS breakdown
INNER JOIN products p ON p.product_code = breakdown.product_code
ON DUPLICATE KEY UPDATE rating_percentage = VALUES(rating_percentage);

INSERT INTO product_reviews
    (product_id, reviewer_name, rating, review_text, reviewed_at, avatar_image, is_sample)
SELECT p.product_id, reviews.reviewer_name, reviews.rating, reviews.review_text,
       reviews.reviewed_at, reviews.avatar_image, 1
FROM (
    SELECT 'HOME-BS-001' AS product_code, 'bienkiet_1242' AS reviewer_name, 5 AS rating,
           'Nội dung đánh giá mẫu. Sách in đẹp, nội dung dễ hiểu.' AS review_text,
           '2026-10-12' AS reviewed_at, 'pfp-icon-set/Avatar_Lucia_Elowen.webp' AS avatar_image
    UNION ALL
    SELECT 'HOME-BS-001', 'chumeonangmua_', 4,
           'Nội dung đánh giá mẫu. Giao hàng nhanh, đóng gói cẩn thận.',
           '2026-10-05', 'pfp-icon-set/Avatar_Dialyn.webp'
) AS reviews
INNER JOIN products p ON p.product_code = reviews.product_code
WHERE NOT EXISTS (
    SELECT 1
    FROM product_reviews existing
    WHERE existing.product_id = p.product_id
      AND existing.reviewer_name = reviews.reviewer_name
      AND existing.reviewed_at = reviews.reviewed_at
      AND existing.is_sample = 1
)
ON DUPLICATE KEY UPDATE
    rating = VALUES(rating),
    review_text = VALUES(review_text),
    avatar_image = VALUES(avatar_image);

INSERT INTO home_content (content_key, content_value) VALUES
('banner_image', 'images/banner/main-banner.png'),
('about_image', 'images/banner/about-us.png'),
('about_title', 'Về cửa hàng BetaBook của chúng tôi'),
('about_description', 'BetaBook là nhà sách trực tuyến chuyên cung cấp đa dạng các dòng sách từ học tập - tham khảo - ngoại ngữ cho đến tiểu thuyết, văn học, truyện tranh và kỹ năng sống. Với định hướng trở thành không gian đọc toàn diện như một nhà sách truyền thống thu nhỏ, chúng tôi không chỉ mang đến những công cụ tri thức giúp bạn bứt phá trong học tập và sự nghiệp, mà còn là nơi nuôi dưỡng tâm hồn qua từng trang sách giải trí lôi cuốn.'),
('service_description', 'Các dịch vụ song song với việc mua sách của công ty chúng tôi.')
ON DUPLICATE KEY UPDATE content_value = VALUES(content_value);

INSERT INTO home_reviews (reviewer_name, member_label, quote, avatar_image, sort_order, is_active) VALUES
('bienkiet_1242', 'Hội viên', 'Nhiều thể loại sách cho mọi lứa tuổi, có nhiều chương trình ưu đãi hấp dẫn!', 'pfp-icon-set/Avatar_Lucia_Elowen.webp', 1, 1),
('chumeonangmua_', 'Hội viên', 'Chất lượng sách tốt, giao hàng nhanh chóng!', 'pfp-icon-set/Avatar_Dialyn.webp', 2, 1)
ON DUPLICATE KEY UPDATE
    reviewer_name = VALUES(reviewer_name),
    member_label = VALUES(member_label),
    quote = VALUES(quote),
    avatar_image = VALUES(avatar_image),
    is_active = VALUES(is_active);

INSERT INTO home_coupons (coupon_code, description, expires_at, image_path, sort_order, is_active) VALUES
('BETA10', 'Giảm 10% đơn đầu tiên', '2026-12-31', 'voucher.ico', 1, 1),
('SACH50K', 'Giảm 50.000₫ cho đơn từ 300.000₫', '2026-11-30', 'voucher.ico', 2, 1),
('FREESHIP', 'Miễn phí vận chuyển toàn quốc', '2026-10-31', 'voucher.ico', 3, 1),
('COMBO20', 'Giảm 20% khi mua combo 3 cuốn', '2026-12-15', 'voucher.ico', 4, 1),
('HSSV15', 'Ưu đãi 15% cho học sinh - sinh viên', '2026-12-31', 'voucher.ico', 5, 1),
('BETA50K', 'Giảm 50.000₫ cho hội viên mới', '2027-02-28', 'voucher.ico', 6, 1)
ON DUPLICATE KEY UPDATE
    description = VALUES(description),
    expires_at = VALUES(expires_at),
    image_path = VALUES(image_path),
    is_active = VALUES(is_active);

INSERT INTO home_events (event_slug, event_date, title, description, image_path, sort_order, is_active) VALUES
('xa-kho-mua-thu-2026', '2026-10-04', 'Chương trình xả kho - Mùa thu 2026', 'Đặc biệt giảm giá lên đến 50% cho tất cả các sản phẩm trong chương trình.', 'banner/xả-kho-mùa-thu.png', 1, 1),
('back-to-school-2026', '2026-09-05', 'Back to school 2026 - Cùng bạn đến trường', 'Khuyến mãi đặc biệt cho học sinh và sinh viên trong chương trình Back to school 2026.', 'banner/back-to-school.png', 2, 1)
ON DUPLICATE KEY UPDATE
    event_date = VALUES(event_date),
    title = VALUES(title),
    description = VALUES(description),
    image_path = VALUES(image_path),
    is_active = VALUES(is_active);
