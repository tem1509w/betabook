<?php
/**
 * Trang chi tiết sách - Product Detail
 * Từ product-detail.html gốc BetaBook
 * Biến có sẵn: $product, $relatedProducts
 */

$title = e($product['title']);
$author = e($product['author'] ?? '');
$publisher = e($product['publisher_name'] ?? '');
$category = e($product['category_name'] ?? '');
$price = money($product['price']);
$oldPrice = $product['original_price'] && $product['original_price'] > $product['price'] 
    ? money($product['original_price']) : '';
$description = $product['description'] ?? '';
$additionalDescription = $product['content'] ?? '';
$coverImage = product_image_src($product);
$inStock = ($product['stock_quantity'] ?? 0) > 0;
$productCode = e($product['product_code'] ?? '');
$isbn = e($product['isbn'] ?? '');
$coverType = e($product['cover_type'] ?? '');
$pages = (int)($product['page_count'] ?? 0);
$dimensions = e($product['dimensions'] ?? '');
$publishYear = (int)($product['publication_year'] ?? 0);
$weight = (float)($product['weight'] ?? 0);
$ratingAverage = isset($product['rating_average']) ? (float)$product['rating_average'] : 0;
$ratingCount = (int)($product['rating_count'] ?? 0);
$soldQuantity = (int)($product['sold_quantity'] ?? 0);
$soldLabel = $soldQuantity >= 1000
    ? number_format($soldQuantity / 1000, 1, '.', '') . 'k'
    : number_format($soldQuantity);
?>

