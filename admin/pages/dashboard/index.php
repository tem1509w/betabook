<div class="admin-content dashboard-content">
    <div class="dashboard-heading">
        <div>
            <p class="dashboard-eyebrow">BETA BOOK · QUẢN TRỊ</p>
            <h2>Tổng quan</h2>
            <p class="dashboard-subtitle">Tình hình danh mục sách và tồn kho hiện tại.</p>
        </div>
        <a class="dashboard-refresh" href="<?= admin_url('?page=dashboard') ?>">
            <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
            Cập nhật
        </a>
    </div>

    <div class="dashboard-stats">
        <article class="dashboard-stat">
            <div class="dashboard-stat-icon orange"><i class="bi bi-book" aria-hidden="true"></i></div>
            <div><p>Sách đang kinh doanh</p><strong><?= number_format((int)$dashboardStats['active_books']) ?></strong></div>
            <span class="bi bi-arrow-up-right stat-corner" aria-hidden="true"></span>
        </article>
        <article class="dashboard-stat">
            <div class="dashboard-stat-icon blue"><i class="bi bi-tags" aria-hidden="true"></i></div>
            <div><p>Danh mục hoạt động</p><strong><?= number_format((int)$dashboardStats['active_categories']) ?></strong></div>
            <span class="bi bi-arrow-up-right stat-corner" aria-hidden="true"></span>
        </article>
        <article class="dashboard-stat">
            <div class="dashboard-stat-icon green"><i class="bi bi-stars" aria-hidden="true"></i></div>
            <div><p>Sách bán chạy</p><strong><?= number_format((int)$dashboardStats['best_sellers']) ?></strong></div>
            <span class="bi bi-arrow-up-right stat-corner" aria-hidden="true"></span>
        </article>
        <article class="dashboard-stat">
            <div class="dashboard-stat-icon purple"><i class="bi bi-box-seam" aria-hidden="true"></i></div>
            <div><p>Lượt bán ghi nhận</p><strong><?= number_format((int)$dashboardStats['units_sold']) ?></strong></div>
            <span class="bi bi-arrow-up-right stat-corner" aria-hidden="true"></span>
        </article>
    </div>

    <div class="dashboard-grid">
        <section class="dashboard-card recent-books-card">
            <div class="dashboard-card-heading">
                <div>
                    <h3>Sách cập nhật gần đây</h3>
                    <p>Các đầu sách mới nhất trong danh mục.</p>
                </div>
                <a href="<?= admin_url('?page=books') ?>">Quản lý sách <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr><th>Sách</th><th>Danh mục</th><th>Giá bán</th><th>Tồn kho</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBooks as $book): ?>
                            <tr>
                                <td>
                                    <a class="dashboard-book-title" href="<?= admin_url('?page=book-form&id=' . (int)$book['product_id']) ?>">
                                        <?= e($book['title']) ?>
                                    </a>
                                    <small><?= e($book['product_code']) ?></small>
                                </td>
                                <td><?= e($book['category_name'] ?? 'Chưa phân loại') ?></td>
                                <td class="dashboard-price"><?= money($book['price']) ?></td>
                                <td><span class="stock-pill <?= (int)$book['stock_quantity'] <= 5 ? 'low' : 'healthy' ?>"><?= number_format((int)$book['stock_quantity']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$recentBooks): ?>
                            <tr><td colspan="4" class="dashboard-empty">Chưa có sách hoạt động.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dashboard-card category-card">
            <div class="dashboard-card-heading">
                <div>
                    <h3>Sách theo danh mục</h3>
                    <p>Số đầu sách đang hoạt động.</p>
                </div>
                <i class="bi bi-pie-chart dashboard-heading-icon" aria-hidden="true"></i>
            </div>
            <div class="category-bars">
                <?php foreach ($categoryStats as $category): ?>
                    <?php $bookCount = (int)$category['book_count']; ?>
                    <div class="category-bar-row">
                        <div class="category-bar-label"><span><?= e($category['name']) ?></span><b><?= number_format($bookCount) ?></b></div>
                        <div class="category-bar-track"><span style="width: <?= $maxCategoryBooks > 0 ? (int)round($bookCount / $maxCategoryBooks * 100) : 0 ?>%"></span></div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$categoryStats): ?>
                    <p class="dashboard-empty">Chưa có danh mục hoạt động.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="dashboard-card stock-card">
            <div class="dashboard-card-heading">
                <div>
                    <h3>Cần chú ý tồn kho</h3>
                    <p><?= number_format((int)$dashboardStats['low_stock_books']) ?> sách đang có tồn kho không quá 5 cuốn.</p>
                </div>
                <span class="stock-alert-count"><?= number_format((int)$dashboardStats['low_stock_books']) ?></span>
            </div>
            <?php if ($lowStockBooks): ?>
                <ul class="stock-alert-list">
                    <?php foreach ($lowStockBooks as $book): ?>
                        <li>
                            <a href="<?= admin_url('?page=book-form&id=' . (int)$book['product_id']) ?>">
                                <span class="stock-alert-icon"><i class="bi bi-exclamation" aria-hidden="true"></i></span>
                                <span class="stock-alert-title"><?= e($book['title']) ?><small><?= e($book['product_code']) ?></small></span>
                                <strong><?= (int)$book['stock_quantity'] === 0 ? 'Hết hàng' : number_format((int)$book['stock_quantity']) . ' cuốn' ?></strong>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="dashboard-healthy">
                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                    Tồn kho các sách đang hoạt động đều trên 5 cuốn.
                </div>
            <?php endif; ?>
        </section>

        <aside class="dashboard-note">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <div><strong>Quản lý đơn hàng</strong><p>Phần đơn hàng và doanh thu sẽ hiển thị khi module đơn hàng được kết nối. Dashboard hiện chỉ tổng hợp dữ liệu đã có trong danh mục sách.</p></div>
        </aside>
    </div>
</div>
