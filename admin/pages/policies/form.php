<?php
$isEdit = !empty($policy);
$currentId = $isEdit ? (int)$policy['policy_id'] : 0;
$titleVal = $formData['title'] ?? '';
$slugVal = $formData['slug'] ?? '';
$groupVal = $formData['page_group'] ?? 'Chính sách';
$sortVal = isset($formData['sort_order']) ? (int)$formData['sort_order'] : 0;
$isActiveVal = isset($formData['is_active']) ? (int)$formData['is_active'] : 1;
$contentVal = $formData['content'] ?? '';
?>

<div class="admin-content">
    <div class="page-header">
        <h2><?= $isEdit ? 'Chỉnh sửa chính sách' : 'Thêm chính sách mới' ?></h2>
        <a href="<?= admin_url('?page=policies') ?>" class="btn-add" style="background:#6c757d;">← Quay lại danh sách</a>
    </div>

    <?php if (!empty($formErrors)): ?>
        <div style="background:#f8d7da; color:#721c24; padding:12px 16px; border-radius:6px; margin-bottom:16px;">
            <ul style="margin:0; padding-left:20px;">
                <?php foreach ($formErrors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= admin_url('?action=save-policy') ?>" style="background:#fff; padding:24px; border-radius:8px; border:1px solid #e5e5e5; max-width:850px;">
        <input type="hidden" name="policy_id" value="<?= $currentId ?>">

        <div style="margin-bottom:16px;">
            <label style="display:block; font-weight:600; margin-bottom:6px;">Tiêu đề chính sách *</label>
            <input type="text" name="title" required value="<?= e($titleVal) ?>" placeholder="Ví dụ: Chính sách đổi trả" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr 120px; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-weight:600; margin-bottom:6px;">Đường dẫn (Slug)</label>
                <input type="text" name="slug" value="<?= e($slugVal) ?>" placeholder="Để trống tự tạo từ tiêu đề" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            </div>

            <div>
                <label style="display:block; font-weight:600; margin-bottom:6px;">Nhóm hiển thị</label>
                <select name="page_group" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    <option value="Chính sách" <?= $groupVal === 'Chính sách' ? 'selected' : '' ?>>Chính sách</option>
                    <option value="Thông tin" <?= $groupVal === 'Thông tin' ? 'selected' : '' ?>>Thông tin</option>
                </select>
            </div>

            <div>
                <label style="display:block; font-weight:600; margin-bottom:6px;">Thứ tự</label>
                <input type="number" name="sort_order" min="0" value="<?= $sortVal ?>" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:inline-flex; align-items:center; gap:8px; font-weight:600; cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" <?= $isActiveVal === 1 ? 'checked' : '' ?>>
                Hiển thị chính sách trên website
            </label>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-weight:600; margin-bottom:6px;">Nội dung chính sách *</label>
            <textarea name="content" rows="10" required placeholder="Nhập nội dung chi tiết..." style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; font-family:inherit;"><?= e($contentVal) ?></textarea>
        </div>

        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn-add" style="border:none; cursor:pointer;">Lưu chính sách</button>
            <a href="<?= admin_url('?page=policies') ?>" class="action-delete" style="text-decoration:none; padding:8px 16px; display:inline-block;">Hủy</a>
        </div>
    </form>
</div>