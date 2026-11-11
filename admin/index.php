<?php
/**
 * Admin router for BetaBook.
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/router.php';
require_once __DIR__ . '/../helpers/common.php';

$db = db();

$sessionUserId = filter_var($_SESSION['user']['user_id'] ?? null, FILTER_VALIDATE_INT);
$adminRole = $sessionUserId !== false && $sessionUserId !== null && $sessionUserId > 0
    ? $db->fetchOne(
        'SELECT r.role_id, r.role_key, r.role_name
         FROM users u
         INNER JOIN roles r ON r.role_id = u.role_id
         WHERE u.user_id = ? AND u.is_active = 1 AND r.is_active = 1
           AND u.role_id IN (1, 2)',
        [$sessionUserId]
    )
    : false;

if (!$adminRole) {
    http_response_code(403);
    exit('Bạn cần đăng nhập bằng tài khoản quản trị viên hoặc nhân viên để truy cập trang admin.');
}

$adminRoleId = (int)$adminRole['role_id'];
$adminRoleKey = $adminRole['role_key'];
$_SESSION['admin_role_id'] = $adminRoleId;
$_SESSION['user']['role_id'] = $adminRoleId;
$adminRoleName = $adminRole['role_name'];
$adminMenuItems = $db->fetchAll(
    'SELECT m.menu_key, m.label, m.page_key, m.active_pages, m.icon_class,
            m.is_external
     FROM admin_menu_items m
     INNER JOIN admin_menu_permissions p ON p.menu_id = m.menu_id
     WHERE p.role_id = ? AND p.can_view = 1 AND m.is_active = 1
     ORDER BY m.sort_order, m.menu_id',
    [$adminRoleId]
);
$allowedMenuKeys = array_column(
    $db->fetchAll(
        'SELECT m.menu_key
         FROM admin_menu_items m
         INNER JOIN admin_menu_permissions p ON p.menu_id = m.menu_id
         WHERE p.role_id = ? AND p.can_view = 1 AND m.is_active = 1',
        [$adminRoleId]
    ),
    'menu_key'
);

$routes = [
    'dashboard' => 'pages/dashboard/index.php',
    'books' => 'pages/books/index.php',
    'book-form' => 'pages/books/form.php',
    'categories' => 'pages/categories/index.php',
    'category-form' => 'pages/categories/form.php',
    'promotions' => 'pages/discount-event/index.php',
    'users' => 'pages/users/index.php',
    'users-form' => 'pages/users/form.php',
    'orders' => 'pages/orders/index.php',
];

$actions = [
    'delete-book' => 'actions/delete-book.php',
    'delete-category' => 'actions/delete-category.php',
    'save-book' => 'actions/save-book.php',
    'save-category' => 'actions/save-category.php',
    'save-coupon' => 'pages/discount-event/action.php',
    'delete-coupon' => 'pages/discount-event/action.php',
    'save-event' => 'pages/discount-event/action.php',
    'delete-event' => 'pages/discount-event/action.php',
    'save-user' => 'actions/save-user.php',
    'delete-user' => 'actions/delete-user.php',
    'update-order-status' => 'pages/orders/update.php',
    'delete-order' => 'pages/orders/delete.php',
];

$actionPermissions = [
    'delete-book' => 'books',
    'save-book' => 'books',
    'delete-category' => 'categories',
    'save-category' => 'categories',
    'save-coupon' => 'promotions',
    'delete-coupon' => 'promotions',
    'save-event' => 'promotions',
    'delete-event' => 'promotions',
    'save-user' => 'users',
    'delete-user' => 'users',
    'update-order-status' => 'orders',
    'delete-order' => 'orders',
];

$actionName = get_param('action');
if (is_string($actionName) && isset($actions[$actionName])) {
    if ($actionName === 'delete-user' && $adminRoleKey !== 'admin') {
        http_response_code(403);
        exit('Chỉ quản trị viên mới được xóa tài khoản.');
    }

    if (!in_array($actionPermissions[$actionName], $allowedMenuKeys, true)) {
        http_response_code(403);
        exit('Bạn không có quyền thực hiện thao tác này.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method Not Allowed');
    }

    require __DIR__ . '/' . $actions[$actionName];
    exit;
}

$page = get_param('page', 'dashboard');
$pagePermissions = [
    'dashboard' => 'dashboard',
    'books' => 'books',
    'book-form' => 'books',
    'categories' => 'categories',
    'category-form' => 'categories',
    'promotions' => 'promotions',
    'users' => 'users',
    'users-form' => 'users',
    'orders' => 'orders',
];

if (is_string($page) && isset($routes[$page])
    && !in_array($pagePermissions[$page], $allowedMenuKeys, true)) {
    http_response_code(403);
    exit('Bạn không có quyền truy cập trang này.');
}

if (!is_string($page) || !isset($routes[$page])) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy trang';
    require __DIR__ . '/../layouts/admin/header.php';
    require __DIR__ . '/../layouts/admin/sidebar.php';
    echo '<div class="admin-content"><h1>Trang Admin không tồn tại</h1></div>';
    require __DIR__ . '/../layouts/admin/footer.php';
    exit;
}

$file = __DIR__ . '/' . $routes[$page];
$actionFile = dirname($file) . '/action.php';

if (is_file($actionFile)) {
    require $actionFile;
}

require __DIR__ . '/../layouts/admin/header.php';
require __DIR__ . '/../layouts/admin/sidebar.php';
require $file;
require __DIR__ . '/../layouts/admin/footer.php';
