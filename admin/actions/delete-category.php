<?php
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Mã danh mục không hợp lệ.');
}

$db->execute('DELETE FROM categories WHERE category_id = ?', [$id]);
redirect(admin_url('?page=categories'));
