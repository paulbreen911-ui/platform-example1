<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';
require_login();

$gamesDir = __DIR__ . '/games/';
if (!is_dir($gamesDir)) mkdir($gamesDir, 0777, true);

$playerName = trim($_POST['player_name'] ?? '');
$gameName   = trim($_POST['game_name'] ?? '');

if (!$playerName || !$gameName) {
    header('Location: /tictactoe/dashboard.php');
    exit;
}

// Generate unique 6-char game ID
do {
    $id = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
} while (file_exists($gamesDir . $id . '.json'));

$game = [
    'id'        => $id,
    'name'      => $gameName,
    'player_x'  => $playerName,
    'player_o'  => null,
    'board'     => array_fill(0, 9, ''),
    'turn'      => 'X',
    'status'    => 'waiting',   // waiting | playing | finished
    'winner'    => null,
    'created'   => time(),
    'last_move' => time(),
    'chat'      => [],
];

file_put_contents($gamesDir . $id . '.json', json_encode($game));

// Store session info
$_SESSION['game_id']   = $id;
$_SESSION['player']    = 'X';
$_SESSION['name']      = $playerName;

header('Location: game.php?id=' . $id);
exit;
