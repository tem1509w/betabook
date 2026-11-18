<?php
$pageTitle = 'Đăng nhập';
$bodyClass = 'login-page-body';
$extraCss = [asset('css/login.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/login.css')];
$username = '';
$registerUsername = '';
$registerEmail = '';
$registerPhone = '';
$loginMode = ($_SERVER['REQUEST_METHOD'] === 'POST'
    && is_string($_POST['login_action'] ?? null)
    && $_POST['login_action'] === 'register')
    ? 'register'
    : 'login';
$loginError = null;
$registerErrors = [];
$loginNotice = $_SESSION['login_notice'] ?? null;
unset($_SESSION['login_notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(400);
        if ($loginMode === 'register') {
            $registerErrors[] = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
        } else {
            $loginError = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
        }
    } elseif (($_POST['login_action'] ?? '') === 'logout') {
        unset($_SESSION['cart']);
        unset($_SESSION['cart_coupon_id']);
        unset($_SESSION['user'], $_SESSION['admin_role_id'], $_SESSION['admin_role'], $_SESSION['csrf_token']);
        session_regenerate_id(true);
        $_SESSION['login_notice'] = 'Bạn đã đăng xuất.';
        redirect(url('login'));
    } elseif (($_POST['login_action'] ?? '') === 'register') {
        $registerUsername = trim(is_string($_POST['username'] ?? null) ? $_POST['username'] : '');
        $registerEmail = trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : '');
        $registerPhone = trim(is_string($_POST['phone'] ?? null) ? $_POST['phone'] : '');
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $confirmPassword = is_string($_POST['confirm_password'] ?? null) ? $_POST['confirm_password'] : '';
        $stringLength = static function (string $value): int {
            $length = preg_match_all('/./us', $value);
            return $length === false ? strlen($value) : $length;
        };

        if ($registerUsername === '' || $stringLength($registerUsername) > 191) {
            $registerErrors[] = 'Tên đăng nhập không được để trống và tối đa 191 ký tự.';
        }
        if (!filter_var($registerEmail, FILTER_VALIDATE_EMAIL) || $stringLength($registerEmail) > 191) {
            $registerErrors[] = 'Email không hợp lệ hoặc vượt quá 191 ký tự.';
        }
        if ($registerPhone !== '' && !is_valid_phone_number($registerPhone)) {
            $registerErrors[] = 'Số điện thoại phải gồm 10 hoặc 11 chữ số và bắt đầu bằng 0.';
        }
        if ($stringLength($password) < 6) {
            $registerErrors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }
        if ($confirmPassword !== $password) {
            $registerErrors[] = 'Mật khẩu nhập lại không khớp.';
        }

        if (!$registerErrors) {
            $customerRole = $db->fetchOne(
                'SELECT role_id FROM roles
                 WHERE role_id = 3 AND role_key = \'user\' AND is_active = 1
                 LIMIT 1'
            );
            if (!$customerRole) {
                $registerErrors[] = 'Vai trò khách hàng chưa được cấu hình. Vui lòng liên hệ quản trị viên.';
            }
        }

        if (!$registerErrors) {
            $duplicate = $db->fetchOne(
                'SELECT user_id FROM users
                 WHERE username = ? OR email = ?
                 LIMIT 1',
                [$registerUsername, $registerEmail]
            );
            if ($duplicate) {
                $registerErrors[] = 'Tên đăng nhập hoặc email đã được sử dụng.';
            }
        }

        if (!$registerErrors) {
            $duplicateAccount = false;
            try {
                $result = $db->execute(
                    'INSERT INTO users (username, email, password, phone, role_id, is_active)
                     VALUES (?, ?, ?, ?, 3, 1)',
                    [
                        $registerUsername,
                        $registerEmail,
                        password_hash($password, PASSWORD_DEFAULT),
                        $registerPhone !== '' ? $registerPhone : null,
                    ]
                );
            } catch (PDOException $exception) {
                if ($exception->getCode() !== '23000'
                    || (int)($exception->errorInfo[1] ?? 0) !== 1062) {
                    throw $exception;
                }
                $duplicateAccount = true;
            }

            if ($duplicateAccount) {
                $registerErrors[] = 'Tên đăng nhập hoặc email đã được sử dụng.';
            } elseif ($result !== true) {
                $registerErrors[] = 'Không thể tạo tài khoản. Vui lòng thử lại.';
            } else {
                $user = auth_login($registerUsername, $password);
                if ($user) {
                    $_SESSION['flash_message'] = 'Tạo tài khoản thành công. Xin chào, ' . $user['username'];
                    redirect(url(''));
                }

                $loginMode = 'login';
                $username = $registerUsername;
                $loginNotice = 'Tài khoản đã được tạo. Vui lòng đăng nhập.';
            }
        }
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
