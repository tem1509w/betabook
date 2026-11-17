<main class="container section">
    <h1 class="section-title">Giỏ hàng</h1>

    <?php if (!$cartItems): ?>
        <p>Giỏ hàng của bạn đang trống.</p>
        <a class="button" href="<?= url('') ?>">Tiếp tục mua sắm</a>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sách</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?= e($item['title']) ?></td>
                            <td><?= money($item['price']) ?></td>
                            <td><?= (int)$item['cart_quantity'] ?></td>
                            <td><?= money($item['line_total']) ?></td>
                            <td>
                                <form method="POST" action="<?= url('cart') ?>">
                                    <input type="hidden" name="remove_product" value="<?= (int)$item['product_id'] ?>">
                                    <button type="submit" class="action-delete">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Tổng cộng</th>
                        <th><?= money($cartTotal) ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <a class="button" href="<?= url('') ?>">Tiếp tục mua sắm</a>
    <?php endif; ?>
</main>
