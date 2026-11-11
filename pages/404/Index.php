<section class="container section" style="text-align:center; padding:80px 16px;">
  <h1 style="font-size:96px; line-height:1; margin:0; color:#e63946;">404</h1>
  <h2 style="margin:16px 0 8px;">Không tìm thấy trang bạn cần</h2>
  <p style="color:#666; max-width:520px; margin:0 auto 28px;">
    <?php echo e($errorMessage); ?>
    Hãy kiểm tra lại đường dẫn, hoặc quay về các trang chính của BetaBook.
  </p>

  <p>
    <a class="button" href="<?php echo e(url('booklist')); ?>">Xem tủ sách</a>
    &nbsp;
    <a class="button" href="<?php echo e(url('discount-event')); ?>">Mã giảm &amp; Sự kiện</a>
    &nbsp;
    <a class="button" href="<?php echo e(url('')); ?>">Về trang chủ</a>
  </p>
</section>