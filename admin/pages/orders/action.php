<?php
require_once __DIR__ . '/../../../helpers/orders.php';

$pageTitle = 'Quản lý đơn hàng - Trang quản lý';
$search = trim((string)get_param('q', ''));
$statusFilter = (string)get_param('status', '');
$allowedStatuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
$conditions = [];
$params = [];

if ($search !== '') {
    $conditions[] = '(o.order_code LIKE ? OR o.customer_name LIKE ? OR o.email LIKE ? OR o.phone LIKE ?)';
    $searchLike = '%' . $search . '%';
    array_push($params, $searchLike, $searchLike, $searchLike, $searchLike);
}
if (in_array($statusFilter, $allowedStatuses, true)) {
    $conditions[] = 'o.order_status = ?';
    $params[] = $statusFilter;
} else {
    $statusFilter = '';
}
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
$pageNumber = max(1, (int)get_param('p', 1));
$pageSize = 25;
$count = $db->fetchOne("SELECT COUNT(*) AS total FROM orders o $where", $params);
$totalOrders = (int)($count['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalOrders / $pageSize));
$pageNumber = min($pageNumber, $totalPages);
$orders = $db->fetchAll(
    "SELECT o.* FROM orders o $where
     ORDER BY o.created_at DESC, o.order_id DESC
     LIMIT $pageSize OFFSET " . (($pageNumber - 1) * $pageSize),
    $params
);
$orders = orders_with_items($orders);
$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
