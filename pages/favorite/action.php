<?php
require_once __DIR__ . '/../../helpers/favorites.php';
require_once __DIR__ . '/../../helpers/cart.php';

$isJsonRequest = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

if (favorite_user_id() === null) {
    if ($isJsonRequest) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => 'Vui lòng đăng nhập để sử dụng danh sách yêu thích.',
            'favorite_count' => 0,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    $_SESSION['flash_message'] = 'Vui lòng đăng nhập để sử dụng danh sách yêu thích.';
    redirect(url('login'));
}

$favoriteError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $favoriteError = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
        if ($isJsonRequest) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'error',
                'message' => $favoriteError,
                'favorite_count' => favorite_count(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
        http_response_code(400);
    } else {
        $action = is_string($_POST['favorite_action'] ?? null)
            ? $_POST['favorite_action']
            : (isset($_POST['remove_product']) ? 'remove' : (isset($_POST['add_cart_product']) ? 'add_cart' : ''));

        if ($action === 'add') {
            $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
            $error = $productId && $productId > 0
                ? favorite_add((int)$productId)
                : 'Mã sách không hợp lệ.';
            if ($isJsonRequest) {
                if ($error !== null) {
                    http_response_code(422);
                }
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status' => $error === null ? 'success' : 'error',
                    'message' => $error ?? 'Đã thêm sách vào danh sách yêu thích.',
                    'favorite_count' => favorite_count(),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
            $_SESSION['flash_message'] = $error ?? 'Đã thêm sách vào sản phẩm yêu thích.';
            redirect(url('favorite'));
        } elseif ($action === 'remove') {
            $productId = filter_var($_POST['remove_product'] ?? $_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
            if (!$productId || $productId < 1) {
                $favoriteError = 'Mã sách không hợp lệ.';
            } else {
                favorite_remove((int)$productId);
                $_SESSION['flash_message'] = 'Đã xóa sách khỏi danh sách yêu thích.';
                redirect(url('favorite'));
            }
        } elseif ($action === 'clear') {
            favorite_clear();
            $_SESSION['flash_message'] = 'Đã xóa toàn bộ danh sách yêu thích.';
            redirect(url('favorite'));
        } elseif ($action === 'add_cart') {
            $productId = filter_var($_POST['add_cart_product'] ?? $_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
            $error = $productId && $productId > 0
                ? cart_add((int)$productId, 1)
                : 'Mã sách không hợp lệ.';
            if ($error !== null) {
                $favoriteError = $error;
            } else {
                $_SESSION['flash_message'] = 'Đã thêm sách vào giỏ hàng.';
                redirect(url('favorite'));
            }
        } elseif ($action === 'add_all_cart') {
            $items = favorite_items();
            $addedCount = 0;
            foreach ($items as $item) {
                if ((int)$item['stock_quantity'] > 0) {
                    $error = cart_add((int)$item['product_id'], 1);
                    if ($error === null) {
                        $addedCount++;
                    }
                }
            }
            $_SESSION['flash_message'] = "Đã thêm $addedCount cuốn sách còn hàng vào giỏ hàng.";
            redirect(url('favorite'));
        } else {
            $favoriteError = 'Yêu cầu không hợp lệ.';
        }
    }
}

$favoriteItems = favorite_items();
$pageTitle = 'Sản phẩm yêu thích';
$bodyClass = 'favorite-page';
$extraCss = [
    asset('css/favorite.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/favorite.css'),
];