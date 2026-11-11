<?php
/**
 * Public Footer - Từ index.html gốc BetaBook
 */
?>

<!-- ============ 11. FOOTER ============ -->
<footer class="footer">
  <div class="container footer-inner">

    <div class="footer-brand">
      <a class="footer-logo" href="<?php echo url(''); ?>">
        <img src="<?php echo asset('images/logo.png'); ?>" alt="BetaBook">
      </a>
      <p>Công ty TNHH BetaBook<br>Tin tưởng - đồng hành</p>
      <h4>Contact for work:</h4>
      <div class="footer-newsletter">
        <input type="email" placeholder="Địa chỉ email">
        <button type="button">Submit</button>
      </div>
    </div>

    <div>
      <h4>THÔNG TIN</h4>
      <ul>
        <li><a href="<?php echo e(url('') . '#about'); ?>">Giới thiệu</a></li>
        <li><a href="<?php echo e(url('404')); ?>">Hệ thống nhà sách</a></li>
        <li><a href="<?php echo e(url('404')); ?>">Tuyển dụng</a></li>
        <li><a href="<?php echo e(url('404')); ?>">Hợp tác phân phối</a></li>
      </ul>
    </div>

    <div>
      <h4>CHÍNH SÁCH</h4>
      <ul>
        <li><a href="<?php echo e(url('404')); ?>">Chính sách thanh toán</a></li>
        <li><a href="<?php echo e(url('404')); ?>">Chính sách vận chuyển</a></li>
        <li><a href="<?php echo e(url('404')); ?>">Chính sách bảo mật</a></li>
        <li><a href="<?php echo e(url('404')); ?>">Chính sách đổi trả</a></li>
      </ul>
    </div>

    <div id="contact">
      <h4>LIÊN HỆ</h4>
      <p>📍 Tầng 3, Tòa Black Home Center, 11A Cộng Hòa, Tân Bình, TPHCM</p>
      <p>📞 0123 456 789</p>
      <p>✉️ mkt.betabook@gmail.com</p>
    </div>

  </div>

  <div class="copyright">© BetaBook, 2026</div>
</footer>

</body>
</html>