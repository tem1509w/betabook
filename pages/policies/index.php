<section class="page-banner">
  <h1>Chính sách BetaBook</h1>
  <p>Mua sách minh bạch, đổi trả dễ dàng, thông tin của bạn luôn được bảo vệ.</p>
</section>

<main class="container section">
  <div class="policy-layout">
    <aside class="policy-nav">
      <h2>Danh mục chính sách</h2>
      <ul>
        <?php if (!empty($policies)): ?>
          <?php foreach ($policies as $item): ?>
            <li>
              <a href="#policy-<?php echo (int)$item['policy_id']; ?>">
                <?php echo e((string)($item['title'] ?? '')); ?>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li><span>Chưa có chính sách nào.</span></li>
        <?php endif; ?>
      </ul>

      <p class="update">
        Cập nhật lần cuối:
        <?php echo $latestUpdate ? e(date('d/m/Y', strtotime($latestUpdate))) : 'Chưa cập nhật'; ?>
      </p>
    </aside>

    <div class="policy-content">
      <?php if (!empty($policies)): ?>
        <?php foreach ($policies as $item): ?>
          <section class="policy" id="policy-<?php echo (int)$item['policy_id']; ?>">
            <div class="policy-head">
              <div class="policy-icon"><?php echo !empty($item['icon']) ? e((string)$item['icon']) : '📋'; ?></div>
              <div>
                <h2><?php echo e((string)($item['title'] ?? '')); ?></h2>
                <p>Nhóm: <?php echo e((string)($item['page_group'] ?? 'Chính sách')); ?></p>
              </div>
            </div>

            <div class="policy-body" style="line-height: 1.6; white-space: pre-line;">
              <?php echo e((string)($item['content'] ?? '')); ?>
            </div>
          </section>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Hiện tại chưa có chính sách nào được công bố.</p>
      <?php endif; ?>

      <div class="policy-contact">
        <div>
          <h2>Vẫn còn thắc mắc?</h2>
          <p>Hotline 0123 456 789 · mkt.betabook@gmail.com</p>
        </div>
        <a class="button white" href="<?php echo e(url('contact')); ?>">Liên hệ BetaBook</a>
      </div>
    </div>
  </div>
</main>
