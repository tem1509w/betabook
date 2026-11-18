<div class="admin-content">
    <div class="page-header">
        <h2><?= $bookId > 0 ? 'Sửa sách' : 'Thêm sách mới' ?></h2>
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

    <form action="<?= admin_url('?action=save-book') ?>" method="POST" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="book_id" value="<?= $bookId ?>">
        <div class="form-group">
            <label for="title">Tên sách <span class="required">*</span></label>
            <input id="title" type="text" name="title" value="<?= e((string)($data['title'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="product_code">Mã sản phẩm <span class="required">*</span></label>
            <input id="product_code" type="text" name="product_code" value="<?= e((string)($data['product_code'] ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="category_id">Danh mục <span class="required">*</span></label>
            <select id="category_id" name="category_id" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['category_id'] ?>" <?= (int)($data['category_id'] ?? 0) === (int)$category['category_id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="publisher_id">Nhà xuất bản</label>
            <select id="publisher_id" name="publisher_id">
                <option value="">-- Chọn nhà xuất bản --</option>
                <?php foreach ($publishers as $publisher): ?>
                    <option value="<?= (int)$publisher['publisher_id'] ?>" <?= (int)($data['publisher_id'] ?? 0) === (int)$publisher['publisher_id'] ? 'selected' : '' ?>>
                        <?= e($publisher['publisher_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="author">Tác giả</label>
            <input id="author" type="text" name="author" value="<?= e((string)($data['author'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="price">Giá bán (VND) <span class="required">*</span></label>
            <input id="price" type="number" name="price" value="<?= e((string)($data['price'] ?? '0')) ?>" min="0" step="1" required>
        </div>
        <div class="form-group">
            <label for="original_price">Giá gốc (VND)</label>
            <input id="original_price" type="number" name="original_price" value="<?= e((string)($data['original_price'] ?? '0')) ?>" min="0" step="1">
        </div>
        <div class="form-group">
            <label for="stock_quantity">Số lượng tồn</label>
            <input id="stock_quantity" type="number" name="stock_quantity" value="<?= e((string)($data['stock_quantity'] ?? '0')) ?>" min="0">
        </div>
        <?php
        $primaryImageSlot = 0;
        foreach ($bookImages as $imageSlot => $bookImage) {
            if ((int)($bookImage['is_primary'] ?? 0) === 1) {
                $primaryImageSlot = (int)$imageSlot;
                break;
            }
        }
        $postedPrimarySlot = is_scalar($data['primary_slot'] ?? null)
            ? filter_var($data['primary_slot'], FILTER_VALIDATE_INT)
            : false;
        if ($postedPrimarySlot !== false && $postedPrimarySlot >= 0 && $postedPrimarySlot < 4) {
            $primaryImageSlot = $postedPrimarySlot;
        }
        $postedRemoveSlots = [];
        if (is_array($data['remove_image_slots'] ?? null)) {
            foreach ($data['remove_image_slots'] as $postedRemoveSlot) {
                if (!is_scalar($postedRemoveSlot)) {
                    continue;
                }
                $postedRemoveSlot = filter_var($postedRemoveSlot, FILTER_VALIDATE_INT);
                if ($postedRemoveSlot !== false && $postedRemoveSlot >= 0 && $postedRemoveSlot < 4) {
                    $postedRemoveSlots[] = $postedRemoveSlot;
                }
            }
        }
        ?>
        <div class="form-group book-images-field">
            <label>Hình ảnh sản phẩm</label>
            <p class="book-images-help">Tối đa 4 ảnh (gồm ảnh bìa), JPG/PNG/WEBP, tối đa 2 MB mỗi ảnh. Ảnh mới sẽ thay ảnh ở cùng khung.</p>
            <div class="book-image-grid">
                <?php for ($imageSlot = 0; $imageSlot < 4; $imageSlot++): ?>
                    <?php
                    $bookImage = $bookImages[$imageSlot] ?? null;
                    $imageUrl = $bookImage
                        ? product_image_src(['image_url' => $bookImage['image_url']])
                        : '';
                    ?>
                    <div class="book-image-slot">
                        <div class="book-image-preview">
                            <img
                                <?= $imageUrl !== '' ? 'src="' . e($imageUrl) . '"' : '' ?>
                                data-original-src="<?= e($imageUrl) ?>"
                                alt="Ảnh sản phẩm <?= $imageSlot + 1 ?>"
                                <?= $imageUrl === '' ? 'hidden' : '' ?>>
                            <span class="book-image-placeholder" <?= $imageUrl !== '' ? 'hidden' : '' ?>>
                                Ảnh <?= $imageSlot + 1 ?>
                            </span>
                        </div>
                        <label class="book-image-file-label" for="product_image_<?= $imageSlot ?>">
                            <?= $imageUrl !== '' ? 'Thay ảnh' : 'Chọn ảnh' ?>
                        </label>
                        <input
                            id="product_image_<?= $imageSlot ?>"
                            class="book-image-file"
                            type="file"
                            name="product_images[<?= $imageSlot ?>]"
                            accept="image/jpeg,image/png,image/webp">
                        <?php if ($bookImage): ?>
                            <label class="book-image-remove">
                                <input type="checkbox" name="remove_image_slots[]" value="<?= $imageSlot ?>" <?= in_array($imageSlot, $postedRemoveSlots, true) ? 'checked' : '' ?>>
                                Xóa ảnh hiện tại
                            </label>
                        <?php endif; ?>
                        <label class="book-image-primary">
                            <input
                                type="radio"
                                name="primary_slot"
                                value="<?= $imageSlot ?>"
                                <?= $primaryImageSlot === $imageSlot ? 'checked' : '' ?>>
                            Ảnh bìa chính
                        </label>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="publication_year">Năm xuất bản</label>
            <input id="publication_year" type="number" name="publication_year" value="<?= e((string)($data['publication_year'] ?? '')) ?>" min="0">
        </div>
        <div class="form-group">
            <label for="page_count">Số trang</label>
            <input id="page_count" type="number" name="page_count" value="<?= e((string)($data['page_count'] ?? '')) ?>" min="0">
        </div>
        <div class="form-group">
            <label for="dimensions">Kích thước</label>
            <input id="dimensions" type="text" name="dimensions" value="<?= e((string)($data['dimensions'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description" rows="4"><?= e((string)($data['description'] ?? '')) ?></textarea>
        </div>
        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input id="isbn" type="text" name="isbn" value="<?= e((string)($data['isbn'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="cover_type">Loại bìa</label>
            <select id="cover_type" name="cover_type">
                <?php foreach (['paperback' => 'Bìa mềm', 'hardcover' => 'Bìa cứng', 'ebook' => 'Ebook'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($data['cover_type'] ?? 'paperback') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php foreach (['is_active' => 'Hoạt động', 'is_best_seller' => 'Bán chạy', 'is_new_release' => 'Sách mới', 'is_recommended' => 'Khuyến nghị'] as $flag => $label): ?>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="<?= e($flag) ?>" value="1" <?= (int)($data[$flag] ?? ($flag === 'is_active' ? 1 : 0)) === 1 ? 'checked' : '' ?>>
                    <?= e($label) ?>
                </label>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-submit">Lưu thay đổi</button>
        <a href="<?= admin_url('?page=books') ?>" class="btn-cancel">Hủy</a>
    </form>
</div>

<script>
document.querySelectorAll('.book-image-file').forEach((input) => {
    input.addEventListener('change', () => {
        const slot = input.closest('.book-image-slot');
        const preview = slot.querySelector('.book-image-preview img');
        const placeholder = slot.querySelector('.book-image-placeholder');
        const removeCheckbox = slot.querySelector('.book-image-remove input');
        const file = input.files && input.files[0];

        if (preview.dataset.previewUrl) {
            URL.revokeObjectURL(preview.dataset.previewUrl);
            delete preview.dataset.previewUrl;
        }
        if (!file) {
            if (preview.dataset.originalSrc === '') {
                preview.removeAttribute('src');
                preview.hidden = true;
            } else {
                preview.src = preview.dataset.originalSrc;
                preview.hidden = false;
            }
            placeholder.hidden = !preview.hidden;
            return;
        }

        preview.dataset.previewUrl = URL.createObjectURL(file);
        preview.src = preview.dataset.previewUrl;
        preview.hidden = false;
        placeholder.hidden = true;
        if (removeCheckbox) removeCheckbox.checked = false;
    });
});
</script>
