<?php
if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.');
}

$userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT) ?: 0;
$username = trim(is_string($_POST['username'] ?? null) ? $_POST['username'] : '');
$fullName = trim(is_string($_POST['full_name'] ?? null) ? $_POST['full_name'] : '');
$email = trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : '');
$phone = trim(is_string($_POST['phone'] ?? null) ? $_POST['phone'] : '');
$roleId = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT) ?: 0;
$avatarImage = is_string($_POST['avatar_image'] ?? null) ? $_POST['avatar_image'] : '';
$password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
$isActive = isset($_POST['is_active']) ? 1 : 0;
$avatarOptions = user_avatar_options();
$errors = [];
$stringLength = static function (string $value): int {
    $characterCount = preg_match_all('/./us', $value);
    return $characterCount === false ? strlen($value) : $characterCount;
};

if ($username === '' || $stringLength($username) > 191) {
    $errors[] = 'Tên đăng nhập không được để trống và tối đa 191 ký tự.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $stringLength($email) > 191) {
    $errors[] = 'Email không hợp lệ hoặc vượt quá 191 ký tự.';
}
if ($stringLength($fullName) > 191 || $stringLength($phone) > 20) {
    $errors[] = 'Họ tên hoặc số điện thoại vượt quá độ dài cho phép.';
}
if ($avatarImage !== '' && !in_array($avatarImage, $avatarOptions, true)) {
    $errors[] = 'Ảnh đại diện không hợp lệ.';
}
$role = $roleId > 0
    ? $db->fetchOne('SELECT role_id FROM roles WHERE role_id = ? AND is_active = 1', [$roleId])
    : false;
if (!$role) {
    $errors[] = 'Vai trò được chọn không tồn tại hoặc đã bị vô hiệu hóa.';
}
if (($userId === 0 && $stringLength($password) < 6) || ($password !== '' && $stringLength($password) < 6)) {
    $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
}

$existingUser = $userId > 0
    ? $db->fetchOne('SELECT user_id, role_id, is_active FROM users WHERE user_id = ?', [$userId])
    : false;
if ($userId > 0 && !$existingUser) {
    $errors[] = 'Tài khoản cần sửa không tồn tại.';
}
$currentUserId = (int)($_SESSION['user']['user_id'] ?? 0);
if ($userId === $currentUserId && ($roleId !== 1 || $isActive !== 1)) {
    $errors[] = 'Không thể đổi vai trò hoặc khóa tài khoản quản trị đang đăng nhập.';
}

$formData = $_POST;
unset($formData['password'], $formData['csrf_token']);

$duplicate = $db->fetchOne(
    'SELECT user_id FROM users
     WHERE (username = ? OR email = ?) AND user_id != ?
     LIMIT 1',
    [$username, $email, $userId]
);
if ($duplicate) {
    $errors[] = 'Tên đăng nhập hoặc email đã được tài khoản khác sử dụng.';
}

if ($errors) {
    $_SESSION['admin_user_form_errors'] = $errors;
    $_SESSION['admin_user_form_data'] = $formData;
    redirect(admin_url('?page=users-form' . ($userId > 0 ? '&id=' . $userId : '')));
}

$avatarImage = $avatarImage !== '' ? $avatarImage : user_default_avatar();
$fullName = $fullName !== '' ? $fullName : null;
$phone = $phone !== '' ? $phone : null;

if ($userId > 0) {
    if ($password !== '') {
        $result = $db->execute(
            'UPDATE users
             SET username = ?, full_name = ?, email = ?, phone = ?, avatar_image = ?,
                 role_id = ?, is_active = ?, password = ?
             WHERE user_id = ?',
            [$username, $fullName, $email, $phone, $avatarImage, $roleId, $isActive, password_hash($password, PASSWORD_DEFAULT), $userId]
        );
    } else {
        $result = $db->execute(
            'UPDATE users
             SET username = ?, full_name = ?, email = ?, phone = ?, avatar_image = ?,
                 role_id = ?, is_active = ?
             WHERE user_id = ?',
            [$username, $fullName, $email, $phone, $avatarImage, $roleId, $isActive, $userId]
        );
    }
} else {
    $result = $db->execute(
        'INSERT INTO users (username, full_name, email, phone, avatar_image, role_id, is_active, password)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
        [$username, $fullName, $email, $phone, $avatarImage, $roleId, $isActive, password_hash($password, PASSWORD_DEFAULT)]
    );
}

if ($result === false) {
    $_SESSION['admin_user_form_errors'] = ['Không thể lưu tài khoản. Vui lòng kiểm tra dữ liệu và cấu hình cơ sở dữ liệu.'];
    $_SESSION['admin_user_form_data'] = $formData;
    redirect(admin_url('?page=users-form' . ($userId > 0 ? '&id=' . $userId : '')));
}

if ($userId === $currentUserId) {
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['full_name'] = $fullName;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['role_id'] = $roleId;
    $_SESSION['user']['avatar_image'] = $avatarImage;
}

$_SESSION['admin_user_notice'] = $userId > 0
    ? 'Đã cập nhật tài khoản.'
    : 'Đã tạo tài khoản mới.';
redirect(admin_url('?page=users'));
