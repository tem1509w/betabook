<?php
$pageTitle = 'Quản lý tài khoản - Trang quản lý';
$avatarOptions = user_avatar_options();
$canDeleteUsers = $adminRoleKey === 'admin';

if ($page === 'users') {
    $search = trim((string)get_param('q', ''));
    $roleId = filter_var(get_param('role_id', ''), FILTER_VALIDATE_INT);
    $status = get_param('status', '');
    $pageNumber = max(1, (int)get_param('p', 1));
    $pageSize = 25;
    $conditions = [];
    $params = [];

    if ($search !== '') {
        $conditions[] = '(u.username LIKE ? OR u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
        $searchTerm = '%' . $search . '%';
        array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }
    if ($roleId !== false && $roleId !== null && $roleId > 0) {
        $conditions[] = 'u.role_id = ?';
        $params[] = $roleId;
    }
    if ($status === 'active' || $status === 'inactive') {
        $conditions[] = 'u.is_active = ?';
        $params[] = $status === 'active' ? 1 : 0;
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $countRow = $db->fetchOne(
        "SELECT COUNT(*) AS total FROM users u $where",
        $params
    );
    $totalUsers = (int)$countRow['total'];
    $totalPages = max(1, (int)ceil($totalUsers / $pageSize));
    $pageNumber = min($pageNumber, $totalPages);
    $offset = ($pageNumber - 1) * $pageSize;
    $users = $db->fetchAll(
        "SELECT u.user_id, u.username, u.full_name, u.email, u.phone, u.avatar_image,
                u.role_id, u.is_active, u.created_at, u.last_login, r.role_name
         FROM users u
         INNER JOIN roles r ON r.role_id = u.role_id
         $where
         ORDER BY u.user_id DESC
         LIMIT $pageSize OFFSET $offset",
        $params
    );
    $roles = $db->fetchAll(
        'SELECT role_id, role_key, role_name FROM roles WHERE is_active = 1 ORDER BY role_id'
    );
    $accountNotice = $_SESSION['admin_user_notice'] ?? null;
    unset($_SESSION['admin_user_notice']);
    return;
}

$userId = (int)get_param('id', 0);
$user = $userId > 0
    ? ($db->fetchOne(
        'SELECT user_id, username, full_name, email, phone, avatar_image, role_id, is_active
         FROM users WHERE user_id = ?',
        [$userId]
    ) ?: [])
    : [];
$roles = $db->fetchAll(
    'SELECT role_id, role_key, role_name FROM roles WHERE is_active = 1 ORDER BY role_id'
);
$formData = $_SESSION['admin_user_form_data'] ?? [];
$formErrors = $_SESSION['admin_user_form_errors'] ?? [];
unset($_SESSION['admin_user_form_data'], $_SESSION['admin_user_form_errors']);
$data = array_merge($user, $formData);
$defaultRoleId = 0;
foreach ($roles as $role) {
    if ($role['role_key'] === 'user') {
        $defaultRoleId = (int)$role['role_id'];
        break;
    }
}
$data['role_id'] = isset($data['role_id']) ? (int)$data['role_id'] : $defaultRoleId;
if ($formData) {
    $data['is_active'] = isset($formData['is_active']) ? 1 : 0;
} else {
    $data['is_active'] = isset($data['is_active']) ? (int)$data['is_active'] : 1;
}
$data['avatar_image'] = in_array($data['avatar_image'] ?? '', $avatarOptions, true)
    ? $data['avatar_image']
    : user_default_avatar();

if ($userId > 0 && !$user) {
    http_response_code(404);
    $formErrors[] = 'Không tìm thấy tài khoản cần sửa.';
}
