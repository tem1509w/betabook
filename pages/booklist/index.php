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
    <?php if ($category === 'tatca'): ?>
      <strong>Tủ sách</strong>
    <?php else: ?>
      <a href="<?php echo e(url('booklist')); ?>">Tủ sách</a>
      <span>›</span>
      <strong><?php echo e($categoryName); ?></strong>
    <?php endif; ?>
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

        <form class="main-search" action="<?php echo e(url('')); ?>" method="get">
          <input type="hidden" name="route" value="booklist">
          <span class="search-icon">
            <img src="<?php echo e(asset('images/search.ico')); ?>" alt="">
          </span>
          <input type="search" name="q" value="<?php echo e($search); ?>"
                 aria-label="Tìm sách"
                 placeholder="Tìm kiếm sách theo tên...">
          <input type="hidden" name="danhmuc" value="<?php echo e($category); ?>">
          <button type="submit" class="search-submit">Tìm sách</button>
        </form>

        <div class="sort-box">
          <label class="sort-label" for="sapxep">Sắp xếp</label>
          <form class="select-wrap" action="<?php echo e(url('')); ?>" method="get">
            <input type="hidden" name="route" value="booklist">
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

      <?php if ($totalPages > 1): ?>
        <?php
          $paginationParams = array_filter([
              'q' => $search,
              'danhmuc' => $category,
              'sapxep' => $sort,
          ], static function ($value) { return $value !== ''; });
          if ($totalPages <= 7) {
              $visiblePages = range(1, $totalPages);
          } elseif ($currentPage <= 2) {
              $visiblePages = [1, 2, 3];
          } elseif ($currentPage >= $totalPages - 1) {
              $visiblePages = [$totalPages - 2, $totalPages - 1, $totalPages];
          } else {
              $visiblePages = [$currentPage - 1, $currentPage, $currentPage + 1];
          }
        ?>
        <nav class="pagination booklist-pagination" aria-label="Điều hướng trang sách">
          <?php if ($currentPage > 1): ?>
            <a href="<?php echo e(url('booklist', array_merge($paginationParams, ['page' => $currentPage - 1]))); ?>"
               rel="prev" aria-label="Trang trước">‹</a>
          <?php endif; ?>

          <?php if ($visiblePages[0] > 1): ?>
            <a href="<?php echo e(url('booklist', array_merge($paginationParams, ['page' => 1]))); ?>">1</a>
            <?php if ($visiblePages[0] > 2): ?>
              <details class="pagination-jump">
                <summary aria-label="Nhập số trang muốn xem">…</summary>
                <form action="<?php echo e(url('')); ?>" method="get">
                  <input type="hidden" name="route" value="booklist">
                  <input type="hidden" name="q" value="<?php echo e($search); ?>">
                  <input type="hidden" name="danhmuc" value="<?php echo e($category); ?>">
                  <input type="hidden" name="sapxep" value="<?php echo e($sort); ?>">
                  <label>
                    <span class="visually-hidden">Số trang muốn xem</span>
                    <input type="number" name="page" min="1" max="<?php echo $totalPages; ?>"
                           placeholder="Trang (1-<?php echo $totalPages; ?>)" required>
                  </label>
                  <button type="submit">Đi</button>
                </form>
              </details>
            <?php endif; ?>
          <?php endif; ?>

          <?php foreach ($visiblePages as $visiblePage): ?>
            <a href="<?php echo e(url('booklist', array_merge($paginationParams, ['page' => $visiblePage]))); ?>"
               class="<?php echo $currentPage === $visiblePage ? 'on' : ''; ?>"
               <?php echo $currentPage === $visiblePage ? 'aria-current="page"' : ''; ?>>
              <?php echo $visiblePage; ?>
            </a>
          <?php endforeach; ?>

          <?php if (end($visiblePages) < $totalPages): ?>
            <?php if (end($visiblePages) < $totalPages - 1): ?>
              <details class="pagination-jump">
                <summary aria-label="Nhập số trang muốn xem">…</summary>
                <form action="<?php echo e(url('')); ?>" method="get">
                  <input type="hidden" name="route" value="booklist">
                  <input type="hidden" name="q" value="<?php echo e($search); ?>">
                  <input type="hidden" name="danhmuc" value="<?php echo e($category); ?>">
                  <input type="hidden" name="sapxep" value="<?php echo e($sort); ?>">
                  <label>
                    <span class="visually-hidden">Số trang muốn xem</span>
                    <input type="number" name="page" min="1" max="<?php echo $totalPages; ?>"
                           placeholder="Trang (1-<?php echo $totalPages; ?>)" required>
                  </label>
                  <button type="submit">Đi</button>
                </form>
              </details>
            <?php endif; ?>
            <a href="<?php echo e(url('booklist', array_merge($paginationParams, ['page' => $totalPages]))); ?>">
              <?php echo $totalPages; ?>
            </a>
          <?php endif; ?>

          <?php if ($currentPage < $totalPages): ?>
            <a href="<?php echo e(url('booklist', array_merge($paginationParams, ['page' => $currentPage + 1]))); ?>"
               rel="next" aria-label="Trang tiếp theo">›</a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>

    </div>
  </div>
</section>