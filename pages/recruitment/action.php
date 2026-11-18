<?php
$pageTitle = 'Tuyển dụng - BetaBook';
$bodyClass = 'recruitment-page';
$extraCss = [];

$recruitmentCssPath = __DIR__ . '/../../assets/css/recruitment.css';
if (is_file($recruitmentCssPath)) {
    $extraCss[] = asset('css/recruitment.css') . '?v=' . filemtime($recruitmentCssPath);
}

$contactCssPath = __DIR__ . '/../../assets/css/contact.css';
if (is_file($contactCssPath)) {
    $extraCss[] = asset('css/contact.css') . '?v=' . filemtime($contactCssPath);
}
