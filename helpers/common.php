<?php
/**
 * Common Helpers - e(), money(), redirect()
 * Tuân theo MiniShop Basic PHP
 */

/**
 * Escape HTML output (ngăn XSS)
 * @param string $value
 * @return string
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Format tiền tệ VND
 * @param int|float $amount
 * @return string
 */
function money($amount): string {
    return number_format((float)$amount, 0, ',', '.') . 'đ';
}

/**
 * Get a product's primary image, falling back to the legacy cover_image field.
 * @param array $product
 * @return string
 */
function product_image_src(array $product): string {
    $imageUrl = trim((string)($product['image_url'] ?? ''));
    if ($imageUrl !== '') {
        return asset(strpos($imageUrl, 'images/') === 0
            ? $imageUrl
            : 'images/products/' . $imageUrl);
    }

    $coverImage = trim((string)($product['cover_image'] ?? ''));
    return asset($coverImage !== ''
        ? 'images/products/' . $coverImage
        : 'images/logo.png');
}
