<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';
require_login();
header('Content-Type: application/json');

$gamesDir = __DIR__ . '/games/';
$input    = json_decode(file_get_contents('php://input'), true);
$gameId   = strtoupper(trim($input['game_id'] ?? ''));
$player   = $_SESSION['player'] ?? null;

if (!$gameId || !$player) {
    echo json_encode(['error' => 'Invalid']);
    exit;
}

$file = $gamesDir . $gameId . '.json';
if (!file_exists($file)) {
    echo json_encode(['error' => 'Not found']);
    exit;
}

$fp = fopen($file, 'r+');
if (flock($fp, LOCK_EX)) {
    $game = json_decode(stream_get_contents($fp), true);

    if ($game['status'] !== 'finished') {
        flock($fp, LOCK_UN); fclose($fp);
        echo json_encode(['error' => 'Game not finished']);
        exit;
    }

    // Mark player wants rematch
    $game['rematch_' . strtolower($player)] = true;

    // If both want rematch, reset the board
    if (!empty($game['rematch_x']) && !empty($game['rematch_o'])) {
        // Swap sides
        $oldX = $game['player_x'];
        $oldO = $game['player_o'];
        $game['player_x']     = $oldO;
        $game['player_o']     = $oldX;
        $game['board']        = array_fill(0, 9, '');
        $game['turn']         = 'X';
        $game['status']       = 'playing';
        $game['winner']       = null;
        $game['winning_line'] = null;
        $game['rematch_x']    = false;
        $game['rematch_o']    = false;
        $game['last_move']    = time();

        // Update session sides
        $_SESSION['player'] = ($player === 'X') ? 'O' : 'X';
    }

    rewind($fp);
    fwrite($fp, json_encode($game));
    ftruncate($fp, ftell($fp));
    flock($fp, LOCK_UN);
}
fclose($fp);

echo json_encode(['success' => true, 'game' => $game]);
