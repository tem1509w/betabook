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
        <p class="product-author"><?= e($product['author'] ?? '') ?: '&nbsp;' ?></p>
        <p class="price">
            <span class="current-price"><?= $productPrice ?></span>
            <?php if ($productOriginalPrice !== ''): ?>
                <s class="old-price"><?= $productOriginalPrice ?></s>
            <?php endif; ?>
        </p>
    </div>
</a>
