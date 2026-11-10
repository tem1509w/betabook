<?php
/**
 * Admin Header - BetaBook BT03
 * Dựa trên giao diện admin.html gốc
 */
require_once __DIR__ . '/../../helpers/router.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e((string)($pageTitle ?? 'Trang quản lý')) ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/fonts.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/fonts.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/bootstrap-icons.min.css') . '?v=1.11.3' ?>">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Header Admin -->
        <header class="admin-header">
            <div class="admin-header-left">
                <h1 class="admin-logo"><a href="<?= url('admin/?page=dashboard') ?>">Trang quản lý</a></h1>
            </div>
            <div class="admin-header-right">
                <a href="<?= url('') ?>" class="btn-view-site" target="_blank">Xem trang web</a>
                <span class="admin-user">Xin chào, <?= e((string)($adminRoleName ?? 'Quản trị viên')) ?></span>
            </div>
        </header>