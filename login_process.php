<?php
require_once 'config.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

csrf_verify();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$ip       = client_ip();

if (empty($username) || empty($password)) {
    header('Location: /login.php?error=required');
    exit;
}

if (!rate_limit_check($pdo, "login:{$ip}", 'login', 10, 300)) {
    header('Location: /login.php?error=ratelimit');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        rate_limit_reset($pdo, "login:{$ip}", 'login');
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        // Regenerate session ID on login
        session_regenerate_id(true);
        header('Location: /myprofile.php');
        exit;
    }

    header('Location: /login.php?error=invalid');
    exit;

} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
