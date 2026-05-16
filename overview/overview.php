<?php

$page_title = 'Overview';

require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/functions.php';
include ROOT_PATH . '/required/header.php';

require_login();

$username    = $_SESSION['display_name'] ?? $_SESSION['username'] ?? 'there';
$first_name  = explode(' ', $username)[0];

$hour = (int)date('G');
if      ($hour < 12) $greeting = 'Good morning';
elseif  ($hour < 18) $greeting = 'Good afternoon';
else                 $greeting = 'Good evening';

$date_label = date('l, F j');
?>

<style>
/* ── LAYOUT ──────────────────────────────────────────────────────────────── */
main {
  padding-top: 0;
  background: var(--black);
  min-height: calc(100vh - var(--nav-h));
}
.shell {
  display: flex;
  min-height: calc(100vh - var(--nav-h));
  padding-top: var(--nav-h);
}
.dash-wrap {
  flex: 1;
  min-width: 0;
  max-width: 1280px;
  margin: 0 auto;
  padding: 32px 28px 60px;
}

/* ── PAGE HEADER ─────────────────────────────────────────────────────────── */
.dash-ph          { margin-bottom: 28px; }
.dash-ph-ey       { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: var(--text-3); margin-bottom: 6px; }
.dash-ph-title    { font-family: var(--disp); font-size: 38px; letter-spacing: 1.5px; color: var(--text-1); line-height: 1; }
.dash-ph-title em { font-style: normal; color: var(--gold); }

/* ── LIVE BANNER ─────────────────────────────────────────────────────────── */
.live-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  background: rgba(232,53,10,.08);
  border: .5px solid rgba(232,53,10,.3);
  border-radius: 10px;
  padding: 14px 20px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.live-l         { display: flex; align-items: center; gap: 14px; }
