<?php
/**
 * Common Helpers - e(), money(), redirect()
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

function user_avatar_options(): array {
    return [
        'pfp-icon-set/Avatar_Aria.webp',
        'pfp-icon-set/Avatar_Dialyn.webp',
        'pfp-icon-set/Avatar_Ellen_Joe.webp',
        'pfp-icon-set/Avatar_Hoshimi_Miyabi.webp',
        'pfp-icon-set/Avatar_Lucia_Elowen.webp',
        'pfp-icon-set/Avatar_Nangong_Yu.webp',
        'pfp-icon-set/Avatar_Pyrois.webp',
        'pfp-icon-set/Avatar_Remielle_Dan.webp',
        'pfp-icon-set/Avatar_Roxy_Ifrita_Pryce.webp',
        'pfp-icon-set/Avatar_Sunna.webp',
        'pfp-icon-set/Avatar_Vivian_Banshee.webp',
        'pfp-icon-set/Avatar_Ye_Shunguang.webp',
        'pfp-icon-set/Avatar_Yixuan.webp',
    ];
}

function auth_login(string $username, string $password): ?array {
    $user = db()->fetchOne(
        'SELECT user_id, username, email, password, full_name, role_id, avatar_image
         FROM users
         WHERE is_active = 1 AND username = ?
         LIMIT 1',
        [$username]
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
        'role_id' => (int)$user['role_id'],
        'avatar_image' => $user['avatar_image'],
    ];

    return $_SESSION['user'];
}
