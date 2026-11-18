<?php

function favorite_user_id(): ?int {
    $userId = filter_var($_SESSION['user']['user_id'] ?? null, FILTER_VALIDATE_INT);
    return $userId && $userId > 0 ? (int)$userId : null;
}

function favorite_product_ids(): array {
    $userId = favorite_user_id();
    if ($userId === null) {
        return [];
    }

    $rows = db()->fetchAll(
        'SELECT product_id FROM user_favorite_items WHERE user_id = ? ORDER BY created_at ASC',
        [$userId]
    );
    $ids = [];
    foreach ($rows as $row) {
        $productId = (int)$row['product_id'];
        $ids[$productId] = $productId;
    }
    return $ids;
}

function favorite_count(): int {
    $userId = favorite_user_id();
    if ($userId === null) {
        return 0;
    }

    $row = db()->fetchOne(
        'SELECT COUNT(*) AS total FROM user_favorite_items WHERE user_id = ?',
        [$userId]
    );
    return (int)($row['total'] ?? 0);
}

function favorite_add(int $productId): ?string {
    $userId = favorite_user_id();
    if ($userId === null) {
        return 'Vui lòng đăng nhập để sử dụng danh sách yêu thích.';
    }

    if ($productId < 1) {
        return 'Mã sách không hợp lệ.';
    }

    $product = db()->fetchOne(
        'SELECT product_id FROM products WHERE product_id = ? AND is_active = 1',
        [$productId]
    );
    if (!$product) {
        return 'Sách không tồn tại hoặc đã bị ẩn.';
    }

    db()->execute(
        'INSERT INTO user_favorite_items (user_id, product_id)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP',
        [$userId, $productId]
    );
    return null;
}

function favorite_remove(int $productId): void {
    $userId = favorite_user_id();
    if ($userId === null) {
        return;
    }

    db()->execute(
        'DELETE FROM user_favorite_items WHERE user_id = ? AND product_id = ?',
        [$userId, $productId]
    );
}

function favorite_clear(): void {
    $userId = favorite_user_id();
    if ($userId === null) {
        return;
    }

    db()->execute('DELETE FROM user_favorite_items WHERE user_id = ?', [$userId]);
}

function favorite_items(): array {
    $productIds = favorite_product_ids();
    $items = [];

    foreach ($productIds as $rawId) {
        $productId = filter_var($rawId, FILTER_VALIDATE_INT);
        if (!$productId || $productId < 1) {
            favorite_remove((int)$rawId);
            continue;
        }

        $product = db()->fetchOne(
            'SELECT p.product_id, p.product_code, p.title, p.author, p.price,
                    p.original_price, p.stock_quantity, p.cover_image,
                    (SELECT pi.image_url
                     FROM product_images pi
                     WHERE pi.product_id = p.product_id
                     ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.image_id ASC
                     LIMIT 1) AS image_url
             FROM products p
             WHERE p.product_id = ? AND p.is_active = 1',
            [(int)$productId]
        );
        if (!$product) {
            favorite_remove((int)$rawId);
            continue;
        }

        $items[] = $product;
    }

    return $items;
}