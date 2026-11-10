<?php
/**
 * Router Helpers - url(), get_param(), asset()
 * Tuân theo MiniShop Basic PHP
 */

/**
 * Lấy tham số từ GET/POST
 * @param string $key Tên tham số
 * @param mixed $default Giá trị mặc định
 * @return mixed
 */
function get_param(string $key, $default = null) {
    if (isset($_GET[$key]) && is_string($_GET[$key])) return trim($_GET[$key]);
    if (isset($_POST[$key]) && is_string($_POST[$key])) return trim($_POST[$key]);
    return $default;
}

/**
 * Tạo đường dẫn gốc của ứng dụng.
 */
function base_url(string $path = ''): string {
    static $basePath = null;
    if ($basePath === null) {
        $config = require __DIR__ . '/../config/app.php';
        $basePath = rtrim((string)(parse_url($config['base_url'], PHP_URL_PATH) ?: ''), '/');
    }

    $path = ltrim($path, '/');
    return $basePath . '/' . $path;
}

/**
 * Tạo URL cho Public
 * @param string $page Tên route hoặc đường dẫn tĩnh cũ
 * @param array $params Query parameters
 * @return string
 */
function url(string $page = '', array $params = []): string {
    if ($page === '') {
        return base_url();
    }

    // Keep existing links to static .html pages and legacy query-string calls working.
    if (strpos($page, '?') !== false || strpos($page, '/') !== false || preg_match('/\.(?:html|php)(?:[?#].*)?$/i', $page)) {
        return base_url($page);
    }

    $params = array_merge(['page' => $page], $params);
    return base_url('index.php?' . http_build_query($params));
}

/**
 * Tạo URL cho Admin
 * @param string $path Đường dẫn
 * @param array $params Query parameters
 * @return string
 */
function admin_url(string $path = '', array $params = []): string {
    $query = $params ? '?' . http_build_query($params) : '';
    return base_url('admin/' . ltrim($path, '/')) . $query;
}

/**
 * Tạo đường dẫn asset (CSS, JS, images)
 * @param string $path Đường dẫn từ thư mục assets
 * @return string
 */
function asset(string $path): string {
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Redirect an toàn
 * @param string $url
 * @param int $statusCode
 */
function redirect(string $url, int $statusCode = 302): void {
    header("Location: $url", true, $statusCode);
    exit;
}