<?php

function upload_image_file(array $file, int $maxSize = 2097152): array {
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if (!is_int($error)) {
        return [null, 'Thông tin ảnh tải lên không hợp lệ.'];
    }
    if ($error === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        return [null, 'Ảnh vượt giới hạn upload của PHP. Kiểm tra upload_max_filesize.'];
    }
    if ($error !== UPLOAD_ERR_OK) {
        return [null, 'Tải ảnh thất bại (mã lỗi ' . (int)$error . ').'];
    }

    $temporaryPath = $file['tmp_name'] ?? '';
    if (!is_string($temporaryPath) || $temporaryPath === '' || !is_uploaded_file($temporaryPath)) {
        return [null, 'Ảnh không được tải lên bằng biểu mẫu hợp lệ.'];
    }

    $size = $file['size'] ?? 0;
    if (!is_int($size) || $size < 1 || $size > $maxSize) {
        return [null, 'Dung lượng mỗi ảnh phải từ 1 byte đến ' . (int)($maxSize / 1048576) . ' MB.'];
    }

    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($temporaryPath);
    $imageInfo = @getimagesize($temporaryPath);
    if (!is_string($mime) || !isset($types[$mime]) || $imageInfo === false || $imageInfo['mime'] !== $mime) {
        return [null, 'Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP hợp lệ.'];
    }
    if ($imageInfo[0] > 7000 || $imageInfo[1] > 7000 || $imageInfo[0] * $imageInfo[1] > 20000000) {
        return [null, 'Kích thước ảnh quá lớn (tối đa 20 triệu pixel).'];
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $types[$mime];
    $relativePath = 'images/products/uploads/' . $filename;
    $directory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR
        . 'images' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
        return [null, 'Không tạo được thư mục lưu ảnh sản phẩm.'];
    }
    if (!move_uploaded_file($temporaryPath, $directory . DIRECTORY_SEPARATOR . $filename)) {
        return [null, 'Không lưu được ảnh trên máy chủ.'];
    }

    return [$relativePath, null];
}

function upload_image_slots(array $files, int $slotCount = 4): array {
    if (!$files) {
        return [[], null];
    }
    if (!isset($files['name'], $files['error'], $files['tmp_name'], $files['size'])
        || !is_array($files['name'])
        || !is_array($files['error'])
        || !is_array($files['tmp_name'])
        || !is_array($files['size'])) {
        return [[], 'Thông tin ảnh tải lên không hợp lệ.'];
    }

    $paths = [];
    foreach ($files['name'] as $slot => $name) {
        $slotNumber = filter_var($slot, FILTER_VALIDATE_INT);
        if ($slotNumber === false || $slotNumber < 0 || $slotNumber >= $slotCount) {
            upload_delete_files($paths);
            return [[], 'Chỉ được tải tối đa ' . $slotCount . ' ảnh sản phẩm.'];
        }

        try {
            [$path, $error] = upload_image_file([
                'name' => $name,
                'error' => $files['error'][$slot] ?? UPLOAD_ERR_NO_FILE,
                'tmp_name' => $files['tmp_name'][$slot] ?? '',
                'size' => $files['size'][$slot] ?? 0,
            ]);
        } catch (Throwable $error) {
            upload_delete_files($paths);
            throw $error;
        }
        if ($error !== null) {
            upload_delete_files($paths);
            return [[], $error];
        }
        if ($path !== null) {
            $paths[$slotNumber] = $path;
        }
    }

    return [$paths, null];
}

function upload_delete_files(array $paths): void {
    foreach ($paths as $path) {
        if (!is_string($path)
            || !preg_match('#^images/products/uploads/[a-f0-9]{24}\.(jpg|png|webp)$#D', $path)) {
            continue;
        }

        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (is_file($file) && !@unlink($file)) {
            error_log('BetaBook could not remove a managed product image.');
        }
    }
}
