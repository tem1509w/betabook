<?php
$dashboardStats = $db->fetchOne(
    'SELECT
        (SELECT COUNT(*) FROM products WHERE is_active = 1) AS active_books,
        (SELECT COUNT(*) FROM categories WHERE is_active = 1) AS active_categories,
        (SELECT COUNT(*) FROM products WHERE is_active = 1 AND is_best_seller = 1) AS best_sellers,
        (SELECT COALESCE(SUM(sold_quantity), 0) FROM products WHERE is_active = 1) AS units_sold,
        (SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock_quantity <= 5) AS low_stock_books'
);
$recentBooks = $db->fetchAll(
    'SELECT p.product_id, p.title, p.product_code, p.price, p.stock_quantity,
            p.updated_at, c.name AS category_name
     FROM products p
     LEFT JOIN categories c ON c.category_id = p.category_id
     WHERE p.is_active = 1
     ORDER BY p.updated_at DESC, p.product_id DESC
     LIMIT 6'
);
$lowStockBooks = $db->fetchAll(
    'SELECT product_id, title, product_code, stock_quantity
     FROM products
     WHERE is_active = 1 AND stock_quantity <= 5
     ORDER BY stock_quantity ASC, title ASC
     LIMIT 6'
);
$categoryStats = $db->fetchAll(
    'SELECT c.name, COUNT(p.product_id) AS book_count
     FROM categories c
     LEFT JOIN products p ON p.category_id = c.category_id AND p.is_active = 1
     WHERE c.is_active = 1
     GROUP BY c.category_id, c.name, c.sort_order
     ORDER BY book_count DESC, c.sort_order, c.name
     LIMIT 6'
);
$maxCategoryBooks = max(array_map(
    static fn(array $category): int => (int)$category['book_count'],
    $categoryStats ?: [['book_count' => 1]]
));

$pageTitle = 'Tổng quan - Trang quản lý';
