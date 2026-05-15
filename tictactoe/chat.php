<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';
require_login();

$user = get_user_by_id($pdo, $_SESSION['user_id']);
if (!$user) { header('Location: /login.php'); exit; }
header('Content-Type: application/json');

$gamesDir = __DIR__ . '/games/';
$input    = json_decode(file_get_contents('php://input'), true);
$gameId   = strtoupper(trim($input['game_id'] ?? ''));
$msg      = trim($input['message'] ?? '');

// Use the authenticated user's display name
$name = $user['display_name'] ?: $user['username'];

if (!$gameId || !$msg) {
    echo json_encode(['error' => 'Invalid']);
    exit;
}

$msg = substr(htmlspecialchars($msg, ENT_QUOTES), 0, 120);

$file = $gamesDir . $gameId . '.json';
if (!file_exists($file)) {
    echo json_encode(['error' => 'Not found']);
    exit;
}

$fp = fopen($file, 'r+');
if (flock($fp, LOCK_EX)) {
    $game = json_decode(stream_get_contents($fp), true);
    $game['chat'][] = [
        'name' => $name,
        'msg'  => $msg,
        'time' => date('H:i'),
    ];
    // Keep last 50 messages
    if (count($game['chat']) > 50) {
        $game['chat'] = array_slice($game['chat'], -50);
    }
    rewind($fp);
    fwrite($fp, json_encode($game));
    ftruncate($fp, ftell($fp));
    flock($fp, LOCK_UN);
}
fclose($fp);

echo json_encode(['success' => true]);
