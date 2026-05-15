<?php
// ── Bootstrap: piggyback on the parent site's session & auth ──────────────
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/functions.php';
require_login();   // redirects to /login.php if not authenticated

// Fetch the logged-in user record (same pattern as myprofile.php)
$user = get_user_by_id($pdo, $_SESSION['user_id']);
if (!$user) {
    session_destroy();
    header('Location: /login.php');
    exit;
}

$page_title = 'Noughts & Crosses';

// ── Clean up stale games (>1 hour old) ───────────────────────────────────
$gamesDir = __DIR__ . '/games/';
if (!is_dir($gamesDir)) mkdir($gamesDir, 0777, true);

foreach (glob($gamesDir . '*.json') as $f) {
    if (time() - filemtime($f) > 3600) unlink($f);
}

// ── Load open / active games for the lobby ───────────────────────────────
$games = [];
foreach (glob($gamesDir . '*.json') as $f) {
    $d = json_decode(file_get_contents($f), true);
    if ($d && $d['status'] !== 'finished') $games[] = $d;
}

// Sort: waiting rooms first, then most-recently-active
usort($games, fn($a, $b) =>
    ($a['status'] === 'waiting' ? 0 : 1) - ($b['status'] === 'waiting' ? 0 : 1)
    ?: $b['last_move'] - $a['last_move']
);

// ── Flash error from redirects ────────────────────────────────────────────
$flash = $_GET['error'] ?? null;

// ── Pre-fill player name from the logged-in user ─────────────────────────
$prefill = e($user['display_name'] ?: $user['username']);

include dirname(__DIR__) . '/header.php';
?>

<!-- ════════════════════════════════════════════════════════════════════════
     Inline styles — scoped under .nc-* so they never clash with parent CSS
     ════════════════════════════════════════════════════════════════════════ -->
