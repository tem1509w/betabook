<div class="admin-content">
  <div class="page-header">
    <h2>Sản phẩm yêu thích</h2>
  </div>

  <?php if (is_string($flashMessage) && $flashMessage !== ''): ?>
    <div class="alert success"><?= e($flashMessage) ?></div>
  <?php endif; ?>

  <form method="get" action="<?= e(admin_url()) ?>" class="user-filter">
    <input type="hidden" name="page" value="favorites">
    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Tìm tên sách, mã sách, tác giả, tài khoản khách hàng">
    <select name="status" aria-label="Lọc tình trạng tồn kho">
      <option value="">Tất cả tình trạng</option>
      <option value="in_stock" <?= $stockFilter === 'in_stock' ? 'selected' : '' ?>>Còn hàng</option>
      <option value="out_of_stock" <?= $stockFilter === 'out_of_stock' ? 'selected' : '' ?>>Hết hàng</option>
    </select>
    <button type="submit" class="button">Lọc</button>
  </form>

  <p>Tìm thấy <?= number_format($totalFavorites) ?> lượt lưu yêu thích</p>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>Ngày lưu</th><th>Khách hàng</th><th>Mã sách</th><th>Tên sách</th><th>Đơn giá</th><th>Tồn kho</th><th>Thao tác</th></tr>
      </thead>
      <tbody>
        <?php if (!$favorites): ?>
          <tr><td colspan="7">Chưa có sản phẩm yêu thích phù hợp.</td></tr>
        <?php else: ?>
          <?php foreach ($favorites as $favorite): ?>
            <tr>
              <td><?= e(date('d/m/Y H:i', strtotime($favorite['created_at']))) ?></td>
              <td>
                <strong><?= e($favorite['full_name'] ?: $favorite['username']) ?></strong><br>
                <a href="mailto:<?= e($favorite['email']) ?>"><?= e($favorite['email']) ?></a>
              </td>
              <td><?= e($favorite['product_code']) ?></td>
              <td>
                <a href="<?= e(url('product-detail', ['id' => (int)$favorite['product_id']])) ?>" target="_blank">
                  <strong><?= e($favorite['title']) ?></strong>
                </a><br>
                <small>Tác giả: <?= e($favorite['author'] ?: 'Chưa cập nhật') ?></small>
              </td>
              <td><?= e(money($favorite['price'])) ?></td>
              <td><?= (int)$favorite['stock_quantity'] > 0 ? 'Còn ' . number_format((int)$favorite['stock_quantity']) . ' cuốn' : 'Hết hàng' ?></td>
              <td>
                <form method="post" action="<?= e(admin_url('?action=delete-favorite')) ?>" onsubmit="return confirm('Xóa mục yêu thích này?');">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="user_id" value="<?= (int)$favorite['user_id'] ?>">
                  <input type="hidden" name="product_id" value="<?= (int)$favorite['product_id'] ?>">
                  <button type="submit" class="button outline small">Xóa</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
    <nav class="user-pagination" aria-label="Phân trang sản phẩm yêu thích">
      <?php if ($pageNumber > 1): ?>
        <a class="button outline small" href="<?= e(admin_url('?' . http_build_query(['page' => 'favorites', 'q' => $search, 'status' => $stockFilter, 'p' => $pageNumber - 1]))) ?>">Trước</a>
      <?php endif; ?>
      <span>Trang <?= $pageNumber ?> / <?= $totalPages ?></span>
      <?php if ($pageNumber < $totalPages): ?>
        <a class="button outline small" href="<?= e(admin_url('?' . http_build_query(['page' => 'favorites', 'q' => $search, 'status' => $stockFilter, 'p' => $pageNumber + 1]))) ?>">Sau</a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
</div>