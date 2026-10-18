<?php
/**
 * WebTech Lab Hub — Full-Stack Web Applications
 * Experiment 6: PDO Connection Handler & Session Helpers
 */

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getFoodDbConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 5
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log('Experiment 6 DB Connection Error: ' . $e->getMessage());
        return null;
    }
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

function isAdmin() {
    return !empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireAuth() {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php';
        header('Location: ' . (file_exists('login.php') ? 'login.php' : '../login.php'));
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['flash_error'] = 'Administrative authorization required to access this resource.';
        header('Location: ' . (file_exists('login.php') ? 'login.php' : '../login.php'));
        exit;
    }
}

function getCartCount() {
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }
    return array_sum($_SESSION['cart']);
}
