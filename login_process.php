<?php
ob_start();
require_once 'config.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header('Location: /login.php?error=required');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // DEBUG — remove after fixing
    echo '<pre>';
    echo 'Username entered: ' . htmlspecialchars($username) . "\n";
    echo 'User found in DB: ' . ($user ? 'YES' : 'NO') . "\n";
    if ($user) {
        echo 'Hash in DB: ' . $user['password'] . "\n";
        echo 'password_verify result: ' . (password_verify($password, $user['password']) ? 'TRUE' : 'FALSE') . "\n";
    }
    echo '</pre>';
    die('--- DEBUG END ---');
    // END DEBUG

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: /myprofile.php');
        exit;
    } else {
        header('Location: /login.php?error=invalid');
        exit;
    }
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
