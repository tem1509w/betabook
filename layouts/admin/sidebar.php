<?php
/** Database-driven navigation; route permissions are checked in admin/index.php. */
require_once __DIR__ . '/../../helpers/router.php';
$currentPage = get_param('page', 'dashboard');
?>
        <!-- Sidebar Admin -->
        <aside class="admin-sidebar">
            <a class="admin-sidebar-brand" href="<?= admin_url('?page=dashboard') ?>">
                <img src="<?= e(asset('images/logo.png')) ?>" alt="BetaBook">
                <span>TRANG QUẢN LÝ</span>
            </a>
            <nav class="admin-nav">
                <ul class="admin-menu">
                    <?php foreach ($adminMenuItems as $menuItem): ?>
                        <?php if ($menuItem['menu_key'] === 'public'): ?>
                            <li class="admin-menu-item admin-menu-divider" aria-hidden="true"></li>
                        <?php endif; ?>
                        <?php
                        $activePages = array_filter(explode(',', $menuItem['active_pages']));
                        $menuIsActive = !$menuItem['is_external']
                            && in_array($currentPage, $activePages, true);
                        $menuUrl = $menuItem['is_external']
                            ? url('')
                            : admin_url('?page=' . rawurlencode($menuItem['page_key']));
                        ?>
                        <li class="admin-menu-item">
                            <a href="<?= e($menuUrl) ?>"
                               class="admin-menu-link <?= $menuIsActive ? 'active' : '' ?>"
                               <?= $menuItem['is_external'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                                <i class="<?= e($menuItem['icon_class']) ?>" aria-hidden="true"></i>
                                <span class="menu-text"><?= e($menuItem['label']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content Area -->
        <main class="admin-main">