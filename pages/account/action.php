<?php
require_once __DIR__ . '/../../helpers/orders.php';

$pageTitle = 'Tài khoản của tôi';
$bodyClass = 'account-page-body';
$extraCss = [asset('css/account.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/account.css')];
$avatarOptions = user_avatar_options();
$accountError = null;
$profileFormData = [];
$accountNotice = $_SESSION['account_notice'] ?? null;
$accountActiveTab = $_SESSION['account_active_tab'] ?? get_param('tab', 'info');
if (!in_array($accountActiveTab, ['info', 'password', 'orders'], true)) {
    $accountActiveTab = 'info';
}
unset($_SESSION['account_notice'], $_SESSION['account_active_tab']);

if (empty($_SESSION['user']['user_id'])) {
    redirect(url('login'));
}

$accountUserId = (int)$_SESSION['user']['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['account_action'] ?? '') === 'change_password') {
        $accountActiveTab = 'password';
    }

    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(400);
        $accountError = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } elseif (($_POST['account_action'] ?? '') === 'logout') {
        unset($_SESSION['cart']);
        unset($_SESSION['user'], $_SESSION['admin_role_id'], $_SESSION['admin_role'], $_SESSION['csrf_token'], $_SESSION['account_notice']);
        session_regenerate_id(true);
        $_SESSION['login_notice'] = 'Bạn đã đăng xuất.';
        redirect(url('login'));
    } elseif (($_POST['account_action'] ?? '') === 'update_avatar') {
        $avatarImage = is_string($_POST['avatar_image'] ?? null) ? $_POST['avatar_image'] : '';
        if (!in_array($avatarImage, $avatarOptions, true)) {
            $accountError = 'Ảnh đại diện không hợp lệ. Vui lòng chọn ảnh trong danh sách.';
        } else {
            db()->execute(
                'UPDATE users SET avatar_image = ? WHERE user_id = ? AND is_active = 1',
                [$avatarImage, $accountUserId]
            );
            $_SESSION['user']['avatar_image'] = $avatarImage;
            $_SESSION['account_notice'] = 'Ảnh đại diện đã được cập nhật.';
            redirect(url('account'));
        }
    } elseif (($_POST['account_action'] ?? '') === 'update_profile') {
        $profileFormData = [
            'username' => trim(is_string($_POST['username'] ?? null) ? $_POST['username'] : ''),
            'full_name' => trim(is_string($_POST['full_name'] ?? null) ? $_POST['full_name'] : ''),
            'email' => trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : ''),
            'phone' => trim(is_string($_POST['phone'] ?? null) ? $_POST['phone'] : ''),
        ];
        $stringLength = static function (string $value): int {
            $characterCount = preg_match_all('/./us', $value);
            return $characterCount === false ? strlen($value) : $characterCount;
        };
        $profileErrors = [];

        if ($profileFormData['username'] === '' || $stringLength($profileFormData['username']) > 191) {
            $profileErrors[] = 'Tên đăng nhập không được để trống và tối đa 191 ký tự.';
        }
        if (!filter_var($profileFormData['email'], FILTER_VALIDATE_EMAIL)
            || $stringLength($profileFormData['email']) > 191) {
            $profileErrors[] = 'Email không hợp lệ hoặc vượt quá 191 ký tự.';
        }
        if ($stringLength($profileFormData['full_name']) > 191
            || $stringLength($profileFormData['phone']) > 20) {
            $profileErrors[] = 'Họ tên hoặc số điện thoại vượt quá độ dài cho phép.';
        }

        if (!$profileErrors) {
            $duplicate = db()->fetchOne(
                'SELECT user_id FROM users
                 WHERE (username = ? OR email = ?) AND user_id != ?
                 LIMIT 1',
                [$profileFormData['username'], $profileFormData['email'], $accountUserId]
            );
            if ($duplicate) {
                $profileErrors[] = 'Tên đăng nhập hoặc email đã được tài khoản khác sử dụng.';
            }
        }

        if ($profileErrors) {
            $accountError = implode(' ', $profileErrors);
        } else {
            $fullName = $profileFormData['full_name'] !== '' ? $profileFormData['full_name'] : null;
            $phone = $profileFormData['phone'] !== '' ? $profileFormData['phone'] : null;
            db()->execute(
                'UPDATE users
                 SET username = ?, full_name = ?, email = ?, phone = ?
                 WHERE user_id = ? AND is_active = 1',
                [
                    $profileFormData['username'],
                    $fullName,
                    $profileFormData['email'],
                    $phone,
                    $accountUserId,
                ]
            );
            $_SESSION['user']['username'] = $profileFormData['username'];
            $_SESSION['user']['full_name'] = $fullName;
            $_SESSION['user']['email'] = $profileFormData['email'];
            $_SESSION['account_notice'] = 'Thông tin tài khoản đã được cập nhật.';
            redirect(url('account'));
        }
    } elseif (($_POST['account_action'] ?? '') === 'change_password') {
        $currentPassword = is_string($_POST['current_password'] ?? null)
            ? $_POST['current_password']
            : '';
        $newPassword = is_string($_POST['new_password'] ?? null)
            ? $_POST['new_password']
            : '';
        $confirmPassword = is_string($_POST['confirm_password'] ?? null)
            ? $_POST['confirm_password']
            : '';
        $passwordErrors = [];

        if ($currentPassword === '') {
            $passwordErrors[] = 'Vui lòng nhập mật khẩu hiện tại.';
        }
        if (strlen($newPassword) < 6) {
            $passwordErrors[] = 'Mật khẩu mới cần ít nhất 6 ký tự.';
        }
        if ($newPassword !== $confirmPassword) {
            $passwordErrors[] = 'Mật khẩu xác nhận không khớp.';
        }

        if (!$passwordErrors) {
            $userPassword = db()->fetchOne(
                'SELECT password FROM users WHERE user_id = ? AND is_active = 1',
                [$accountUserId]
            );
            if (!$userPassword || !password_verify($currentPassword, $userPassword['password'])) {
                $passwordErrors[] = 'Mật khẩu hiện tại không chính xác.';
            }
        }

        if ($passwordErrors) {
            $accountError = implode(' ', $passwordErrors);
        } else {
            db()->execute(
                'UPDATE users SET password = ? WHERE user_id = ? AND is_active = 1',
                [password_hash($newPassword, PASSWORD_DEFAULT), $accountUserId]
            );
            $_SESSION['account_notice'] = 'Mật khẩu đã được cập nhật.';
            $_SESSION['account_active_tab'] = 'password';
            redirect(url('account'));
        }
    } else {
        http_response_code(400);
        $accountError = 'Yêu cầu không hợp lệ.';
    }
}

