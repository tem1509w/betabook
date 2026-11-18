<?php
if ($page === 'policies') {
    $pageTitle = 'Quản lý chính sách - Trang quản lý';
    $policies = $db->fetchAll(
        'SELECT * FROM policies ORDER BY sort_order ASC, policy_id DESC'
    );
    return;
}

$policyId = filter_var(get_param('id'), FILTER_VALIDATE_INT);
$policyId = $policyId !== false && $policyId !== null && $policyId > 0 ? $policyId : 0;
$policy = $policyId > 0
    ? ($db->fetchOne('SELECT * FROM policies WHERE policy_id = ?', [$policyId]) ?: null)
    : null;

if ($policyId > 0 && !$policy) {
    http_response_code(404);
    redirect(admin_url('?page=policies'));
}

$pageTitle = ($policy ? 'Sửa chính sách' : 'Thêm chính sách') . ' - Trang quản lý';
$formErrors = $_SESSION['form_errors'] ?? [];
$formData = array_merge($policy ?? [], $_SESSION['form_data'] ?? []);
unset($_SESSION['form_errors'], $_SESSION['form_data']);
