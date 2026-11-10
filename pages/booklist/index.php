<!-- ============ BANNER ĐẦU TRANG ============ -->
<section class="page-banner">
  <p><a href="<?php echo e(url('')); ?>">Trang chủ</a></p>
  <h1>Tủ sách BetaBook</h1>
</section>

<section class="offer-banner" aria-label="banner booklist">
  <img src="<?php echo e(asset('images/banner/offer-banner.jpg')); ?>" alt="banner booklist">
</section>

<!-- ============ NỘI DUNG: SIDEBAR + DANH SÁCH SÁCH ============ -->
<section class="container section">

  <nav class="breadcrumb" aria-label="Đường dẫn">
    <a href="<?php echo e(url('')); ?>">Trang chủ</a>
    <span>›</span>
    <a href="<?php echo e(url('booklist')); ?>"><strong>Tủ sách</strong></a>
  </nav>

  <div class="booklist-layout">

    <!-- ===== SIDEBAR TRÁI ===== -->
    <aside class="sidebar">
      <h3>Danh mục sách</h3>
      <ul class="cat-list">
        <li<?php echo $category === 'tatca' ? ' class="active"' : ''; ?>>
          <a href="<?php echo e(url('booklist', array_filter([
              'q' => $search,
              'sapxep' => $sort,
          ], static function ($value) { return $value !== ''; }))); ?>"
             <?php echo $category === 'tatca' ? 'aria-current="page"' : ''; ?>>
            Tất cả sách <span class="cat-count">(<?php echo $totalAll; ?>)</span>
          </a>
        </li>
        <?php foreach ($categories as $cat): ?>
          <li<?php echo $category === $cat['slug'] ? ' class="active"' : ''; ?>>
            <a href="<?php echo e(url('booklist', array_filter([
                'q' => $search,
                'danhmuc' => $cat['slug'],
                'sapxep' => $sort,
            ], static function ($value) { return $value !== ''; }))); ?>"
               <?php echo $category === $cat['slug'] ? 'aria-current="page"' : ''; ?>>
              <?php echo e($cat['name']); ?>
              <span class="cat-count">(<?php echo (int)$cat['book_count']; ?>)</span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <!-- ===== DANH SÁCH SÁCH (PHẢI) ===== -->
    <div class="book-main">

      <!-- Thanh công cụ: tìm kiếm + sắp xếp -->
      <div class="toolbar">

        <form class="main-search" action="<?php echo e(url('booklist')); ?>" method="get">
          <span class="search-icon">
            <img src="<?php echo e(asset('images/search.ico')); ?>" alt="">
          </span>
          <input type="search" name="q" value="<?php echo e($search); ?>"
                 aria-label="Tìm sách"
                 placeholder="Tìm kiếm sách theo tên, tác giả...">
          <input type="hidden" name="danhmuc" value="<?php echo e($category); ?>">
        </form>

        <div class="sort-box">
          <span class="sort-label">Sắp xếp:</span>
          <form class="select-wrap" action="<?php echo e(url('booklist')); ?>" method="get">
            <input type="hidden" name="q" value="<?php echo e($search); ?>">
            <input type="hidden" name="danhmuc" value="<?php echo e($category); ?>">
            <select id="sapxep" name="sapxep" aria-label="Sắp xếp sách"
                    onchange="this.form.submit()">
              <?php foreach ($sortOptions as $value => $label): ?>
                <option value="<?php echo e($value); ?>" <?php echo $sort === $value ? 'selected' : ''; ?>>
                  <?php echo e($label); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <noscript>
              <button type="submit">Lọc</button>
            </noscript>
            <span class="chevron">▾</span>
          </form>
        </div>

      </div>

      <h2 class="group-title">
        <?php echo e($categoryName); ?>
        <span class="book-count">(<?php echo $resultCount; ?> sách)</span>
      </h2>

      <div class="product-list">
        <?php if (!empty($books)): ?>
          <?php foreach ($books as $book): ?>
            <?php
              $bookUrl = url('product-detail', ['id' => (int)$book['product_id']]);
              $img = product_image_src($book);
            ?>
            <a class="product-card-link" href="<?php echo e($bookUrl); ?>">
              <div class="product">
                <div class="img-box">
                  <img src="<?php echo e($img); ?>" alt="<?php echo e($book['title']); ?>">
                </div>
                <h3><?php echo e($book['title']); ?></h3>
                <?php if (!empty($book['author'])): ?>
                  <p><?php echo e($book['author']); ?></p>
                <?php endif; ?>
                <p>
                  <span class="price"><?php echo money($book['price']); ?></span>
                  <?php if ($book['original_price'] !== null && $book['original_price'] > $book['price']): ?>
                    <s class="old-price"><?php echo money($book['original_price']); ?></s>
                  <?php endif; ?>
                </p>
              </div>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="booklist-empty">
            Không tìm thấy sách phù hợp.
            <a href="<?php echo e(url('booklist')); ?>">Xóa bộ lọc</a>
          </p>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>