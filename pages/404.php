<?php
/**
 * Template trang 404 - BetaBook
 * View only; the public router owns the shared layout.
 */
?>

<div class="error-page">
    <div class="container">
        <h1><?= http_response_code() === 400 ? '400 - Yêu cầu không hợp lệ' : '404 - Trang không tìm thấy' ?></h1>
        <p><?= e($errorMessage ?? 'Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.') ?></p>
        <a href="<?= url('') ?>" class="btn-home">Về trang chủ</a>
    </div>
</div>