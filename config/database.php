<?php
/**
 * Database Configuration
 * Local XAMPP defaults. Environment variables can override these values.
 */
return [
    'host' => getenv('BETABOOK_DB_HOST') ?: '127.0.0.1',
    'port' => (int)(getenv('BETABOOK_DB_PORT') ?: 3306),
    'dbname' => getenv('BETABOOK_DB_NAME') ?: 'betabook',
    'username' => getenv('BETABOOK_DB_USER') ?: 'root',
    'password' => getenv('BETABOOK_DB_PASSWORD') !== false ? getenv('BETABOOK_DB_PASSWORD') : '',
    'charset' => getenv('BETABOOK_DB_CHARSET') ?: 'utf8mb4',
];