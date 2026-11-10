<?php
/**
 * Database Helper - Singleton pattern để lấy instance Database
 * Tuân theo MiniShop Basic PHP
 */

require_once __DIR__ . '/../classes/Database.php';

function db(): Database {
    static $instance = null;
    if ($instance === null) {
        $instance = new Database();
    }
    return $instance;
}