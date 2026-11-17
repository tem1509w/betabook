<div class="admin-content">
    <div class="page-header">
        <h2>Quản lý sách</h2>
        <a href="<?= admin_url('?page=book-form') ?>" class="btn-add">Thêm sách mới</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên sách</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($books): ?>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= (int)$book['product_id'] ?></td>
                        <td><?= e($book['title']) ?></td>
                        <td><?= e($book['category_name'] ?? 'Chưa phân loại') ?></td>
                        <td><?= money($book['price']) ?></td>
                        <td><?= $book['is_active'] ? 'Hoạt động' : 'Đã ẩn' ?></td>
                        <td>
                            <a class="action-edit" href="<?= admin_url('?page=book-form&id=' . (int)$book['product_id']) ?>">Sửa</a>
                            <form method="POST" action="<?= admin_url('?action=delete-book') ?>" style="display:inline" onsubmit="return confirm('Bạn có chắc muốn xóa sách này?')">
                                <input type="hidden" name="id" value="<?= (int)$book['product_id'] ?>">
                                <button type="submit" class="action-delete">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Chưa có sách trong hệ thống.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
