<?php
// Khai báo thông tin meta & CSS riêng cho layout public
$pageTitle = 'Hợp tác phân phối - BetaBook';
$bodyClass = 'distribution-page';
$extraCss = [];

$distributionCssPath = __DIR__ . '/../../assets/css/distribution.css';
if (file_exists($distributionCssPath)) {
    $extraCss[] = asset('css/distribution.css') . '?v=' . filemtime($distributionCssPath);
}

// Nạp thêm contact.css nếu trang này dùng chung class layout (contact-banner, contact-layout...)
$contactCssPath = __DIR__ . '/../../assets/css/contact.css';
if (file_exists($contactCssPath)) {
    $extraCss[] = asset('css/contact.css') . '?v=' . filemtime($contactCssPath);
}