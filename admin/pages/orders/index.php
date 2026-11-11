<div class="admin-content">
  <div class="page-header">
    <h2>Quản lý đơn hàng</h2>
  </div>

  <?php if (is_string($flashMessage) && $flashMessage !== ''): ?>
    <div class="alert success"><?= e($flashMessage) ?></div>
  <?php endif; ?>

  <form method="get" action="<?= e(admin_url()) ?>" class="user-filter">
    <input type="hidden" name="page" value="orders">
    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Tìm mã đơn, khách hàng, email, điện thoại">
    <select name="status" aria-label="Lọc trạng thái đơn hàng">
      <option value="">Tất cả trạng thái</option>
      <?php foreach ($allowedStatuses as $status): ?>
        <option value="<?= e($status) ?>" <?= $statusFilter === $status ? 'selected' : '' ?>>
          <?= e(order_status_label($status)) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="button">Lọc</button>
  </form>

  <p>Tìm thấy <?= number_format($totalOrders) ?> đơn hàng</p>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>Mã đơn</th><th>Ngày đặt</th><th>Khách hàng</th><th>Tổng tiền</th><th>Trạng thái</th><th>Chi tiết / cập nhật</th></tr>
      </thead>
      <tbody>
        <?php if (!$orders): ?>
          <tr><td colspan="6">Chưa có đơn hàng phù hợp.</td></tr>
        <?php else: ?>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td><?= e($order['order_code']) ?></td>
              <td><?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
              <td>
                <strong><?= e($order['customer_name']) ?></strong><br>
                <?= e($order['phone']) ?><br>
                <small><?= e($order['email']) ?></small>
              </td>
              <td><?= money($order['total_amount']) ?></td>
              <td><?= e(order_status_label($order['order_status'])) ?></td>
              <td>
                <details>
                  <summary>Xem / quản lý</summary>
                  <div class="order-admin-details">
                    <p>Địa chỉ: <?= e(implode(', ', array_filter([
                        $order['address_line'], $order['ward'], $order['district'], $order['city'],
                    ]))) ?></p>
                    <p>Thanh toán: <?= e(order_payment_label($order['payment_method'])) ?> —
                      <?= e(order_payment_status_label($order['payment_status'])) ?></p>
                    <?php if ($order['customer_note']): ?>
                      <p>Ghi chú: <?= e($order['customer_note']) ?></p>
                    <?php endif; ?>
                    <table class="data-table">
                      <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                      <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                          <tr>
                            <td><?= e($item['product_title']) ?> <small>(<?= e($item['product_code']) ?>)</small></td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td><?= money($item['unit_price']) ?></td>
                            <td><?= money($item['line_total']) ?></td>
                          </tr>
                        <?php endforeach; ?>
                        <tr><td colspan="3">Tạm tính</td><td><?= money($order['subtotal']) ?></td></tr>
                        <tr><th colspan="3">Tổng cộng</th><th><?= money($order['total_amount']) ?></th></tr>
                      </tbody>
                    </table>
                    <form method="post" action="<?= e(admin_url('?action=update-order-status')) ?>" class="order-status-form">
                      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="order_id" value="<?= (int)$order['order_id'] ?>">
                      <input type="hidden" name="q" value="<?= e($search) ?>">
                      <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
                      <label>Trạng thái đơn
                        <select name="order_status">
                          <?php foreach ($allowedStatuses as $status): ?>
                            <option value="<?= e($status) ?>" <?= $order['order_status'] === $status ? 'selected' : '' ?>>
                              <?= e(order_status_label($status)) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </label>
                      <label>Thanh toán
                        <select name="payment_status">
                          <?php foreach (['unpaid' => 'Chưa thanh toán', 'pending' => 'Chờ thanh toán', 'paid' => 'Đã thanh toán', 'refunded' => 'Đã hoàn tiền'] as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= $order['payment_status'] === $key ? 'selected' : '' ?>>
                              <?= e($label) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </label>
                      <button type="submit" class="button">Lưu trạng thái</button>
                    </form>
                    <form method="post" action="<?= e(admin_url('?action=delete-order')) ?>" class="order-delete-form"
                          onsubmit="return confirm('Xóa đơn hàng này? Nếu đơn chưa giao, tồn kho sẽ được hoàn lại.');">
                      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="order_id" value="<?= (int)$order['order_id'] ?>">
                      <input type="hidden" name="q" value="<?= e($search) ?>">
                      <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
                      <button type="submit" class="action-delete-button">Xóa đơn hàng</button>
                    </form>
                  </div>
                </details>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
    <nav class="user-pagination" aria-label="Phân trang đơn hàng">
      <?php if ($pageNumber > 1): ?>
        <a class="button outline small" href="<?= e(admin_url('?' . http_build_query(['page' => 'orders', 'q' => $search, 'status' => $statusFilter, 'p' => $pageNumber - 1]))) ?>">Trước</a>
      <?php endif; ?>
      <span>Trang <?= $pageNumber ?> / <?= $totalPages ?></span>
      <?php if ($pageNumber < $totalPages): ?>
        <a class="button outline small" href="<?= e(admin_url('?' . http_build_query(['page' => 'orders', 'q' => $search, 'status' => $statusFilter, 'p' => $pageNumber + 1]))) ?>">Sau</a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
</div>
