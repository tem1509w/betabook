<?php

function order_status_label(string $status): string {
    $labels = [
        'pending' => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];
    return $labels[$status] ?? 'Không xác định';
}

function order_payment_label(string $method): string {
    return $method === 'bank_transfer' ? 'Chuyển khoản' : 'Thanh toán khi nhận hàng';
}

function order_payment_status_label(string $status): string {
    $labels = [
        'unpaid' => 'Chưa thanh toán',
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'refunded' => 'Đã hoàn tiền',
    ];
    return $labels[$status] ?? 'Không xác định';
}

function orders_with_items(array $orders): array {
    if (!$orders) {
        return [];
    }

    $ids = array_map(static function ($order) {
        return (int)$order['order_id'];
    }, $orders);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $rows = db()->fetchAll(
        "SELECT order_id, product_id, product_title, product_code, cover_image,
                unit_price, quantity, line_total
         FROM order_items WHERE order_id IN ($placeholders)
         ORDER BY order_item_id",
        $ids
    );

    $itemsByOrder = [];
    foreach ($rows as $row) {
        $itemsByOrder[(int)$row['order_id']][] = $row;
    }
    foreach ($orders as &$order) {
        $order['items'] = $itemsByOrder[(int)$order['order_id']] ?? [];
    }
    unset($order);
    return $orders;
}

function order_create_from_cart(array $data): int {
    $cart = cart_quantities();
    if (!$cart) {
        throw new DomainException('Giỏ hàng đang trống.');
    }

    $db = db();
    $connection = $db->getConnection();
    $connection->beginTransaction();

    try {
        $lockedItems = [];
        $subtotal = 0.0;
        foreach ($cart as $productId => $quantity) {
            $productId = filter_var($productId, FILTER_VALIDATE_INT);
            $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
            if (!$productId || $productId < 1 || !$quantity || $quantity < 1 || $quantity > 99) {
                throw new DomainException('Giỏ hàng có sản phẩm hoặc số lượng không hợp lệ.');
            }

            $product = $db->fetchOne(
                'SELECT product_id, product_code, title, price, stock_quantity, cover_image
                 FROM products
                 WHERE product_id = ? AND is_active = 1
                 FOR UPDATE',
                [(int)$productId]
            );
            if (!$product) {
                throw new DomainException('Một sản phẩm trong giỏ không còn được bán.');
            }
            if ((int)$product['stock_quantity'] < (int)$quantity) {
                throw new DomainException('Sách "' . $product['title'] . '" không đủ tồn kho. Vui lòng cập nhật giỏ hàng.');
            }

            $product['quantity'] = (int)$quantity;
            $product['line_total'] = (float)$product['price'] * (int)$quantity;
            $subtotal += $product['line_total'];
            $lockedItems[] = $product;
        }

        $shippingFee = 0.00;
        $discount = 0.00;
        $total = $subtotal + $shippingFee - $discount;
        $userId = filter_var($_SESSION['user']['user_id'] ?? null, FILTER_VALIDATE_INT);
        $userId = $userId && $userId > 0 ? (int)$userId : null;
        $orderCode = 'BB-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(6)));

        $db->execute(
            'INSERT INTO orders
                (order_code, user_id, customer_name, email, phone, address_line,
                 ward, district, city, customer_note, payment_method, payment_status,
                 order_status, subtotal, shipping_fee, discount_amount, total_amount)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $orderCode,
                $userId,
                $data['customer_name'],
                $data['email'],
                $data['phone'],
                $data['address_line'],
                $data['ward'],
                $data['district'],
                $data['city'],
                $data['customer_note'] !== '' ? $data['customer_note'] : null,
                $data['payment_method'],
                $data['payment_method'] === 'bank_transfer' ? 'pending' : 'unpaid',
                'pending',
                $subtotal,
                $shippingFee,
                $discount,
                $total,
            ]
        );
        $orderId = (int)$db->insertId();

        foreach ($lockedItems as $item) {
            $db->execute(
                'UPDATE products
                 SET stock_quantity = stock_quantity - ?, sold_quantity = sold_quantity + ?
                 WHERE product_id = ? AND stock_quantity >= ?',
                [$item['quantity'], $item['quantity'], $item['product_id'], $item['quantity']]
            );
            $db->execute(
                'INSERT INTO order_items
                    (order_id, product_id, product_title, product_code, cover_image,
                     unit_price, quantity, line_total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $orderId,
                    $item['product_id'],
                    $item['title'],
                    $item['product_code'],
                    $item['cover_image'],
                    $item['price'],
                    $item['quantity'],
                    $item['line_total'],
                ]
            );
        }

        if ($userId !== null) {
            $db->execute('DELETE FROM user_cart_items WHERE user_id = ?', [$userId]);
        }

        $connection->commit();
        if ($userId === null) {
            cart_clear();
        }
        return $orderId;
    } catch (Throwable $error) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }
        throw $error;
    }
}

