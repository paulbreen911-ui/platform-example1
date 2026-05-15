<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';
require_login();

$gamesDir = __DIR__ . '/games/';

$playerName = trim($_POST['player_name'] ?? '');
$gameId     = strtoupper(trim($_POST['game_id'] ?? ''));

if (!$playerName || !$gameId) {
    header('Location: /tictactoe/dashboard.php');
    exit;
}

$file = $gamesDir . $gameId . '.json';
if (!file_exists($file)) {
    header('Location: /tictactoe/dashboard.php?error=notfound');
    exit;
}

// Use file locking to safely read/write
$fp = fopen($file, 'r+');
if (flock($fp, LOCK_EX)) {
    $game = json_decode(stream_get_contents($fp), true);

    if ($game['status'] !== 'waiting') {
        flock($fp, LOCK_UN);
        fclose($fp);
        header('Location: game.php?id=' . $gameId . '&spectate=1');
        exit;
    }

    // Assign as player O
    $game['player_o'] = $playerName;
    $game['status']   = 'playing';

    rewind($fp);
    fwrite($fp, json_encode($game));
    ftruncate($fp, ftell($fp));
    flock($fp, LOCK_UN);
}
fclose($fp);

$_SESSION['game_id'] = $gameId;
$_SESSION['player']  = 'O';
$_SESSION['name']    = $playerName;

header('Location: game.php?id=' . $gameId);
exit;