.live-dot       { width: 10px; height: 10px; border-radius: 50%; background: #E8350A; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(232,53,10,.25); animation: livepulse 1.6s ease-in-out infinite; }
@keyframes livepulse {
  0%,100% { box-shadow: 0 0 0 3px rgba(232,53,10,.25); }
  50%     { box-shadow: 0 0 0 7px rgba(232,53,10,.08); }
}
.live-title     { font-size: 14px; font-weight: 600; color: var(--text-1); }
.live-sub       { font-size: 11px; color: var(--text-3); margin-top: 2px; }
.btn-danger {
  background: rgba(232,53,10,.15);
  border: .5px solid rgba(232,53,10,.4);
  color: #E8350A;
  font-family: var(--sans);
  font-size: 11px;
  font-weight: 600;
  padding: 8px 16px;
  border-radius: 6px;
  text-decoration: none;
  white-space: nowrap;
  transition: all .15s;
}
.btn-danger:hover { background: rgba(232,53,10,.25); }

/* ── STAT CARDS ──────────────────────────────────────────────────────────── */
.stat-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 24px; }
.stat {
  background: var(--dark);
  border: .5px solid var(--border);
  border-radius: 10px;
  padding: 16px 18px;
  text-decoration: none;
  transition: border-color .15s, background .15s;
  display: block;
}
.stat:hover       { border-color: var(--gold-bd); background: var(--dark-2); }
.stat-lbl         { font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: var(--text-3); margin-bottom: 8px; }
.stat-val         { font-family: var(--disp); font-size: 36px; letter-spacing: 1px; color: var(--text-1); line-height: 1; }
.stat-sub         { font-size: 11px; color: var(--text-3); margin-top: 5px; }
.stat-sub.up      { color: #4caf50; }
.stat-sub.warn    { color: #ffb300; }

/* ── BRIEFING ─────────────────────────────────────────────────────────────── */
.briefing-hd      { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: var(--text-3); margin-bottom: 10px; }
.briefing-grid    { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 28px; }
.brief-card {
  background: var(--dark);
  border: .5px solid var(--border);
  border-radius: 10px;
  padding: 16px 18px;
  text-decoration: none;
  transition: border-color .15s;
  display: block;
}
.brief-card:hover         { border-color: var(--gold-bd); }
.brief-card.brief-urgent  { border-color: rgba(255,179,0,.3); background: rgba(255,179,0,.04); }
.brief-card-ey            { font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: var(--text-3); margin-bottom: 6px; }
.brief-card-title         { font-size: 14px; font-weight: 600; color: var(--text-1); margin-bottom: 5px; }
.brief-card-sub           { font-size: 11px; color: var(--text-3); line-height: 1.5; }
.brief-urgent .brief-card-ey { color: #ffb300; }

/* ── MAIN GRID ────────────────────────────────────────────────────────────── */
.dash-grid { display: grid; grid-template-columns: 1fr 380px; gap: 16px; align-items: start; }
.dash-col  { display: flex; flex-direction: column; gap: 16px; }

/* ── CARDS ────────────────────────────────────────────────────────────────── */
.card { background: var(--dark); border: .5px solid var(--border); border-radius: 10px; overflow: hidden; }
.chd  { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: .5px solid var(--border); }
.cht  { font-size: 12px; font-weight: 600; color: var(--text-1); letter-spacing: .3px; }
.cha  { font-size: 11px; color: var(--text-3); text-decoration: none; transition: color .15s; }
.cha:hover { color: var(--gold); }

/* ── SECTION DIVIDERS ────────────────────────────────────────────────────── */
.section-divider       { padding: 8px 18px 6px; background: var(--dark-2); }
.section-divider-label { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: var(--text-3); }

/* ── WORK ROWS ────────────────────────────────────────────────────────────── */
.work-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 18px;
  border-bottom: .5px solid var(--border);
  text-decoration: none;
  transition: background .12s;
}
.work-row:last-child  { border-bottom: none; }
.work-row:hover       { background: var(--dark-2); }
.work-icon  { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.work-info  { flex: 1; min-width: 0; }
.work-name  { font-size: 13px; font-weight: 500; color: var(--text-1); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.work-meta  { display: flex; gap: 10px; font-size: 11px; color: var(--text-3); margin-top: 3px; flex-wrap: wrap; }
.work-right { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
.work-date  { font-size: 10px; color: var(--text-3); }

/* ── PILLS ────────────────────────────────────────────────────────────────── */
.pill        { font-size: 10px; letter-spacing: .5px; padding: 2px 8px; border-radius: 4px; font-weight: 600; white-space: nowrap; }
.pill-live   { background: rgba(232,53,10,.12); color: #E8350A; border: .5px solid rgba(232,53,10,.3); }
.pill-teal   { background: rgba(0,200,212,.10);  color: #00c8d4;  border: .5px solid rgba(0,200,212,.25); }
.pill-plan   { background: rgba(91,159,224,.10); color: #5B9FE0;  border: .5px solid rgba(91,159,224,.25); }
.pill-draft  { background: rgba(255,255,255,.06); color: var(--text-3); border: .5px solid var(--border); }

/* ── TASKS ────────────────────────────────────────────────────────────────── */
.task-row { display: flex; align-items: flex-start; gap: 12px; padding: 11px 18px; border-bottom: .5px solid var(--border); transition: background .12s; }
.task-row:last-child { border-bottom: none; }
.task-row:hover      { background: var(--dark-2); }
.chk {
  width: 16px; height: 16px; border-radius: 4px;
  border: .5px solid var(--border); flex-shrink: 0;
  cursor: pointer; margin-top: 2px; transition: all .15s;
  display: flex; align-items: center; justify-content: center;
}
.chk:hover          { border-color: var(--gold); }
.chk.done           { background: rgba(76,175,80,.2); border-color: rgba(76,175,80,.4); }
.chk.done::after    { content: '✓'; font-size: 9px; color: #4caf50; }
.tb     { flex: 1; min-width: 0; }
.tn     { font-size: 12px; color: var(--text-1); }
.tn.done { text-decoration: line-through; color: var(--text-3); }
.tr-ref { font-size: 10px; color: var(--text-3); margin-top: 2px; }
.td     { font-size: 11px; color: var(--text-3); flex-shrink: 0; }
.td.over { color: #ff6b6b; }

/* ── ACTIVITY ─────────────────────────────────────────────────────────────── */
.act-row { display: flex; align-items: flex-start; gap: 12px; padding: 11px 18px; border-bottom: .5px solid var(--border); }
.act-row:last-child { border-bottom: none; }
.act-av  { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
.act-text { font-size: 12px; color: var(--text-2); line-height: 1.5; }
.act-ref  { color: var(--text-3); }
.act-time { font-size: 10px; color: var(--text-3); margin-top: 3px; }

/* ── NEXT SHOW CARD ───────────────────────────────────────────────────────── */
.ns-hd   { padding: 16px 18px 14px; border-bottom: .5px solid var(--border); background: linear-gradient(135deg,rgba(91,159,224,.06) 0%,transparent 60%); }
.ns-ey   { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: var(--text-3); margin-bottom: 5px; }
.ns-name { font-family: var(--disp); font-size: 22px; letter-spacing: 1px; color: var(--text-1); }
.ns-date { font-size: 11px; color: var(--text-3); margin-top: 4px; }
.ns-body { padding: 14px 18px; }
.ns-row  { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: .5px solid var(--border); }
.ns-row:last-of-type { border-bottom: none; }
.ns-lbl  { font-size: 10px; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3); }
.ns-val  { font-size: 12px; color: var(--text-1); font-weight: 500; }
.prog-wrap { margin-top: 14px; }
.prog-lbl  { display: flex; justify-content: space-between; font-size: 10px; color: var(--text-3); margin-bottom: 6px; }
.prog-bar  { background: var(--dark-2); border-radius: 4px; height: 5px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg,#5B9FE0,#00c8d4); }

/* ── BUTTONS ─────────────────────────────────────────────────────────────── */
.btn-outline {
  display: flex; align-items: center;
  background: transparent;
  border: .5px solid var(--border);
  color: var(--text-2);
  font-family: var(--sans); font-size: 12px;
  padding: 9px 16px; border-radius: 6px;
  text-decoration: none; transition: all .15s;
}
.btn-outline:hover { border-color: var(--gold-bd); color: var(--gold); }
.w-full { width: 100%; box-sizing: border-box; }
.btn-ghost-sm {
  font-family: var(--sans); font-size: 11px;
  color: var(--text-3); text-decoration: none;
  padding: 5px 10px; border-radius: 5px;
  border: .5px solid var(--border); transition: all .15s;
}
.btn-ghost-sm:hover { color: var(--text-1); border-color: rgba(255,255,255,.15); }

/* ── MINI CALENDAR ────────────────────────────────────────────────────────── */
.mini-cal { padding: 10px 14px 14px; }
.mc-grid  { display: grid; grid-template-columns: repeat(7,1fr); gap: 3px; }
.mc-dow   { font-size: 9px; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3); text-align: center; padding: 4px 0; }
.mc-d     { font-size: 12px; color: var(--text-2); text-align: center; padding: 5px 2px; border-radius: 5px; text-decoration: none; transition: background .12s; }
.mc-d.other     { color: var(--text-3); }
.mc-d.ev        { color: #5B9FE0; background: rgba(91,159,224,.08); cursor: pointer; }
.mc-d.ev:hover  { background: rgba(91,159,224,.16); }
.mc-d.show-day  { background: rgba(232,53,10,.15); color: #E8350A; font-weight: 700; cursor: pointer; }

/* ── UPCOMING ─────────────────────────────────────────────────────────────── */
.up-row    { display: flex; align-items: center; gap: 14px; padding: 10px 18px; border-bottom: .5px solid var(--border); }
.up-row:last-child { border-bottom: none; }
.up-date   { text-align: center; flex-shrink: 0; min-width: 30px; }
.up-d      { font-family: var(--disp); font-size: 20px; color: var(--text-1); line-height: 1; }
.up-m      { font-size: 9px; letter-spacing: 1px; text-transform: uppercase; color: var(--text-3); }
.up-div    { width: .5px; height: 30px; background: var(--border); flex-shrink: 0; }
.up-name   { font-size: 12px; color: var(--text-1); font-weight: 500; }
.up-detail { font-size: 10px; color: var(--text-3); margin-top: 2px; }

/* ── QUICK ACTIONS ────────────────────────────────────────────────────────── */
.qa-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.qa-btn  { padding: 16px 18px; border-bottom: .5px solid var(--border); border-right: .5px solid var(--border); text-decoration: none; transition: background .12s; display: block; }
.qa-btn:nth-child(even)     { border-right: none; }
.qa-btn:nth-last-child(-n+2){ border-bottom: none; }
.qa-btn:hover { background: var(--dark-2); }
.qa-icon { font-size: 20px; margin-bottom: 6px; }
.qa-lbl  { font-size: 12px; font-weight: 600; color: var(--text-1); }
.qa-sub  { font-size: 10px; color: var(--text-3); margin-top: 2px; }

/* ── COLOUR DIMS ──────────────────────────────────────────────────────────── */
:root {
  --live-dim:  rgba(232,53,10,.15);
  --blue-dim:  rgba(91,159,224,.15);
  --green-dim: rgba(76,175,80,.15);
  --teal-dim:  rgba(0,200,212,.15);
}

/* ── RESPONSIVE ───────────────────────────────────────────────────────────── */
@media (max-width: 1024px) { .dash-grid { grid-template-columns: 1fr; } }
@media (max-width: 700px)  {
  .stat-row      { grid-template-columns: 1fr 1fr; }
  .briefing-grid { grid-template-columns: 1fr; }
  .dash-wrap     { padding: 20px 14px 48px; }
  .dash-ph-title { font-size: 28px; }
}
@media (max-width: 420px)  { .stat-row { grid-template-columns: 1fr; } }
</style>

<div class="shell">
  <?php include __DIR__ . '../sidebar.php'; ?>

  <div class="dash-wrap">

    <!-- Page header -->
    <div class="dash-ph">
      <div class="dash-ph-ey"><?= $date_label ?></div>
      <div class="dash-ph-title"><?= $greeting ?>, <em><?= htmlspecialchars($first_name) ?></em></div>
    </div>

    <!-- Live banner -->
    <div class="live-banner">
      <div class="live-l">
        <div class="live-dot"></div>
        <div>
          <div class="live-title">Show live — Summerset Corporate Gala</div>
          <div class="live-sub">Sarah M. calling · Cue Q14 of 23 · 5 crew connected · Grand Ballroom, Las Vegas</div>
        </div>
      </div>
      <a class="btn-danger" href="/overview/production.php?id=gala&tab=ros">Open run of show →</a>
    </div>

    <!-- Stat cards -->
    <div class="stat-row">
      <a class="stat" href="/overview/productions.php">
        <div class="stat-lbl">Active productions</div>
        <div class="stat-val">3</div>
        <div class="stat-sub up">+1 this month</div>
      </a>
      <a class="stat" href="/overview/projects.php">
        <div class="stat-lbl">Active projects</div>
        <div class="stat-val">2</div>
        <div class="stat-sub">Long-horizon builds</div>
      </a>
      <a class="stat" href="/overview/tasks.php">
        <div class="stat-lbl">Tasks due this week</div>
        <div class="stat-val">11</div>
        <div class="stat-sub warn">3 overdue</div>
      </a>
      <a class="stat" href="/overview/production.php?id=summit">
        <div class="stat-lbl">Next show</div>
        <div class="stat-val">96</div>
        <div class="stat-sub">days · Tech Summit</div>
      </a>
    </div>

    <!-- Morning briefing -->
    <div class="briefing-hd">Today's briefing</div>
    <div class="briefing-grid">
      <a class="brief-card brief-urgent" href="/overview/tasks.php">
        <div class="brief-card-ey">⚠ Needs attention</div>
        <div class="brief-card-title">3 overdue tasks</div>
        <div class="brief-card-sub">AV contract · Permit application · Insurance certs — all past due</div>
      </a>
      <a class="brief-card" href="/overview/production.php?id=gala&tab=callsheet">
        <div class="brief-card-ey">Tonight · Summerset Gala</div>
        <div class="brief-card-title">Crew call 3:00 PM · Show 6:30 PM</div>
        <div class="brief-card-sub">Grand Ballroom, Las Vegas · 340 guests · SM: Sarah M.</div>
      </a>
      <a class="brief-card" href="/overview/calendar.php">
        <div class="brief-card-ey">This week</div>
        <div class="brief-card-title">Load-in Jun 12 · Tech Jun 13</div>
        <div class="brief-card-sub">Strike Jun 15 · Milestone Jun 22 · Tech Summit 96 days out</div>
      </a>
    </div>

    <!-- Main grid -->
    <div class="dash-grid">

      <!-- LEFT COLUMN -->
      <div class="dash-col">

        <!-- Projects & Productions -->
        <div class="card">
          <div class="chd">
            <div class="cht">Projects &amp; productions</div>
            <div style="display:flex;gap:6px">
              <a class="btn-ghost-sm" href="/overview/projects.php">Projects →</a>
              <a class="btn-ghost-sm" href="/overview/productions.php">Productions →</a>
            </div>
          </div>
          <div class="section-divider"><div class="section-divider-label">Projects</div></div>
          <a class="work-row" href="/overview/project.php?id=sphere">
            <div class="work-icon" style="background:var(--teal-dim)">🏗</div>
            <div class="work-info">
              <div class="work-name">Sphere Residency Build — Phase 2</div>
              <div class="work-meta"><span>Las Vegas · Q3 2026</span><span>48% complete</span></div>
            </div>
            <div class="work-right"><span class="pill pill-teal">In progress</span></div>
          </a>
          <a class="work-row" href="/overview/project.php?id=venue">
            <div class="work-icon" style="background:var(--teal-dim)">🏗</div>
            <div class="work-info">
              <div class="work-name">Downtown Venue Buildout — AV Infrastructure</div>
              <div class="work-meta"><span>Chicago · Jun 2026</span><span>18% complete</span></div>
            </div>
            <div class="work-right"><span class="pill pill-plan">Planning</span></div>
          </a>
          <div class="section-divider" style="border-top:.5px solid var(--border)"><div class="section-divider-label">Productions</div></div>
          <a class="work-row" href="/overview/production.php?id=gala">
            <div class="work-icon" style="background:var(--live-dim)">🎭</div>
            <div class="work-info">
              <div class="work-name">Summerset Corporate Gala</div>
              <div class="work-meta"><span>Grand Ballroom, LV</span><span>340 guests</span><span>Jun 14</span></div>
            </div>
            <div class="work-right"><span class="pill pill-live">Live tonight</span></div>
          </a>
          <a class="work-row" href="/overview/production.php?id=summit">
            <div class="work-icon" style="background:var(--blue-dim)">🎭</div>
            <div class="work-info">
              <div class="work-name">Tech Summit 2025</div>
              <div class="work-meta"><span>Convention Center, Austin</span><span>1,200 guests</span><span>Aug 3</span></div>
            </div>
            <div class="work-right">
              <span class="pill pill-plan">Planning</span>
              <div class="work-date">96 days</div>
            </div>
          </a>
          <a class="work-row" href="/overview/production.php?id=dinner">
            <div class="work-icon" style="background:var(--dark-3)">🎭</div>
            <div class="work-info">
              <div class="work-name">Meridian Holiday Dinner</div>
              <div class="work-meta"><span>The Rooftop, LA</span><span>120 guests</span><span>Dec 5</span></div>
            </div>
            <div class="work-right"><span class="pill pill-draft">Draft</span></div>
          </a>
        </div>

        <!-- Tasks -->
        <div class="card">
          <div class="chd">
            <div class="cht">Open tasks</div>
            <a class="cha" href="/overview/tasks.php">View all →</a>
          </div>
          <div class="task-row">
            <div class="chk" onclick="toggleTask(this)"></div>
            <div class="tb"><div class="tn">Confirm AV vendor contract</div><div class="tr-ref">Tech Summit 2025 · Production</div></div>
            <div class="td over">Apr 27</div>
          </div>
          <div class="task-row">
            <div class="chk" onclick="toggleTask(this)"></div>
            <div class="tb"><div class="tn">Submit permit application — loading dock</div><div class="tr-ref">Sphere Residency · Project</div></div>
            <div class="td over">Apr 28</div>
          </div>
          <div class="task-row">
            <div class="chk" onclick="toggleTask(this)"></div>
            <div class="tb"><div class="tn">Send runsheet to venue coordinator</div><div class="tr-ref">Summerset Gala · Production</div></div>
            <div class="td">May 3</div>
          </div>
          <div class="task-row">
            <div class="chk done" onclick="toggleTask(this)"></div>
            <div class="tb"><div class="tn done">Finalize menu selections</div><div class="tr-ref">Summerset Gala</div></div>
            <div class="td">Apr 22</div>
          </div>
          <div class="task-row">
            <div class="chk" onclick="toggleTask(this)"></div>
            <div class="tb"><div class="tn">Review structural engineering report</div><div class="tr-ref">Downtown Venue · Project</div></div>
            <div class="td">May 8</div>
          </div>
        </div>

        <!-- Activity -->
        <div class="card">
          <div class="chd"><div class="cht">Recent activity</div></div>
          <div class="act-row">
            <div class="act-av" style="background:var(--live-dim);color:#FF7043">SM</div>
            <div>
              <div class="act-text"><strong>Sarah M.</strong> started the run of show <span class="act-ref">· Summerset Gala</span></div>
              <div class="act-time">1h ago</div>
            </div>
          </div>
          <div class="act-row">
            <div class="act-av" style="background:var(--blue-dim);color:#5B9FE0">TR</div>
            <div>
              <div class="act-text"><strong>Tom R.</strong> uploaded AV contract PDF <span class="act-ref">· Summerset Gala</span></div>
              <div class="act-time">2h ago</div>
            </div>
          </div>
          <div class="act-row">
            <div class="act-av" style="background:var(--green-dim);color:#66BB6A">ML</div>
            <div>
              <div class="act-text"><strong>Mara L.</strong> completed 3 tasks <span class="act-ref">· Tech Summit</span></div>
              <div class="act-time">3h ago</div>
            </div>
          </div>
          <div class="act-row">
            <div class="act-av" style="background:var(--teal-dim);color:#4DB6AC">DK</div>
            <div>
              <div class="act-text"><strong>Dana K.</strong> added milestone <span class="act-ref">· Downtown Venue</span></div>
              <div class="act-time">Yesterday</div>
            </div>
          </div>
        </div>

      </div><!-- /left col -->

      <!-- RIGHT COLUMN -->
      <div class="dash-col">

        <!-- Next show -->
        <div class="card">
          <div class="ns-hd">
            <div class="ns-ey">Next production</div>
            <div class="ns-name">Tech Summit 2025</div>
            <div class="ns-date">Aug 3, 2025 · Austin, TX</div>
          </div>
          <div class="ns-body">
            <div class="ns-row"><span class="ns-lbl">Venue</span><span class="ns-val">Convention Center</span></div>
            <div class="ns-row"><span class="ns-lbl">Guests</span><span class="ns-val">1,200</span></div>
            <div class="ns-row"><span class="ns-lbl">Budget</span><span class="ns-val">$142,000</span></div>
            <div class="ns-row"><span class="ns-lbl">Days out</span><span class="ns-val">96</span></div>
            <div class="prog-wrap">
              <div class="prog-lbl"><span>Pre-production</span><span>34%</span></div>
              <div class="prog-bar"><div class="prog-fill" style="width:34%"></div></div>
            </div>
            <a class="btn-outline w-full" href="/overview/production.php?id=summit" style="justify-content:center;margin-top:12px;font-size:11px">Open workspace →</a>
          </div>
        </div>

        <!-- Messages -->
        <div class="card">
          <div class="chd">
            <div class="cht">Messages</div>
            <a class="cha" href="/overview/messages.php">All messages →</a>
          </div>
          <a class="work-row" href="/overview/messages.php?chat=gala-team" style="align-items:center;background:rgba(232,184,75,.04)">
            <div class="work-icon" style="background:var(--live-dim);font-size:16px">🎭</div>
            <div class="work-info">
              <div class="work-name" style="font-size:12px">Summerset Gala — Team</div>
              <div class="work-meta">Sarah M.: ROS loaded and ready, cue 1 standby</div>
            </div>
            <div class="work-right">
              <div style="font-size:10px;color:var(--text-3)">8m ago</div>
              <div style="width:8px;height:8px;border-radius:50%;background:var(--gold)"></div>
            </div>
          </a>
          <a class="work-row" href="/overview/messages.php?chat=tom" style="align-items:center;background:rgba(232,184,75,.04)">
            <div class="work-icon" style="background:var(--blue-dim);font-size:11px;font-weight:600;color:#5B9FE0">TR</div>
            <div class="work-info">
              <div class="work-name" style="font-size:12px">Tom R.</div>
              <div class="work-meta">Did you see the updated AV spec from Apex?</div>
            </div>
            <div class="work-right">
              <div style="font-size:10px;color:var(--text-3)">22m ago</div>
              <div style="width:8px;height:8px;border-radius:50%;background:var(--gold)"></div>
            </div>
          </a>
          <a class="work-row" href="/overview/messages.php?chat=summit-team" style="align-items:center">
            <div class="work-icon" style="background:var(--blue-dim);font-size:16px">🎭</div>
            <div class="work-info">
              <div class="work-name" style="font-size:12px">Tech Summit — Team</div>
              <div class="work-meta">You: Shared the updated venue contact sheet</div>
            </div>
            <div class="work-right"><div style="font-size:10px;color:var(--text-3)">2h ago</div></div>
          </a>
          <a class="work-row" href="/overview/messages.php?chat=sarah" style="align-items:center">
            <div class="work-icon" style="background:var(--live-dim);font-size:11px;font-weight:600;color:#FF7043">SM</div>
            <div class="work-info">
              <div class="work-name" style="font-size:12px">Sarah M.</div>
              <div class="work-meta">Crew call confirmed for 3pm. All good.</div>
            </div>
            <div class="work-right"><div style="font-size:10px;color:var(--text-3)">3h ago</div></div>
          </a>
        </div>

        <!-- Mini calendar -->
        <div class="card">
          <div class="chd">
            <div class="cht">June 2025</div>
            <a class="cha" href="/overview/calendar.php">Full calendar →</a>
          </div>
          <div class="mini-cal">
            <div class="mc-grid">
              <div class="mc-dow">S</div><div class="mc-dow">M</div><div class="mc-dow">T</div><div class="mc-dow">W</div><div class="mc-dow">T</div><div class="mc-dow">F</div><div class="mc-dow">S</div>
              <div class="mc-d other">1</div><div class="mc-d other">2</div><div class="mc-d other">3</div><div class="mc-d other">4</div><div class="mc-d other">5</div><div class="mc-d other">6</div><div class="mc-d other">7</div>
              <div class="mc-d">8</div><div class="mc-d">9</div><div class="mc-d">10</div><div class="mc-d">11</div>
              <a class="mc-d ev" href="/overview/calendar.php">12</a>
              <a class="mc-d ev" href="/overview/calendar.php">13</a>
              <a class="mc-d show-day" href="/overview/production.php?id=gala">14</a>
              <a class="mc-d ev" href="/overview/calendar.php">15</a>
              <div class="mc-d">16</div><div class="mc-d">17</div><div class="mc-d">18</div><div class="mc-d">19</div><div class="mc-d">20</div><div class="mc-d">21</div>
              <div class="mc-d">22</div><div class="mc-d">23</div><div class="mc-d">24</div><div class="mc-d">25</div><div class="mc-d">26</div><div class="mc-d">27</div><div class="mc-d">28</div>
              <div class="mc-d">29</div><div class="mc-d">30</div><div class="mc-d other">1</div><div class="mc-d other">2</div><div class="mc-d other">3</div><div class="mc-d other">4</div><div class="mc-d other">5</div>
            </div>
          </div>
        </div>

        <!-- Upcoming -->
        <div class="card">
          <div class="chd">
            <div class="cht">Upcoming</div>
            <a class="cha" href="/overview/calendar.php">Full calendar →</a>
          </div>
          <div class="up-row"><div class="up-date"><div class="up-d">12</div><div class="up-m">Jun</div></div><div class="up-div"></div><div class="up-info"><div class="up-name">Load-in — Summerset Gala</div><div class="up-detail">All crew · 8:00 AM · Las Vegas</div></div></div>
          <div class="up-row"><div class="up-date"><div class="up-d">14</div><div class="up-m">Jun</div></div><div class="up-div"></div><div class="up-info"><div class="up-name">Show — Summerset Gala</div><div class="up-detail">Crew 3pm · Show 6:30pm</div></div></div>
          <div class="up-row"><div class="up-date"><div class="up-d">15</div><div class="up-m">Jun</div></div><div class="up-div"></div><div class="up-info"><div class="up-name">Strike — Summerset Gala</div><div class="up-detail">All crew · 8:00 AM</div></div></div>
          <div class="up-row"><div class="up-date"><div class="up-d">22</div><div class="up-m">Jun</div></div><div class="up-div"></div><div class="up-info"><div class="up-name">Milestone — Sphere Build</div><div class="up-detail">AV infrastructure sign-off</div></div></div>
          <div class="up-row"><div class="up-date"><div class="up-d">3</div><div class="up-m">Aug</div></div><div class="up-div"></div><div class="up-info"><div class="up-name">Tech Summit 2025</div><div class="up-detail">Convention Center · Austin TX</div></div></div>
        </div>

        <!-- Quick actions -->
        <div class="card">
          <div class="chd"><div class="cht">Quick actions</div></div>
          <div class="qa-grid">
            <a class="qa-btn" href="/overview/new-production.php"><div class="qa-icon">🎭</div><div class="qa-lbl">New production</div><div class="qa-sub">Start from scratch</div></a>
            <a class="qa-btn" href="/overview/new-project.php"><div class="qa-icon">🏗</div><div class="qa-lbl">New project</div><div class="qa-sub">Long-term build</div></a>
            <a class="qa-btn" href="/overview/production.php?id=gala&tab=ros"><div class="qa-icon">▶</div><div class="qa-lbl">Open ROS</div><div class="qa-sub">Summerset Gala · Live</div></a>
            <a class="qa-btn" href="/overview/messages.php"><div class="qa-icon">💬</div><div class="qa-lbl">Messages</div><div class="qa-sub">4 unread</div></a>
          </div>
        </div>

      </div><!-- /right col -->
    </div><!-- /dash-grid -->

  </div><!-- /dash-wrap -->
</div><!-- /shell -->

<script>
function toggleTask(el) {
  el.classList.toggle('done');
  const tn = el.closest('.task-row').querySelector('.tn');
  if (tn) tn.classList.toggle('done');
}
</script>

<script>window.PC_PAGE = 'dashboard';</script>
<script src="/../js/nav.js"></script>

<?php include ROOT_PATH . '/required/footer.php'; ?>
