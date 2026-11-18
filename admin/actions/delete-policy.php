<?php
if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Phiên làm việc không hợp lệ. Vui lòng tải lại trang và thử lại.');
}

$policyId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($policyId === false || $policyId === null || $policyId < 1) {
    http_response_code(400);
    exit('Mã chính sách không hợp lệ.');
}

$result = $db->execute('DELETE FROM policies WHERE policy_id = ?', [$policyId]);
if ($result === false) {
    http_response_code(500);
    exit('Không thể xóa chính sách. Vui lòng thử lại.');
}

redirect(admin_url('?page=policies'));
