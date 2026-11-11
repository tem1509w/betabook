<main class="container cart-page">
  <h1 class="page-title">Giỏ hàng &amp; thanh toán</h1>

  <ol class="steps" aria-label="Các bước đặt hàng">
    <li class="on">1. Giỏ hàng</li>
    <li>2. Thông tin &amp; thanh toán</li>
    <li>3. Hoàn tất</li>
  </ol>

  <?php if ($cartError !== null): ?>
    <p class="form-msg error" role="alert"><?php echo e($cartError); ?></p>
  <?php endif; ?>

  <?php if (!$cartItems): ?>
    <section class="box cart-empty">
      <h2>Giỏ hàng của bạn đang trống</h2>
      <p>Hãy chọn những cuốn sách bạn yêu thích để tiếp tục.</p>
      <a class="button" href="<?php echo e(url('booklist')); ?>">← Khám phá tủ sách</a>
    </section>
  <?php else: ?>
    <?php
    $cartCanCheckout = true;
    foreach ($cartItems as $cartItem) {
        if ((int)$cartItem['stock_quantity'] < (int)$cartItem['cart_quantity']) {
            $cartCanCheckout = false;
        }
    }
    ?>
    <form class="cart-layout" action="<?php echo e(url('cart')); ?>" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
      <div class="cart-main">
        <section class="box">
          <h2>Sản phẩm trong giỏ (<?php echo count($cartItems); ?>)</h2>
          <div class="cart-head">
            <span>Sản phẩm</span><span>Đơn giá</span><span>Số lượng</span><span>Thành tiền</span><span></span>
          </div>

          <?php foreach ($cartItems as $item): ?>
            <div class="cart-item">
              <div class="item-info">
                <a class="img-box item-img" href="<?php echo e(url('product-detail', ['id' => (int)$item['product_id']])); ?>">
                  <img src="<?php echo e(product_image_src($item)); ?>" alt="<?php echo e($item['title']); ?>">
                </a>
                <div>
                  <h3><?php echo e($item['title']); ?></h3>
                  <p>Tác giả: <?php echo e((string)($item['author'] ?? 'Chưa cập nhật')); ?></p>
                  <?php if ((int)$item['stock_quantity'] < (int)$item['cart_quantity']): ?>
                    <p class="cart-stock-error">Chỉ còn <?php echo (int)$item['stock_quantity']; ?> cuốn. Vui lòng cập nhật số lượng.</p>
                  <?php endif; ?>
                </div>
              </div>
              <span class="price"><?php echo money($item['price']); ?></span>
              <input class="qty" type="number" name="quantities[<?php echo (int)$item['product_id']; ?>]"
                     value="<?php echo (int)$item['cart_quantity']; ?>" min="1"
                     max="<?php echo max(1, min(99, (int)$item['stock_quantity'])); ?>"
                     aria-label="Số lượng <?php echo e($item['title']); ?>">
              <span class="price"><?php echo money($item['line_total']); ?></span>
              <button class="remove" type="submit" formnovalidate name="remove_product" value="<?php echo (int)$item['product_id']; ?>"
                      title="Xóa khỏi giỏ">✕ Xóa</button>
            </div>
          <?php endforeach; ?>

          <div class="cart-actions">
            <a class="button outline" href="<?php echo e(url('booklist')); ?>">← Tiếp tục mua sắm</a>
            <div>
              <button class="button outline" type="submit" name="cart_action" value="clear"
                      formnovalidate
                      onclick="return confirm('Xóa toàn bộ giỏ hàng?')">Xóa toàn bộ giỏ hàng</button>
              <button class="button" type="submit" name="cart_action" value="update"
                      formaction="<?php echo e(url('cart')); ?>" formnovalidate>Cập nhật giỏ hàng</button>
            </div>
          </div>
        </section>

        <section class="box">
          <h2>Thông tin nhận hàng</h2>
          <div class="form-grid">
            <div class="field">
              <label for="checkout-name">Họ và tên *</label>
              <input id="checkout-name" name="customer_name" type="text" maxlength="191"
                     value="<?php echo e($checkoutData['customer_name']); ?>" autocomplete="name" required>
            </div>
            <div class="field">
              <label for="checkout-phone">Số điện thoại *</label>
              <input id="checkout-phone" name="phone" type="tel" maxlength="20"
                     value="<?php echo e($checkoutData['phone']); ?>" autocomplete="tel" required>
            </div>
            <div class="field full">
              <label for="checkout-email">Email *</label>
              <input id="checkout-email" name="email" type="email" maxlength="191"
                     value="<?php echo e($checkoutData['email']); ?>" autocomplete="email" required>
            </div>
            <div class="field full">
              <label for="checkout-address">Địa chỉ cụ thể *</label>
              <input id="checkout-address" name="address_line" type="text" maxlength="255"
                     value="<?php echo e($checkoutData['address_line']); ?>" autocomplete="street-address" required>
            </div>
            <div class="field">
              <label for="checkout-ward">Phường / xã *</label>
              <input id="checkout-ward" name="ward" type="text" maxlength="191"
                     value="<?php echo e($checkoutData['ward']); ?>" required>
            </div>
            <div class="field">
              <label for="checkout-district">Quận / huyện *</label>
              <input id="checkout-district" name="district" type="text" maxlength="191"
                     value="<?php echo e($checkoutData['district']); ?>" required>
            </div>
            <div class="field full">
              <label for="checkout-city">Tỉnh / thành phố *</label>
              <input id="checkout-city" name="city" type="text" maxlength="191"
                     value="<?php echo e($checkoutData['city']); ?>" autocomplete="address-level1" required>
            </div>
            <div class="field full">
              <label for="checkout-note">Ghi chú đơn hàng</label>
              <textarea id="checkout-note" name="customer_note" maxlength="2000" rows="3"><?php echo e($checkoutData['customer_note']); ?></textarea>
            </div>
          </div>
        </section>

        <section class="box payment">
          <h2>Phương thức thanh toán</h2>
          <label class="option">
            <input type="radio" name="payment_method" value="cod"
                   <?php echo $checkoutData['payment_method'] === 'cod' ? 'checked' : ''; ?>>
            <span><strong>Thanh toán khi nhận hàng (COD)</strong><br><small>Thanh toán cho đơn vị giao hàng khi nhận sách.</small></span>
          </label>
          <label class="option">
            <input type="radio" name="payment_method" value="bank_transfer"
                   <?php echo $checkoutData['payment_method'] === 'bank_transfer' ? 'checked' : ''; ?>>
            <span><strong>Chuyển khoản ngân hàng</strong><br><small>Đơn hàng sẽ chờ xác nhận thanh toán từ BetaBook.</small></span>
          </label>
        </section>
      </div>

      <aside class="summary box">
        <h2>Tóm tắt đơn hàng</h2>
        <div class="coupon">
          <input type="text" placeholder="Nhập Mã giảm" aria-label="Mã giảm">
          <button type="button" class="button">Áp dụng</button>
        </div>
        <div class="row"><span>Tạm tính</span><span><?php echo money($cartTotal); ?></span></div>
        <div class="row"><span>Giảm giá</span><span>-0đ</span></div>
        <div class="row total">
          <span>Tổng cộng</span>
          <span class="price"><?php echo money($cartTotal); ?></span>
        </div>
        <label class="agree">
          <input type="checkbox" name="agree_terms" value="1" required>
          <span>Tôi đồng ý với điều khoản mua hàng của BetaBook</span>
        </label>
        <button type="submit" name="cart_action" value="checkout" class="button full"
                <?php echo $cartCanCheckout ? '' : 'disabled'; ?>>Đặt hàng</button>
        <?php if (!$cartCanCheckout): ?>
          <p class="cart-stock-error">Cập nhật số lượng sản phẩm trước khi đặt hàng.</p>
        <?php endif; ?>
        <a class="back" href="<?php echo e(url('booklist')); ?>">← Tiếp tục mua sắm</a>
      </aside>
    </form>
  <?php endif; ?>
</main>
