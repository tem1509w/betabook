<div class="admin-content">
  <div class="page-header">
    <h2><?= $showFavoriteDetails ? 'Chi tiết sản phẩm yêu thích' : 'Sản phẩm yêu thích' ?></h2>
  </div>

  <?php if (is_string($flashMessage) && $flashMessage !== ''): ?>
    <div class="alert success"><?= e($flashMessage) ?></div>
  <?php endif; ?>

  <?php if ($showFavoriteDetails): ?>
    <p>
      <a class="button outline small" href="<?= e(admin_url('?' . http_build_query([
          'page' => 'favorites',
          'q' => $search,
          'p' => max(1, (int)get_param('p', 1)),
      ]))) ?>">Quay lại danh sách</a>
    </p>

    <?php if (!$favoriteUser): ?>
      <p>Không tìm thấy tài khoản khách hàng.</p>
    <?php else: ?>
      <p>
        <strong><?= e($favoriteUser['full_name'] ?: $favoriteUser['username']) ?></strong>
        (<?= e($favoriteUser['email']) ?>) — <?= count($favoriteBooks) ?> sách yêu thích
      </p>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>STT</th><th>Ngày yêu thích</th><th>Mã sách</th><th>Tên sách</th><th>Đơn giá</th><th>Tồn kho</th><th>Thao tác</th></tr>
          </thead>
          <tbody>
            <?php if (!$favoriteBooks): ?>
              <tr><td colspan="7">Tài khoản này hiện không có sách yêu thích.</td></tr>
            <?php else: ?>
              <?php foreach ($favoriteBooks as $index => $favorite): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= e(date('d/m/Y H:i', strtotime($favorite['created_at']))) ?></td>
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
                    <form method="post" action="<?= e(admin_url('?action=delete-favorite')) ?>" onsubmit="return confirm('Xóa sách này khỏi danh sách yêu thích?');">
                      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="user_id" value="<?= (int)$favoriteUser['user_id'] ?>">
                      <input type="hidden" name="product_id" value="<?= (int)$favorite['product_id'] ?>">
                      <input type="hidden" name="return_user_id" value="<?= (int)$favoriteUser['user_id'] ?>">
                      <button type="submit" class="button outline small">Xóa</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  <?php else: ?>
  <form method="get" action="<?= e(admin_url()) ?>" class="user-filter">
    <input type="hidden" name="page" value="favorites">
    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Tìm tên sách, mã sách, tác giả, tài khoản khách hàng">
    <button type="submit" class="button">Tìm kiếm</button>
  </form>

  <p>Tìm thấy <?= number_format($totalCustomers) ?> khách hàng có sách yêu thích</p>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>STT</th><th>Khách hàng</th><th>Ngày thay đổi</th><th>Chi tiết</th><th>Thao tác</th></tr>
      </thead>
      <tbody>
        <?php if (!$customers): ?>
          <tr><td colspan="5">Chưa có sản phẩm yêu thích phù hợp.</td></tr>
        <?php else: ?>
          <?php foreach ($customers as $index => $customer): ?>
            <tr>
              <td><?= (($pageNumber - 1) * $pageSize) + $index + 1 ?></td>
              <td>
                <strong><?= e($customer['full_name'] ?: $customer['username']) ?></strong><br>
                <small><?= e($customer['username']) ?> · <?= e($customer['email']) ?></small>
              </td>
              <td>
                <?= e(date('d/m/Y H:i', strtotime($customer['changed_at']))) ?>
              </td>
              <td>
                <a class="button outline small" href="<?= e(admin_url('?' . http_build_query([
                    'page' => 'favorites',
                    'user_id' => (int)$customer['user_id'],
                    'q' => $search,
                    'p' => $pageNumber,
                ]))) ?>">Xem <?= (int)$customer['favorite_count'] ?> sách</a>
              </td>
              <td>
                <form method="post" action="<?= e(admin_url('?action=delete-user-favorites')) ?>" onsubmit="return confirm('Xóa toàn bộ sách yêu thích của khách hàng này?');">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="user_id" value="<?= (int)$customer['user_id'] ?>">
                  <button type="submit" class="button outline small">Xóa tất cả</button>
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
        <a class="button outline small" href="<?= e(admin_url('?' . http_build_query(['page' => 'favorites', 'q' => $search, 'p' => $pageNumber - 1]))) ?>">Trước</a>
      <?php endif; ?>
      <span>Trang <?= $pageNumber ?> / <?= $totalPages ?></span>
      <?php if ($pageNumber < $totalPages): ?>
        <a class="button outline small" href="<?= e(admin_url('?' . http_build_query(['page' => 'favorites', 'q' => $search, 'p' => $pageNumber + 1]))) ?>">Sau</a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
  <?php endif; ?>
</div>