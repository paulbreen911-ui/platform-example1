<?php
// Database Connection Configuration - Railway.com Setup
$db_name = getenv('PGDATABASE') ?: 'railway';
$db_host = getenv('PGHOST') ?: 'postgres.railway.internal';
$db_password = getenv('PGPASSWORD') ?: 'weClyUXLTNPRvKFQFobjNuWLymxCsRhu';
$db_port = getenv('PGPORT') ?: '5432';
$db_user = getenv('PGUSER') ?: 'postgres';
$db_charset = 'utf8';

try {
    $pdo = new PDO(
        "pgsql:host=$db_host;port=$db_port;dbname=$db_name",
        $db_user,
        $db_password,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Start session
session_start();
