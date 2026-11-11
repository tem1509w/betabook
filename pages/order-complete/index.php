<main class="container cart-page">
  <ol class="steps" aria-label="Các bước đặt hàng">
    <li>1. Giỏ hàng</li>
    <li>2. Thông tin &amp; thanh toán</li>
    <li class="on">3. Hoàn tất</li>
  </ol>
  <section class="box order-confirmation">
    <h1 class="page-title">Đặt hàng thành công</h1>
    <p>Cảm ơn <?php echo e($order['customer_name']); ?> đã mua sắm tại BetaBook.</p>
    <p>Mã đơn hàng: <strong><?php echo e($order['order_code']); ?></strong></p>
    <p>Trạng thái: <strong><?php echo e(order_status_label($order['order_status'])); ?></strong></p>
    <p>Thanh toán: <?php echo e(order_payment_label($order['payment_method'])); ?> —
      <?php echo e(order_payment_status_label($order['payment_status'])); ?></p>
    <?php if ($order['payment_method'] === 'bank_transfer'): ?>
      <p>Đơn hàng chuyển khoản đang chờ BetaBook xác nhận thanh toán. Hiện chưa tích hợp cổng thanh toán trực tuyến.</p>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="data-table">
        <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
        <tbody>
          <?php foreach ($order['items'] as $item): ?>
            <tr>
              <td><?php echo e($item['product_title']); ?></td>
              <td><?php echo (int)$item['quantity']; ?></td>
              <td><?php echo money($item['unit_price']); ?></td>
              <td><?php echo money($item['line_total']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><th colspan="3">Tạm tính</th><th><?php echo money($order['subtotal']); ?></th></tr>
          <tr><th colspan="3">Tổng cộng</th><th><?php echo money($order['total_amount']); ?></th></tr>
        </tfoot>
      </table>
    </div>
    <p>Giao đến: <?php echo e(implode(', ', array_filter([
        $order['address_line'], $order['ward'], $order['district'], $order['city'],
    ]))); ?></p>
    <?php if ($order['user_id'] !== null): ?>
      <a class="button" href="<?php echo e(url('account', ['tab' => 'orders'])); ?>">Xem đơn hàng của tôi</a>
    <?php endif; ?>
    <a class="button outline" href="<?php echo e(url('booklist')); ?>">Tiếp tục mua sắm</a>
  </section>
</main>
