<?php
$dashboardRoleId = (int)($_SESSION['admin_role_id'] ?? 0);
$isDashboardOwner = $dashboardRoleId === 1;

$dashboardStats = $db->fetchOne(
    'SELECT
        (SELECT COUNT(*) FROM products WHERE is_active = 1) AS active_books,
        (SELECT COUNT(*) FROM categories WHERE is_active = 1) AS active_categories,
        (SELECT COUNT(*) FROM products WHERE is_active = 1 AND is_best_seller = 1) AS best_sellers,
        (SELECT COALESCE(SUM(sold_quantity), 0) FROM products WHERE is_active = 1) AS units_sold,
        (SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock_quantity <= 5) AS low_stock_books'
);

$totalRevenue = null;
$revenuePeriod = 'day';
$revenueChart = [];
$maxRevenue = 0;

if ($isDashboardOwner) {
    $revenueStats = $db->fetchOne(
        'SELECT COALESCE(SUM(total_amount), 0) AS total_revenue
         FROM orders
         WHERE order_status = \'completed\' AND payment_status = \'paid\''
    );
    $totalRevenue = (float)$revenueStats['total_revenue'];

    $revenuePeriod = get_param('revenue_period', 'day');
    if (!is_string($revenuePeriod) || !in_array($revenuePeriod, ['day', 'month', 'year'], true)) {
        $revenuePeriod = 'day';
    }

    $today = new DateTimeImmutable('today');
    if ($revenuePeriod === 'month') {
        $firstPeriod = $today->modify('first day of this month')->modify('-11 months');
        $lastPeriod = $today->modify('first day of this month');
        $periodExpression = "DATE_FORMAT(created_at, '%Y-%m')";
        $periodCondition = 'DATE_FORMAT(created_at, \'%Y-%m\') BETWEEN ? AND ?';
        $periodParams = [$firstPeriod->format('Y-m'), $lastPeriod->format('Y-m')];
        $revenuePeriods = [];
        for ($index = 0; $index < 12; $index++) {
            $period = $firstPeriod->modify('+' . $index . ' months');
            $revenuePeriods[] = [
                'key' => $period->format('Y-m'),
                'label' => $period->format('n/y'),
            ];
        }
    } elseif ($revenuePeriod === 'year') {
        $firstYear = (int)$today->format('Y') - 4;
        $lastYear = (int)$today->format('Y');
        $periodExpression = 'YEAR(created_at)';
        $periodCondition = 'YEAR(created_at) BETWEEN ? AND ?';
        $periodParams = [$firstYear, $lastYear];
        $revenuePeriods = [];
        for ($year = $firstYear; $year <= $lastYear; $year++) {
            $revenuePeriods[] = ['key' => (string)$year, 'label' => (string)$year];
        }
    } else {
        $firstPeriod = $today->modify('-13 days');
        $periodExpression = 'DATE(created_at)';
        $periodCondition = 'DATE(created_at) BETWEEN ? AND ?';
        $periodParams = [$firstPeriod->format('Y-m-d'), $today->format('Y-m-d')];
        $revenuePeriods = [];
        for ($daysAgo = 13; $daysAgo >= 0; $daysAgo--) {
            $period = $today->modify('-' . $daysAgo . ' days');
            $revenuePeriods[] = [
                'key' => $period->format('Y-m-d'),
                'label' => $period->format('d/m'),
            ];
        }
    }

    $revenueRows = $db->fetchAll(
        "SELECT $periodExpression AS period_key, SUM(total_amount) AS revenue
         FROM orders
         WHERE order_status = 'completed' AND payment_status = 'paid'
           AND $periodCondition
         GROUP BY period_key
         ORDER BY period_key",
        $periodParams
    );
    $revenueByPeriod = [];
    foreach ($revenueRows as $revenueRow) {
        $revenueByPeriod[(string)$revenueRow['period_key']] = (float)$revenueRow['revenue'];
    }
    foreach ($revenuePeriods as $period) {
        $revenueChart[] = [
            'label' => $period['label'],
            'revenue' => $revenueByPeriod[$period['key']] ?? 0,
        ];
    }
    $maxRevenue = max(array_column($revenueChart, 'revenue') ?: [0]);
}

$topSellingBooks = $isDashboardOwner
    ? $db->fetchAll(
        'SELECT product_id, title, product_code, sold_quantity
         FROM products
         WHERE is_active = 1 AND sold_quantity > 0
         ORDER BY sold_quantity DESC, title ASC
         LIMIT 5'
    )
    : [];

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
