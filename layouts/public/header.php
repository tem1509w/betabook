<?php
/**
 * Public Header - Từ index.html gốc BetaBook
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - BetaBook' : 'BetaBook'; ?></title>
  <link rel="icon" type="image/x-icon" href="<?php echo asset('images/logo.ico'); ?>">
  <link rel="preload" href="<?php echo asset('fonts/BeVietnamPro-Regular-Latin.woff2'); ?>" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?php echo asset('fonts/BeVietnamPro-Regular-Vietnamese.woff2'); ?>" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?php echo asset('css/fonts.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/fonts.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset('css/style.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/style.css'); ?>">
<?php if (isset($extraCss)): ?>
  <?php foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?php echo e($css); ?>">
  <?php endforeach; ?>
<?php endif; ?>
</head>
<body class="<?php echo isset($bodyClass) ? e($bodyClass) : 'home-page'; ?>">

<?php
$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<?php if (is_string($flashMessage) && $flashMessage !== ''): ?>
  <div id="flash-message" class="flash-message" role="status" aria-live="polite">
    <?php echo e($flashMessage); ?>
  </div>
<?php endif; ?>

<!-- ============ 1. HEADER ============ -->
<header class="header">
  <div class="container header-inner">

    <!-- Logo -->
<a class="header-logo" href="<?php echo url(''); ?>">
  <img src="<?php echo asset('images/logo.png'); ?>" alt="BetaBook">
</a>

    <!-- Menu chính -->
    <nav class="menu">
      <ul>
        <li><a href="<?php echo url(''); ?>">Trang chủ</a></li>
        <li><a href="<?php echo e(url('introduction')); ?>">Về BetaBook</a></li>
        <li>
          <a href="<?php echo url('booklist'); ?>">Tủ sách</a>
          <?php
            $topLevelCategories = array_filter(
                $navCategories,
                static function ($category) { return $category['parent_id'] === null; }
            );
          ?>
          <?php if ($topLevelCategories): ?>
            <ul class="submenu">
              <?php foreach ($topLevelCategories as $parentCategory): ?>
                <li>
                  <a href="<?php echo e(url('booklist', ['danhmuc' => $parentCategory['slug']])); ?>">
                    <?php echo e($parentCategory['name']); ?>
                  </a>
                  <?php
                    $childCategories = array_filter(
                        $navCategories,
                        static function ($category) use ($parentCategory) {
                            return (int)$category['parent_id'] === (int)$parentCategory['category_id'];
                        }
                    );
                  ?>
                  <?php if ($childCategories): ?>
                    <ul class="submenu">
                      <?php foreach ($childCategories as $childCategory): ?>
                        <li>
                          <a href="<?php echo e(url('booklist', ['danhmuc' => $childCategory['slug']])); ?>">
                            <?php echo e($childCategory['name']); ?>
                          </a>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </li>
        <li>
          <a href="<?php echo e(url('404')); ?>">Dịch vụ</a>
          <ul class="submenu">
            <li><a href="<?php echo e(url('404')); ?>">Hợp tác phân phối</a></li>
            <li><a href="<?php echo e(url('404')); ?>">Nội dung số</a></li>
          </ul>
        </li>
        <li><a href="<?php echo e(url('discount-event')); ?>">Mã giảm - Sự kiện</a></li>
        <li><a href="<?php echo e(url('contact')); ?>">Liên hệ</a></li>
      </ul>
    </nav>

    <!-- Tìm kiếm, tài khoản, giỏ hàng -->
<div class="tools">

  <form class="search-form" action="<?php echo e(url('')); ?>" method="get">
    <input type="hidden" name="route" value="booklist">
    <input type="search" name="q" value="<?php echo isset($search) ? e($search) : ''; ?>"
           placeholder="Tìm kiếm sản phẩm..." aria-label="Tìm kiếm sản phẩm theo tên">
    <button type="submit" class="search-button" aria-label="Tìm kiếm">
      <img src="<?php echo asset('images/search.ico'); ?>" alt="Tìm">
    </button>
  </form>

  <a class="icon-button" href="<?php echo e(url(!empty($_SESSION['user']) ? 'account' : 'login')); ?>" title="Tài khoản">
    <img src="<?php echo asset('images/user.ico'); ?>" alt="Tài khoản">
  </a>

  <a class="icon-button" href="<?php echo url('cart'); ?>" title="Giỏ hàng">
    <img src="<?php echo asset('images/cart.ico'); ?>" alt="Giỏ hàng">
  </a>

</div>
  </div>
</header>