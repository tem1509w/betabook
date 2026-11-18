<?php
require_once __DIR__ . '/../../helpers/upload.php';

if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Phiên làm việc không hợp lệ. Vui lòng tải lại trang và thử lại.');
}

$bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT) ?: 0;
$title = trim(is_string($_POST['title'] ?? null) ? $_POST['title'] : '');
$productCode = trim(is_string($_POST['product_code'] ?? null) ? $_POST['product_code'] : '');
$author = trim(is_string($_POST['author'] ?? null) ? $_POST['author'] : '');
$coverImage = null;
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

$removeImageSlots = [];
if (isset($_POST['remove_image_slots'])) {
    if (!is_array($_POST['remove_image_slots'])) {
        $errors[] = 'Danh sách ảnh cần xóa không hợp lệ.';
    } else {
        foreach ($_POST['remove_image_slots'] as $slotValue) {
            if (!is_scalar($slotValue)) {
                $errors[] = 'Vị trí ảnh cần xóa không hợp lệ.';
                break;
            }
            $slot = filter_var($slotValue, FILTER_VALIDATE_INT);
            if ($slot === false || $slot < 0 || $slot > 3) {
                $errors[] = 'Vị trí ảnh cần xóa không hợp lệ.';
                break;
            }
            $removeImageSlots[] = $slot;
        }
    }
}
$removeImageSlots = array_values(array_unique($removeImageSlots));

$submittedPrimarySlot = null;
if (isset($_POST['primary_slot'])) {
    $submittedPrimarySlot = is_scalar($_POST['primary_slot'])
        ? filter_var($_POST['primary_slot'], FILTER_VALIDATE_INT)
        : false;
    if ($submittedPrimarySlot === false || $submittedPrimarySlot < 0 || $submittedPrimarySlot > 3) {
        $errors[] = 'Ảnh bìa chính được chọn không hợp lệ.';
        $submittedPrimarySlot = null;
    }
}

$currentImages = [];
$extraImages = [];
$extraImagePaths = [];
if (!$errors && $bookId > 0) {
    $currentBook = $db->fetchOne(
        'SELECT product_id, cover_image FROM products WHERE product_id = ?',
        [$bookId]
    );
    if (!$currentBook) {
        $errors[] = 'Không tìm thấy sách cần cập nhật.';
    } else {
        $currentImages = $db->fetchAll(
            'SELECT image_id, image_url, is_primary, alt_text
             FROM product_images
             WHERE product_id = ?
             ORDER BY is_primary DESC, sort_order ASC, image_id ASC',
            [$bookId]
        );
        $extraImages = array_slice($currentImages, 4);
        $currentImages = array_slice($currentImages, 0, 4);
        if (!$currentImages && !empty($currentBook['cover_image'])) {
            $legacyImage = trim((string)$currentBook['cover_image']);
            $currentImages[] = [
                'image_id' => null,
                'image_url' => strpos($legacyImage, 'images/') === 0
                    ? $legacyImage
                    : 'images/products/' . ltrim($legacyImage, '/'),
                'is_primary' => 1,
                'alt_text' => null,
            ];
        }
    }
}
$extraImagePaths = array_values(array_column($extraImages, 'image_url'));

$newImagePaths = [];
if (!$errors) {
    [$newImagePaths, $uploadError] = upload_image_slots($_FILES['product_images'] ?? []);
    if ($uploadError !== null) {
        $errors[] = $uploadError;
    }
}

$finalImagePaths = [];
$finalImageAltTexts = [];
for ($slot = 0; $slot < 4; $slot++) {
    if (isset($newImagePaths[$slot])) {
        $finalImagePaths[$slot] = $newImagePaths[$slot];
        $finalImageAltTexts[$slot] = 'Ảnh sản phẩm ' . ($slot + 1);
    } elseif (!in_array($slot, $removeImageSlots, true) && isset($currentImages[$slot])) {
        $finalImagePaths[$slot] = $currentImages[$slot]['image_url'];
        $finalImageAltTexts[$slot] = $currentImages[$slot]['alt_text'] ?? null;
    }
}

$primarySlot = null;
if ($submittedPrimarySlot !== null && isset($finalImagePaths[$submittedPrimarySlot])) {
    $primarySlot = $submittedPrimarySlot;
} else {
    foreach ($currentImages as $slot => $image) {
        if ((int)($image['is_primary'] ?? 0) === 1 && isset($finalImagePaths[$slot])) {
            $primarySlot = (int)$slot;
            break;
        }
    }
}
if ($primarySlot === null) {
    foreach ($finalImagePaths as $slot => $path) {
        $primarySlot = (int)$slot;
        break;
    }
}

$primaryExtraIndex = null;
$primaryImagePath = $primarySlot !== null ? $finalImagePaths[$primarySlot] : null;
if ($primaryImagePath === null && $extraImagePaths) {
    $primaryExtraIndex = 0;
    $primaryImagePath = $extraImagePaths[0];
}
$coverImage = $primaryImagePath !== null
    ? preg_replace('#^images/products/#', '', $primaryImagePath)
    : null;

if ($errors) {
    upload_delete_files($newImagePaths);
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

$oldImagePaths = array_values(array_column($currentImages, 'image_url'));
$newFilePaths = array_values($newImagePaths);
$removedImagePaths = array_values(array_diff(
    array_merge($oldImagePaths, $extraImagePaths),
    array_merge($finalImagePaths, $extraImagePaths)
));
$formBookId = $bookId;
$connection = null;
try {
    $connection = $db->getConnection();
    $connection->beginTransaction();
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
        $bookId = (int)$db->insertId();
    }

    if ($result === false || $bookId < 1) {
        throw new RuntimeException('Không lưu được thông tin sách hoặc mã sản phẩm.');
    }

    $db->execute('DELETE FROM product_images WHERE product_id = ?', [$bookId]);
    foreach ($finalImagePaths as $slot => $imagePath) {
        $db->execute(
            'INSERT INTO product_images (product_id, image_url, is_primary, sort_order, alt_text)
             VALUES (?, ?, ?, ?, ?)',
            [
                $bookId,
                $imagePath,
                (int)$slot === $primarySlot ? 1 : 0,
                (int)$slot + 1,
                $finalImageAltTexts[$slot] ?? null,
            ]
        );
    }
    foreach ($extraImagePaths as $extraIndex => $imagePath) {
        $db->execute(
            'INSERT INTO product_images (product_id, image_url, is_primary, sort_order, alt_text)
             VALUES (?, ?, ?, ?, ?)',
            [
                $bookId,
                $imagePath,
                $extraIndex === $primaryExtraIndex ? 1 : 0,
                5 + $extraIndex,
                $extraImages[$extraIndex]['alt_text'] ?? null,
            ]
        );
    }

    $connection->commit();
} catch (Throwable $error) {
    if ($connection instanceof PDO && $connection->inTransaction()) {
        $connection->rollBack();
    }
    upload_delete_files($newFilePaths);
    error_log('BetaBook could not save an admin book: ' . $error->getMessage());
    $_SESSION['form_errors'] = ['Không thể lưu sách và hình ảnh. Vui lòng kiểm tra dữ liệu rồi thử lại.'];
    $_SESSION['form_data'] = $_POST;
    redirect(admin_url('?page=book-form' . ($formBookId > 0 ? '&id=' . $formBookId : '')));
}

upload_delete_files($removedImagePaths);
redirect(admin_url('?page=books'));
