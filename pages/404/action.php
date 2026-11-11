<?php
http_response_code(404);
$pageTitle = 'Không tìm thấy trang';
$bodyClass = 'not-found-page';
$errorMessage = 'Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.';
$file = __DIR__ . '/Index.php';