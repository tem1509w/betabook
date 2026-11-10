<?php
/**
 * Trang chủ - Home Page
 * Từ index.html gốc BetaBook: 3 section (Sách bán chạy, Sách mới, Khuyến nghị)
 * Biến có sẵn: $bestSellers, $newReleases, $recommended
 */

?>

<!-- ============ 2. BANNER LỚN ============ -->
<section class="banner img-box banner-image">
  <img src="<?php echo e(asset($homeContent['banner_image'])); ?>" alt="Banner lớn">
</section>

<!-- ============ 3. GIỚI THIỆU ============ -->
<section class="container about" id="about">
  <div class="img-box about-img">
    <img src="<?php echo e(asset($homeContent['about_image'])); ?>" alt="Về BetaBook">
  </div>
  <div>
    <h2><?php echo e($homeContent['about_title']); ?></h2>
    <p><?php echo e($homeContent['about_description']); ?></p>
    <a class="button" href="<?php echo e(url('booklist')); ?>">XEM THÊM SÁCH</a>
  </div>
</section>

<!-- ============ 4. SÁCH BÁN CHẠY ============ -->
<section class="container section">
  <h2 class="section-title">Sản phẩm bán chạy</h2>

  <?php if (!empty($bestSellers)): ?>
    <div class="carousel<?php echo count($bestSellers) > 4 ? ' has-navigation' : ''; ?>">
      <?php if (count($bestSellers) > 4): ?>
        <input class="page-1" type="radio" name="best-sellers-page" id="best-sellers-page-1" checked>
        <input class="page-2" type="radio" name="best-sellers-page" id="best-sellers-page-2">
        <label class="arrow arrow-prev" for="best-sellers-page-1" aria-label="Trang trước">‹</label>
        <label class="arrow arrow-next" for="best-sellers-page-2" aria-label="Trang sau">›</label>
      <?php endif; ?>

      <div class="carousel-viewport">
        <div class="carousel-track product-list">
          <?php foreach ($bestSellers as $product): ?>
            <?php require __DIR__ . '/../../layouts/public/product-card.php'; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php else: ?>
    <p>Chưa có sách bán chạy.</p>
  <?php endif; ?>
</section>

<!-- ============ 5. HAI BANNER QUẢNG CÁO ============ -->
<section class="container ads">
  <div class="img-box">Ảnh quảng cáo 1</div>
  <div class="img-box">Ảnh quảng cáo 2</div>
</section>

<!-- ============ 6. SÁCH MỚI ============ -->
<section class="container section">
  <h2 class="section-title">Sách mới phát hành</h2>

  <?php if (!empty($newReleases)): ?>
    <?php $featured = $newReleases[0]; ?>
    <div class="new-book<?php echo empty($featured['image_url']) && empty($featured['cover_image']) ? ' new-book-no-cover' : ''; ?>">
      <div class="img-box">
        <img src="<?php echo e(product_image_src($featured)); ?>"
             alt="<?php echo e($featured['title']); ?>">
      </div>
      <div>
        <h3><?php echo e($featured['title']); ?></h3>
        <p class="author"><?php echo e($featured['author'] ?? ''); ?></p>
        <p><?php echo e($featured['description'] ?? ''); ?></p>
        <p>
          <span class="price"><?php echo money($featured['price']); ?></span>
          <?php if (!empty($featured['original_price']) && $featured['original_price'] > $featured['price']): ?>
            <s class="old-price"><?php echo money($featured['original_price']); ?></s>
          <?php endif; ?>
        </p>
        <a class="button" href="<?php echo url('product-detail', ['id' => (int)$featured['product_id']]); ?>">Đặt sách</a>
      </div>
    </div>
  <?php else: ?>
    <p>Chưa có sách mới phát hành.</p>
  <?php endif; ?>
</section>

