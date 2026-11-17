<?php
$policyId = filter_input(INPUT_POST, 'policy_id', FILTER_VALIDATE_INT) ?: 0;
$title = trim(is_string($_POST['title'] ?? null) ? $_POST['title'] : '');
$slugInput = trim(is_string($_POST['slug'] ?? null) ? $_POST['slug'] : '');
$pageGroup = trim(is_string($_POST['page_group'] ?? null) ? $_POST['page_group'] : 'Chính sách');
$sortOrder = filter_input(INPUT_POST, 'sort_order', FILTER_VALIDATE_INT) ?: 0;
$isActive = isset($_POST['is_active']) ? 1 : 0;
$content = trim(is_string($_POST['content'] ?? null) ? $_POST['content'] : '');

$errors = [];
if ($title === '') {
    $errors[] = 'Tiêu đề chính sách không được để trống.';
}
if ($content === '') {
    $errors[] = 'Nội dung chính sách không được để trống.';
}

// Tạo slug tự động nếu bỏ trống, và đảm bảo không bị trùng
$rawSlug = $slugInput !== '' ? $slugInput : $title;
$slugSource = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $rawSlug);
$slugBase = trim((string)preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($slugSource ?: $rawSlug)), '-');
$slug = $slugBase !== '' ? $slugBase : 'policy';

$slugSuffix = 2;
while ($db->fetchOne(
    'SELECT policy_id FROM policies WHERE slug = ? AND policy_id != ?',
    [$slug, $policyId]
)) {
    $slug = ($slugBase !== '' ? $slugBase : 'policy') . '-' . $slugSuffix++;
}

if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    redirect(admin_url('?page=policy-form' . ($policyId > 0 ? '&id=' . $policyId : '')));
}

if ($policyId > 0) {
    $result = $db->execute(
        'UPDATE policies SET title = ?, slug = ?, page_group = ?, sort_order = ?, is_active = ?, content = ? WHERE policy_id = ?',
        [$title, $slug, $pageGroup, $sortOrder, $isActive, $content, $policyId]
    );
} else {
    $result = $db->execute(
        'INSERT INTO policies (title, slug, page_group, sort_order, is_active, content) VALUES (?, ?, ?, ?, ?, ?)',
        [$title, $slug, $pageGroup, $sortOrder, $isActive, $content]
    );
}

if ($result === false) {
    $_SESSION['form_errors'] = ['Không thể lưu chính sách. Vui lòng kiểm tra lại dữ liệu.'];
    $_SESSION['form_data'] = $_POST;
    redirect(admin_url('?page=policy-form' . ($policyId > 0 ? '&id=' . $policyId : '')));
}

redirect(admin_url('?page=policies'));