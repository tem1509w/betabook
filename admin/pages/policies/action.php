<?php

if ($page === 'policies') {
    $pageTitle = 'Quản lý chính sách - Trang quản lý';
    $policies = $db->fetchAll(
        'SELECT * FROM policies ORDER BY sort_order ASC, policy_id DESC'
    );
}

if ($page === 'policy-form') {
    $policyId = filter_var(get_param('id'), FILTER_VALIDATE_INT) ?: 0;
    $policy = null;

    if ($policyId > 0) {
        $policy = $db->fetchOne('SELECT * FROM policies WHERE policy_id = ?', [$policyId]);
        if (!$policy) {
            redirect(admin_url('?page=policies'));
        }
    }

    $pageTitle = ($policy ? 'Sửa chính sách' : 'Thêm chính sách') . ' - Trang quản lý';
    $formErrors = $_SESSION['form_errors'] ?? [];
    $formData = $_SESSION['form_data'] ?? $policy ?? [];
    unset($_SESSION['form_errors'], $_SESSION['form_data']);
}