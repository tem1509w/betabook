<?php
$pageTitle = 'Quản lý danh mục - Trang quản lý';

if ($page === 'categories') {
    $categories = $db->fetchAll(
        'SELECT c.*, p.name AS parent_name
         FROM categories c
         LEFT JOIN categories p ON c.parent_id = p.category_id
         ORDER BY c.category_id ASC'
    );
    return;
}

$categoryId = (int)get_param('id', 0);
$category = $categoryId > 0
    ? ($db->fetchOne('SELECT * FROM categories WHERE category_id = ?', [$categoryId]) ?: [])
    : [];
$parentCategories = $db->fetchAll(
    'SELECT category_id, name FROM categories WHERE is_active = 1 ORDER BY name'
);

$formData = $_SESSION['form_data'] ?? [];
$formErrors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data'], $_SESSION['form_errors']);
$data = array_merge($category, $formData);

if ($categoryId > 0 && !$category) {
    http_response_code(404);
    $formErrors[] = 'Không tìm thấy danh mục cần sửa.';
}
