<?php
$promotionUrl = admin_url('?page=promotions');
$isEditingCoupon = $recordType === 'coupon';
$isEditingEvent = $recordType === 'event';
$couponSortDefault = $coupons
    ? max(array_map('intval', array_column($coupons, 'sort_order'))) + 1
    : 1;
$eventSortDefault = $events
    ? max(array_map('intval', array_column($events, 'sort_order'))) + 1
    : 1;
?>
<div class="admin-content">
    <div class="page-header">
        <h2>Quản lý mã giảm &amp; sự kiện</h2>
    </div>

    <?php if ($formErrors): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($formErrors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <section>
        <div class="page-header">
            <h2>Mã giảm giá</h2>
            <a href="<?= e($promotionUrl . '&type=coupon') ?>" class="btn-add">Thêm mã giảm giá</a>
        </div>

        <?php if ($isEditingCoupon): ?>
            <form action="<?= e(admin_url('?action=save-coupon')) ?>" method="POST" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="coupon_id" value="<?= (int)($data['coupon_id'] ?? 0) ?>">
                <div class="form-group">
                    <label for="coupon_code">Mã giảm giá <span class="required">*</span></label>
                    <input id="coupon_code" type="text" name="coupon_code" maxlength="50" value="<?= e((string)($data['coupon_code'] ?? '')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="coupon_description">Mô tả <span class="required">*</span></label>
                    <input id="coupon_description" type="text" name="description" maxlength="255" value="<?= e((string)($data['description'] ?? '')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="expires_at">Ngày hết hạn <span class="required">*</span></label>
                    <input id="expires_at" type="date" name="expires_at" value="<?= e((string)($data['expires_at'] ?? '')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="coupon_image_path">Đường dẫn hình ảnh</label>
                    <input id="coupon_image_path" type="text" name="image_path" maxlength="255" value="<?= e((string)($data['image_path'] ?? '')) ?>" placeholder="voucher.ico">
                </div>
                <div class="form-group">
                    <label for="coupon_sort_order">Thứ tự hiển thị <span class="required">*</span></label>
                    <input id="coupon_sort_order" type="number" name="sort_order" min="0" step="1" value="<?= e((string)($data['sort_order'] ?? $couponSortDefault)) ?>" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" <?= (int)($data['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
                        Đang hoạt động
                    </label>
                </div>
                <button type="submit" class="btn-submit">Lưu mã giảm giá</button>
                <a href="<?= e($promotionUrl) ?>" class="btn-cancel">Hủy</a>
            </form>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã</th>
                    <th>Mô tả</th>
                    <th>Hạn dùng</th>
                    <th>Thứ tự</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($coupons): ?>
                    <?php foreach ($coupons as $coupon): ?>
                        <tr>
                            <td><?= (int)$coupon['coupon_id'] ?></td>
                            <td><?= e($coupon['coupon_code']) ?></td>
                            <td><?= e($coupon['description']) ?></td>
                            <td><?= e(date('d/m/Y', strtotime($coupon['expires_at']))) ?></td>
                            <td><?= (int)$coupon['sort_order'] ?></td>
                            <td><?= (int)$coupon['is_active'] === 1 ? 'Hoạt động' : 'Đã ẩn' ?></td>
                            <td>
                                <a class="action-edit" href="<?= e($promotionUrl . '&type=coupon&id=' . (int)$coupon['coupon_id']) ?>">Sửa</a>
                                <form method="POST" action="<?= e(admin_url('?action=delete-coupon')) ?>" style="display:inline" onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$coupon['coupon_id'] ?>">
                                    <button type="submit" class="action-delete">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">Chưa có mã giảm giá trong hệ thống.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section>
        <div class="page-header">
            <h2>Sự kiện</h2>
            <a href="<?= e($promotionUrl . '&type=event') ?>" class="btn-add">Thêm sự kiện</a>
        </div>

        <?php if ($isEditingEvent): ?>
            <form action="<?= e(admin_url('?action=save-event')) ?>" method="POST" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="event_id" value="<?= (int)($data['event_id'] ?? 0) ?>">
                <div class="form-group">
                    <label for="event_title">Tên sự kiện <span class="required">*</span></label>
                    <input id="event_title" type="text" name="title" maxlength="255" value="<?= e((string)($data['title'] ?? '')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="event_slug">Đường dẫn (slug)</label>
                    <input id="event_slug" type="text" name="event_slug" maxlength="100" value="<?= e((string)($data['event_slug'] ?? '')) ?>" placeholder="Tự tạo từ tên sự kiện nếu để trống">
                </div>
                <div class="form-group">
                    <label for="event_date">Ngày sự kiện <span class="required">*</span></label>
                    <input id="event_date" type="date" name="event_date" value="<?= e((string)($data['event_date'] ?? '')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="event_description">Mô tả <span class="required">*</span></label>
                    <textarea id="event_description" name="description" rows="4" required><?= e((string)($data['description'] ?? '')) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="event_image_path">Đường dẫn hình ảnh</label>
                    <input id="event_image_path" type="text" name="image_path" maxlength="255" value="<?= e((string)($data['image_path'] ?? '')) ?>" placeholder="banner/ten-su-kien.png">
                </div>
                <div class="form-group">
                    <label for="event_sort_order">Thứ tự hiển thị <span class="required">*</span></label>
                    <input id="event_sort_order" type="number" name="sort_order" min="0" step="1" value="<?= e((string)($data['sort_order'] ?? $eventSortDefault)) ?>" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" <?= (int)($data['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
                        Đang hoạt động
                    </label>
                </div>
                <button type="submit" class="btn-submit">Lưu sự kiện</button>
                <a href="<?= e($promotionUrl) ?>" class="btn-cancel">Hủy</a>
            </form>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sự kiện</th>
                    <th>Slug</th>
                    <th>Ngày</th>
                    <th>Thứ tự</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($events): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= (int)$event['event_id'] ?></td>
                            <td><?= e($event['title']) ?></td>
                            <td><?= e($event['event_slug']) ?></td>
                            <td><?= e(date('d/m/Y', strtotime($event['event_date']))) ?></td>
                            <td><?= (int)$event['sort_order'] ?></td>
                            <td><?= (int)$event['is_active'] === 1 ? 'Hoạt động' : 'Đã ẩn' ?></td>
                            <td>
                                <a class="action-edit" href="<?= e($promotionUrl . '&type=event&id=' . (int)$event['event_id']) ?>">Sửa</a>
                                <form method="POST" action="<?= e(admin_url('?action=delete-event')) ?>" style="display:inline" onsubmit="return confirm('Bạn có chắc muốn xóa sự kiện này?')">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$event['event_id'] ?>">
                                    <button type="submit" class="action-delete">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">Chưa có sự kiện trong hệ thống.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>