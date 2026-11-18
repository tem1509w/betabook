<div class="admin-content">
    <div class="page-header">
        <h2>Quản lý chính sách</h2>
        <a href="<?= e(admin_url('?page=policy-form')) ?>" class="btn-add">Thêm chính sách</a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Đường dẫn (Slug)</th>
                <th>Nhóm hiển thị</th>
                <th>Thứ tự</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($policies)): ?>
                <?php foreach ($policies as $item): ?>
                    <tr>
                        <td><?= (int)$item['policy_id'] ?></td>
                        <td><strong><?= e((string)$item['title']) ?></strong></td>
                        <td><code>/<?= e((string)$item['slug']) ?></code></td>
                        <td><?= e((string)($item['page_group'] ?? 'Chính sách')) ?></td>
                        <td><?= (int)$item['sort_order'] ?></td>
                        <td><?= !empty($item['is_active']) ? 'Hiển thị' : 'Đã ẩn' ?></td>
                        <td>
                            <a class="action-edit" href="<?= e(admin_url('?page=policy-form&id=' . (int)$item['policy_id'])) ?>">Sửa</a>
                            <form method="POST" action="<?= e(admin_url('?action=delete-policy')) ?>" style="display:inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chính sách này?')">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int)$item['policy_id'] ?>">
                                <button type="submit" class="action-delete">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">Chưa có chính sách nào trong hệ thống.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
