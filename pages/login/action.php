<?php
$pageTitle = 'Đăng nhập';
$bodyClass = 'login-page-body';
$extraCss = [asset('css/login.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/login.css')];
$identifier = '';
$loginError = null;
$loginNotice = $_SESSION['login_notice'] ?? null;
unset($_SESSION['login_notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(400);
        $loginError = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } elseif (($_POST['login_action'] ?? '') === 'logout') {
        unset($_SESSION['user'], $_SESSION['csrf_token']);
        session_regenerate_id(true);
        $_SESSION['login_notice'] = 'Bạn đã đăng xuất.';
        redirect(url('login'));
    } else {
        $identifier = trim(is_string($_POST['identifier'] ?? null) ? $_POST['identifier'] : '');
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $user = $identifier !== '' && $password !== ''
            ? auth_login($identifier, $password)
            : null;

        if ($user) {
            redirect(url('home'));
        }

        $loginError = 'Tên đăng nhập, email, số điện thoại hoặc mật khẩu không đúng.';
    }
}
