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

$adminRoleId = filter_var($_SESSION['admin_role_id'] ?? null, FILTER_VALIDATE_INT);
if ($adminRoleId === false || $adminRoleId === null || $adminRoleId < 1) {
$legacyRoleKey = (string)($_SESSION['admin_role'] ?? 'admin');
$roleKeyMap = [
    'admin' => 'manager',
    'manager' => 'manager',
    'staff' => 'employee',
    'employee' => 'employee',
    'user' => 'user',
];
$roleKey = $roleKeyMap[$legacyRoleKey] ?? '';
$adminRole = $db->fetchOne(
    'SELECT role_id FROM admin_roles WHERE role_key = ? AND is_active = 1',
    [$roleKey]
);
$adminRoleId = $adminRole ? (int)$adminRole['role_id'] : 0;
}

$adminRole = $adminRoleId > 0
? $db->fetchOne(
    'SELECT role_id, role_name FROM admin_roles WHERE role_id = ? AND is_active = 1',
    [$adminRoleId]
)
: false;

if (!$adminRole) {
    http_response_code(403);
    exit('Tài khoản không có vai trò quản lý hợp lệ.');
}

$adminRoleId = (int)$adminRole['role_id'];
$_SESSION['admin_role_id'] = $adminRoleId;
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
];

$actions = [
    'delete-book' => 'actions/delete-book.php',
    'delete-category' => 'actions/delete-category.php',
    'save-book' => 'actions/save-book.php',
    'save-category' => 'actions/save-category.php',
];

$actionPermissions = [
    'delete-book' => 'books',
    'save-book' => 'books',
    'delete-category' => 'categories',
    'save-category' => 'categories',
];

$actionName = get_param('action');
if (is_string($actionName) && isset($actions[$actionName])) {
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
