<section class="page-banner">
  <h1>Mã giảm &amp; Sự kiện</h1>
  <p>Cập nhật ưu đãi và chương trình khuyến mãi mới nhất từ BetaBook.</p>
</section>

<section class="container section">
  <input class="tab-input" type="radio" name="discount-event-tab" id="tab-magiam" checked>
  <input class="tab-input" type="radio" name="discount-event-tab" id="tab-sukien">

  <div class="tabs-wrap">
    <div class="tabs" role="tablist" aria-label="Mã giảm và sự kiện">
      <label for="tab-magiam" role="tab">Mã giảm</label>
      <label for="tab-sukien" role="tab">Sự kiện</label>
    </div>

    <div class="tab-panel panel-magiam" role="tabpanel">
      <div class="coupon-grid">
        <?php if ($coupons): ?>
          <?php foreach ($coupons as $coupon): ?>
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
          <p class="discount-event-empty">Hiện chưa có mã giảm giá nào.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="tab-panel panel-sukien" role="tabpanel">
      <div class="event-list">
        <?php if ($events): ?>
          <?php foreach ($events as $event): ?>
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
          <p class="discount-event-empty">Hiện chưa có sự kiện nào.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
