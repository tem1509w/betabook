<?php
require_once __DIR__ . '/../../../helpers/orders.php';

if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['flash_message'] = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
} else {
    $orderId = filter_var($_POST['order_id'] ?? null, FILTER_VALIDATE_INT);
    $error = !$orderId || $orderId < 1
        ? 'Mã đơn hàng không hợp lệ.'
        : order_delete((int)$orderId);
    $_SESSION['flash_message'] = $error ?? 'Đã xóa đơn hàng.';
}

$search = is_string($_POST['q'] ?? null) ? trim($_POST['q']) : '';
$status = is_string($_POST['status'] ?? null) ? $_POST['status'] : '';
redirect(admin_url('?' . http_build_query([
    'page' => 'orders',
    'q' => $search,
    'status' => $status,
])));
