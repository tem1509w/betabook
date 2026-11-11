<?php

if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['admin_user_notice'] = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
} else {
    $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
    $currentUserId = (int)($_SESSION['user']['user_id'] ?? 0);
    $message = null;

    if (!$userId || $userId < 1) {
        $message = 'Mã tài khoản không hợp lệ.';
    } elseif ((int)$userId === $currentUserId) {
        $message = 'Không thể xóa tài khoản quản trị đang đăng nhập.';
    } else {
        $connection = $db->getConnection();
        $connection->beginTransaction();
        try {
            $target = $db->fetchOne(
                'SELECT u.user_id, u.role_id, u.is_active, r.role_key
                 FROM users u
                 INNER JOIN roles r ON r.role_id = u.role_id
                 WHERE u.user_id = ?
                 FOR UPDATE',
                [(int)$userId]
            );

            if (!$target) {
                $message = 'Không tìm thấy tài khoản cần xóa.';
            } elseif ($target['role_key'] === 'admin' && (int)$target['is_active'] === 1) {
                $activeAdmins = $db->fetchAll(
                    "SELECT u.user_id
                     FROM users u
                     INNER JOIN roles r ON r.role_id = u.role_id
                     WHERE r.role_key = 'admin' AND u.is_active = 1
                     FOR UPDATE"
                );
                if (count($activeAdmins) <= 1) {
                    $message = 'Không thể xóa quản trị viên cuối cùng đang hoạt động.';
                }
            }

            if ($message === null) {
                $db->execute('DELETE FROM users WHERE user_id = ?', [(int)$userId]);
                $message = 'Đã xóa tài khoản. Lịch sử đơn hàng được giữ lại.';
            }

            $connection->commit();
        } catch (Throwable $error) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            throw $error;
        }
    }

    $_SESSION['admin_user_notice'] = $message;
}

$search = is_string($_POST['q'] ?? null) ? trim($_POST['q']) : '';
$roleId = filter_var($_POST['role_id'] ?? null, FILTER_VALIDATE_INT);
$status = is_string($_POST['status'] ?? null) ? $_POST['status'] : '';
redirect(admin_url('?' . http_build_query([
    'page' => 'users',
    'q' => $search,
    'role_id' => $roleId && $roleId > 0 ? $roleId : '',
    'status' => $status,
])));
