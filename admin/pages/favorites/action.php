<?php
if (($actionName ?? null) === 'delete-favorite') {
    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $_SESSION['flash_message'] = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } else {
        $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
        $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$userId || $userId < 1 || !$productId || $productId < 1) {
            $_SESSION['flash_message'] = 'Mục yêu thích không hợp lệ.';
        } else {
            $db->execute(
                'DELETE FROM user_favorite_items WHERE user_id = ? AND product_id = ?',
                [(int)$userId, (int)$productId]
            );
            $_SESSION['flash_message'] = 'Đã xóa sản phẩm khỏi danh sách yêu thích.';
        }
    }

    redirect(admin_url('?page=favorites'));
}

$pageTitle = 'Sản phẩm yêu thích - Trang quản lý';
$search = trim((string)get_param('q', ''));
$stockFilter = (string)get_param('status', '');
$conditions = [];
$params = [];

if ($search !== '') {
    $conditions[] = '(p.title LIKE ? OR p.product_code LIKE ? OR p.author LIKE ? OR u.username LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)';
    $searchLike = '%' . $search . '%';
    array_push($params, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike);
}

if ($stockFilter === 'in_stock') {
    $conditions[] = 'p.stock_quantity > 0';
} elseif ($stockFilter === 'out_of_stock') {
    $conditions[] = 'p.stock_quantity <= 0';
} else {
    $stockFilter = '';
}

$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
$pageNumber = max(1, (int)get_param('p', 1));
$pageSize = 25;

$count = $db->fetchOne(
    "SELECT COUNT(*) AS total
     FROM user_favorite_items f
     INNER JOIN products p ON p.product_id = f.product_id
     INNER JOIN users u ON u.user_id = f.user_id
     $where",
    $params
);
$totalFavorites = (int)($count['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalFavorites / $pageSize));
$pageNumber = min($pageNumber, $totalPages);

$favorites = $db->fetchAll(
    "SELECT f.user_id, f.product_id, f.created_at,
            p.product_code, p.title, p.author, p.price, p.stock_quantity,
            u.username, u.full_name, u.email
     FROM user_favorite_items f
     INNER JOIN products p ON p.product_id = f.product_id
     INNER JOIN users u ON u.user_id = f.user_id
     $where
     ORDER BY f.created_at DESC
     LIMIT $pageSize OFFSET " . (($pageNumber - 1) * $pageSize),
    $params
);

$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);