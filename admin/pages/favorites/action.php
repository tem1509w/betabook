<?php
if (in_array(($actionName ?? null), ['delete-favorite', 'delete-user-favorites'], true)) {
    $isDeletingCustomerFavorites = $actionName === 'delete-user-favorites';
    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $_SESSION['flash_message'] = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } else {
        $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
        $productId = $isDeletingCustomerFavorites
            ? null
            : filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
        if ($userId === false || $userId === null || $userId < 1
            || (!$isDeletingCustomerFavorites && ($productId === false || $productId === null || $productId < 1))) {
            $_SESSION['flash_message'] = 'Mục yêu thích không hợp lệ.';
        } else {
            if ($isDeletingCustomerFavorites) {
                $db->execute('DELETE FROM user_favorite_items WHERE user_id = ?', [(int)$userId]);
                $_SESSION['flash_message'] = 'Đã xóa danh sách yêu thích của khách hàng.';
            } else {
                $db->execute(
                    'DELETE FROM user_favorite_items WHERE user_id = ? AND product_id = ?',
                    [(int)$userId, (int)$productId]
                );
                $_SESSION['flash_message'] = 'Đã xóa sản phẩm khỏi danh sách yêu thích.';
            }
        }
    }

    $returnUserId = filter_var($_POST['return_user_id'] ?? null, FILTER_VALIDATE_INT);
    redirect($returnUserId !== false && $returnUserId !== null && $returnUserId > 0
        ? admin_url('?' . http_build_query(['page' => 'favorites', 'user_id' => (int)$returnUserId]))
        : admin_url('?page=favorites'));
}

$pageTitle = 'Sản phẩm yêu thích - Trang quản lý';
$search = trim((string)get_param('q', ''));
$requestedUserId = get_param('user_id', '');
$detailUserId = is_scalar($requestedUserId)
    ? filter_var((string)$requestedUserId, FILTER_VALIDATE_INT)
    : false;
$showFavoriteDetails = $requestedUserId !== '' && $detailUserId !== false && $detailUserId > 0;
$favoriteUser = false;
$favoriteBooks = [];

if ($showFavoriteDetails) {
    $favoriteUser = $db->fetchOne(
        'SELECT user_id, username, full_name, email FROM users WHERE user_id = ?',
        [(int)$detailUserId]
    );
    if ($favoriteUser) {
        $favoriteBooks = $db->fetchAll(
            'SELECT f.product_id, f.created_at, p.product_code, p.title, p.author, p.price, p.stock_quantity
             FROM user_favorite_items f
             INNER JOIN products p ON p.product_id = f.product_id
             WHERE f.user_id = ?
             ORDER BY f.created_at ASC, f.product_id ASC',
            [(int)$detailUserId]
        );
    }

    $totalCustomers = 0;
    $totalPages = 1;
    $pageNumber = 1;
    $customers = [];
} else {
    $conditions = [];
    $params = [];

    if ($search !== '') {
        $conditions[] = '(p.title LIKE ? OR p.product_code LIKE ? OR p.author LIKE ? OR u.username LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)';
        $searchLike = '%' . $search . '%';
        array_push($params, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike);
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $pageNumber = max(1, (int)get_param('p', 1));
    $pageSize = 25;

    $count = $db->fetchOne(
        "SELECT COUNT(DISTINCT f.user_id) AS total
         FROM user_favorite_items f
         INNER JOIN products p ON p.product_id = f.product_id
         INNER JOIN users u ON u.user_id = f.user_id
         $where",
        $params
    );
    $totalCustomers = (int)($count['total'] ?? 0);
    $totalPages = max(1, (int)ceil($totalCustomers / $pageSize));
    $pageNumber = min($pageNumber, $totalPages);

    $customers = $db->fetchAll(
        "SELECT u.user_id, u.username, u.full_name, u.email,
                MAX(f_all.created_at) AS changed_at, MIN(f_all.created_at) AS first_favorited_at,
                COUNT(*) AS favorite_count
         FROM (
             SELECT DISTINCT f.user_id
             FROM user_favorite_items f
             INNER JOIN products p ON p.product_id = f.product_id
             INNER JOIN users u ON u.user_id = f.user_id
             $where
         ) matched_users
         INNER JOIN users u ON u.user_id = matched_users.user_id
         INNER JOIN user_favorite_items f_all ON f_all.user_id = matched_users.user_id
         GROUP BY u.user_id, u.username, u.full_name, u.email
         ORDER BY first_favorited_at ASC, u.user_id ASC
         LIMIT $pageSize OFFSET " . (($pageNumber - 1) * $pageSize),
        $params
    );
}

$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);