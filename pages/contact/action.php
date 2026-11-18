<?php
$extraCss[] = asset('css/contact.css');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    return;
}

if (!isset($_POST['contact_submit'])) {
    return;
}

$name = trim(is_string($_POST['contact_name'] ?? null) ? $_POST['contact_name'] : '');
$email = trim(is_string($_POST['contact_email'] ?? null) ? $_POST['contact_email'] : '');
$phone = trim(is_string($_POST['contact_phone'] ?? null) ? $_POST['contact_phone'] : '');
$subject = trim(is_string($_POST['contact_subject'] ?? null) ? $_POST['contact_subject'] : '');
$message = trim(is_string($_POST['contact_message'] ?? null) ? $_POST['contact_message'] : '');
$stringLength = static function (string $value): int {
    $characterCount = preg_match_all('/./us', $value);
    return $characterCount === false ? strlen($value) : $characterCount;
};

$contactErrors = [];

if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $contactErrors[] = 'Phiên gửi tin nhắn đã hết hạn. Vui lòng tải lại trang.';
}

if ($name === '') {
    $contactErrors[] = 'Vui lòng nhập họ tên.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $contactErrors[] = 'Vui lòng nhập email hợp lệ.';
}

if ($stringLength($name) > 191 || $stringLength($email) > 191
    || $stringLength($phone) > 20 || $stringLength($subject) > 255) {
    $contactErrors[] = 'Thông tin liên hệ vượt quá độ dài cho phép.';
}

if ($message === '') {
    $contactErrors[] = 'Vui lòng nhập nội dung tin nhắn.';
} elseif (strlen($message) > 65535) {
    $contactErrors[] = 'Nội dung tin nhắn quá dài.';
}

if ($phone !== '' && !is_valid_phone_number($phone)) {
    $contactErrors[] = 'Số điện thoại phải gồm 10 hoặc 11 chữ số và bắt đầu bằng 0.';
}

if ($contactErrors) {
    $contactError = implode(' ', $contactErrors);
    return;
}

db()->execute(
    'INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)',
    [$name, $email, $phone !== '' ? $phone : null, $subject !== '' ? $subject : null, $message]
);

$contactSuccess = 'Cảm ơn bạn đã liên hệ với BetaBook. Chúng tôi sẽ phản hồi qua email trong thời gian sớm nhất.';
$_SESSION['contact_success'] = $contactSuccess;
redirect(url('contact'));
