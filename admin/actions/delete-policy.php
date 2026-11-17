<?php
$policyId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;

if ($policyId > 0) {
    $db->execute('DELETE FROM policies WHERE policy_id = ?', [$policyId]);
}

redirect(admin_url('?page=policies'));