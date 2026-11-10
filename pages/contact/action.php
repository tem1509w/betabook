<?php
$extraCss[] = asset('css/contact.css');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    return;
}

if (!isset($_POST['contact_submit'])) {
    return;
}

$name = trim((string)($_POST['contact_name'] ?? ''));
$email = trim((string)($_POST['contact_email'] ?? ''));
$phone = trim((string)($_POST['contact_phone'] ?? ''));
$subject = trim((string)($_POST['contact_subject'] ?? ''));
$message = trim((string)($_POST['contact_message'] ?? ''));

$contactErrors = [];

if ($name === '') {
    $contactErrors[] = 'Vui lòng nhập họ tên.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $contactErrors[] = 'Vui lòng nhập email hợp lệ.';
}

if ($message === '') {
    $contactErrors[] = 'Vui lòng nhập nội dung tin nhắn.';
}

if ($contactErrors) {
    $contactError = implode(' ', $contactErrors);
    return;
}

$contactSuccess = 'Cảm ơn bạn đã liên hệ với BetaBook. Chúng tôi sẽ phản hồi qua email trong thời gian sớm nhất.';
$_SESSION['contact_success'] = $contactSuccess;
redirect(url('contact'));
