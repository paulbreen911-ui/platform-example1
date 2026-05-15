<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';
require_login();
header('Content-Type: application/json');

$gamesDir = __DIR__ . '/games/';

$input  = json_decode(file_get_contents('php://input'), true);
$gameId = strtoupper(trim($input['game_id'] ?? $_GET['game_id'] ?? ''));
$cell   = $input['cell'] ?? -1;
$player = $_SESSION['player'] ?? null;

if (!$gameId || !$player || $cell < 0 || $cell > 8) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$file = $gamesDir . $gameId . '.json';
if (!file_exists($file)) {
    echo json_encode(['error' => 'Game not found']);
    exit;
}

$fp = fopen($file, 'r+');
if (!flock($fp, LOCK_EX)) {
    echo json_encode(['error' => 'Lock failed']);
    exit;
}

$game = json_decode(stream_get_contents($fp), true);

// Validate move
if ($game['status'] !== 'playing') {
    flock($fp, LOCK_UN); fclose($fp);
    echo json_encode(['error' => 'Game not active', 'game' => $game]);
    exit;
}
if ($game['turn'] !== $player) {
    flock($fp, LOCK_UN); fclose($fp);
    echo json_encode(['error' => 'Not your turn', 'game' => $game]);
    exit;
}
if ($game['board'][$cell] !== '') {
    flock($fp, LOCK_UN); fclose($fp);
    echo json_encode(['error' => 'Cell taken', 'game' => $game]);
    exit;
}

// Apply move
$game['board'][$cell] = $player;
$game['last_move']    = time();

// Check win
$wins = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];
$winner = null;
foreach ($wins as $line) {
    if ($game['board'][$line[0]] !== '' &&
        $game['board'][$line[0]] === $game['board'][$line[1]] &&
        $game['board'][$line[1]] === $game['board'][$line[2]]) {
        $winner = $game['board'][$line[0]];
        $game['winning_line'] = $line;
        break;
    }
}

if ($winner) {
    $game['winner'] = $winner;
    $game['status'] = 'finished';
} elseif (!in_array('', $game['board'])) {
    $game['status'] = 'finished';
    $game['winner'] = 'draw';
} else {
    $game['turn'] = ($player === 'X') ? 'O' : 'X';
}

rewind($fp);
fwrite($fp, json_encode($game));
ftruncate($fp, ftell($fp));
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['success' => true, 'game' => $game]);