<main class="container detail-page">

  <!-- ============ 2. ĐƯỜNG DẪN ============ -->
  <nav class="breadcrumb detail-breadcrumb" aria-label="Điều hướng">
    <a href="<?php echo url(''); ?>">Trang chủ</a><span>›</span>
    <a href="<?php echo url('booklist'); ?>">Tủ sách</a><span>›</span>
    <a href="<?php echo e(url('booklist', ['danhmuc' => $product['category_slug'] ?? 'tatca'])); ?>"><?php echo $category ?: 'Tất cả sách'; ?></a><span>›</span>
    <strong><?php echo $title; ?></strong>
  </nav>

  <!-- ============ 3. PHẦN TRẺN: ẢNH + THÔNG TIN MUA HÀNG ============ -->
  <section class="detail-top">

    <!-- ----- Cột trái: thư viện ảnh ----- -->
    <div class="gallery">
      <!-- Radio ẩn: chọn ảnh nào thì ảnh lớn đổi theo -->
      <input type="radio" name="anh" id="g1" checked>
      <input type="radio" name="anh" id="g2">
      <input type="radio" name="anh" id="g3">
      <input type="radio" name="anh" id="g4">

      <div class="gallery-main">
        <?php if ($oldPrice !== ''): ?>
          <?php
          $discountPercent = (int)round(
              (($product['original_price'] - $product['price']) / $product['original_price']) * 100
          );
          ?>
          <span class="sale-tag">-<?php echo $discountPercent; ?>%</span>
        <?php elseif ($product['is_new_release']): ?>
          <span class="sale-tag">Mới</span>
        <?php elseif ($product['is_recommended']): ?>
          <span class="sale-tag">Khuyến nghị</span>
        <?php endif; ?>
        <div class="img-box g-img g1">
          <img src="<?php echo e($coverImage); ?>" alt="<?php echo $title; ?>">
        </div>
        <div class="img-box g-img g2">Ảnh 2</div>
        <div class="img-box g-img g3">Ảnh 3</div>
        <div class="img-box g-img g4">Ảnh 4</div>
      </div>

      <div class="thumbs">
        <label for="g1" class="img-box">
          <img src="<?php echo e($coverImage); ?>" alt="Ảnh 1">
        </label>
        <label for="g2" class="img-box">Ảnh 2</label>
        <label for="g3" class="img-box">Ảnh 3</label>
        <label for="g4" class="img-box">Ảnh 4</label>
      </div>
    </div>

    <!-- ----- Cột phải: thông tin ----- -->
    <div class="info">
      <h1><?php echo $title; ?></h1>

      <!-- Đánh giá (dùng lại .rating và .stars của style.css) -->
      <?php if ($ratingCount > 0): ?>
        <div class="rating" title="<?php echo e(number_format($ratingAverage, 1)); ?> trên 5 sao">
          <span class="stars" style="--rating: <?php echo e(number_format($ratingAverage, 1)); ?>"></span>
          <span class="rating-count">(<?php echo number_format($ratingCount); ?> đánh giá)</span>
          <span class="divider">|</span>
          <span class="rating-count">Đã bán <?php echo e($soldLabel); ?></span>
        </div>
      <?php endif; ?>

      <!-- Giá -->
      <div class="price-box">
        <span class="price big"><?php echo $price; ?></span>
        <?php if ($oldPrice): ?>
          <s class="old-price"><?php echo $oldPrice; ?></s>
        <?php endif; ?>
      </div>

      <!-- Thông tin nhanh -->
      <ul class="meta">
        <li><span>Tác giả:</span> <b><?php echo $author ?: 'Chưa cập nhật'; ?></b></li>
        <li><span>Nhà xuất bản:</span> <b><?php echo $publisher ?: 'Chưa cập nhật'; ?></b></li>
        <li><span>Thể loại:</span> <b><?php echo $category ?: 'Chưa cập nhật'; ?></b></li>
        <li><span>Tình trạng:</span> <b class="<?php echo $inStock ? 'in-stock' : 'out-of-stock'; ?>">
          <?php echo $inStock ? 'Còn hàng' : 'Hết hàng'; ?>
        </b></li>
      </ul>

      <!-- Mã giảm -->
      <div class="vouchers">
        <span class="voucher">Mã giảm 10% cho đơn từ 200k</span>
        <span class="voucher">Freeship đơn từ 300k</span>
      </div>

      <!-- Số lượng + nút mua -->
      <form class="buy" action="<?php echo url('action.php'); ?>" method="post">
        <input type="hidden" name="action" value="add_to_cart">
        <input type="hidden" name="product_id" value="<?php echo (int)$product['product_id']; ?>">
        <div class="field-row">
          <span class="field-label">Số lượng</span>
          <input type="number" class="qty" name="quantity" value="1" min="1" max="<?php echo (int)$product['stock_quantity']; ?>" aria-label="Số lượng">
          <span class="stock">Còn <?php echo number_format((int)$product['stock_quantity']); ?> cuốn</span>
        </div>

        <div class="buy-actions">
          <button type="submit" class="button outline">Thêm vào giỏ</button>
          <button type="submit" class="button" data-go-cart="true">Mua ngay</button>
          <button type="button" class="wish" title="Yêu thích">♥</button>
        </div>
      </form>

      <!-- Cam kết -->
      <ul class="perks">
        <li><span>🚚</span> Giao nhanh 1-5 ngày</li>
        <li><span>↩️</span> Đổi trả trong 7 ngày</li>
        <li><span>✔️</span> Sách chính hãng</li>
        <li><span>💳</span> Thanh toán linh hoạt</li>
      </ul>
    </div>

  </section>

  <script>
  document.querySelectorAll('form.buy').forEach((form) => {
    let goToCart = false;
    const message = document.createElement('p');
    message.setAttribute('aria-live', 'polite');
    form.appendChild(message);

    form.addEventListener('click', (event) => {
      goToCart = Boolean(event.target.closest('[data-go-cart]'));
    });

    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      message.textContent = 'Đang thêm sách vào giỏ...';
      try {
        const response = await fetch(form.action, { method: 'POST', body: new FormData(form) });
        const result = await response.json();
        message.textContent = result.message || 'Không thể cập nhật giỏ hàng.';
        if (response.ok && result.status === 'success' && goToCart) {
          window.location.href = '<?php echo url('cart'); ?>';
        }
      } catch (error) {
        message.textContent = 'Không thể kết nối máy chủ. Vui lòng thử lại.';
      }
      goToCart = false;
    });
  });
  </script>

  <!-- ============ 4. TAB: MÔ TẢ / THÔNG TIN / ĐÁNH GIÁ ============ -->
  <section class="detail-tabs">
    <input class="tab-input" type="radio" name="tab" id="tab-mota" checked>
    <input class="tab-input" type="radio" name="tab" id="tab-thongtin">
    <input class="tab-input" type="radio" name="tab" id="tab-danhgia">

    <div class="tab-bar">
      <label for="tab-mota">Mô tả sản phẩm</label>
      <label for="tab-thongtin">Thông tin chi tiết</label>
      <label for="tab-danhgia">Đánh giá<?php if ($ratingCount > 0): ?> (<?php echo number_format($ratingCount); ?>)<?php endif; ?></label>
    </div>

    <!-- Tab 1: mô tả -->
    <div class="tab-panel panel-mota">
      <h2>Mô tả sản phẩm</h2>
      <p><?php echo nl2br(e($description)); ?></p>
      <?php if ($additionalDescription !== ''): ?>
        <p><?php echo nl2br(e($additionalDescription)); ?></p>
      <?php else: ?>
        <p>Sách được đóng gói cẩn thận, giao hàng toàn quốc. Hỗ trợ đổi trả trong 7 ngày nếu sản phẩm lỗi.</p>
      <?php endif; ?>
    </div>

    <!-- Tab 2: thông tin chi tiết -->
    <div class="tab-panel panel-thongtin">
      <table class="spec">
        <tr><th>Mã sản phẩm</th><td><?php echo $productCode ?: 'N/A'; ?></td></tr>
        <tr><th>ISBN</th><td><?php echo $isbn ?: 'N/A'; ?></td></tr>
        <tr><th>Tác giả</th><td><?php echo $author ?: 'N/A'; ?></td></tr>
        <tr><th>Nhà xuất bản</th><td><?php echo $publisher ?: 'N/A'; ?></td></tr>
        <tr><th>Năm xuất bản</th><td><?php echo $publishYear ? $publishYear : 'N/A'; ?></td></tr>
        <tr><th>Số trang</th><td><?php echo $pages ? $pages : 'N/A'; ?></td></tr>
        <tr><th>Kích thước</th><td><?php echo $dimensions ?: 'N/A'; ?></td></tr>
        <tr><th>Hình thức</th><td><?php echo $coverType ?: 'N/A'; ?></td></tr>
        <tr><th>Trọng lượng</th><td><?php echo $weight ? $weight . ' kg' : 'N/A'; ?></td></tr>
      </table>
    </div>

    <!-- Tab 3: đánh giá -->
    <div class="tab-panel panel-danhgia">

      <!-- Tổng quan sao -->
      <div class="review-summary">
        <div class="score">
          <b><?php echo $ratingCount > 0 ? e(number_format($ratingAverage, 1)) : '0.0'; ?></b>
          <span class="stars" style="--rating: <?php echo e(number_format($ratingAverage, 1)); ?>"></span>
          <small><?php echo number_format($ratingCount); ?> đánh giá</small>
        </div>
        <div class="bars">
          <?php
          $ratingPercentages = [];
          foreach ($ratingBreakdown as $ratingRow) {
              $ratingPercentages[(int)$ratingRow['star_rating']] = (int)$ratingRow['rating_percentage'];
          }
          ?>
          <?php for ($stars = 5; $stars >= 1; $stars--): ?>
            <?php $percentage = $ratingPercentages[$stars] ?? 0; ?>
            <div class="bar">
              <span><?php echo $stars; ?> sao</span>
              <i style="--w: <?php echo $percentage; ?>%"></i>
              <span><?php echo $percentage; ?>%</span>
            </div>
          <?php endfor; ?>
        </div>
        <a class="button" href="#tab-danhgia">Xem đánh giá</a>
      </div>

      <?php if (!empty($productReviews)): ?>
        <?php foreach ($productReviews as $review): ?>
          <div class="review-item">
            <div class="img-box avatar">
              <?php if (!empty($review['avatar_image'])): ?>
                <img src="<?php echo e(asset('images/' . $review['avatar_image'])); ?>" alt="">
              <?php endif; ?>
            </div>
            <div>
              <b><?php echo e($review['reviewer_name']); ?></b>
              <div class="rating">
                <span class="stars" style="--rating: <?php echo (int)$review['rating']; ?>"></span>
                <small><?php echo e(date('d/m/Y', strtotime($review['reviewed_at']))); ?></small>
              </div>
              <p><?php echo e($review['review_text']); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="no-reviews">Sản phẩm chưa có đánh giá.</p>
      <?php endif; ?>
    </div>
  </section>

</main>

<!-- ============ 5. SẢN PHẨM LIÊN QUAN (nền xám) ============ -->
<section class="gray-bg">
  <div class="container section">
    <h2 class="section-title">Sách đề cử</h2>

    <div class="product-list">
      <?php if (!empty($relatedProducts)): ?>
        <?php foreach ($relatedProducts as $related): ?>
          <?php $product = $related; ?>
          <?php require __DIR__ . '/../../layouts/public/product-card.php'; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Chưa có sách liên quan.</p>
      <?php endif; ?>
    </div>
  </div>
</section>