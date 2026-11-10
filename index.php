<?php
/**
 * Public router for BetaBook.
 * Pages are selected from this allowlist and rendered inside the shared layout.
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/database.php';
require_once __DIR__ . '/helpers/router.php';
require_once __DIR__ . '/helpers/common.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$db = db();
$navCategories = $db->fetchAll(
    'SELECT category_id, parent_id, slug, name
     FROM categories
     WHERE is_active = 1
     ORDER BY sort_order ASC, name ASC'
);

$routes = [
    'home' => 'home/index.php',
    'booklist' => 'booklist/index.php',
    'discount-event' => 'discount-event/index.php',
    'product-detail' => 'product-detail/index.php',
    'cart' => 'cart/index.php',
    'contact' => 'contact/index.php',
    'login' => 'login/index.php',
    'account' => 'account/index.php',
];

$page = get_param('route', get_param('page', 'home'));
$isKnownPage = is_string($page) && isset($routes[$page]);

if ($isKnownPage) {
    $file = __DIR__ . '/pages/' . $routes[$page];
} else {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy trang';
    $errorMessage = 'Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.';
    $file = __DIR__ . '/pages/404.php';
}

$pageTitle = $pageTitle ?? 'BetaBook';
$bodyClass = $bodyClass ?? 'home-page';
$extraCss = $extraCss ?? [];

if ($isKnownPage) {
    $action = dirname($file) . '/action.php';
    if (is_file($action)) {
        require $action;
    }
}

require __DIR__ . '/layouts/public/header.php';
require $file;
require __DIR__ . '/layouts/public/footer.php';
