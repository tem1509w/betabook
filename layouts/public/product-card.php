<?php
$productTitle = e($product['title']);
$productUrl = url('product-detail', ['id' => (int)$product['product_id']]);
$productImage = e(product_image_src($product));
$productPrice = money($product['price']);
$productOriginalPrice = $product['original_price'] && $product['original_price'] > $product['price']
    ? money($product['original_price'])
    : '';
?>
<a class="product-card-link" href="<?= e($productUrl) ?>">
    <div class="product">
        <div class="img-box">
            <img src="<?= $productImage ?>" alt="<?= $productTitle ?>">
        </div>
        <h3><?= $productTitle ?></h3>
        <?php if (!empty($product['rating_count'])): ?>
            <div class="rating" title="<?= e($product['rating_average']) ?> trên 5 sao">
                <span class="stars" style="--rating: <?= e($product['rating_average']) ?>"></span>
                <span class="rating-count">(<?= (int)$product['rating_count'] ?>)</span>
            </div>
        <?php endif; ?>
        <p class="price">
            <span class="current-price"><?= $productPrice ?></span>
            <?php if ($productOriginalPrice !== ''): ?>
                <s class="old-price"><?= $productOriginalPrice ?></s>
            <?php endif; ?>
        </p>
    </div>
</a>
