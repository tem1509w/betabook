<?php
$pageTitle = 'Quản lý sách - Trang quản lý';

if ($page === 'books') {
    $books = $db->fetchAll(
        "SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.category_id
         ORDER BY p.is_active DESC, p.is_best_seller DESC, p.product_id DESC"
    );
    return;
}

$bookId = (int)get_param('id', 0);
$book = $bookId > 0
    ? ($db->fetchOne('SELECT * FROM products WHERE product_id = ?', [$bookId]) ?: [])
    : [];

$categories = $db->fetchAll(
    'SELECT category_id, name FROM categories WHERE is_active = 1 ORDER BY name'
);
$publishers = $db->fetchAll(
    'SELECT publisher_id, publisher_name FROM publishers WHERE is_active = 1 ORDER BY publisher_name'
);

$formData = $_SESSION['form_data'] ?? [];
$formErrors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data'], $_SESSION['form_errors']);
$data = array_merge($book, $formData);

if ($bookId > 0 && !$book) {
    http_response_code(404);
    $formErrors[] = 'Không tìm thấy sách cần sửa.';
}
