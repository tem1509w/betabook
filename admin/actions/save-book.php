<?php
$bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT) ?: 0;
$title = trim(is_string($_POST['title'] ?? null) ? $_POST['title'] : '');
$productCode = trim(is_string($_POST['product_code'] ?? null) ? $_POST['product_code'] : '');
$author = trim(is_string($_POST['author'] ?? null) ? $_POST['author'] : '');
$coverImage = trim(is_string($_POST['cover_image'] ?? null) ? $_POST['cover_image'] : '');
$isbn = trim(is_string($_POST['isbn'] ?? null) ? $_POST['isbn'] : '');
$coverType = is_string($_POST['cover_type'] ?? null) ? $_POST['cover_type'] : 'paperback';
$categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: 0;
$publisherId = filter_input(INPUT_POST, 'publisher_id', FILTER_VALIDATE_INT) ?: null;
$priceInput = is_string($_POST['price'] ?? null) ? $_POST['price'] : '';
$originalPriceInput = is_string($_POST['original_price'] ?? null) ? $_POST['original_price'] : '';
$stockInput = is_string($_POST['stock_quantity'] ?? null) ? $_POST['stock_quantity'] : '0';
$description = trim(is_string($_POST['description'] ?? null) ? $_POST['description'] : '');
$publicationYearInput = is_string($_POST['publication_year'] ?? null) ? $_POST['publication_year'] : '';
$pageCountInput = is_string($_POST['page_count'] ?? null) ? $_POST['page_count'] : '';
$dimensions = trim(is_string($_POST['dimensions'] ?? null) ? $_POST['dimensions'] : '');
$isActive = isset($_POST['is_active']) ? 1 : 0;
$isBestSeller = isset($_POST['is_best_seller']) ? 1 : 0;
$isNewRelease = isset($_POST['is_new_release']) ? 1 : 0;
$isRecommended = isset($_POST['is_recommended']) ? 1 : 0;

$errors = [];
if ($title === '') {
    $errors[] = 'Tên sách không được để trống.';
}
if ($productCode === '') {
    $errors[] = 'Mã sản phẩm không được để trống.';
}
if ($categoryId < 1) {
    $errors[] = 'Vui lòng chọn danh mục.';
}
if (!is_numeric($priceInput) || (float)$priceInput < 0) {
    $errors[] = 'Giá bán không hợp lệ.';
}
if ($originalPriceInput !== '' && (!is_numeric($originalPriceInput) || (float)$originalPriceInput < 0)) {
    $errors[] = 'Giá gốc không hợp lệ.';
}
if (filter_var($stockInput, FILTER_VALIDATE_INT) === false || (int)$stockInput < 0) {
    $errors[] = 'Số lượng tồn không hợp lệ.';
}
if (!in_array($coverType, ['hardcover', 'paperback', 'ebook'], true)) {
    $errors[] = 'Loại bìa không hợp lệ.';
}
if ($publicationYearInput !== '' && (filter_var($publicationYearInput, FILTER_VALIDATE_INT) === false || (int)$publicationYearInput < 0)) {
    $errors[] = 'Năm xuất bản không hợp lệ.';
}
if ($pageCountInput !== '' && (filter_var($pageCountInput, FILTER_VALIDATE_INT) === false || (int)$pageCountInput < 0)) {
    $errors[] = 'Số trang không hợp lệ.';
}

$slugSource = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title);
$slugBase = trim((string)preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($slugSource ?: $title)), '-');
$codeSuffix = trim((string)preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($productCode)), '-');
$slug = ($slugBase !== '' ? $slugBase : 'book') . '-' . ($codeSuffix !== '' ? $codeSuffix : 'item');

if (!$errors) {
    if (!$db->fetchOne('SELECT category_id FROM categories WHERE category_id = ?', [$categoryId])) {
        $errors[] = 'Danh mục được chọn không tồn tại.';
    }
    if ($publisherId !== null && !$db->fetchOne('SELECT publisher_id FROM publishers WHERE publisher_id = ?', [$publisherId])) {
        $errors[] = 'Nhà xuất bản được chọn không tồn tại.';
    }

    $duplicateCode = $db->fetchOne(
        'SELECT product_id FROM products WHERE product_code = ? AND product_id != ?',
        [$productCode, $bookId]
    );
    if ($duplicateCode) {
        $errors[] = 'Mã sản phẩm đã tồn tại.';
    }

    $duplicateSlug = $db->fetchOne(
        'SELECT product_id FROM products WHERE slug = ? AND product_id != ?',
        [$slug, $bookId]
    );
    if ($duplicateSlug) {
        $errors[] = 'Tên sách tạo ra slug đã tồn tại; hãy đổi tên sách.';
    }
}

if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    redirect(admin_url('?page=book-form' . ($bookId > 0 ? '&id=' . $bookId : '')));
}

$values = [
    $productCode,
    $slug,
    $title,
    $author,
    $categoryId,
    $publisherId,
    (float)$priceInput,
    $originalPriceInput === '' ? null : (float)$originalPriceInput,
    (int)$stockInput,
    $isbn,
    $coverType,
    $coverImage,
    $isActive,
    $isBestSeller,
    $isNewRelease,
    $isRecommended,
    $description,
    $publicationYearInput === '' ? null : (int)$publicationYearInput,
    $pageCountInput === '' ? null : (int)$pageCountInput,
    $dimensions,
];

if ($bookId > 0) {
    $result = $db->execute(
        'UPDATE products
         SET product_code = ?, slug = ?, title = ?, author = ?, category_id = ?, publisher_id = ?,
             price = ?, original_price = ?, stock_quantity = ?, isbn = ?, cover_type = ?, cover_image = ?,
             is_active = ?, is_best_seller = ?, is_new_release = ?, is_recommended = ?, description = ?,
             publication_year = ?, page_count = ?, dimensions = ?
         WHERE product_id = ?',
        array_merge($values, [$bookId])
    );
} else {
    $result = $db->execute(
        'INSERT INTO products
         (product_code, slug, title, author, category_id, publisher_id, price, original_price,
          stock_quantity, isbn, cover_type, cover_image, is_active, is_best_seller, is_new_release,
          is_recommended, description, publication_year, page_count, dimensions)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        $values
    );
}

if ($result === false) {
    $_SESSION['form_errors'] = ['Không thể lưu sách. Vui lòng kiểm tra dữ liệu và cấu hình cơ sở dữ liệu.'];
    $_SESSION['form_data'] = $_POST;
    redirect(admin_url('?page=book-form' . ($bookId > 0 ? '&id=' . $bookId : '')));
}

redirect(admin_url('?page=books'));