function order_update_status(int $orderId, string $status, string $paymentStatus): ?string {
    $allowedStatuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
    $allowedPaymentStatuses = ['unpaid', 'pending', 'paid', 'refunded'];
    if ($orderId < 1 || !in_array($status, $allowedStatuses, true)
        || !in_array($paymentStatus, $allowedPaymentStatuses, true)) {
        return 'Trạng thái đơn hàng không hợp lệ.';
    }

    $db = db();
    $connection = $db->getConnection();
    $connection->beginTransaction();
    try {
        $order = $db->fetchOne(
            'SELECT order_id, order_status, payment_status
             FROM orders WHERE order_id = ? FOR UPDATE',
            [$orderId]
        );
        if (!$order) {
            $connection->rollBack();
            return 'Không tìm thấy đơn hàng.';
        }
        if ($order['order_status'] === 'cancelled' && $status !== 'cancelled') {
            $connection->rollBack();
            return 'Đơn đã hủy không thể mở lại.';
        }
        if ($status === 'cancelled' && $order['order_status'] !== 'cancelled') {
            if (in_array($order['order_status'], ['shipping', 'completed'], true)) {
                $connection->rollBack();
                return 'Không thể hủy đơn hàng đã giao hoặc hoàn thành.';
            }
            $items = $db->fetchAll(
                'SELECT product_id, quantity FROM order_items WHERE order_id = ?',
                [$orderId]
            );
            foreach ($items as $item) {
                if ($item['product_id'] !== null) {
                    $db->execute(
                        'UPDATE products
                         SET stock_quantity = stock_quantity + ?,
                             sold_quantity = GREATEST(sold_quantity - ?, 0)
                         WHERE product_id = ?',
                        [(int)$item['quantity'], (int)$item['quantity'], (int)$item['product_id']]
                    );
                }
            }
        }

        $eligibleAfter = ($status === 'completed' && $paymentStatus === 'paid')
            || $paymentStatus === 'refunded'
            || $status === 'cancelled';

        $db->execute(
            'UPDATE orders
             SET order_status = ?, payment_status = ?,
                 retention_started_at = CASE
                     WHEN ? = 1 THEN COALESCE(retention_started_at, CURRENT_TIMESTAMP)
                     ELSE NULL
                 END
             WHERE order_id = ?',
            [$status, $paymentStatus, $eligibleAfter ? 1 : 0, $orderId]
        );
        $connection->commit();
        return null;
    } catch (Throwable $error) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }
        throw $error;
    }
}

function order_delete(int $orderId): ?string {
    if ($orderId < 1) {
        return 'Mã đơn hàng không hợp lệ.';
    }

    $db = db();
    $connection = $db->getConnection();
    $connection->beginTransaction();
    try {
        $order = $db->fetchOne(
            'SELECT order_id, order_status
             FROM orders WHERE order_id = ? FOR UPDATE',
            [$orderId]
        );
        if (!$order) {
            $connection->rollBack();
            return 'Không tìm thấy đơn hàng.';
        }

        if (in_array($order['order_status'], ['pending', 'confirmed'], true)) {
            $items = $db->fetchAll(
                'SELECT product_id, quantity FROM order_items WHERE order_id = ?',
                [$orderId]
            );
            foreach ($items as $item) {
                if ($item['product_id'] !== null) {
                    $db->execute(
                        'UPDATE products
                         SET stock_quantity = stock_quantity + ?,
                             sold_quantity = GREATEST(sold_quantity - ?, 0)
                         WHERE product_id = ?',
                        [(int)$item['quantity'], (int)$item['quantity'], (int)$item['product_id']]
                    );
                }
            }
        }

        $db->execute('DELETE FROM orders WHERE order_id = ?', [$orderId]);
        $connection->commit();
        return null;
    } catch (Throwable $error) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }
        throw $error;
    }
}
