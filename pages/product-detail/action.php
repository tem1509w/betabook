<?php
$productId = get_param('id');

if (!is_string($productId) || !ctype_digit($productId) || (int)$productId < 1) {
    http_response_code(400);
    $pageTitle = 'Thiếu mã sách';
    $errorMessage = 'Vui lòng cung cấp mã sách hợp lệ.';
    $file = __DIR__ . '/../404/Index.php';
    return;
}

$product = $db->fetchOne(
    "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
            pub.publisher_name,
            (SELECT pi.image_url
             FROM product_images pi
             WHERE pi.product_id = p.product_id
             ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.image_id ASC
             LIMIT 1) AS image_url
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.category_id
     LEFT JOIN publishers pub ON p.publisher_id = pub.publisher_id
     WHERE p.product_id = ? AND p.is_active = 1",
    [(int)$productId]
);

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy sách';
    $errorMessage = 'Sách bạn đang tìm kiếm không tồn tại hoặc đã bị ẩn.';
    $file = __DIR__ . '/../404/Index.php';
    return;
}

$relatedProducts = $db->fetchAll(
    "SELECT p.*, c.name AS category_name,
            (SELECT pi.image_url
             FROM product_images pi
             WHERE pi.product_id = p.product_id
             ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.image_id ASC
             LIMIT 1) AS image_url
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.category_id
     WHERE p.is_recommended = 1 AND p.product_id != ? AND p.is_active = 1
     ORDER BY p.product_code ASC
     LIMIT 4",
    [(int)$productId]
);

$pageTitle = $product['title'];
$bodyClass = 'product-detail-page';
$extraCss = [asset('css/product-detail.css')];
