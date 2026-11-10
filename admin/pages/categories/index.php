<div class="admin-content">
    <div class="page-header">
        <h2>Quản lý danh mục</h2>
        <a href="<?= admin_url('?page=category-form') ?>" class="btn-add">Thêm danh mục</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Danh mục cha</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($categories): ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= (int)$category['category_id'] ?></td>
                        <td><?= e($category['name']) ?></td>
                        <td><?= e($category['parent_name'] ?? 'Danh mục gốc') ?></td>
                        <td><?= $category['is_active'] ? 'Hoạt động' : 'Đã ẩn' ?></td>
                        <td>
                            <a class="action-edit" href="<?= admin_url('?page=category-form&id=' . (int)$category['category_id']) ?>">Sửa</a>
                            <form method="POST" action="<?= admin_url('?action=delete-category') ?>" style="display:inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                <input type="hidden" name="id" value="<?= (int)$category['category_id'] ?>">
                                <button type="submit" class="action-delete">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Chưa có danh mục trong hệ thống.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
