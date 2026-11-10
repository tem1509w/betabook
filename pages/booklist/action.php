<?php
$search = trim((string)get_param('q', ''));
$category = trim((string)get_param('danhmuc', 'tatca'));

$sortOptions = [
    'az' => 'Tên A-Z',
    'za' => 'Tên Z-A',
    'price-asc' => 'Giá tăng dần',
    'price-desc' => 'Giá giảm dần',
    'newest' => 'Mới nhất',
    'oldest' => 'Cũ nhất',
];
$sort = (string)get_param('sapxep', 'az');
if (!isset($sortOptions[$sort])) {
    $sort = 'az';
}

$orderBy = [
    'az' => 'p.title ASC',
    'za' => 'p.title DESC',
    'price-asc' => 'p.price ASC, p.title ASC',
    'price-desc' => 'p.price DESC, p.title ASC',
    'newest' => 'p.created_at DESC, p.product_id DESC',
    'oldest' => 'p.created_at ASC, p.product_id ASC',
];

$categories = $db->fetchAll(
    "SELECT c.slug, c.name, COUNT(p.product_id) AS book_count
     FROM categories c
     LEFT JOIN products p ON p.category_id = c.category_id AND p.is_active = 1
     WHERE c.is_active = 1
     GROUP BY c.category_id, c.slug, c.name
     ORDER BY c.sort_order ASC, c.name ASC"
);

$validCategories = array_column($categories, 'slug');
if ($category !== 'tatca' && !in_array($category, $validCategories, true)) {
    $category = 'tatca';
}

$totalAll = (int)$db->fetchOne(
    'SELECT COUNT(*) AS book_count FROM products WHERE is_active = 1'
)['book_count'];

$conditions = ['p.is_active = 1'];
$params = [];
if ($search !== '') {
    $conditions[] = '(p.title LIKE ? OR p.author LIKE ? OR p.product_code LIKE ?)';
    $searchTerm = '%' . $search . '%';
    array_push($params, $searchTerm, $searchTerm, $searchTerm);
}
if ($category !== '' && $category !== 'tatca') {
    $conditions[] = 'c.slug = ? AND c.is_active = 1';
    $params[] = $category;
}

$books = $db->fetchAll(
    'SELECT p.product_id, p.title, p.author, p.price, p.original_price, p.cover_image,
            (SELECT pi.image_url
             FROM product_images pi
             WHERE pi.product_id = p.product_id
             ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.image_id ASC
             LIMIT 1) AS image_url
     FROM products p
     LEFT JOIN categories c ON c.category_id = p.category_id
     WHERE ' . implode(' AND ', $conditions) . '
     ORDER BY ' . $orderBy[$sort],
    $params
);
$resultCount = count($books);

$categoryName = 'Tất cả sách';
foreach ($categories as $bookCategory) {
    if ($bookCategory['slug'] === $category) {
        $categoryName = $bookCategory['name'];
        break;
    }
}

$pageTitle = $categoryName;
$bodyClass = 'booklist-page';
$extraCss = [asset('css/booklist.css')];