$accountUser = db()->fetchOne(
    'SELECT u.user_id, u.username, u.email, u.full_name, u.phone, u.role_id,
            u.avatar_image, r.role_name, r.is_active AS role_is_active
     FROM users u
     INNER JOIN roles r ON r.role_id = u.role_id
     WHERE u.user_id = ? AND u.is_active = 1',
    [$accountUserId]
);

if (!$accountUser) {
    unset($_SESSION['user'], $_SESSION['admin_role_id'], $_SESSION['admin_role'], $_SESSION['csrf_token']);
    session_regenerate_id(true);
    redirect(url('login'));
}

$accountUser['role_id'] = (int)$accountUser['role_id'];
$accountUser['avatar_image'] = in_array($accountUser['avatar_image'], $avatarOptions, true)
    ? $accountUser['avatar_image']
    : user_default_avatar();
$_SESSION['user']['role_id'] = $accountUser['role_id'];
$_SESSION['user']['avatar_image'] = $accountUser['avatar_image'];
$canAccessAdmin = in_array($accountUser['role_id'], [1, 2], true)
    && (int)$accountUser['role_is_active'] === 1;
$profileData = array_merge($accountUser, $profileFormData);
$accountOrders = orders_with_items($db->fetchAll(
    'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 50',
    [$accountUserId]
));