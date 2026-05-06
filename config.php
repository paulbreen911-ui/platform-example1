<?php
// Database Connection Configuration
$db_host = 'localhost';
$db_port = '5432';
$db_name = 'my_website_db';
$db_user = 'postgres';
$db_password = 'your_password'; // Change this to your PostgreSQL password
$db_charset = 'utf8';

try {
    $pdo = new PDO(
        "pgsql:host=$db_host;port=$db_port;dbname=$db_name",
        $db_user,
        $db_password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Start session
session_start();
?>
