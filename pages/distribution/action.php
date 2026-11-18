<?php
$pageTitle = 'Hợp tác phân phối - BetaBook';
$bodyClass = 'distribution-page';
$extraCss = [];

$distributionCssPath = __DIR__ . '/../../assets/css/distribution.css';
if (is_file($distributionCssPath)) {
    $extraCss[] = asset('css/distribution.css') . '?v=' . filemtime($distributionCssPath);
}

$contactCssPath = __DIR__ . '/../../assets/css/contact.css';
if (is_file($contactCssPath)) {
    $extraCss[] = asset('css/contact.css') . '?v=' . filemtime($contactCssPath);
}
