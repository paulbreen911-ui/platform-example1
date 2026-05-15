<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';
require_login();
header('Content-Type: application/json');

$gamesDir = __DIR__ . '/games/';
$gameId   = strtoupper(trim($_GET['game_id'] ?? ''));

if (!$gameId) {
    echo json_encode(['error' => 'No game ID']);
    exit;
}

$file = $gamesDir . $gameId . '.json';
if (!file_exists($file)) {
    echo json_encode(['error' => 'Game not found']);
    exit;
}

$game = json_decode(file_get_contents($file), true);
echo json_encode($game);
