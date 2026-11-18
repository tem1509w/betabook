<?php
$policies = $db->fetchAll(
    'SELECT * FROM policies
     WHERE is_active = 1
     ORDER BY sort_order ASC, policy_id DESC'
);

$latestUpdate = null;
foreach ($policies as $policy) {
    if (!empty($policy['updated_at'])
        && ($latestUpdate === null || strtotime($policy['updated_at']) > strtotime($latestUpdate))) {
        $latestUpdate = $policy['updated_at'];
    }
}

$pageTitle = 'Chính sách BetaBook';
$bodyClass = 'policies-page';
$extraCss = [];
$policyCssPath = __DIR__ . '/../../assets/css/policies.css';
if (is_file($policyCssPath)) {
    $extraCss[] = asset('css/policies.css') . '?v=' . filemtime($policyCssPath);
}
