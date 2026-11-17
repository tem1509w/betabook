<?php
$pageTitle = 'Liên hệ';
$bodyClass = 'contact-page';

if (!empty($_SESSION['contact_success'])) {
    $contactSuccess = $_SESSION['contact_success'];
    unset($_SESSION['contact_success']);
}

$contactError = $contactError ?? '';
?>

<section class="contact-banner">
  <h1>Liên hệ với chúng tôi</h1>
  <p>BetaBook luôn sẵn sàng lắng nghe và hỗ trợ bạn.</p>
</section>

<section class="container section">
  <div class="contact-layout">
    <div class="contact-info">
      <h2>Thông tin liên hệ</h2>

      <div class="info-list">
        <div class="info-item">
          <div class="info-icon">📍</div>
          <div>
            <h3>Địa chỉ</h3>
            <p>Tầng 3, Tòa Black Home Center, 11A Cộng Hòa, Tân Bình, TPHCM</p>
          </div>
        </div>

        <div class="info-item">
          <div class="info-icon">📞</div>
          <div>
            <h3>Điện thoại</h3>
            <p>0123 456 789</p>
          </div>
        </div>

        <div class="info-item">
          <div class="info-icon">✉️</div>
          <div>
            <h3>Email</h3>
            <p>mkt.betabook@gmail.com</p>
          </div>
        </div>

        <div class="info-item">
          <div class="info-icon">🕐</div>
          <div>
            <h3>Giờ làm việc</h3>
            <p>Thứ 2 - Thứ 7: 8:00 - 21:00<br>Chủ nhật: 9:00 - 18:00</p>
          </div>
        </div>
      </div>

      <h2>Kết nối với BetaBook</h2>
      <div class="social-row">
        <a href="#" title="Facebook">
          <img src="<?= e(asset('images/facebook.png')) ?>" alt="Facebook">
        </a>
        <a href="#" title="Google">
          <img src="<?= e(asset('images/google.png')) ?>" alt="Google">
        </a>
      </div>
    </div>

    <div>
      <?php if (!empty($contactSuccess)): ?>
        <div class="contact-alert contact-alert-success" role="status"><?= e($contactSuccess) ?></div>
      <?php endif; ?>

      <?php if ($contactError !== ''): ?>
        <div class="contact-alert contact-alert-error" role="alert"><?= e($contactError) ?></div>
      <?php endif; ?>

      <form class="contact-form" method="post" action="<?= e(url('contact')) ?>">
        <h2>Gửi tin nhắn cho chúng tôi</h2>
        <p class="sub">Điền thông tin bên dưới, BetaBook sẽ phản hồi sớm nhất có thể.</p>

        <div class="form-grid">
          <div class="field">
            <label for="ten">Họ và tên *</label>
            <input type="text" id="ten" name="contact_name" required
                   placeholder="Nhập họ và tên"
                   value="<?= e((string)($_POST['contact_name'] ?? '')) ?>">
          </div>

          <div class="field">
            <label for="email">Email *</label>
            <input type="email" id="email" name="contact_email" required
                   placeholder="Nhập địa chỉ email"
                   value="<?= e((string)($_POST['contact_email'] ?? '')) ?>">
          </div>

          <div class="field">
            <label for="sdt">Số điện thoại</label>
            <input type="tel" id="sdt" name="contact_phone" pattern="0[0-9]{9}"
                   title="Số điện thoại gồm 10 chữ số, bắt đầu bằng 0."
                   placeholder="Ví dụ: 0912345678"
                   value="<?= e((string)($_POST['contact_phone'] ?? '')) ?>">
          </div>

          <div class="field">
            <label for="chude">Chủ đề</label>
            <?php $selectedSubject = (string)($_POST['contact_subject'] ?? ''); ?>
            <select id="chude" name="contact_subject">
              <option value="">-- Chọn chủ đề --</option>
              <option value="Hỏi về sản phẩm" <?= $selectedSubject === 'Hỏi về sản phẩm' ? 'selected' : '' ?>>Hỏi về sản phẩm</option>
              <option value="Hỗ trợ đơn hàng" <?= $selectedSubject === 'Hỗ trợ đơn hàng' ? 'selected' : '' ?>>Hỗ trợ đơn hàng</option>
              <option value="Hợp tác phân phối" <?= $selectedSubject === 'Hợp tác phân phối' ? 'selected' : '' ?>>Hợp tác phân phối</option>
              <option value="Góp ý / Khiếu nại" <?= $selectedSubject === 'Góp ý / Khiếu nại' ? 'selected' : '' ?>>Góp ý / Khiếu nại</option>
              <option value="Khác" <?= $selectedSubject === 'Khác' ? 'selected' : '' ?>>Khác</option>
            </select>
          </div>

          <div class="field full">
            <label for="noidung">Nội dung *</label>
            <textarea id="noidung" name="contact_message" required
                      placeholder="Nhập nội dung bạn muốn gửi..."><?= e((string)($_POST['contact_message'] ?? '')) ?></textarea>
          </div>
        </div>

        <button type="submit" name="contact_submit" value="1" class="button full">Gửi tin nhắn</button>
      </form>
    </div>
  </div>
</section>

<section class="container section map-section">
  <h2 class="section-title">Bản đồ chỉ đường</h2>
  <div class="img-box map-box">
    <img src="<?= e(asset('images/cửa hàng fake.png')) ?>" alt="Bản đồ chỉ đường đến BetaBook">
  </div>
</section>

<section class="gray-bg">
  <div class="container section">
    <h2 class="section-title">Câu hỏi thường gặp</h2>

    <div class="faq">
      <div class="faq-item">
        <h3>Làm sao để đặt sách tại BetaBook?</h3>
        <p>Bạn chọn sách, thêm vào giỏ hàng, điền thông tin giao hàng và bấm "Đặt hàng".</p>
      </div>

      <div class="faq-item">
        <h3>BetaBook có giao hàng toàn quốc không?</h3>
        <p>Có. BetaBook giao hàng trên toàn quốc, phí vận chuyển hiển thị khi thanh toán.</p>
      </div>

      <div class="faq-item">
        <h3>Tôi có thể đổi trả sách không?</h3>
        <p>Có. Vui lòng xem chính sách đổi trả hoặc liên hệ để được hỗ trợ trong vòng 7 ngày.</p>
      </div>

      <div class="faq-item">
        <h3>Liên hệ hỗ trợ nhanh nhất bằng cách nào?</h3>
        <p>Gọi hotline 0123 456 789 hoặc gửi tin nhắn qua biểu mẫu liên hệ ở trên.</p>
      </div>
    </div>
  </div>
</section>
