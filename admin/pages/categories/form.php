<div class="admin-content">
    <div class="page-header">
        <h2><?= $categoryId > 0 ? 'Sửa danh mục' : 'Thêm danh mục mới' ?></h2>
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

    <form action="<?= admin_url('?action=save-category') ?>" method="POST" class="admin-form">
        <input type="hidden" name="category_id" value="<?= $categoryId ?>">
        <div class="form-group">
            <label for="name">Tên danh mục <span class="required">*</span></label>
            <input id="name" type="text" name="name" value="<?= e((string)($data['name'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="parent_id">Danh mục cha</label>
            <select id="parent_id" name="parent_id">
                <option value="0">-- Danh mục gốc --</option>
                <?php foreach ($parentCategories as $parent): ?>
                    <?php if ((int)$parent['category_id'] !== $categoryId): ?>
                        <option value="<?= (int)$parent['category_id'] ?>" <?= (int)($data['parent_id'] ?? 0) === (int)$parent['category_id'] ? 'selected' : '' ?>>
                            <?= e($parent['name']) ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description" rows="4"><?= e((string)($data['description'] ?? '')) ?></textarea>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" value="1" <?= (int)($data['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
                Hoạt động
            </label>
        </div>
        <button type="submit" class="btn-submit">Lưu thay đổi</button>
        <a href="<?= admin_url('?page=categories') ?>" class="btn-cancel">Hủy</a>
    </form>
</div>
