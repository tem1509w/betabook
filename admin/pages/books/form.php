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

    <form action="<?= admin_url('?action=save-book') ?>" method="POST" class="admin-form">
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
        <div class="form-group">
            <label for="cover_image">Ảnh bìa (tên file)</label>
            <input id="cover_image" type="text" name="cover_image" value="<?= e((string)($data['cover_image'] ?? '')) ?>">
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
