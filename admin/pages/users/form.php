<div class="admin-content">
    <div class="page-header">
        <h2><?= $userId > 0 ? 'Sửa tài khoản' : 'Thêm tài khoản' ?></h2>
    </div>

    <?php if ($formErrors): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($formErrors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($userId === 0 || $user): ?>
        <form action="<?= admin_url('?action=save-user') ?>" method="post" class="admin-form user-admin-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="user_id" value="<?= $userId ?>">

            <div class="form-group">
                <label for="username">Tên đăng nhập <span class="required">*</span></label>
                <input id="username" type="text" name="username" maxlength="191" value="<?= e((string)($data['username'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
                <label for="full_name">Họ và tên</label>
                <input id="full_name" type="text" name="full_name" maxlength="191" value="<?= e((string)($data['full_name'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input id="email" type="email" name="email" maxlength="191" value="<?= e((string)($data['email'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input id="phone" type="tel" name="phone" maxlength="20" value="<?= e((string)($data['phone'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label for="role_id">Vai trò <span class="required">*</span></label>
                <select id="role_id" name="role_id" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= (int)$role['role_id'] ?>" <?= $data['role_id'] === (int)$role['role_id'] ? 'selected' : '' ?>>
                            <?= e($role['role_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="avatar_image">Ảnh đại diện</label>
                <select id="avatar_image" name="avatar_image">
                    <option value="">Ảnh mặc định</option>
                    <?php foreach ($avatarOptions as $avatarOption): ?>
                        <option value="<?= e($avatarOption) ?>" <?= $data['avatar_image'] === $avatarOption ? 'selected' : '' ?>>
                            <?= e(pathinfo($avatarOption, PATHINFO_FILENAME)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="password"><?= $userId > 0 ? 'Mật khẩu mới (để trống nếu không đổi)' : 'Mật khẩu ban đầu' ?></label>
                <input id="password" type="password" name="password" minlength="6" autocomplete="new-password" <?= $userId === 0 ? 'required' : '' ?>>
                <small>Mật khẩu phải có ít nhất 6 ký tự; mật khẩu hiện tại không được hiển thị.</small>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" <?= $data['is_active'] === 1 ? 'checked' : '' ?>>
                    Tài khoản được phép đăng nhập
                </label>
            </div>
            <button type="submit" class="btn-submit">Lưu tài khoản</button>
            <a href="<?= admin_url('?page=users') ?>" class="btn-cancel">Hủy</a>
        </form>
    <?php endif; ?>
</div>
