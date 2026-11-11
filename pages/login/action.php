<?php
$pageTitle = 'Đăng nhập';
$bodyClass = 'login-page-body';
$extraCss = [asset('css/login.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/login.css')];
$username = '';
$loginError = null;
$loginNotice = $_SESSION['login_notice'] ?? null;
unset($_SESSION['login_notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(400);
        $loginError = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } elseif (($_POST['login_action'] ?? '') === 'logout') {
        unset($_SESSION['user'], $_SESSION['admin_role_id'], $_SESSION['admin_role'], $_SESSION['csrf_token']);
        session_regenerate_id(true);
        $_SESSION['login_notice'] = 'Bạn đã đăng xuất.';
        redirect(url('login'));
    } else {
        $username = trim(is_string($_POST['username'] ?? null) ? $_POST['username'] : '');
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $user = $username !== '' && $password !== ''
            ? auth_login($username, $password)
            : null;

        if ($user) {
            $_SESSION['flash_message'] = 'Xin chào, ' . $user['username'];
            redirect(url(''));
        }

        $loginError = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    }
}
