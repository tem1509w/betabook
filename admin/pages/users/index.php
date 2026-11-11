<div class="admin-content">
    <div class="page-header">
        <h2>Quản lý tài khoản</h2>
        <a href="<?= admin_url('?page=users-form') ?>" class="btn-add">Thêm tài khoản</a>
    </div>

    <?php if ($accountNotice): ?>
        <div class="alert success"><?= e($accountNotice) ?></div>
    <?php endif; ?>

    <form method="get" action="<?= admin_url() ?>" class="user-filter">
        <input type="hidden" name="page" value="users">
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="Tìm username, tên, email, số điện thoại">
        <select name="role_id" aria-label="Lọc theo vai trò">
            <option value="">Tất cả vai trò</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?= (int)$role['role_id'] ?>" <?= (int)$roleId === (int)$role['role_id'] ? 'selected' : '' ?>>
                    <?= e($role['role_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="status" aria-label="Lọc theo trạng thái">
            <option value="">Mọi trạng thái</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Đã khóa</option>
        </select>
        <button type="submit" class="button">Lọc</button>
    </form>

    <p class="user-count">Tổng số: <?= $totalUsers ?> tài khoản</p>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tài khoản</th>
                    <th>Thông tin liên hệ</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Đăng nhập gần nhất</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users): ?>
                    <?php foreach ($users as $user): ?>
                        <?php
                        $avatar = in_array($user['avatar_image'], $avatarOptions, true)
                            ? $user['avatar_image']
                            : user_default_avatar();
                        ?>
                        <tr>
                            <td><?= (int)$user['user_id'] ?></td>
                            <td>
                                <span class="user-list-identity">
                                    <img src="<?= e(asset('images/' . $avatar)) ?>" alt="">
                                    <span>
                                        <strong><?= e($user['username']) ?></strong>
                                        <small><?= e((string)($user['full_name'] ?? '')) ?></small>
                                    </span>
                                </span>
                            </td>
                            <td><?= e($user['email']) ?><br><small><?= e((string)($user['phone'] ?? '')) ?></small></td>
                            <td><?= e($user['role_name']) ?></td>
                            <td><?= (int)$user['is_active'] === 1 ? 'Hoạt động' : 'Đã khóa' ?></td>
                            <td><?= e((string)$user['created_at']) ?></td>
                            <td><?= e((string)($user['last_login'] ?? 'Chưa đăng nhập')) ?></td>
                            <td>
                                <a class="action-edit" href="<?= admin_url('?page=users-form&id=' . (int)$user['user_id']) ?>">Sửa</a>
                                <?php if ($canDeleteUsers && (int)$user['user_id'] !== (int)($_SESSION['user']['user_id'] ?? 0)): ?>
                                    <form method="post" action="<?= e(admin_url('?action=delete-user')) ?>" class="inline-delete-form"
                                          onsubmit="return confirm('Xóa vĩnh viễn tài khoản này? Lịch sử đơn hàng sẽ được giữ lại nhưng không còn gắn với tài khoản.');">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="user_id" value="<?= (int)$user['user_id'] ?>">
                                        <input type="hidden" name="q" value="<?= e($search) ?>">
                                        <input type="hidden" name="role_id" value="<?= $roleId ? (int)$roleId : '' ?>">
                                        <input type="hidden" name="status" value="<?= e((string)$status) ?>">
                                        <button class="action-delete-button" type="submit">Xóa</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">Không tìm thấy tài khoản phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="user-pagination" aria-label="Phân trang tài khoản">
            <?php if ($pageNumber > 1): ?>
                <a class="button outline small" href="<?= admin_url('?' . http_build_query(['page' => 'users', 'q' => $search, 'role_id' => $roleId ?: '', 'status' => $status, 'p' => $pageNumber - 1])) ?>">Trước</a>
            <?php endif; ?>
            <span>Trang <?= $pageNumber ?> / <?= $totalPages ?></span>
            <?php if ($pageNumber < $totalPages): ?>
                <a class="button outline small" href="<?= admin_url('?' . http_build_query(['page' => 'users', 'q' => $search, 'role_id' => $roleId ?: '', 'status' => $status, 'p' => $pageNumber + 1])) ?>">Sau</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>
