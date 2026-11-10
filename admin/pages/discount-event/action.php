<?php
$discountAction = get_param('action');

if (in_array($discountAction, ['save-coupon', 'save-event', 'delete-coupon', 'delete-event'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method Not Allowed');
    }

    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Yêu cầu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang.');
    }

    $recordType = strpos($discountAction, 'coupon') !== false ? 'coupon' : 'event';
    $isCoupon = $recordType === 'coupon';
    $table = $isCoupon ? 'home_coupons' : 'home_events';
    $idColumn = $isCoupon ? 'coupon_id' : 'event_id';
    $pageUrl = admin_url('?page=promotions');
    $idInput = $isCoupon ? 'coupon_id' : 'event_id';
    $stringLength = static function (string $value): int {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    };

    if (strpos($discountAction, 'delete-') === 0) {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id === null || $id < 1) {
            http_response_code(400);
            exit('Mã nội dung không hợp lệ.');
        }

        $db->execute("DELETE FROM {$table} WHERE {$idColumn} = ?", [$id]);
        redirect($pageUrl);
    }

    $id = filter_var($_POST[$idInput] ?? 0, FILTER_VALIDATE_INT);
    $id = $id === false || $id === null ? -1 : (int)$id;
    $title = trim(is_string($_POST[$isCoupon ? 'coupon_code' : 'title'] ?? null)
        ? $_POST[$isCoupon ? 'coupon_code' : 'title']
        : '');
    $description = trim(is_string($_POST['description'] ?? null) ? $_POST['description'] : '');
    $date = trim(is_string($_POST[$isCoupon ? 'expires_at' : 'event_date'] ?? null)
        ? $_POST[$isCoupon ? 'expires_at' : 'event_date']
        : '');
    $imagePath = trim(is_string($_POST['image_path'] ?? null) ? $_POST['image_path'] : '');
    $sortOrderInput = is_string($_POST['sort_order'] ?? null) ? $_POST['sort_order'] : '';
    $sortOrder = filter_var($sortOrderInput, FILTER_VALIDATE_INT);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $errors = [];

    if ($id < 0) {
        $errors[] = 'Mã nội dung không hợp lệ.';
    }
    if ($title === '') {
        $errors[] = $isCoupon ? 'Mã giảm giá không được để trống.' : 'Tên sự kiện không được để trống.';
    }
    if ($isCoupon && $stringLength($title) > 50) {
        $errors[] = 'Mã giảm giá không được vượt quá 50 ký tự.';
    }
    if (!$isCoupon && $stringLength($title) > 255) {
        $errors[] = 'Tên sự kiện không được vượt quá 255 ký tự.';
    }
    if ($description === '') {
        $errors[] = 'Mô tả không được để trống.';
    }
    if ($isCoupon && $stringLength($description) > 255) {
        $errors[] = 'Mô tả mã giảm giá không được vượt quá 255 ký tự.';
    }

    $parsedDate = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
        $errors[] = $isCoupon ? 'Ngày hết hạn không hợp lệ.' : 'Ngày sự kiện không hợp lệ.';
    }

    if ($sortOrder === false || $sortOrder < 0) {
        $errors[] = 'Thứ tự hiển thị phải là số nguyên không âm.';
    }
    if ($stringLength($imagePath) > 255) {
        $errors[] = 'Đường dẫn hình ảnh không được vượt quá 255 ký tự.';
    }

    $slug = '';
    if (!$isCoupon) {
        $slug = strtolower(trim(is_string($_POST['event_slug'] ?? null) ? $_POST['event_slug'] : ''));
        if ($slug === '') {
            $slugSource = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title);
            $slug = trim((string)preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($slugSource ?: $title)), '-');
        }
        if ($slug === '' || strlen($slug) > 100 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            $errors[] = 'Đường dẫn sự kiện chỉ được gồm chữ thường, số và dấu gạch ngang.';
        }
    } else {
        $title = strtoupper($title);
    }

    if (!$errors && $id > 0 && !$db->fetchOne(
        "SELECT {$idColumn} FROM {$table} WHERE {$idColumn} = ?",
        [$id]
    )) {
        $errors[] = 'Không tìm thấy nội dung cần cập nhật.';
    }

    if (!$errors) {
        $duplicateTitle = $isCoupon
            ? $db->fetchOne(
                'SELECT coupon_id FROM home_coupons WHERE coupon_code = ? AND coupon_id != ?',
                [$title, $id]
            )
            : $db->fetchOne(
                'SELECT event_id FROM home_events WHERE event_slug = ? AND event_id != ?',
                [$slug, $id]
            );
        if ($duplicateTitle) {
            $errors[] = $isCoupon ? 'Mã giảm giá đã tồn tại.' : 'Đường dẫn sự kiện đã tồn tại.';
        }

        $duplicateOrder = $db->fetchOne(
            "SELECT {$idColumn} FROM {$table} WHERE sort_order = ? AND {$idColumn} != ?",
            [(int)$sortOrder, $id]
        );
        if ($duplicateOrder) {
            $errors[] = 'Thứ tự hiển thị đã được sử dụng trong danh sách này.';
        }
    }

    if ($errors) {
        $_SESSION['discount_event_form_errors'] = $errors;
        $_SESSION['discount_event_form_data'] = $_POST;
        $_SESSION['discount_event_form_type'] = $recordType;
        redirect($pageUrl . '&type=' . $recordType . ($id > 0 ? '&id=' . $id : ''));
    }

    if ($isCoupon) {
        $values = [$title, $description, $date, $imagePath !== '' ? $imagePath : null, (int)$sortOrder, $isActive];
        if ($id > 0) {
            $result = $db->execute(
                'UPDATE home_coupons
                 SET coupon_code = ?, description = ?, expires_at = ?, image_path = ?, sort_order = ?, is_active = ?
                 WHERE coupon_id = ?',
                array_merge($values, [$id])
            );
        } else {
            $result = $db->execute(
                'INSERT INTO home_coupons (coupon_code, description, expires_at, image_path, sort_order, is_active)
                 VALUES (?, ?, ?, ?, ?, ?)',
                $values
            );
        }
    } else {
        $values = [$slug, $date, $title, $description, $imagePath !== '' ? $imagePath : null, (int)$sortOrder, $isActive];
        if ($id > 0) {
            $result = $db->execute(
                'UPDATE home_events
                 SET event_slug = ?, event_date = ?, title = ?, description = ?, image_path = ?, sort_order = ?, is_active = ?
                 WHERE event_id = ?',
                array_merge($values, [$id])
            );
        } else {
            $result = $db->execute(
                'INSERT INTO home_events (event_slug, event_date, title, description, image_path, sort_order, is_active)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                $values
            );
        }
    }

    if ($result === false) {
        $_SESSION['discount_event_form_errors'] = ['Không thể lưu nội dung. Vui lòng kiểm tra dữ liệu và cấu hình cơ sở dữ liệu.'];
        $_SESSION['discount_event_form_data'] = $_POST;
        $_SESSION['discount_event_form_type'] = $recordType;
        redirect($pageUrl . '&type=' . $recordType . ($id > 0 ? '&id=' . $id : ''));
    }

    redirect($pageUrl);
}

