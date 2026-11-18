<?php

function csrf_token(): string {
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function valid_csrf_token($token): bool {
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function security_get_admin_role($db, $userId) {
    $userId = filter_var($userId, FILTER_VALIDATE_INT);
    if ($userId === false || $userId === null || $userId < 1) {
        return false;
    }

    return $db->fetchOne(
        'SELECT r.role_id, r.role_key, r.role_name
         FROM users u
         INNER JOIN roles r ON r.role_id = u.role_id
         WHERE u.user_id = ? AND u.is_active = 1 AND r.is_active = 1
           AND u.role_id IN (1, 2)',
        [$userId]
    );
}

function security_user_has_role($roleKey, array $allowedRoleKeys): bool {
    return is_string($roleKey) && in_array($roleKey, $allowedRoleKeys, true);
}

function security_has_admin_permission(array $allowedMenuKeys, string $menuKey): bool {
    return in_array($menuKey, $allowedMenuKeys, true);
}
