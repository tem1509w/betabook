<?php
require_once __DIR__ . '/../../helpers/orders.php';

$orderId = filter_var($_SESSION['checkout_order_id'] ?? null, FILTER_VALIDATE_INT);
$order = $orderId && $orderId > 0
    ? $db->fetchOne('SELECT * FROM orders WHERE order_id = ?', [(int)$orderId])
    : false;

if (!$order) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy đơn hàng';
    $errorMessage = 'Không tìm thấy đơn hàng vừa đặt trong phiên làm việc này.';
    $file = __DIR__ . '/../404/Index.php';
    return;
}

$order = orders_with_items([$order])[0];
$pageTitle = 'Đặt hàng thành công';
$bodyClass = 'cart-page';
$extraCss = [asset('css/cart.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/cart.css')];
