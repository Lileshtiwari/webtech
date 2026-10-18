<?php
/**
 * WebTech Lab Hub — Full-Stack Web Applications
 * Experiment 6: Food Delivery System Configuration
 */

// Allow local developer override
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

defined('DB_HOST') or define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
defined('DB_PORT') or define('DB_PORT', getenv('DB_PORT') ?: '3306');
defined('DB_NAME') or define('DB_NAME', getenv('DB_NAME_EXP6') ?: 'food_delivery');
defined('DB_USER') or define('DB_USER', getenv('DB_USER') ?: 'root');
defined('DB_PASS') or define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'root');