<style>
/* ── Layout ── */
.nc-wrap         { max-width: 960px; margin: 0 auto; padding: 40px 24px 80px; }
.nc-breadcrumb   { font-size: 13px; color: var(--text-3, #888); margin-bottom: 32px; }
.nc-breadcrumb a { color: var(--gold, #c9a84c); text-decoration: none; }
.nc-breadcrumb a:hover { text-decoration: underline; }

/* ── Hero / page title ── */
.nc-hero         { margin-bottom: 40px; }
.nc-hero-eyebrow { font-size: 11px; letter-spacing: .2em; text-transform: uppercase;
                   color: var(--gold, #c9a84c); margin-bottom: 8px; }
.nc-hero h1      { font-size: clamp(2rem, 5vw, 3rem); font-weight: 700; line-height: 1.1;
                   color: var(--text-1, #f0f0f0); margin: 0 0 8px; }
.nc-hero-sub     { font-size: 14px; color: var(--text-3, #888); }

/* ── Two-column action grid ── */
.nc-action-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 40px; }
@media (max-width: 640px) { .nc-action-grid { grid-template-columns: 1fr; } }

/* ── Cards (inherit parent .settings-card look where possible) ── */
.nc-card         { background: var(--surface-2, #1a1a1a);
                   border: 1px solid var(--border, #2e2e2e);
                   border-radius: var(--radius, 8px);
                   padding: 28px; }
.nc-card-title   { font-size: 13px; font-weight: 600; letter-spacing: .08em;
                   text-transform: uppercase; color: var(--text-2, #bbb);
                   margin-bottom: 18px; }
.nc-card-accent  { border-top: 2px solid var(--gold, #c9a84c); }
.nc-card-red     { border-top: 2px solid #e05252; }

/* ── Form inputs ── */
.nc-input        { width: 100%; background: var(--surface-1, #111);
                   border: 1px solid var(--border, #2e2e2e);
                   border-radius: var(--radius-sm, 6px);
                   color: var(--text-1, #f0f0f0);
                   font-size: 14px; padding: 10px 14px;
                   outline: none; margin-bottom: 10px;
                   transition: border-color .2s; }
.nc-input:focus  { border-color: var(--gold, #c9a84c); }
.nc-input::placeholder { color: var(--text-3, #666); }

/* ── Lobby table ── */
.nc-section-title { font-size: 13px; font-weight: 600; letter-spacing: .1em;
                    text-transform: uppercase; color: var(--text-2, #bbb);
                    margin-bottom: 16px;
                    display: flex; align-items: center; gap: 12px; }
.nc-badge        { background: var(--gold, #c9a84c); color: #000;
                   font-size: 10px; font-weight: 700; padding: 2px 8px;
                   border-radius: 20px; }

.nc-table        { width: 100%; border-collapse: collapse; }
.nc-table th     { font-size: 10px; letter-spacing: .15em; text-transform: uppercase;
                   color: var(--text-3, #666); padding: 0 12px 12px;
                   text-align: left; font-weight: 400;
                   border-bottom: 1px solid var(--border, #2e2e2e); }
.nc-table td     { padding: 14px 12px; font-size: 14px;
                   border-bottom: 1px solid var(--border, #2e2e2e);
                   color: var(--text-2, #ccc); vertical-align: middle; }
.nc-table tr:last-child td { border-bottom: none; }
.nc-table tr:hover td { background: rgba(255,255,255,.02); }

.nc-room-name    { font-weight: 500; color: var(--text-1, #f0f0f0); }
.nc-room-meta    { font-size: 12px; color: var(--text-3, #777); margin-top: 3px; }
.nc-code         { font-family: monospace; letter-spacing: .15em;
                   color: var(--gold, #c9a84c); font-size: 13px; }

.nc-pill         { display: inline-block; font-size: 10px; letter-spacing: .15em;
                   text-transform: uppercase; padding: 3px 10px;
                   border: 1px solid; border-radius: 3px; white-space: nowrap; }
.nc-pill-wait    { color: var(--gold, #c9a84c); border-color: var(--gold, #c9a84c); }
.nc-pill-play    { color: #e05252; border-color: #e05252; }

.nc-empty        { text-align: center; padding: 48px 0; color: var(--text-3, #666);
                   font-size: 14px; }
.nc-empty-icon   { font-size: 36px; margin-bottom: 12px; }

/* ── Flash / alert ── */
.nc-flash        { background: rgba(224,82,82,.1);
                   border: 1px solid rgba(224,82,82,.4);
                   border-radius: var(--radius-sm, 6px);
                   color: #e07b7b; padding: 12px 16px; font-size: 13px;
                   margin-bottom: 24px; }

/* ── Refresh hint ── */
.nc-live         { font-size: 11px; color: var(--text-3, #666); margin-top: 14px;
                   display: flex; align-items: center; gap: 6px; }
.nc-dot          { width: 6px; height: 6px; border-radius: 50%;
                   background: var(--gold, #c9a84c); flex-shrink: 0;
                   animation: nc-blink 2s infinite; }
@keyframes nc-blink { 0%,100%{opacity:1} 50%{opacity:.25} }
</style>

<div class="nc-wrap">

  <!-- Breadcrumb -->
  <div class="nc-breadcrumb">
    <a href="/">Home</a> ›
    <a href="/myprofile.php">My Profile</a> ›
    Noughts &amp; Crosses
  </div>

  <!-- Hero -->
  <div class="nc-hero">
    <div class="nc-hero-eyebrow">Mini Games</div>
    <h1>Noughts &amp; Crosses</h1>
    <p class="nc-hero-sub">Create a room, share the code, and play against anyone on the network.</p>
  </div>

  <?php if ($flash === 'notfound'): ?>
    <div class="nc-flash">That game code doesn't exist or has already expired.</div>
  <?php endif; ?>

  <!-- ── Create / Join ─────────────────────────────────────────────────── -->
  <div class="nc-action-grid">

    <!-- Create -->
    <div class="nc-card nc-card-accent">
      <div class="nc-card-title">Create a room</div>
      <form action="create.php" method="POST">
        <input
          class="nc-input"
          type="text"
          name="player_name"
          placeholder="Your name"
          maxlength="20"
          value="<?= $prefill ?>"
          required
          autocomplete="off"
        >
        <input
          class="nc-input"
          type="text"
          name="game_name"
          placeholder="Room name (e.g. Friday Night)"
          maxlength="30"
          required
          autocomplete="off"
        >
        <button type="submit" class="btn-gold-lg" style="width:100%">Create room →</button>
      </form>
    </div>

    <!-- Join by code -->
    <div class="nc-card nc-card-red">
      <div class="nc-card-title">Join by code</div>
      <form action="join.php" method="POST">
        <input
          class="nc-input"
          type="text"
          name="player_name"
          placeholder="Your name"
          maxlength="20"
          value="<?= $prefill ?>"
          required
          autocomplete="off"
        >
        <input
          class="nc-input"
          type="text"
          name="game_id"
          placeholder="6-character code"
          maxlength="6"
          required
          autocomplete="off"
          style="text-transform:uppercase;letter-spacing:.2em"
        >
        <button type="submit" class="btn-gold-lg" style="width:100%;background:#e05252;border-color:#e05252">Join room →</button>
      </form>
    </div>

  </div>

  <!-- ── Open Rooms ─────────────────────────────────────────────────────── -->
  <div class="nc-section-title">
    Open rooms
    <span class="nc-badge"><?= count($games) ?></span>
  </div>

  <div class="nc-card" style="padding:0;overflow:hidden">
    <?php if (empty($games)): ?>
      <div class="nc-empty">
        <div class="nc-empty-icon">♟</div>
        No open rooms yet — create one above to get started.
      </div>
    <?php else: ?>
      <table class="nc-table">
        <thead>
          <tr>
            <th>Room</th>
            <th>Code</th>
            <th>Players</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($games as $g): ?>
            <tr>
              <td>
                <div class="nc-room-name"><?= e($g['name']) ?></div>
                <div class="nc-room-meta">Created by <?= e($g['player_x']) ?></div>
              </td>
              <td><span class="nc-code"><?= $g['id'] ?></span></td>
              <td style="font-size:13px">
                <?= e($g['player_x']) ?>
                <?= $g['player_o'] ? ' vs ' . e($g['player_o']) : '<span style="color:var(--text-3)">waiting…</span>' ?>
              </td>
              <td>
                <?php if ($g['status'] === 'waiting'): ?>
                  <span class="nc-pill nc-pill-wait">Waiting</span>
                <?php else: ?>
                  <span class="nc-pill nc-pill-play">In progress</span>
                <?php endif; ?>
              </td>
              <td style="text-align:right">
                <?php if ($g['status'] === 'waiting'): ?>
                  <form method="POST" action="join.php" style="display:inline">
                    <input type="hidden" name="game_id"     value="<?= $g['id'] ?>">
                    <input type="hidden" name="player_name" value="<?= $prefill ?>">
                    <button type="submit" class="btn-ghost-lg" style="font-size:12px;padding:7px 16px">Join →</button>
                  </form>
                <?php else: ?>
                  <a class="btn-ghost-lg" href="game.php?id=<?= $g['id'] ?>&spectate=1"
                     style="font-size:12px;padding:7px 16px">Watch →</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <div class="nc-live">
    <span class="nc-dot"></span>
    Lobby refreshes every 10 seconds
  </div>

</div>

<script>
  // Auto-refresh lobby
  setTimeout(() => location.reload(), 10000);
</script>

<?php include dirname(__DIR__) . '/footer.php'; ?>
