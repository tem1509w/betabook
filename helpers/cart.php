<?php

function cart_init(): void {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function cart_user_id(): ?int {
    $userId = filter_var($_SESSION['user']['user_id'] ?? null, FILTER_VALIDATE_INT);
    return $userId && $userId > 0 ? (int)$userId : null;
}

function cart_quantities(): array {
    $userId = cart_user_id();
    if ($userId !== null) {
        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            cart_merge_guest();
        }
        $rows = db()->fetchAll(
            'SELECT product_id, quantity FROM user_cart_items WHERE user_id = ?',
            [$userId]
        );
        $quantities = [];
        foreach ($rows as $row) {
            $quantities[(int)$row['product_id']] = (int)$row['quantity'];
        }
        return $quantities;
    }

    cart_init();
    return $_SESSION['cart'];
}

function cart_store_quantity(int $productId, int $quantity): void {
    $userId = cart_user_id();
    if ($userId !== null) {
        db()->execute(
            'INSERT INTO user_cart_items (user_id, product_id, quantity)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)',
            [$userId, $productId, $quantity]
        );
        return;
    }

    cart_init();
    $_SESSION['cart'][$productId] = $quantity;
}

function cart_count(): int {
    $count = 0;
    foreach (cart_quantities() as $quantity) {
        if (is_numeric($quantity) && (int)$quantity > 0) {
            $count++;
        }
    }
    return $count;
}

function cart_add(int $productId, int $quantity): ?string {
    if ($productId < 1 || $quantity < 1 || $quantity > 99) {
        return 'Số lượng mỗi sản phẩm hợp lệ từ 1 đến 99.';
    }

    $product = db()->fetchOne(
        'SELECT product_id, title, stock_quantity
         FROM products WHERE product_id = ? AND is_active = 1',
        [$productId]
    );
    if (!$product) {
        return 'Sách không tồn tại hoặc đã bị ẩn.';
    }
    if ((int)$product['stock_quantity'] < 1) {
        return 'Sản phẩm đã hết hàng.';
    }

    $quantities = cart_quantities();
    $currentQuantity = (int)($quantities[$productId] ?? 0);
    $newQuantity = $currentQuantity + $quantity;
    if ($newQuantity > 99) {
        return 'Mỗi sản phẩm chỉ được tối đa 99 cuốn.';
    }
    if ((int)$product['stock_quantity'] < $newQuantity) {
        return 'Số lượng trong giỏ vượt quá tồn kho hiện tại.';
    }

    cart_store_quantity($productId, $newQuantity);
    return null;
}

function cart_update(int $productId, int $quantity): ?string {
    if ($productId < 1 || $quantity < 0 || $quantity > 99) {
        return 'Số lượng hợp lệ từ 0 đến 99.';
    }

    $quantities = cart_quantities();
    if (!isset($quantities[$productId])) {
        return 'Sách này không còn trong giỏ hàng.';
    }
    if ($quantity === 0) {
        cart_remove($productId);
        return null;
    }

    $product = db()->fetchOne(
        'SELECT stock_quantity FROM products
         WHERE product_id = ? AND is_active = 1',
        [$productId]
    );
    if (!$product) {
        return 'Sách không tồn tại hoặc đã bị ẩn.';
    }
    if ((int)$product['stock_quantity'] < $quantity) {
        return 'Số lượng vượt quá tồn kho hiện tại.';
    }

    cart_store_quantity($productId, $quantity);
    return null;
}

function cart_remove(int $productId): void {
    $userId = cart_user_id();
    if ($userId !== null) {
        db()->execute(
            'DELETE FROM user_cart_items WHERE user_id = ? AND product_id = ?',
            [$userId, $productId]
        );
        return;
    }

    cart_init();
    unset($_SESSION['cart'][$productId]);
}

function cart_clear(): void {
    $userId = cart_user_id();
    if ($userId !== null) {
        db()->execute('DELETE FROM user_cart_items WHERE user_id = ?', [$userId]);
        return;
    }

    $_SESSION['cart'] = [];
}

function cart_merge_guest(): void {
    $userId = cart_user_id();
    if ($userId === null) {
        return;
    }

    cart_init();
    $guestCart = $_SESSION['cart'];
    if (!$guestCart) {
        return;
    }

    $connection = db()->getConnection();
    $connection->beginTransaction();
    try {
        $rows = db()->fetchAll(
            'SELECT product_id, quantity FROM user_cart_items WHERE user_id = ?',
            [$userId]
        );
        $savedQuantities = [];
        foreach ($rows as $row) {
            $savedQuantities[(int)$row['product_id']] = (int)$row['quantity'];
        }

        foreach ($guestCart as $productId => $guestQuantity) {
            $productId = filter_var($productId, FILTER_VALIDATE_INT);
            $guestQuantity = filter_var($guestQuantity, FILTER_VALIDATE_INT);
            if (!$productId || $productId < 1 || !$guestQuantity || $guestQuantity < 1) {
                continue;
            }

            $product = db()->fetchOne(
                'SELECT stock_quantity FROM products
                 WHERE product_id = ? AND is_active = 1',
                [(int)$productId]
            );
            if (!$product || (int)$product['stock_quantity'] < 1) {
                continue;
            }

            $savedQuantity = (int)($savedQuantities[(int)$productId] ?? 0);
            $available = min(
                99 - $savedQuantity,
                (int)$product['stock_quantity'] - $savedQuantity
            );
            if ($available > 0) {
                $mergedQuantity = $savedQuantity + min((int)$guestQuantity, $available);
                cart_store_quantity((int)$productId, $mergedQuantity);
                $savedQuantities[(int)$productId] = $mergedQuantity;
            }
        }

        $connection->commit();
        unset($_SESSION['cart']);
    } catch (Throwable $error) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }
        throw $error;
    }
}

function cart_items(): array {
    $quantities = cart_quantities();
    $items = [];

    foreach ($quantities as $cartKey => $quantity) {
        $productId = filter_var($cartKey, FILTER_VALIDATE_INT);
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
        if (!$productId || $productId < 1 || !$quantity || $quantity < 1) {
            cart_remove((int)$cartKey);
            continue;
        }

        $product = db()->fetchOne(
            'SELECT p.product_id, p.product_code, p.title, p.author, p.price,
                    p.stock_quantity, p.cover_image,
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
            cart_remove((int)$cartKey);
            continue;
        }

        $product['cart_quantity'] = (int)$quantity;
        $product['line_total'] = (float)$product['price'] * (int)$quantity;
        $items[] = $product;
    }

    return $items;
}

function cart_total(array $items): float {
    return array_sum(array_column($items, 'line_total'));
}
