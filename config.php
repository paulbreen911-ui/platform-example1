<?php
ob_start();

// Database Connection Configuration - Railway.com Setup
$db_name     = getenv('PGDATABASE') ?: 'railway';
$db_host     = getenv('PGHOST')     ?: 'postgres.railway.internal';
$db_password = getenv('PGPASSWORD') ?: 'weClyUXLTNPRvKFQFobjNuWLymxCsRhu';
$db_port     = getenv('PGPORT')     ?: '5432';
$db_user     = getenv('PGUSER')     ?: 'postgres';

try {
    $pdo = new PDO(
        "pgsql:host=$db_host;port=$db_port;dbname=$db_name",
        $db_user,
        $db_password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Use PostgreSQL to store sessions (required for Railway containerized environment)
require_once __DIR__ . '/session_handler.php';
$handler = new PgSessionHandler($pdo);
session_set_save_handler($handler, true);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
