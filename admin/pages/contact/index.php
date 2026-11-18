<div class="admin-content">
  <div class="page-header">
    <h2>Tin nhắn liên hệ</h2>
  </div>

  <?php if (is_string($flashMessage) && $flashMessage !== ''): ?>
    <div class="alert success"><?= e($flashMessage) ?></div>
  <?php endif; ?>

  <form method="get" action="<?= e(admin_url()) ?>" class="user-filter">
    <input type="hidden" name="page" value="contact">
    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Tìm tên, email, số điện thoại, chủ đề, nội dung">
    <select name="status" aria-label="Lọc trạng thái tin nhắn">
      <option value="">Tất cả tin nhắn</option>
      <option value="unread" <?= $readFilter === 'unread' ? 'selected' : '' ?>>Chưa đọc</option>
      <option value="read" <?= $readFilter === 'read' ? 'selected' : '' ?>>Đã đọc</option>
    </select>
    <button type="submit" class="button">Lọc</button>
  </form>

  <p>Tìm thấy <?= number_format($totalMessages) ?> tin nhắn</p>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>Ngày gửi</th><th>Người gửi</th><th>Chủ đề</th><th>Trạng thái</th><th>Nội dung</th><th>Thao tác</th></tr>
      </thead>
      <tbody>
        <?php if (!$messages): ?>
          <tr><td colspan="6">Chưa có tin nhắn liên hệ phù hợp.</td></tr>
        <?php else: ?>
          <?php foreach ($messages as $message): ?>
            <tr>
              <td><?= e(date('d/m/Y H:i', strtotime($message['created_at']))) ?></td>
              <td>
                <strong><?= e($message['name']) ?></strong><br>
                <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a><br>
                <?= e($message['phone'] ?: 'Không cung cấp số điện thoại') ?>
              </td>
              <td><?= e($message['subject'] ?: 'Không có chủ đề') ?></td>
              <td><?= (int)$message['is_read'] === 1 ? 'Đã đọc' : 'Chưa đọc' ?></td>
              <td>
                <details>
                  <summary>Xem nội dung</summary>
                  <p><?= nl2br(e($message['message'])) ?></p>
                  <?php if ((int)$message['is_read'] !== 1): ?>
                    <form method="post" action="<?= e(admin_url('?action=mark-contact-read')) ?>">
                      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="message_id" value="<?= (int)$message['message_id'] ?>">
                      <input type="hidden" name="q" value="<?= e($search) ?>">
                      <input type="hidden" name="status" value="<?= e($readFilter) ?>">
                      <input type="hidden" name="p" value="<?= (int)$pageNumber ?>">
                      <button type="submit" class="button">Đánh dấu đã đọc</button>
                    </form>
                  <?php endif; ?>
                </details>
              </td>
              <td>
                <form method="post" action="<?= e(admin_url('?action=delete-contact-message')) ?>"
                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin nhắn này?');">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="message_id" value="<?= (int)$message['message_id'] ?>">
                  <input type="hidden" name="q" value="<?= e($search) ?>">
                  <input type="hidden" name="status" value="<?= e($readFilter) ?>">
                  <input type="hidden" name="p" value="<?= (int)$pageNumber ?>">
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
    <nav class="user-pagination" aria-label="Phân trang tin nhắn liên hệ">
      <?php if ($pageNumber > 1): ?>
        <a class="button outline small" href="<?= e(admin_url('?' . http_build_query(['page' => 'contact', 'q' => $search, 'status' => $readFilter, 'p' => $pageNumber - 1]))) ?>">Trước</a>
      <?php endif; ?>
      <span>Trang <?= $pageNumber ?> / <?= $totalPages ?></span>
      <?php if ($pageNumber < $totalPages): ?>
        <a class="button outline small" href="<?= e(admin_url('?' . http_build_query(['page' => 'contact', 'q' => $search, 'status' => $readFilter, 'p' => $pageNumber + 1]))) ?>">Sau</a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
</div>