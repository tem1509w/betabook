<?php
$isEdit = !empty($policy);
$currentId = $isEdit ? (int)$policy['policy_id'] : 0;
$titleVal = (string)($formData['title'] ?? '');
$slugVal = (string)($formData['slug'] ?? '');
$groupVal = (string)($formData['page_group'] ?? 'Chính sách');
$sortVal = isset($formData['sort_order']) ? (int)$formData['sort_order'] : 0;
$isActiveVal = isset($formData['is_active']) ? (int)$formData['is_active'] : 1;
$contentVal = (string)($formData['content'] ?? '');
?>

<div class="admin-content">
    <div class="page-header">
        <h2><?= $isEdit ? 'Chỉnh sửa chính sách' : 'Thêm chính sách mới' ?></h2>
        <a href="<?= e(admin_url('?page=policies')) ?>" class="btn-add" style="background:#6c757d;">← Quay lại danh sách</a>
    </div>

    <?php if (!empty($formErrors)): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($formErrors as $error): ?>
                    <li><?= e((string)$error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e(admin_url('?action=save-policy')) ?>" class="admin-form" style="max-width:850px;">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="policy_id" value="<?= $currentId ?>">

        <div class="form-group">
            <label for="policy-title">Tiêu đề chính sách <span class="required">*</span></label>
            <input id="policy-title" type="text" name="title" required value="<?= e($titleVal) ?>" placeholder="Ví dụ: Chính sách đổi trả">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="policy-slug">Đường dẫn (Slug)</label>
                <input id="policy-slug" type="text" name="slug" value="<?= e($slugVal) ?>" placeholder="Để trống tự tạo từ tiêu đề">
            </div>
            <div class="form-group">
                <label for="policy-group">Nhóm hiển thị</label>
                <select id="policy-group" name="page_group">
                    <option value="Chính sách" <?= $groupVal === 'Chính sách' ? 'selected' : '' ?>>Chính sách</option>
                    <option value="Thông tin" <?= $groupVal === 'Thông tin' ? 'selected' : '' ?>>Thông tin</option>
                </select>
            </div>
            <div class="form-group">
                <label for="policy-sort">Thứ tự</label>
                <input id="policy-sort" type="number" name="sort_order" min="0" value="<?= $sortVal ?>">
            </div>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" value="1" <?= $isActiveVal === 1 ? 'checked' : '' ?>>
                Hiển thị chính sách trên website
            </label>
        </div>

        <div class="form-group">
            <label for="policy-content">Nội dung chính sách <span class="required">*</span></label>
            <textarea id="policy-content" name="content" rows="10" required placeholder="Nhập nội dung chi tiết..."><?= e($contentVal) ?></textarea>
        </div>

        <button type="submit" class="btn-submit">Lưu chính sách</button>
        <a href="<?= e(admin_url('?page=policies')) ?>" class="btn-cancel">Hủy</a>
    </form>
</div>
