<?php
$categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: 0;
$name = trim(is_string($_POST['name'] ?? null) ? $_POST['name'] : '');
$description = trim(is_string($_POST['description'] ?? null) ? $_POST['description'] : '');
$parentId = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT) ?: 0;
$isActive = isset($_POST['is_active']) ? 1 : 0;

$errors = [];
if ($name === '') {
    $errors[] = 'Tên danh mục không được để trống.';
}
if ($parentId > 0 && $parentId === $categoryId) {
    $errors[] = 'Danh mục không thể là cha của chính nó.';
}
if ($parentId > 0 && !$db->fetchOne('SELECT category_id FROM categories WHERE category_id = ?', [$parentId])) {
    $errors[] = 'Danh mục cha được chọn không tồn tại.';
}

$slugSource = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
$slugBase = trim((string)preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($slugSource ?: $name)), '-');
$slug = $slugBase !== '' ? $slugBase : 'category';

$slugSuffix = 2;
while ($db->fetchOne(
    'SELECT category_id FROM categories WHERE slug = ? AND category_id != ?',
    [$slug, $categoryId]
)) {
    $slug = ($slugBase !== '' ? $slugBase : 'category') . '-' . $slugSuffix++;
}

if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    redirect(admin_url('?page=category-form' . ($categoryId > 0 ? '&id=' . $categoryId : '')));
}

$parentId = $parentId > 0 ? $parentId : null;
if ($categoryId > 0) {
    $result = $db->execute(
        'UPDATE categories SET name = ?, slug = ?, parent_id = ?, description = ?, is_active = ? WHERE category_id = ?',
        [$name, $slug, $parentId, $description, $isActive, $categoryId]
    );
} else {
    $result = $db->execute(
        'INSERT INTO categories (name, slug, parent_id, description, is_active) VALUES (?, ?, ?, ?, ?)',
        [$name, $slug, $parentId, $description, $isActive]
    );
}

if ($result === false) {
    $_SESSION['form_errors'] = ['Không thể lưu danh mục. Vui lòng kiểm tra dữ liệu và cấu hình cơ sở dữ liệu.'];
    $_SESSION['form_data'] = $_POST;
    redirect(admin_url('?page=category-form' . ($categoryId > 0 ? '&id=' . $categoryId : '')));
}

redirect(admin_url('?page=categories'));
