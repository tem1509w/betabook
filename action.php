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
    require_once __DIR__ . '/helpers/cart.php';
    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.']);
        exit;
    }

    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $quantity = $quantity === null ? 1 : $quantity;

    $error = !$productId || $quantity < 1
        ? 'Sản phẩm hoặc số lượng không hợp lệ.'
        : cart_add((int)$productId, (int)$quantity);
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

if ($action === 'contact') {
    $name = trim(is_string($_POST['name'] ?? null) ? $_POST['name'] : '');
    $email = trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : '');
    $phone = trim(is_string($_POST['phone'] ?? null) ? $_POST['phone'] : '');
    $subject = trim(is_string($_POST['subject'] ?? null) ? $_POST['subject'] : '');
    $message = trim(is_string($_POST['message'] ?? null) ? $_POST['message'] : '');
    $stringLength = static function (string $value): int {
        $characterCount = preg_match_all('/./us', $value);
        return $characterCount === false ? strlen($value) : $characterCount;
    };

    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.']);
        exit;
    }

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === ''
        || strlen($message) > 65535
        || $stringLength($name) > 191 || $stringLength($email) > 191
        || $stringLength($phone) > 20 || $stringLength($subject) > 255
        || ($phone !== '' && !is_valid_phone_number($phone))) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng kiểm tra tên, email, số điện thoại, chủ đề và nội dung.']);
        exit;
    }

    db()->execute(
        'INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)',
        [$name, $email, $phone !== '' ? $phone : null, $subject !== '' ? $subject : null, $message]
    );
    echo json_encode(['status' => 'success', 'message' => 'Gửi tin nhắn thành công.']);
    exit;
}

http_response_code(404);
echo json_encode(['status' => 'error', 'message' => 'Action không tồn tại.']);
