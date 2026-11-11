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
    <input class="tab-input" type="radio" name="muc" id="tab-info" <?php echo $accountActiveTab === 'info' ? 'checked' : ''; ?>>
    <input class="tab-input" type="radio" name="muc" id="tab-pass" <?php echo $accountActiveTab === 'password' ? 'checked' : ''; ?>>
    <input class="tab-input" type="radio" name="muc" id="tab-orders" <?php echo $accountActiveTab === 'orders' ? 'checked' : ''; ?>>

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
        <?php if (!$accountOrders): ?>
          <div class="empty">
            <p>Bạn chưa có đơn hàng nào.</p>
            <a class="button" href="<?php echo e(url('booklist')); ?>">Khám phá tủ sách</a>
          </div>
        <?php else: ?>
          <div class="order-head">
            <span>Mã đơn</span><span>Ngày đặt</span><span>Tổng tiền</span><span>Trạng thái</span><span></span>
          </div>
          <?php foreach ($accountOrders as $order): ?>
            <?php $statusClass = 'status-' . $order['order_status']; ?>
            <details class="order">
              <summary>
                <span class="code"><?php echo e($order['order_code']); ?></span>
                <span><?php echo e(date('d/m/Y', strtotime($order['created_at']))); ?></span>
                <span class="price"><?php echo money($order['total_amount']); ?></span>
                <span><b class="order-status <?php echo e($statusClass); ?>"><?php echo e(order_status_label($order['order_status'])); ?></b></span>
                <span class="view">Xem chi tiết</span>
              </summary>
              <div class="order-body">
                <table class="mini">
                  <thead><tr><th>Sản phẩm</th><th class="num">SL</th><th class="num">Đơn giá</th><th class="num">Thành tiền</th></tr></thead>
                  <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                      <tr>
                        <td><?php echo e($item['product_title']); ?></td>
                        <td class="num"><?php echo (int)$item['quantity']; ?></td>
                        <td class="num"><?php echo money($item['unit_price']); ?></td>
                        <td class="num"><?php echo money($item['line_total']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                    <?php if ((float)$order['discount_amount'] > 0): ?>
                      <tr><td colspan="3">Giảm giá</td><td class="num">-<?php echo money($order['discount_amount']); ?></td></tr>
                    <?php endif; ?>
                    <tr class="total"><td colspan="3">Tổng cộng</td><td class="num price"><?php echo money($order['total_amount']); ?></td></tr>
                  </tbody>
                </table>
                <p class="ship-info">
                  Giao tới: <?php echo e($order['customer_name']); ?> -
                  <?php echo e($order['phone']); ?> -
                  <?php echo e(implode(', ', array_filter([
                      $order['address_line'], $order['ward'], $order['district'], $order['city'],
                  ]))); ?>
                </p>
                <p>Thanh toán: <?php echo e(order_payment_label($order['payment_method'])); ?>
                  (<?php echo e(order_payment_status_label($order['payment_status'])); ?>)
                </p>
                <?php if ($order['customer_note']): ?>
                  <p>Ghi chú: <?php echo e($order['customer_note']); ?></p>
                <?php endif; ?>
              </div>
            </details>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>

    </div>
  </div>
</main>
