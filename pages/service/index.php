<?php
$imgDir = is_dir(__DIR__ . '/../../assets/images/products') ? 'images/products/' : 'images/books/';
?>

<main>
  <section class="service-hero">
    <div class="container service-hero-inner">
      <div class="service-hero-copy">
        <p class="service-eyebrow">BETABOOK <span></span> ĐỒNG HÀNH CÙNG TRI THỨC</p>
        <h1>Mỗi trang sách,<br><span>một kết nối mới.</span></h1>
        <p class="service-hero-description">Từ hợp tác phân phối đến trải nghiệm nội dung số, BetaBook kết nối những cuốn sách hay với người đọc phù hợp.</p>
        <div class="service-hero-actions">
          <a class="button" href="#distribution">Khám phá dịch vụ <span aria-hidden="true">↓</span></a>
          <a class="service-action service-action-secondary" href="<?php echo e(url('contact')); ?>">Kết nối với chúng tôi <span aria-hidden="true">↗</span></a>
        </div>
      </div>
      <figure class="service-hero-visual">
        <img src="<?php echo e(asset('images/banner/main-banner.png')); ?>" alt="Thế giới sách và thương hiệu BetaBook">
        <figcaption>Đọc rộng hơn. Kết nối gần hơn.</figcaption>
      </figure>
    </div>
  </section>

  <section class="service-feature" id="distribution">
    <div class="container service-feature-inner">
      <div class="service-feature-copy">
        <p class="service-kicker"><span>01</span> DÀNH CHO ĐỐI TÁC XUẤT BẢN</p>
        <h2>Đưa sách hay<br>đến đúng độc giả.</h2>
        <p>BetaBook kết nối nhà xuất bản, đơn vị phát hành và tác giả với độc giả thông qua hệ thống bán hàng trực tuyến. Cùng trao đổi về danh mục sách, chính sách phân phối và kế hoạch giới thiệu sản phẩm.</p>
        <ul class="service-points">
          <li>Phát triển danh mục sách phù hợp</li>
          <li>Phối hợp kế hoạch giới thiệu sản phẩm</li>
          <li>Trao đổi trực tiếp cùng đội ngũ BetaBook</li>
        </ul>
        <a class="service-text-link" href="<?php echo e(url('contact')); ?>">Bắt đầu trao đổi <span aria-hidden="true">↗</span></a>
      </div>
      <figure class="service-feature-visual service-partner-visual">
        <img src="<?php echo e(asset('images/banner/our-story.jpg')); ?>" alt="Đối tác cùng phát triển và lan tỏa tri thức">
        <figcaption>Hợp tác để những ý tưởng tốt được lan tỏa.</figcaption>
      </figure>
    </div>
  </section>

  <section class="service-feature service-feature-digital" id="digital-content">
    <div class="container service-feature-inner">
      <div class="service-feature-copy">
        <p class="service-kicker"><span>02</span> MỞ RỘNG TRẢI NGHIỆM ĐỌC</p>
        <h2>Tri thức luôn<br>trong tầm tay.</h2>
        <p>BetaBook hướng tới mở rộng trải nghiệm đọc với nội dung và ấn phẩm số, để độc giả tiếp cận tri thức thuận tiện hơn trên nhiều thiết bị.</p>
        <ul class="service-points">
          <li>Khám phá nội dung theo chủ đề</li>
          <li>Tiếp cận tri thức linh hoạt hơn</li>
          <li>Liên tục mở rộng thế giới sách</li>
        </ul>
        <a class="service-text-link" href="<?php echo e(url('booklist')); ?>">Khám phá tủ sách <span aria-hidden="true">↗</span></a>
      </div>
      <div class="digital-book-visual" aria-label="Một số đầu sách tại BetaBook">
        <img class="digital-book digital-book-one" src="<?php echo e(asset($imgDir . 'NhaGiaKim.webp')); ?>" alt="Bìa sách Nhà Giả Kim">
        <img class="digital-book digital-book-two" src="<?php echo e(asset($imgDir . 'NikolaTesla.webp')); ?>" alt="Bìa sách về Nikola Tesla">
        <img class="digital-book digital-book-three" src="<?php echo e(asset($imgDir . 'Wonka.webp')); ?>" alt="Bìa sách Wonka">
        <p class="digital-book-caption">Mở một cuốn sách.<br>Mở thêm một thế giới.</p>
      </div>
    </div>
  </section>

  <section class="service-contact-band">
    <div class="container service-contact-inner">
      <p>Bạn có một ý tưởng muốn cùng BetaBook phát triển?</p>
      <a href="<?php echo e(url('contact')); ?>">Hãy cùng bắt đầu <span aria-hidden="true">↗</span></a>
    </div>
  </section>
</main>