<!-- ============ 7. SÁCH DÀNH CHO BẠN (nền xám) ============ -->
<section class="gray-bg">
  <div class="container section">
    <h2 class="section-title">Sách dành cho bạn</h2>

    <?php if (!empty($recommended)): ?>
      <div class="carousel<?php echo count($recommended) > 4 ? ' has-navigation' : ''; ?>">
        <?php if (count($recommended) > 4): ?>
          <input class="page-1" type="radio" name="recommended-page" id="recommended-page-1" checked>
          <input class="page-2" type="radio" name="recommended-page" id="recommended-page-2">
          <label class="arrow arrow-prev" for="recommended-page-1" aria-label="Trang trước">‹</label>
          <label class="arrow arrow-next" for="recommended-page-2" aria-label="Trang sau">›</label>
        <?php endif; ?>

        <div class="carousel-viewport">
          <div class="carousel-track product-list">
            <?php foreach ($recommended as $product): ?>
              <?php require __DIR__ . '/../../layouts/public/product-card.php'; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="news-more recommendations-more">
        <a class="button" href="<?php echo url('booklist'); ?>">Xem thêm</a>
      </div>
    <?php else: ?>
      <p>Chưa có sách khuyến nghị.</p>
    <?php endif; ?>
  </div>
</section>

<!-- ============ 8. DỊCH VỤ (nền cam) ============ -->
<section class="service" id="service">
  <div class="container service-inner">
    <div>
      <h2>Dịch vụ tại BetaBook</h2>
      <p><?php echo e($homeContent['service_description']); ?></p>
      <a class="button white" href="<?php echo e(url('booklist')); ?>">Khám phá sách</a>
    </div>
    <div class="service-icons">
      <div class="img-box">Icon 1</div>
      <div class="img-box">Icon 2</div>
      <div class="img-box">Icon 3</div>
    </div>
  </div>
</section>

<!-- ============ 9. CHUYÊN GIA ĐÁNH GIÁ ============ -->
<section class="container section">
  <h2 class="section-title">Đánh giá về BetaBook</h2>

  <div class="reviews">
    <?php foreach ($homeReviews as $review): ?>
    <div class="review">
      <div class="review-profile">
        <div class="img-box avatar">
          <img src="<?php echo e(asset('images/' . $review['avatar_image'])); ?>" alt="">
        </div>
        <b><?php echo e($review['reviewer_name']); ?></b>
        <span><?php echo e($review['member_label']); ?></span>
      </div>
      <div class="review-bubble">
        <p>"<?php echo e($review['quote']); ?>"</p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ============ 10. MÃ GIẢM & SỰ KIỆN ============ -->
<section class="container section" id="events">
  <div class="home-promo">
    <div class="home-promo-group">
      <h3 class="section-title">Mã giảm giá</h3>
      <div class="coupon-grid">
        <?php if ($homeCoupons): ?>
          <?php foreach ($homeCoupons as $coupon): ?>
            <article class="coupon-card">
              <div class="img-box">
                <img src="<?php echo e(asset('images/' . ($coupon['image_path'] ?: 'voucher.ico'))); ?>"
                     alt="Mã <?php echo e($coupon['coupon_code']); ?>">
              </div>
              <span class="code"><?php echo e($coupon['coupon_code']); ?></span>
              <h3><?php echo e($coupon['description']); ?></h3>
              <small>Hạn dùng: <?php echo e(date('d/m/Y', strtotime($coupon['expires_at']))); ?></small>
              <a class="button" href="<?php echo e(url('booklist')); ?>">Khám phá sách</a>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Hiện chưa có mã giảm giá nào.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="home-promo-group">
      <h3 class="section-title">Sự kiện</h3>
      <div class="event-list">
        <?php if ($homeEvents): ?>
          <?php foreach ($homeEvents as $event): ?>
            <article class="event-card">
              <div class="img-box">
                <?php if (!empty($event['image_path'])): ?>
                  <img src="<?php echo e(asset('images/' . $event['image_path'])); ?>"
                       alt="<?php echo e($event['title']); ?>">
                <?php endif; ?>
              </div>
              <div class="event-info">
                <small><?php echo e(date('d/m/Y', strtotime($event['event_date']))); ?></small>
                <h3><?php echo e($event['title']); ?></h3>
                <p><?php echo e($event['description']); ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Hiện chưa có sự kiện nào.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="home-promo-more">
    <a class="button" href="<?php echo e(url('discount-event')); ?>">Xem tất cả mã giảm &amp; sự kiện</a>
  </div>
</section>
