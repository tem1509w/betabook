<?php
// Load the data used by the home page.
$bestSellers = $db->fetchAll(
    "SELECT p.*, c.name AS category_name,
            (SELECT pi.image_url
             FROM product_images pi
             WHERE pi.product_id = p.product_id
             ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.image_id ASC
             LIMIT 1) AS image_url,
            r.rating_average, r.rating_count
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.category_id
     LEFT JOIN home_product_ratings r ON p.product_id = r.product_id
     WHERE p.is_best_seller = 1 AND p.is_active = 1
     ORDER BY p.product_code ASC
     LIMIT 8"
);

$newReleases = $db->fetchAll(
    "SELECT p.*, c.name AS category_name,
            (SELECT pi.image_url
             FROM product_images pi
             WHERE pi.product_id = p.product_id
             ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.image_id ASC
             LIMIT 1) AS image_url
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.category_id
     WHERE p.is_new_release = 1 AND p.is_active = 1
     ORDER BY p.product_code ASC
     LIMIT 8"
);

$recommended = $db->fetchAll(
    "SELECT p.*, c.name AS category_name,
            (SELECT pi.image_url
             FROM product_images pi
             WHERE pi.product_id = p.product_id
             ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.image_id ASC
             LIMIT 1) AS image_url,
            r.rating_average, r.rating_count
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.category_id
     LEFT JOIN home_product_ratings r ON p.product_id = r.product_id
     WHERE p.is_recommended = 1 AND p.is_active = 1
     ORDER BY p.is_best_seller ASC, p.product_code ASC
     LIMIT 8"
);

$homeContent = array_column(
    $db->fetchAll('SELECT content_key, content_value FROM home_content'),
    'content_value',
    'content_key'
);
$homeReviews = $db->fetchAll(
    'SELECT reviewer_name, member_label, quote, avatar_image
     FROM home_reviews
     WHERE is_active = 1
     ORDER BY sort_order ASC'
);
$homeCoupons = $db->fetchAll(
    'SELECT coupon_code, description, expires_at, image_path
     FROM home_coupons
     WHERE is_active = 1
     ORDER BY sort_order ASC, coupon_id ASC
     LIMIT 2'
);
$homeEvents = $db->fetchAll(
    'SELECT event_date, title, description, image_path
     FROM home_events
     WHERE is_active = 1
     ORDER BY sort_order ASC, event_id ASC
     LIMIT 2'
);

$pageTitle = 'Trang chủ';
$bodyClass = 'home-page';
$extraCss = [asset('css/discount-event.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/discount-event.css')];
