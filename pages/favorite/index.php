<main class="container fav-page">
  <h1 class="page-title">Sản phẩm yêu thích</h1>
  <p class="page-subtitle">Những cuốn sách bạn đã lưu lại để mua sau. Hiện có <strong><?php echo count($favoriteItems); ?></strong> sản phẩm.</p>

  <?php if ($favoriteError !== null): ?>
    <p class="form-msg error" role="alert"><?php echo e($favoriteError); ?></p>
  <?php endif; ?>

  <?php if (!$favoriteItems): ?>
    <section class="fav-box fav-empty">
      <div class="fav-empty-icon">♡</div>
      <h3>Chưa có sản phẩm yêu thích</h3>
      <p>Danh sách của bạn đang trống. Hãy chọn những cuốn sách bạn yêu thích để lưu lại nhé!</p>
      <a class="button" href="<?php echo e(url('booklist')); ?>">← Khám phá tủ sách</a>
    </section>
  <?php else: ?>
    <form action="<?php echo e(url('favorite')); ?>" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
      <section class="fav-box">
        <h2>Danh sách yêu thích (<?php echo count($favoriteItems); ?>)</h2>
        <div class="fav-head">
          <span>Sản phẩm</span><span>Đơn giá</span><span>Tình trạng</span><span></span><span></span>
        </div>

        <?php foreach ($favoriteItems as $item): ?>
          <?php $inStock = (int)$item['stock_quantity'] > 0; ?>
          <div class="fav-item">
            <div class="fav-item-info">
              <a class="img-box fav-item-img" href="<?php echo e(url('product-detail', ['id' => (int)$item['product_id']])); ?>">
                <img src="<?php echo e(product_image_src($item)); ?>" alt="<?php echo e($item['title']); ?>">
              </a>
              <div>
                <h3><a href="<?php echo e(url('product-detail', ['id' => (int)$item['product_id']])); ?>"><?php echo e($item['title']); ?></a></h3>
                <p>Tác giả: <?php echo e((string)($item['author'] ?? 'Chưa cập nhật')); ?></p>
              </div>
            </div>
            <span class="price"><?php echo money($item['price']); ?></span>
            <?php if ($inStock): ?>
              <span class="stock in">Còn hàng</span>
              <button class="btn-add-cart" type="submit" name="add_cart_product" value="<?php echo (int)$item['product_id']; ?>">Thêm giỏ</button>
            <?php else: ?>
              <span class="stock out">Hết hàng</span>
              <button class="btn-add-cart disabled" type="button" disabled>Hết hàng</button>
            <?php endif; ?>
            <button class="fav-remove" type="submit" name="remove_product" value="<?php echo (int)$item['product_id']; ?>"
                    onclick="return confirm('Xóa cuốn sách này khỏi danh sách yêu thích?');" title="Bỏ yêu thích">✕ Xóa</button>
          </div>
        <?php endforeach; ?>

        <div class="fav-actions">
          <a class="button outline" href="<?php echo e(url('booklist')); ?>">← Tiếp tục xem sách</a>
          <div>
            <button class="button outline" type="submit" name="favorite_action" value="clear"
                    onclick="return confirm('Xóa toàn bộ danh sách yêu thích?');">Xóa tất cả</button>
            <button class="button" type="submit" name="favorite_action" value="add_all_cart">Thêm tất cả vào giỏ</button>
          </div>
        </div>
      </section>
    </form>
  <?php endif; ?>
</main>