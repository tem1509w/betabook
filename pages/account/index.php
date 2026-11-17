<section class="account-banner">
  <h1>Tài khoản của tôi</h1>
  <p>Quản lý thông tin cá nhân, mật khẩu và đơn hàng của bạn.</p>
</section>

<?php if ($accountNotice): ?>
  <p class="form-msg success" role="status"><?php echo e($accountNotice); ?></p>
<?php endif; ?>
<?php if ($accountError): ?>
  <p class="form-msg error" role="alert"><?php echo e($accountError); ?></p>
<?php endif; ?>

<!-- ============ 3. NỘI DUNG: MENU TRÁI + 3 MỤC ============ -->
<main class="container section">
  <div class="account-layout">

    <!-- Radio ẩn: chọn mục nào thì hiện mục đó. Mặc định: Thông tin cá nhân -->
    <input class="tab-input" type="radio" name="muc" id="tab-info" <?php echo $accountActiveTab === 'password' ? '' : 'checked'; ?>>
    <input class="tab-input" type="radio" name="muc" id="tab-pass" <?php echo $accountActiveTab === 'password' ? 'checked' : ''; ?>>
    <input class="tab-input" type="radio" name="muc" id="tab-orders">

    <!-- ----- Cột trái: hồ sơ + menu ----- -->
    <aside class="account-side">
      <img class="img-box avatar account-avatar"
           src="<?php echo e(asset('images/' . $accountUser['avatar_image'])); ?>"
           alt="Ảnh đại diện">
      <h2><?php echo e((string)($accountUser['username'] ?: $accountUser['username'])); ?></h2>
      <p class="side-mail"><?php echo e($accountUser['email']); ?></p>

      <form action="<?php echo e(url('account')); ?>" method="post" class="account-avatar-form">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
        <input type="hidden" name="account_action" value="update_avatar">
        <fieldset>
          <legend>Chọn ảnh đại diện</legend>
          <div class="account-avatar-options">
            <?php foreach ($avatarOptions as $avatarIndex => $avatarOption): ?>
              <label class="account-avatar-option">
                <input
                  type="radio"
                  name="avatar_image"
                  value="<?php echo e($avatarOption); ?>"
                  <?php echo $accountUser['avatar_image'] === $avatarOption ? 'checked' : ''; ?>
                  required>
                <img
                  src="<?php echo e(asset('images/' . $avatarOption)); ?>"
                  alt="Avatar <?php echo e((string)($avatarIndex + 1)); ?>">
              </label>
            <?php endforeach; ?>
          </div>
        </fieldset>
        <button type="submit" class="button">Lưu ảnh đại diện</button>
      </form>

      <nav class="side-nav">
        <label for="tab-info">Thông tin cá nhân</label>
        <label for="tab-pass">Đổi mật khẩu</label>
        <label for="tab-orders">Đơn hàng của tôi</label>
        <?php if ($canAccessAdmin): ?>
          <a href="<?php echo e(admin_url()); ?>">Trang quản trị</a>
        <?php endif; ?>
        <form action="<?php echo e(url('account')); ?>" method="post">
          <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
          <input type="hidden" name="account_action" value="logout">
          <button type="submit">Đăng xuất</button>
        </form>
      </nav>
    </aside>

    <!-- ----- Cột phải: các mục ----- -->
    <div class="account-main">

      <!-- MỤC 1: THÔNG TIN CÁ NHÂN (bảng users) -->
      <section class="panel panel-info">
        <h2>Thông tin cá nhân</h2>

        <form action="<?php echo e(url('account')); ?>" method="post">
          <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
          <input type="hidden" name="account_action" value="update_profile">
          <div class="form-grid">
            <div class="field">
              <label for="full_name">Họ và tên</label>
              <input type="text" id="full_name" name="full_name" maxlength="191"
                     value="<?php echo e((string)($profileData['full_name'] ?? '')); ?>">
            </div>
            <div class="field">
              <label for="username">Tên đăng nhập *</label>
              <input type="text" id="username" name="username" maxlength="191"
                     value="<?php echo e($profileData['username']); ?>" required>
            </div>
            <div class="field">
              <label for="email">Email *</label>
              <div class="email-control">
                <input type="email" id="email" name="email" maxlength="191"
                       value="<?php echo e($profileData['email']); ?>" required autocomplete="email">
              </div>
            </div>
            <div class="field">
              <label for="phone">Số điện thoại</label>
              <input type="tel" id="phone" name="phone" maxlength="20"
                     value="<?php echo e((string)($profileData['phone'] ?? '')); ?>" autocomplete="tel">
            </div>
          </div>
          <button type="submit" class="button">Lưu thay đổi</button>
        </form>
      </section>

      <!-- MỤC 2: ĐỔI MẬT KHẨU (users.password) -->
      <section class="panel panel-pass">
        <h2>Đổi mật khẩu</h2>

        <form action="<?php echo e(url('account')); ?>" method="post">
          <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
          <input type="hidden" name="account_action" value="change_password">
          <div class="form-grid one">
            <div class="field">
              <label for="current_password">Mật khẩu hiện tại *</label>
              <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
            </div>
            <div class="field">
              <label for="new_password">Mật khẩu mới *</label>
              <input type="password" id="new_password" name="new_password" required minlength="6"
                     title="Mật khẩu cần ít nhất 6 ký tự." autocomplete="new-password" placeholder="Ít nhất 6 ký tự">
            </div>
            <div class="field">
              <label for="confirm_password">Nhập lại mật khẩu mới *</label>
              <input type="password" id="confirm_password" name="confirm_password" required minlength="6" autocomplete="new-password">
            </div>
          </div>
          <button type="submit" class="button">Đổi mật khẩu</button>
        </form>
      </section>

      <!-- MỤC 3: ĐƠN HÀNG CỦA TÔI (bảng orders + order_items) -->
      <section class="panel panel-orders">
        <h2>Đơn hàng của tôi</h2>

        <div class="order-head">
          <span>Mã đơn</span><span>Ngày đặt</span><span>Tổng tiền</span><span>Trạng thái</span><span></span>
        </div>

        <!-- Muốn thêm đơn: copy nguyên một khối <details class="order"> (PHP sẽ lặp khối này bằng foreach) -->
        <details class="order">
          <summary>
            <span class="code">BB-000140</span>
            <span>08/11/2026</span>
            <span class="price">97.000₫</span>
            <span><b class="badge cho">Chờ xử lý</b></span>
            <span class="view">Xem chi tiết</span>
          </summary>
          <div class="order-body">
            <table class="mini">
              <tr><th>Sản phẩm</th><th class="num">SL</th><th class="num">Đơn giá</th><th class="num">Thành tiền</th></tr>
              <tr><td>Nhà Giả Kim (Tái Bản 2025)</td><td class="num">1</td><td class="num">72.000₫</td><td class="num">72.000₫</td></tr>
              <tr><td colspan="3">Phí vận chuyển</td><td class="num">25.000₫</td></tr>
              <tr class="total"><td colspan="3">Tổng cộng</td><td class="num price">97.000₫</td></tr>
            </table>
            <p class="ship-info">Giao tới: Nguyễn Văn A - 0912345678 - Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành</p>
          </div>
        </details>

        <details class="order">
          <summary>
            <span class="code">BB-000131</span>
            <span>05/11/2026</span>
            <span class="price">177.000₫</span>
            <span><b class="badge giao">Đang giao</b></span>
            <span class="view">Xem chi tiết</span>
          </summary>
          <div class="order-body">
            <table class="mini">
              <tr><th>Sản phẩm</th><th class="num">SL</th><th class="num">Đơn giá</th><th class="num">Thành tiền</th></tr>
              <tr><td>Wonka</td><td class="num">2</td><td class="num">76.000₫</td><td class="num">152.000₫</td></tr>
              <tr><td colspan="3">Phí vận chuyển</td><td class="num">25.000₫</td></tr>
              <tr class="total"><td colspan="3">Tổng cộng</td><td class="num price">177.000₫</td></tr>
            </table>
            <p class="ship-info">Giao tới: Nguyễn Văn A - 0912345678 - Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành</p>
          </div>
        </details>

        <details class="order">
          <summary>
            <span class="code">BB-000125</span>
            <span>12/10/2026</span>
            <span class="price">215.000₫</span>
            <span><b class="badge xong">Hoàn thành</b></span>
            <span class="view">Xem chi tiết</span>
          </summary>
          <div class="order-body">
            <table class="mini">
              <tr><th>Sản phẩm</th><th class="num">SL</th><th class="num">Đơn giá</th><th class="num">Thành tiền</th></tr>
              <tr><td>WHO? Chuyện kể về danh nhân thế giới - Nikola Tesla</td><td class="num">1</td><td class="num">55.000₫</td><td class="num">55.000₫</td></tr>
              <tr><td>進撃の巨人 11 - Attack On Titan 11</td><td class="num">1</td><td class="num">135.000₫</td><td class="num">135.000₫</td></tr>
              <tr><td colspan="3">Phí vận chuyển</td><td class="num">25.000₫</td></tr>
              <tr class="total"><td colspan="3">Tổng cộng</td><td class="num price">215.000₫</td></tr>
            </table>
            <p class="ship-info">Giao tới: Nguyễn Văn A - 0912345678 - Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành</p>
          </div>
        </details>

        <!-- Khi chưa có đơn nào: PHP in khối này thay cho danh sách ở trên -->
        <div class="empty" hidden>
          <p>Bạn chưa có đơn hàng nào.</p>
          <a class="button" href="booklist.html">Khám phá tủ sách</a>
        </div>
      </section>

    </div>
  </div>
</main>
