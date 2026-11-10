<?php
/**
 * App Configuration
 */
$documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
$projectRoot = realpath(dirname(__DIR__));
$defaultBaseUrl = 'http://localhost/betabook';

if ($documentRoot !== false && $projectRoot !== false) {
    $documentRootPrefix = rtrim($documentRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (strncasecmp($projectRoot, $documentRootPrefix, strlen($documentRootPrefix)) === 0) {
        $relativePath = substr($projectRoot, strlen($documentRootPrefix));
        $defaultBaseUrl = 'http://localhost/' . str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
    } elseif (strcasecmp($projectRoot, $documentRoot) === 0) {
        $defaultBaseUrl = 'http://localhost';
    }
}

$baseUrl = getenv('BETABOOK_BASE_URL') ?: $defaultBaseUrl;
$baseUrl = rtrim($baseUrl, '/');

return [
    'base_url' => $baseUrl,
    'admin_base_url' => $baseUrl . '/admin',
    'app_name' => 'BetaBook',
];