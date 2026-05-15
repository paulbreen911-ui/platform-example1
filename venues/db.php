<?php

$host = getenv('PGHOST') ?: 'postgres.railway.internal';
$port = getenv('PGPORT') ?: '5432';
$db   = getenv('PGDATABASE') ?: 'railway';
$user = getenv('PGUSER') ?: 'postgres';
$pass = getenv('PGPASSWORD') ?: 'weClyUXLTNPRvKFQFobjNuWLymxCsRhu';

if (!$host || !$db || !$user) {
    die("Missing DB environment variables. Check Railway PG settings.");
}

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die("DB Connection failed: " . $e->getMessage());
}