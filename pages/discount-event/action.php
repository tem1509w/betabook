<?php
$coupons = $db->fetchAll(
    'SELECT coupon_code, description, expires_at, image_path
     FROM home_coupons
     WHERE is_active = 1
     ORDER BY sort_order ASC, coupon_id ASC'
);

$events = $db->fetchAll(
    'SELECT event_date, title, description, image_path
     FROM home_events
     WHERE is_active = 1
     ORDER BY sort_order ASC, event_id ASC'
);

$pageTitle = 'Mã giảm & Sự kiện';
$bodyClass = 'discount-event-page';
$extraCss = [asset('css/discount-event.css')];