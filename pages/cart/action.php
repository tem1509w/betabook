<?php
require_once __DIR__ . '/../../helpers/cart.php';
require_once __DIR__ . '/../../helpers/orders.php';

$cartError = null;
$renderCheckoutErrors = false;
$checkoutData = [
    'customer_name' => '',
    'email' => '',
    'phone' => '',
    'address_line' => '',
    'ward' => '',
    'district' => '',
    'city' => '',
    'customer_note' => '',
    'payment_method' => 'cod',
];

$userId = filter_var($_SESSION['user']['user_id'] ?? null, FILTER_VALIDATE_INT);
if ($userId && $userId > 0) {
    $account = $db->fetchOne(
        'SELECT full_name, email, phone FROM users WHERE user_id = ? AND is_active = 1',
        [(int)$userId]
    );
    if ($account) {
        $checkoutData['customer_name'] = (string)($account['full_name'] ?? '');
        $checkoutData['email'] = (string)$account['email'];
        $checkoutData['phone'] = (string)($account['phone'] ?? '');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(400);
        $cartError = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
        if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'error',
                'message' => $cartError,
                'cart_count' => cart_count(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    } else {
        $action = is_string($_POST['cart_action'] ?? null)
            ? $_POST['cart_action']
            : (isset($_POST['remove_product']) ? 'remove' : '');
        if ($action === 'add') {
            $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
            $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
            $quantity = $quantity === false || $quantity === null ? 0 : (int)$quantity;
            $error = $productId && $productId > 0
                ? cart_add((int)$productId, $quantity)
                : 'Mã sách không hợp lệ.';

            $isJsonRequest = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
            if ($isJsonRequest) {
                header('Content-Type: application/json; charset=utf-8');
                if ($error !== null) {
                    http_response_code(422);
                }
                echo json_encode([
                    'status' => $error === null ? 'success' : 'error',
                    'message' => $error ?? 'Đã thêm sách vào giỏ hàng.',
                    'cart_count' => cart_count(),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            if ($error !== null) {
                $_SESSION['flash_message'] = $error;
                redirect(url('product-detail', ['id' => (int)$productId]));
            }
            if (($_POST['intent'] ?? '') === 'buy_now') {
                redirect(url('cart'));
            }
            $_SESSION['flash_message'] = 'Sách đã được thêm vào giỏ hàng.';
            redirect(url('product-detail', ['id' => (int)$productId]));
        }

        if ($action === 'update') {
            $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
            $quantities = $_POST['quantities'] ?? [];
            if ($productId && $productId > 0) {
                $quantity = is_array($quantities)
                    ? filter_var($quantities[(int)$productId] ?? null, FILTER_VALIDATE_INT)
                    : false;
                $cartError = $quantity === false || $quantity === null
                    ? 'Sản phẩm hoặc số lượng không hợp lệ.'
                    : cart_update((int)$productId, (int)$quantity);
            } elseif (is_array($quantities)) {
                foreach ($quantities as $id => $quantityValue) {
                    $id = filter_var($id, FILTER_VALIDATE_INT);
                    $quantity = filter_var($quantityValue, FILTER_VALIDATE_INT);
                    if (!$id || $id < 1 || $quantity === false || $quantity === null) {
                        $cartError = 'Sản phẩm hoặc số lượng không hợp lệ.';
                        break;
                    }
                    $cartError = cart_update((int)$id, (int)$quantity);
                    if ($cartError !== null) {
                        break;
                    }
                }
            } else {
                $cartError = 'Không tìm thấy sản phẩm cần cập nhật.';
            }
        } elseif ($action === 'remove') {
            $productId = filter_var($_POST['remove_product'] ?? $_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
            if (!$productId || $productId < 1) {
                $cartError = 'Mã sách không hợp lệ.';
            } else {
                cart_remove((int)$productId);
            }
        } elseif ($action === 'clear') {
            cart_clear();
        } elseif ($action === 'checkout') {
            $errors = [];
            foreach ($checkoutData as $field => $default) {
                if (array_key_exists($field, $_POST)) {
                    if (!is_string($_POST[$field])) {
                        $errors[] = 'Dữ liệu thông tin nhận hàng không hợp lệ.';
                    } else {
                        $checkoutData[$field] = trim($_POST[$field]);
                    }
                }
            }
            if ($checkoutData['customer_name'] === '' || strlen($checkoutData['customer_name']) > 191) {
                $errors[] = 'Vui lòng nhập họ tên hợp lệ.';
            }
            if (!filter_var($checkoutData['email'], FILTER_VALIDATE_EMAIL)
                || strlen($checkoutData['email']) > 191) {
                $errors[] = 'Vui lòng nhập email hợp lệ.';
            }
            $phoneDigits = preg_replace('/\D+/', '', $checkoutData['phone']);
            if (strlen($phoneDigits) < 9 || strlen($phoneDigits) > 15
                || strlen($checkoutData['phone']) > 20) {
                $errors[] = 'Vui lòng nhập số điện thoại hợp lệ.';
            }
            foreach (['address_line', 'ward', 'district', 'city'] as $field) {
                if ($checkoutData[$field] === '' || strlen($checkoutData[$field]) > 255) {
                    $errors[] = 'Vui lòng nhập đầy đủ địa chỉ giao hàng.';
                    break;
                }
            }
            if (strlen($checkoutData['ward']) > 191 || strlen($checkoutData['district']) > 191
                || strlen($checkoutData['city']) > 191) {
                $errors[] = 'Thông tin địa chỉ vượt quá độ dài cho phép.';
            }
            if (strlen($checkoutData['customer_note']) > 2000) {
                $errors[] = 'Ghi chú không được vượt quá 2.000 ký tự.';
            }
            if (!in_array($checkoutData['payment_method'], ['cod', 'bank_transfer'], true)) {
                $errors[] = 'Phương thức thanh toán không hợp lệ.';
            }
            if (($_POST['agree_terms'] ?? '') !== '1') {
                $errors[] = 'Vui lòng đồng ý với điều khoản mua hàng.';
            }

            if ($errors) {
                $cartError = implode(' ', $errors);
                $renderCheckoutErrors = true;
            } else {
                try {
                    $orderId = order_create_from_cart($checkoutData);
                    $_SESSION['checkout_order_id'] = $orderId;
                    redirect(url('order-complete'));
                } catch (DomainException $error) {
                    $cartError = $error->getMessage();
                    $renderCheckoutErrors = true;
                }
            }
        } else {
            $cartError = 'Yêu cầu không hợp lệ.';
        }

        if ($action === 'checkout' && $renderCheckoutErrors) {
            http_response_code(422);
        } elseif ($cartError !== null) {
            $_SESSION['flash_message'] = $cartError;
            redirect(url('cart'));
        } else {
            $_SESSION['flash_message'] = 'Giỏ hàng đã được cập nhật.';
            redirect(url('cart'));
        }
    }
}

$cartItems = cart_items();
$cartTotal = cart_total($cartItems);
$pageTitle = 'Giỏ hàng & thanh toán - BetaBook';
$bodyClass = 'cart-page';
$extraCss = [asset('css/cart.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/cart.css')];
