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

function csrf_token(): string {
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function valid_csrf_token($token): bool {
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function auth_login(string $identifier, string $password): ?array {
    $user = db()->fetchOne(
        'SELECT user_id, username, email, password, full_name, role
         FROM users
         WHERE is_active = 1 AND (username = ? OR email = ? OR phone = ?)
         LIMIT 1',
        [$identifier, $identifier, $identifier]
    );

    if (!$user || !password_verify($password, $user['password'])) {
        return null;
    }

    db()->execute('UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?', [(int)$user['user_id']]);

    session_regenerate_id(true);
    unset($_SESSION['csrf_token']);
    $_SESSION['user'] = [
        'user_id' => (int)$user['user_id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
        'role' => $user['role'],
    ];

    return $_SESSION['user'];
}
