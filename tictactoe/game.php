<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';
require_login();

$gamesDir = __DIR__ . '/games/';
$gameId   = strtoupper(trim($_GET['id'] ?? ''));
$spectate = !empty($_GET['spectate']);

if (!$gameId) {
    header('Location: /tictactoe/dashboard.php');
    exit;
}

$file = $gamesDir . $gameId . '.json';
if (!file_exists($file)) {
    header('Location: /tictactoe/dashboard.php?error=notfound');
    exit;
}

$game   = json_decode(file_get_contents($file), true);
$player = $_SESSION['player'] ?? null;
$myName = $user['display_name'] ?: $user['username'];  // from parent auth

if ($spectate || !$player) {
    $role = 'spectator';
} else {
    $role = $player; // 'X' or 'O'
}

$page_title = htmlspecialchars($game['name']) . ' — Noughts & Crosses';
include dirname(__DIR__) . '/header.php';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<style>
/* All classes prefixed nc- to avoid collisions with parent site CSS */
.nc-game-root {
  display: grid;
  grid-template-columns: 1fr 320px;
  min-height: calc(100vh - 80px);
  font-family: 'DM Mono', monospace;
  --bg:      #0f0f13;
  --surface: #161620;
  --border:  #2a2a3a;
  --accent:  var(--gold, #e8ff47);
  --accent2: #ff4757;
  --text:    var(--text-1, #e8e8f0);
  --muted:   var(--text-3, #555568);
  --glow-y:  rgba(232,255,71,0.2);
  --glow-r:  rgba(255,71,87,0.2);
  --x-color: var(--gold, #e8ff47);
  --o-color: #ff4757;
  color: var(--text);
}
@media (max-width:860px){ .nc-game-root{ grid-template-columns:1fr; } }

/* Sub-nav bar */
.nc-topbar {
  grid-column: 1 / -1;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  padding: 12px 28px;
  display: flex; align-items: center; justify-content: space-between;
  gap: 16px; position: relative;
}
.nc-topbar::after {
  content:''; position:absolute; bottom:0; left:0; right:0; height:2px;
  background: linear-gradient(90deg, var(--accent), transparent 60%);
}
.nc-breadcrumb { display:flex; align-items:center; gap:10px; font-size:0.75rem; color:var(--muted); }
.nc-breadcrumb a { color:var(--gold,#c9a84c); text-decoration:none; }
.nc-breadcrumb a:hover { text-decoration:underline; }
.nc-breadcrumb-sep { color:var(--border); }
.nc-game-meta { display:flex; align-items:center; gap:14px; font-size:0.7rem; letter-spacing:.15em; color:var(--muted); text-transform:uppercase; }
.nc-game-code {
  background:var(--bg); border:1px solid var(--border);
  padding:5px 12px; font-size:0.8rem; letter-spacing:.2em;
  color:var(--accent); cursor:pointer; position:relative; font-family:'DM Mono',monospace;
}
.nc-game-code:hover::after {
  content:'Copied!'; position:absolute; top:-26px; left:50%;
  transform:translateX(-50%); background:var(--accent); color:#000;
  font-size:.6rem; padding:2px 8px; white-space:nowrap;
}
.nc-role-badge { padding:4px 10px; font-size:.6rem; letter-spacing:.2em; text-transform:uppercase; border:1px solid; font-family:'DM Mono',monospace; }
.nc-role-x { color:var(--x-color); border-color:var(--x-color); }
.nc-role-o { color:var(--o-color); border-color:var(--o-color); }
.nc-role-s { color:var(--muted);   border-color:var(--border);  }

/* Game area */
.nc-game-area {
  padding:40px; display:flex; flex-direction:column;
  align-items:center; justify-content:center;
  position:relative; z-index:1; background:var(--bg);
}
.nc-status-bar  { margin-bottom:32px; text-align:center; min-height:60px; }
.nc-status-main { font-family:'Bebas Neue',sans-serif; font-size:2.2rem; letter-spacing:.05em; color:var(--text); transition:color .3s; }
.nc-status-main.x-turn { color:var(--x-color); }
.nc-status-main.o-turn { color:var(--o-color); }
.nc-status-sub  { font-size:.65rem; letter-spacing:.2em; color:var(--muted); text-transform:uppercase; margin-top:6px; }

/* Board */
.nc-board-wrap { position:relative; }
.nc-board {
  display:grid; grid-template-columns:repeat(3,1fr);
  width:min(420px,90vw); height:min(420px,90vw); position:relative;
}
.nc-board::before,.nc-board::after { content:''; position:absolute; background:var(--border); z-index:1; pointer-events:none; }
.nc-board::before { top:0; bottom:0; left:33.33%; width:1px; box-shadow:calc(33.33% + 1px) 0 0 var(--border); }
.nc-board::after  { left:0; right:0; top:33.33%; height:1px; box-shadow:0 calc(33.33% + 1px) 0 var(--border); }

.nc-cell {
  display:flex; align-items:center; justify-content:center;
  cursor:pointer; transition:background .15s; position:relative; z-index:2;
  font-family:'Bebas Neue',sans-serif; font-size:clamp(3.5rem,10vw,6rem); user-select:none;
}
.nc-cell:hover:not(.taken):not(.disabled){ background:rgba(255,255,255,.03); }
.nc-cell.taken   { cursor:default; }
.nc-cell.disabled{ cursor:not-allowed; }
.nc-cell .sym { display:block; line-height:1; transition:transform .15s,opacity .15s; transform:scale(0); opacity:0; }
.nc-cell .sym.show { transform:scale(1); opacity:1; animation:nc-pop .25s cubic-bezier(.34,1.56,.64,1); }
@keyframes nc-pop { from{transform:scale(0) rotate(-10deg);opacity:0} to{transform:scale(1) rotate(0deg);opacity:1} }
.nc-cell .sym.x-sym { color:var(--x-color); text-shadow:0 0 30px var(--glow-y); }
.nc-cell .sym.o-sym { color:var(--o-color); text-shadow:0 0 30px var(--glow-r); }
.nc-cell:not(.taken):not(.disabled):hover::before {
  content:attr(data-ghost); position:absolute;
  font-family:'Bebas Neue',sans-serif; font-size:clamp(3.5rem,10vw,6rem);
  line-height:1; opacity:.12; color:var(--accent);
}

/* Win line */
.nc-win-line {
  position:absolute; background:var(--accent); border-radius:2px; z-index:5;
  transform-origin:center; animation:nc-drawLine .4s ease forwards;
  pointer-events:none; opacity:.85; box-shadow:0 0 20px var(--glow-y);
}
@keyframes nc-drawLine { from{clip-path:inset(0 100% 0 0)} to{clip-path:inset(0 0% 0 0)} }
.nc-win-line.o-win { background:var(--o-color); box-shadow:0 0 20px var(--glow-r); }

/* Players strip */
.nc-players { display:flex; gap:40px; margin-top:32px; align-items:center; }
.nc-player-card { text-align:center; min-width:100px; }
.nc-player-sym  { font-family:'Bebas Neue',sans-serif; font-size:2.5rem; line-height:1; margin-bottom:6px; }
.nc-player-sym.x-sym { color:var(--x-color); }
.nc-player-sym.o-sym { color:var(--o-color); }
.nc-player-name { font-size:.7rem; letter-spacing:.1em; color:var(--muted); text-transform:uppercase; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.nc-player-card.active-turn .nc-player-name { color:var(--text); }
.nc-player-card.active-turn .nc-player-sym  { animation:nc-glow 1.5s ease-in-out infinite alternate; }
@keyframes nc-glow { from{filter:drop-shadow(0 0 4px currentColor)} to{filter:drop-shadow(0 0 16px currentColor)} }
.nc-vs { font-family:'Bebas Neue',sans-serif; font-size:1.2rem; color:var(--border); letter-spacing:.1em; }

/* Waiting overlay */
.nc-waiting {
  position:absolute; inset:0; background:rgba(10,10,15,.85);
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  text-align:center; z-index:10; backdrop-filter:blur(4px);
}
.nc-waiting .wcode  { font-family:'Bebas Neue',sans-serif; font-size:5rem; letter-spacing:.2em; color:var(--accent); text-shadow:0 0 60px var(--glow-y); margin:10px 0; }
.nc-waiting .wlabel { font-size:.65rem; letter-spacing:.3em; color:var(--muted); text-transform:uppercase; }
.nc-waiting .whint  { font-size:.7rem; color:var(--muted); margin-top:16px; letter-spacing:.1em; max-width:260px; line-height:1.6; }
.nc-dots   { display:inline-flex; gap:6px; margin-top:20px; }
.nc-dot    { width:6px; height:6px; background:var(--accent); border-radius:50%; animation:nc-bounce 1.4s ease-in-out infinite; }
.nc-dot:nth-child(2){ animation-delay:.2s; }
.nc-dot:nth-child(3){ animation-delay:.4s; }
@keyframes nc-bounce { 0%,80%,100%{transform:scale(.6);opacity:.4} 40%{transform:scale(1);opacity:1} }

/* Result overlay */
.nc-result {
  position:absolute; inset:0; background:rgba(10,10,15,.88);
  display:none; flex-direction:column; align-items:center; justify-content:center;
  text-align:center; z-index:10; backdrop-filter:blur(6px);
}
.nc-result.show { display:flex; }
.nc-result-title { font-family:'Bebas Neue',sans-serif; font-size:4.5rem; letter-spacing:.06em; line-height:1; color:var(--text); margin-bottom:12px; }
.nc-result-title.win-x { color:var(--x-color); text-shadow:0 0 60px var(--glow-y); }
.nc-result-title.win-o { color:var(--o-color); text-shadow:0 0 60px var(--glow-r); }
.nc-result-sub  { font-size:.7rem; letter-spacing:.2em; color:var(--muted); text-transform:uppercase; margin-bottom:24px; }
.nc-rematch-msg { font-size:.65rem; color:var(--muted); letter-spacing:.15em; text-transform:uppercase; margin-bottom:16px; min-height:18px; }
.nc-btn-rematch {
  background:var(--accent); color:#0a0a0f; border:none;
  font-family:'DM Mono',monospace; font-size:.8rem; letter-spacing:.2em; text-transform:uppercase;
  padding:14px 36px; cursor:pointer; font-weight:500; transition:all .15s; margin-bottom:10px; display:block;
}
.nc-btn-rematch:hover  { background:#fff; box-shadow:0 0 30px var(--glow-y); }
.nc-btn-rematch:disabled { background:var(--border); color:var(--muted); cursor:default; box-shadow:none; }
.nc-btn-lobby {
  background:transparent; color:var(--muted); border:1px solid var(--border);
  font-family:'DM Mono',monospace; font-size:.7rem; letter-spacing:.15em; text-transform:uppercase;
  padding:10px 24px; cursor:pointer; text-decoration:none; display:inline-block; transition:all .15s;
}
.nc-btn-lobby:hover { border-color:var(--text); color:var(--text); }

/* Sidebar */
.nc-sidebar { background:var(--surface); border-left:1px solid var(--border); display:flex; flex-direction:column; position:relative; z-index:1; }
@media(max-width:860px){ .nc-sidebar{ border-left:none; border-top:1px solid var(--border); } }
.nc-sidebar-sec { padding:20px; border-bottom:1px solid var(--border); }
.nc-sec-label   { font-size:.58rem; letter-spacing:.3em; color:var(--muted); text-transform:uppercase; margin-bottom:12px; }
.nc-score-grid  { display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; text-align:center; }
.nc-score-item  { background:var(--bg); border:1px solid var(--border); padding:12px 8px; }
.nc-score-num   { font-family:'Bebas Neue',sans-serif; font-size:2rem; line-height:1; }
.nc-score-num.xc{ color:var(--x-color); }
.nc-score-num.oc{ color:var(--o-color); }
.nc-score-num.dc{ color:var(--muted); }
.nc-score-lbl   { font-size:.55rem; letter-spacing:.15em; color:var(--muted); text-transform:uppercase; margin-top:4px; }

/* Chat */
.nc-chat-wrap  { flex:1; display:flex; flex-direction:column; min-height:0; padding:20px; }
.nc-chat-label { font-size:.58rem; letter-spacing:.3em; color:var(--muted); text-transform:uppercase; margin-bottom:12px; }
.nc-chat-msgs  { flex:1; overflow-y:auto; margin-bottom:12px; display:flex; flex-direction:column; gap:6px; min-height:160px; max-height:300px; scrollbar-width:thin; scrollbar-color:var(--border) transparent; }
.nc-chat-msg   { font-size:.72rem; line-height:1.5; color:var(--muted); }
.nc-chat-msg .nm{ color:var(--text); font-weight:500; }
.nc-chat-msg .tm{ font-size:.6rem; color:var(--muted); margin-left:6px; opacity:.6; }
.nc-chat-row   { display:flex; gap:8px; }
.nc-chat-input {
  flex:1; background:var(--bg); border:1px solid var(--border); color:var(--text);
  font-family:'DM Mono',monospace; font-size:.75rem; padding:9px 12px; outline:none; transition:border-color .2s;
}
.nc-chat-input:focus { border-color:var(--accent); }
.nc-chat-input::placeholder { color:var(--muted); }
.nc-chat-send {
  background:var(--accent); color:#0a0a0f; border:none;
  font-family:'DM Mono',monospace; font-size:.7rem; padding:9px 14px;
  cursor:pointer; letter-spacing:.1em; font-weight:500; transition:background .15s;
}
.nc-chat-send:hover { background:#fff; }

/* Connection indicator */
.nc-conn-dot { display:inline-block; width:7px; height:7px; border-radius:50%; background:var(--accent); margin-right:8px; animation:nc-blink 2s infinite; vertical-align:middle; }
@keyframes nc-blink { 0%,100%{opacity:1} 50%{opacity:.3} }
.nc-conn-dot.offline { background:var(--accent2); animation:none; }

/* Share bar */
.nc-share-bar { padding:16px 20px; border-top:1px solid var(--border); font-size:.65rem; color:var(--muted); letter-spacing:.08em; line-height:1.6; }
.nc-share-url { display:block; background:var(--bg); border:1px solid var(--border); padding:8px 10px; font-size:.65rem; color:var(--accent); word-break:break-all; margin-top:8px; cursor:pointer; transition:border-color .2s; }
.nc-share-url:hover { border-color:var(--accent); }
</style>

<div class="nc-game-root">

  <!-- Sub-nav: breadcrumb + room meta -->
  <div class="nc-topbar">
    <div class="nc-breadcrumb">
      <a href="/myprofile.php">← My Profile</a>
      <span class="nc-breadcrumb-sep">/</span>
      <a href="../dashboard.php">Game Lobby</a>
      <span class="nc-breadcrumb-sep">/</span>
      <span style="color:var(--text)"><?= htmlspecialchars($game['name']) ?></span>
    </div>
    <div class="nc-game-meta">
      <span class="nc-game-code" onclick="ncCopyCode()" title="Click to copy"
      >[ <?= $gameId ?> ]</span>
      <?php if ($role !== 'spectator'): ?>
        <span class="nc-role-badge nc-role-<?= strtolower($role) ?>">Playing as <?= $role ?></span>
      <?php else: ?>
        <span class="nc-role-badge nc-role-s">Spectating</span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Game canvas -->
  <div class="nc-game-area">

    <div class="nc-status-bar">
      <div class="nc-status-main" id="ncStatusMain">Loading…</div>
      <div class="nc-status-sub"  id="ncStatusSub"></div>
    </div>

    <div class="nc-board-wrap">

      <!-- Waiting for opponent -->
      <div class="nc-waiting" id="ncWaiting" <?= $game['status'] !== 'waiting' ? 'style="display:none"' : '' ?>>
        <div class="wlabel">Share this code</div>
        <div class="wcode"><?= $gameId ?></div>
        <div class="wlabel">with your opponent</div>
        <div class="whint">Waiting for Player O to join…</div>
        <div class="nc-dots">
          <div class="nc-dot"></div>
          <div class="nc-dot"></div>
          <div class="nc-dot"></div>
        </div>
      </div>

      <!-- Game over / result -->
      <div class="nc-result" id="ncResult">
        <div class="nc-result-title" id="ncResultTitle">—</div>
        <div class="nc-result-sub"   id="ncResultSub">—</div>
        <div class="nc-rematch-msg"  id="ncRematchMsg"></div>
        <?php if ($role !== 'spectator'): ?>
          <button class="nc-btn-rematch" id="ncRematchBtn" onclick="ncRematch()">↺ Rematch</button>
        <?php endif; ?>
        <a href="../dashboard.php" class="nc-btn-lobby">← Back to Lobby</a>
      </div>

      <!-- Board -->
      <div class="nc-board" id="ncBoard">
        <?php for ($i = 0; $i < 9; $i++): ?>
          <div class="nc-cell"
               data-idx="<?= $i ?>"
               data-ghost="<?= $role === 'spectator' ? '' : $role ?>"
               onclick="ncMove(<?= $i ?>)">
            <span class="sym" id="ncCell<?= $i ?>"></span>
          </div>
        <?php endfor; ?>
      </div>

    </div><!-- /board-wrap -->

    <div class="nc-players">
      <div class="nc-player-card" id="ncCardX">
        <div class="nc-player-sym x-sym">X</div>
        <div class="nc-player-name" id="ncNameX"><?= htmlspecialchars($game['player_x']) ?></div>
      </div>
      <div class="nc-vs">vs</div>
      <div class="nc-player-card" id="ncCardO">
        <div class="nc-player-sym o-sym">O</div>
        <div class="nc-player-name" id="ncNameO"><?= $game['player_o'] ? htmlspecialchars($game['player_o']) : '???' ?></div>
      </div>
    </div>

  </div><!-- /game-area -->

  <!-- Sidebar -->
  <div class="nc-sidebar">

    <div class="nc-sidebar-sec">
      <div class="nc-sec-label">// Scoreboard</div>
      <div class="nc-score-grid">
        <div class="nc-score-item"><div class="nc-score-num xc" id="ncSX">0</div><div class="nc-score-lbl">X Wins</div></div>
        <div class="nc-score-item"><div class="nc-score-num dc" id="ncSD">0</div><div class="nc-score-lbl">Draws</div></div>
        <div class="nc-score-item"><div class="nc-score-num oc" id="ncSO">0</div><div class="nc-score-lbl">O Wins</div></div>
      </div>
    </div>

    <div class="nc-sidebar-sec" style="font-size:.68rem;color:var(--muted);letter-spacing:.1em">
      <span class="nc-conn-dot" id="ncDot"></span>
      <span id="ncConn">Connecting…</span>
    </div>

    <div class="nc-chat-wrap">
      <div class="nc-chat-label">// Chat</div>
      <div class="nc-chat-msgs" id="ncChatMsgs"></div>
      <div class="nc-chat-row">
        <input type="text" class="nc-chat-input" id="ncChatInput"
               placeholder="Say something…" maxlength="120"
               onkeydown="if(event.key==='Enter')ncSendChat()">
        <button class="nc-chat-send" onclick="ncSendChat()">↑</button>
      </div>
    </div>

    <div class="nc-share-bar">
      <div>Share this room link:</div>
      <span class="nc-share-url" id="ncShareUrl" onclick="ncCopyUrl(this)">Loading…</span>
    </div>

  </div><!-- /sidebar -->

</div><!-- /nc-game-root -->

<script>
const GAME_ID      = <?= json_encode($gameId) ?>;
const MY_ROLE      = <?= json_encode($role) ?>;
const IS_SPECTATOR = MY_ROLE === 'spectator';

let ncState      = null;
let ncScore      = {X:0,O:0,D:0};
let ncRematchSent = false;
let ncChatKnown  = 0;

const ncCells     = document.querySelectorAll('.nc-cell');
const ncBoard     = document.getElementById('ncBoard');
const ncStatusMain= document.getElementById('ncStatusMain');
const ncStatusSub = document.getElementById('ncStatusSub');
const ncWaiting   = document.getElementById('ncWaiting');
const ncResult    = document.getElementById('ncResult');
const ncReTitle   = document.getElementById('ncResultTitle');
const ncReSub     = document.getElementById('ncResultSub');
const ncReMsg     = document.getElementById('ncRematchMsg');
const ncReBtn     = document.getElementById('ncRematchBtn');
const ncDot       = document.getElementById('ncDot');
const ncConn      = document.getElementById('ncConn');
const ncChatMsgs  = document.getElementById('ncChatMsgs');
const ncCardX     = document.getElementById('ncCardX');
const ncCardO     = document.getElementById('ncCardO');

// Share URL
document.getElementById('ncShareUrl').textContent =
  window.location.href.replace(/[?#].*$/,'') + '?id=' + GAME_ID;

// ── Poll ─────────────────────────────────────────
async function ncPoll() {
  try {
    const r = await fetch('poll.php?game_id=' + GAME_ID);
    const g = await r.json();
    if (g && !g.error) { ncApply(g); ncSetConn(true); }
  } catch(e) { ncSetConn(false); }
}
function ncSetConn(ok) {
  ncDot.className     = 'nc-conn-dot' + (ok ? '' : ' offline');
  ncConn.textContent  = ok ? 'Live · polling every 1.5s' : 'Connection lost — retrying…';
}
ncPoll();
setInterval(ncPoll, 1500);

// ── Apply state ───────────────────────────────────
function ncApply(g) {
  ncState = g;

  // Cells
  g.board.forEach((v,i) => {
    const sp = document.getElementById('ncCell'+i);
    const cl = ncCells[i];
    if (v) {
      sp.textContent = v;
      sp.className   = 'sym show '+(v==='X'?'x-sym':'o-sym');
      cl.classList.add('taken','disabled');
    } else {
      sp.textContent = '';
      sp.className   = 'sym';
      cl.classList.remove('taken');
    }
  });

  // Names
  document.getElementById('ncNameX').textContent = g.player_x || 'Player X';
  document.getElementById('ncNameO').textContent = g.player_o || '???';

  // Turn highlight
  ncCardX.classList.toggle('active-turn', g.status==='playing' && g.turn==='X');
  ncCardO.classList.toggle('active-turn', g.status==='playing' && g.turn==='O');

  // Cell interactivity
  const myTurn = !IS_SPECTATOR && g.status==='playing' && g.turn===MY_ROLE;
  ncCells.forEach(c => {
    if (!c.classList.contains('taken')) c.classList.toggle('disabled', !myTurn);
  });

  // Waiting state
  if (g.status === 'waiting') {
    ncWaiting.style.display = '';
    ncStatusMain.textContent = 'Waiting…';
    ncStatusMain.className   = 'nc-status-main';
    ncStatusSub.textContent  = 'Share the code to invite an opponent';
  } else {
    ncWaiting.style.display = 'none';
  }

  // Playing state
  if (g.status === 'playing') {
    ncResult.classList.remove('show');
    ncRematchSent = false;
    if (myTurn) {
      ncStatusMain.textContent = 'Your Turn';
      ncStatusMain.className   = 'nc-status-main '+(MY_ROLE==='X'?'x-turn':'o-turn');
      ncStatusSub.textContent  = 'You are playing '+MY_ROLE;
    } else {
      const who = g.turn==='X' ? g.player_x : g.player_o;
      ncStatusMain.textContent = (who||g.turn)+"'s Turn";
      ncStatusMain.className   = 'nc-status-main '+(g.turn==='X'?'x-turn':'o-turn');
      ncStatusSub.textContent  = IS_SPECTATOR ? 'Spectating' : 'Waiting for opponent';
    }
  }

  // Finished state
  if (g.status === 'finished') {
    ncDrawLine(g);
    ncResult.classList.add('show');

    if (g.winner === 'draw') {
      ncReTitle.textContent = 'Draw!';
      ncReTitle.className   = 'nc-result-title';
      ncReSub.textContent   = 'No winner this time';
      ncScore.D++;
    } else if (g.winner === 'X') {
      ncReTitle.textContent = (g.player_x||'X')+' Wins!';
      ncReTitle.className   = 'nc-result-title win-x';
      ncReSub.textContent   = 'X takes the round';
      ncScore.X++;
    } else {
      ncReTitle.textContent = (g.player_o||'O')+' Wins!';
      ncReTitle.className   = 'nc-result-title win-o';
      ncReSub.textContent   = 'O takes the round';
      ncScore.O++;
    }

    document.getElementById('ncSX').textContent = ncScore.X;
    document.getElementById('ncSO').textContent = ncScore.O;
    document.getElementById('ncSD').textContent = ncScore.D;

    if (!IS_SPECTATOR && ncReBtn) {
      const mk = 'rematch_'+MY_ROLE.toLowerCase();
      const ok = 'rematch_'+(MY_ROLE==='X'?'o':'x');
      if (g[mk] && !g[ok]) {
        ncReMsg.textContent  = 'Waiting for opponent to accept…';
        ncReBtn.disabled     = true;
        ncReBtn.textContent  = '↺ Rematch Requested';
      } else if (!g[mk]) {
        ncReMsg.textContent  = g[ok] ? '⚡ Opponent wants a rematch!' : '';
        ncReBtn.disabled     = false;
        ncReBtn.textContent  = '↺ Rematch';
      }
    }

    ncStatusMain.textContent = 'Game Over';
    ncStatusMain.className   = 'nc-status-main';
    ncStatusSub.textContent  = '';
  }

  ncRenderChat(g.chat||[]);
}

// ── Win line ──────────────────────────────────────
function ncDrawLine(g) {
  document.querySelectorAll('.nc-win-line').forEach(e=>e.remove());
  if (!g.winning_line) return;
  const ln = g.winning_line;
  const br = ncBoard.getBoundingClientRect();
  const mid = i => {
    const r = ncCells[i].getBoundingClientRect();
    return {x:r.left+r.width/2-br.left, y:r.top+r.height/2-br.top};
  };
  const p1=mid(ln[0]), p2=mid(ln[2]);
  const dx=p2.x-p1.x, dy=p2.y-p1.y;
  const el = document.createElement('div');
  el.className = 'nc-win-line'+(g.winner==='O'?' o-win':'');
  el.style.cssText = `width:${Math.sqrt(dx*dx+dy*dy)+40}px;height:4px;`
    +`left:${(p1.x+p2.x)/2}px;top:${(p1.y+p2.y)/2}px;`
    +`transform:translate(-50%,-50%) rotate(${Math.atan2(dy,dx)*180/Math.PI}deg);`;
  ncBoard.appendChild(el);
}

// ── Move ──────────────────────────────────────────
async function ncMove(idx) {
  if (IS_SPECTATOR||!ncState||ncState.status!=='playing') return;
  if (ncState.turn!==MY_ROLE||ncState.board[idx]!=='') return;
  const sp = document.getElementById('ncCell'+idx);
  sp.textContent = MY_ROLE;
  sp.className   = 'sym show '+(MY_ROLE==='X'?'x-sym':'o-sym');
  ncCells[idx].classList.add('taken','disabled');
  ncCells.forEach(c=>c.classList.add('disabled'));
  try {
    const r = await fetch('move.php',{
      method:'POST', headers:{'Content-Type':'application/json'},
      body:JSON.stringify({game_id:GAME_ID,cell:idx})
    });
    const d = await r.json();
    if (d.game) ncApply(d.game);
  } catch(e) {}
}

// ── Rematch ───────────────────────────────────────
async function ncRematch() {
  if (ncRematchSent) return;
  ncRematchSent = true;
  try {
    const r = await fetch('rematch.php',{
      method:'POST', headers:{'Content-Type':'application/json'},
      body:JSON.stringify({game_id:GAME_ID})
    });
    const d = await r.json();
    if (d.game) ncApply(d.game);
  } catch(e) {}
}

// ── Chat ──────────────────────────────────────────
function ncRenderChat(msgs) {
  if (msgs.length===ncChatKnown) return;
  ncChatKnown = msgs.length;
  ncChatMsgs.innerHTML = '';
  msgs.forEach(m => {
    const div = document.createElement('div');
    div.className = 'nc-chat-msg';
    div.innerHTML = `<span class="nm">${e(m.name)}</span><span class="tm">${m.time}</span><br>${e(m.msg)}`;
    ncChatMsgs.appendChild(div);
  });
  ncChatMsgs.scrollTop = ncChatMsgs.scrollHeight;
}
async function ncSendChat() {
  const inp = document.getElementById('ncChatInput');
  const msg = inp.value.trim();
  if (!msg) return;
  inp.value = '';
  try {
    await fetch('chat.php',{
      method:'POST', headers:{'Content-Type':'application/json'},
      body:JSON.stringify({game_id:GAME_ID,message:msg})
    });
    ncPoll();
  } catch(ex) {}
}

// ── Utilities ─────────────────────────────────────
function e(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function ncCopyCode(){ navigator.clipboard.writeText(GAME_ID).catch(()=>{}); }
function ncCopyUrl(el){ navigator.clipboard.writeText(el.textContent).catch(()=>{}); }
</script>

<?php include dirname(__DIR__) . '/footer.php'; ?>
