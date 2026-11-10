<?php
/**
 * App Configuration
 */
$baseUrl = getenv('BETABOOK_BASE_URL') ?: 'http://localhost/betabook';
$baseUrl = rtrim($baseUrl, '/');

return [
    'base_url' => $baseUrl,
    'admin_base_url' => $baseUrl . '/admin',
    'app_name' => 'BetaBook',
];