$pageTitle = 'Quản lý mã giảm & sự kiện - Trang quản lý';
$coupons = $db->fetchAll('SELECT * FROM home_coupons ORDER BY sort_order, coupon_id');
$events = $db->fetchAll('SELECT * FROM home_events ORDER BY sort_order, event_id');
$recordType = get_param('type', '');
$recordId = filter_var(get_param('id', '0'), FILTER_VALIDATE_INT);
$recordId = $recordId === false || $recordId === null ? 0 : (int)$recordId;
$record = [];

if (in_array($recordType, ['coupon', 'event'], true) && $recordId > 0) {
    $table = $recordType === 'coupon' ? 'home_coupons' : 'home_events';
    $idColumn = $recordType === 'coupon' ? 'coupon_id' : 'event_id';
    $record = $db->fetchOne("SELECT * FROM {$table} WHERE {$idColumn} = ?", [$recordId]) ?: [];
}

$formErrors = ($_SESSION['discount_event_form_type'] ?? null) === $recordType
    ? ($_SESSION['discount_event_form_errors'] ?? [])
    : [];
$formData = ($_SESSION['discount_event_form_type'] ?? null) === $recordType
    ? ($_SESSION['discount_event_form_data'] ?? [])
    : [];
unset(
    $_SESSION['discount_event_form_errors'],
    $_SESSION['discount_event_form_data'],
    $_SESSION['discount_event_form_type']
);

if (in_array($recordType, ['coupon', 'event'], true) && $recordId > 0 && !$record) {
    http_response_code(404);
    $formErrors[] = 'Không tìm thấy nội dung cần sửa.';
}

$data = array_merge($record, $formData);