<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_product'])) {
    $productId = filter_input(INPUT_POST, 'remove_product', FILTER_VALIDATE_INT);
    if ($productId && isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
    redirect(url('cart'));
}

$cartItems = [];
$cartTotal = 0;
$cart = $_SESSION['cart'] ?? [];

foreach ($cart as $productId => $quantity) {
    $product = $db->fetchOne(
        'SELECT product_id, title, price, stock_quantity, cover_image
         FROM products WHERE product_id = ? AND is_active = 1',
        [(int)$productId]
    );
    if (!$product || (int)$product['stock_quantity'] < 1) {
        unset($_SESSION['cart'][$productId]);
        continue;
    }

    $quantity = min((int)$quantity, (int)$product['stock_quantity']);
    $_SESSION['cart'][$productId] = $quantity;
    $product['cart_quantity'] = $quantity;
    $product['line_total'] = (float)$product['price'] * $quantity;
    $cartTotal += $product['line_total'];
    $cartItems[] = $product;
}

$pageTitle = 'Giỏ hàng - BetaBook';
$bodyClass = 'cart-page';
