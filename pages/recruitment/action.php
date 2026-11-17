<?php
// Khai báo thông tin meta & CSS riêng cho layout public
$pageTitle = 'Tuyển dụng - BetaBook';
$bodyClass = 'recruitment-page';
$extraCss = [];

$recruitmentCssPath = __DIR__ . '/../../assets/css/recruitment.css';
if (file_exists($recruitmentCssPath)) {
    $extraCss[] = asset('css/recruitment.css') . '?v=' . filemtime($recruitmentCssPath);
}

// Nếu trang này dùng chung CSS với trang liên hệ (contact.css) thì nạp thêm nếu có
$contactCssPath = __DIR__ . '/../../assets/css/contact.css';
if (file_exists($contactCssPath)) {
    $extraCss[] = asset('css/contact.css') . '?v=' . filemtime($contactCssPath);
}