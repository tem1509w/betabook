<?php
/**
 * JSON endpoint for public form actions.
 */

require_once __DIR__ . '/config/init.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ.']);
    exit;
}

$action = get_param('action');

if ($action === 'add_to_cart') {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $quantity = $quantity === null ? 1 : $quantity;

    if (!$productId || $productId < 1 || $quantity < 1) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Sản phẩm hoặc số lượng không hợp lệ.']);
        exit;
    }

    $product = db()->fetchOne(
        'SELECT product_id, title, stock_quantity FROM products WHERE product_id = ? AND is_active = 1',
        [$productId]
    );
    if (!$product) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Sách không tồn tại hoặc đã bị ẩn.']);
        exit;
    }

    $cart = $_SESSION['cart'] ?? [];
    $currentQuantity = (int)($cart[$productId] ?? 0);
    if ($currentQuantity + $quantity > (int)$product['stock_quantity']) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'message' => 'Số lượng trong giỏ vượt quá tồn kho.']);
        exit;
    }

    $cart[$productId] = $currentQuantity + $quantity;
    $_SESSION['cart'] = $cart;
    echo json_encode([
        'status' => 'success',
        'message' => $product['title'] . ' đã được thêm vào giỏ hàng.',
        'cart_count' => array_sum($cart),
    ]);
    exit;
}

if ($action === 'contact') {
    $name = trim(is_string($_POST['name'] ?? null) ? $_POST['name'] : '');
    $email = trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : '');
    $subject = trim(is_string($_POST['subject'] ?? null) ? $_POST['subject'] : '');
    $message = trim(is_string($_POST['message'] ?? null) ? $_POST['message'] : '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập tên, email hợp lệ và nội dung.']);
        exit;
    }

    db()->execute(
        'INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)',
        [$name, $email, $subject, $message]
    );
    echo json_encode(['status' => 'success', 'message' => 'Gửi tin nhắn thành công.']);
    exit;
}

http_response_code(404);
echo json_encode(['status' => 'error', 'message' => 'Action không tồn tại.']);
