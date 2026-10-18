<?php
/**
 * WebTech Lab Hub — Full-Stack Web Applications
 * Experiment 5: PDO Database Connection Handler
 */

require_once __DIR__ . '/config.php';

function getEventDbConnection() {
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
        // Safe logging without exposing credentials to public view
        error_log('Experiment 5 DB Connection Error: ' . $e->getMessage());
        return null;
    }
}
