<?php
// Lấy danh sách chính sách đang kích hoạt
$policies = $db->fetchAll(
    "SELECT * FROM policies 
     WHERE is_active = 1 
     ORDER BY sort_order ASC, policy_id DESC"
);

// Tìm mốc thời gian cập nhật mới nhất trong danh sách
$latestUpdate = null;
foreach ($policies as $p) {
    if (!empty($p['updated_at'])) {
        if ($latestUpdate === null || strtotime($p['updated_at']) > strtotime($latestUpdate)) {
            $latestUpdate = $p['updated_at'];
        }
    }
}

// Khai báo thông tin meta & CSS cho layout public
$pageTitle = 'Chính sách BetaBook';
$bodyClass = 'policies-page';
$extraCss = [];

$policyCssPath = __DIR__ . '/../../assets/css/policies.css';
if (file_exists($policyCssPath)) {
    $extraCss[] = asset('css/policies.css') . '?v=' . filemtime($policyCssPath);
}