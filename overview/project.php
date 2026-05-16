<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_login();

$id = $_GET['id'] ?? 'sphere';
$tab = $_GET['tab'] ?? 'overview';

$projects = [
  'sphere' => ['name'=>'SPHERE RESIDENCY BUILD', 'sub'=>'Phase 2', 'location'=>'Las Vegas, NV', 'dates'=>'Mar 2025 – Sep 2026', 'team'=>'8 team members', 'budget'=>'$2.4M budget', 'pill'=>'pill-teal', 'pill_text'=>'In progress', 'color'=>'#4DB6AC'],
  'venue'  => ['name'=>'DOWNTOWN VENUE BUILDOUT', 'sub'=>'AV Infrastructure', 'location'=>'Chicago, IL', 'dates'=>'Jan 2026 – Jun 2026', 'team'=>'12 team members', 'budget'=>'$840K budget', 'pill'=>'pill-plan', 'pill_text'=>'Planning', 'color'=>'#5B9FE0'],
];
$proj = $projects[$id] ?? $projects['sphere'];
$page_title = ucwords(strtolower($proj['name']));
include __DIR__ . '/../header.php';
?>
<style>
main { padding-top:0; background:var(--black); min-height:calc(100vh - var(--nav-h)); }
.app-wrap { max-width:1200px; margin:0 auto; padding:20px 24px 60px; }
.tab-content { display:none; } .tab-content.active { display:block; }
/* Hero */
.proj-hero { background:var(--dark); border:.5px solid var(--border); border-radius:10px; padding:22px 24px; margin-bottom:16px; }
.proj-hero-ey   { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); margin-bottom:6px; }
.proj-hero-name { font-family:var(--disp); font-size:28px; letter-spacing:1.5px; color:var(--text-1); margin-bottom:4px; line-height:1; }
.proj-hero-sub  { font-size:12px; color:var(--text-3); margin-bottom:10px; }
.proj-hero-meta { display:flex; align-items:center; gap:16px; flex-wrap:wrap; font-size:12px; color:var(--text-3); }
.proj-hero-item strong { color:var(--text-1); }
/* Tabs */
.prod-tabs { display:flex; gap:0; border-bottom:.5px solid var(--border); margin-bottom:20px; overflow-x:auto; }
.pt { background:transparent; border:none; border-bottom:2px solid transparent; color:var(--text-3); font-family:var(--sans); font-size:12px; padding:9px 16px; cursor:pointer; white-space:nowrap; transition:all .15s; margin-bottom:-1px; }
.pt:hover { color:var(--text-1); }
.pt.active { color:var(--gold); border-bottom-color:var(--gold); }
/* Cards */
.card { background:var(--dark); border:.5px solid var(--border); border-radius:10px; overflow:hidden; margin-bottom:14px; }
.chd  { display:flex; align-items:center; justify-content:space-between; padding:13px 18px; border-bottom:.5px solid var(--border); }
.cht  { font-size:12px; font-weight:600; color:var(--text-1); }
.cha  { font-size:11px; color:var(--text-3); text-decoration:none; cursor:pointer; background:transparent; border:none; transition:color .15s; }
.cha:hover { color:var(--gold); }
/* Grid */
.grid-2 { display:grid; grid-template-columns:1fr 260px; gap:16px; align-items:start; }
/* Task rows */
.task-row { display:flex; align-items:flex-start; gap:12px; padding:11px 18px; border-bottom:.5px solid var(--border); }
.task-row:last-child { border-bottom:none; }
.chk { width:16px; height:16px; border-radius:4px; border:.5px solid var(--border); flex-shrink:0; cursor:pointer; margin-top:2px; transition:all .15s; display:flex; align-items:center; justify-content:center; }
.chk:hover { border-color:var(--gold); }
.chk.done { background:rgba(76,175,80,.2); border-color:rgba(76,175,80,.4); }
.chk.done::after { content:'✓'; font-size:9px; color:#4caf50; }
.tb { flex:1; min-width:0; }
.tn  { font-size:12px; color:var(--text-1); }
.tn.done { text-decoration:line-through; color:var(--text-3); }
.tr-ref { font-size:10px; color:var(--text-3); margin-top:2px; }
.td  { font-size:11px; color:var(--text-3); flex-shrink:0; }
.td.over { color:#ff6b6b; }
/* Milestones */
.milestone-row { display:flex; align-items:flex-start; gap:14px; padding:13px 18px; border-bottom:.5px solid var(--border); }
.milestone-row:last-child { border-bottom:none; }
.milestone-icon { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; margin-top:1px; }
.milestone-name   { font-size:13px; font-weight:500; margin-bottom:3px; color:var(--text-1); }
.milestone-detail { font-size:11px; color:var(--text-3); }
.milestone-date   { font-size:11px; flex-shrink:0; color:var(--text-3); }
/* Activity */
.act-row { display:flex; align-items:flex-start; gap:12px; padding:11px 18px; border-bottom:.5px solid var(--border); }
.act-row:last-child { border-bottom:none; }
.act-av  { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; }
.act-text { font-size:12px; color:var(--text-2); line-height:1.5; }
.act-time { font-size:10px; color:var(--text-3); margin-top:3px; }
/* Pills */
.pill { font-size:10px; letter-spacing:.5px; padding:2px 8px; border-radius:4px; font-weight:600; white-space:nowrap; }
.pill-live  { background:rgba(232,53,10,.12);  color:#E8350A; border:.5px solid rgba(232,53,10,.3); }
.pill-plan  { background:rgba(91,159,224,.10); color:#5B9FE0; border:.5px solid rgba(91,159,224,.25); }
.pill-draft { background:rgba(255,255,255,.06); color:var(--text-3); border:.5px solid var(--border); }
.pill-gold  { background:rgba(232,184,75,.12); color:var(--gold); border:.5px solid var(--gold-bd); }
.pill-green { background:rgba(76,175,80,.12);  color:#66BB6A; border:.5px solid rgba(76,175,80,.25); }
.pill-teal  { background:rgba(0,200,212,.10);  color:#00c8d4; border:.5px solid rgba(0,200,212,.25); }
/* Gantt */
.gantt-wrap { overflow-x:auto; padding:18px; }
.gantt { min-width:640px; }
.gantt-hdr { display:flex; padding-left:180px; margin-bottom:8px; }
.gantt-month { flex:1; font-size:10px; text-transform:uppercase; letter-spacing:1px; color:var(--text-3); text-align:center; }
.gantt-row { display:flex; align-items:center; gap:10px; margin-bottom:6px; }
.gantt-lbl { width:160px; flex-shrink:0; font-size:11px; color:var(--text-2); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.gantt-lbl.phase { font-size:12px; font-weight:500; color:var(--text-1); }
.gantt-lbl.sub { padding-left:14px; font-size:11px; color:var(--text-3); }
.gantt-track { flex:1; height:22px; background:var(--dark-3); border-radius:4px; position:relative; overflow:hidden; }
.gantt-track.tall { height:28px; }
.gantt-bar { position:absolute; height:100%; border-radius:4px; display:flex; align-items:center; padding:0 8px; font-size:10px; color:rgba(255,255,255,.75); white-space:nowrap; }
.gantt-today { position:absolute; top:0; bottom:0; width:1px; background:var(--gold); opacity:.6; }
/* Progress */
.prog-bar  { background:var(--dark-2); border-radius:4px; height:5px; overflow:hidden; }
.prog-fill { height:100%; border-radius:4px; }
/* Messages */
.msg-messages { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:12px; }
.msg-date-divider { text-align:center; font-size:10px; color:var(--text-3); }
.msg-bubble-wrap { display:flex; align-items:flex-end; gap:8px; }
.msg-bubble-wrap.mine { flex-direction:row-reverse; }
.msg-bubble-col { display:flex; flex-direction:column; gap:3px; max-width:75%; }
.msg-bubble { padding:10px 14px; border-radius:12px; font-size:13px; line-height:1.55; }
.msg-bubble.other { background:var(--dark-2); color:var(--text-1); border-radius:4px 12px 12px 12px; }
.msg-bubble.mine  { background:rgba(232,184,75,.15); color:var(--text-1); border:.5px solid var(--gold-bd); border-radius:12px 4px 12px 12px; }
.msg-sender-name  { font-size:10px; color:var(--text-3); margin-bottom:2px; }
.msg-bubble-time  { font-size:10px; color:var(--text-3); }
.mine .msg-bubble-time { text-align:right; }
.msg-ref-picker { display:flex; gap:6px; flex-wrap:wrap; }
.msg-ref-chip { font-size:11px; padding:4px 10px; border:.5px solid var(--border); border-radius:6px; color:var(--text-3); cursor:pointer; transition:all .12s; }
.msg-ref-chip:hover { border-color:var(--gold-bd); color:var(--gold); }
.msg-input-area { padding:12px 16px; border-top:.5px solid var(--border); flex-shrink:0; }
.msg-input-row { display:flex; gap:10px; align-items:flex-end; }
.msg-input-wrap { flex:1; background:var(--dark-2); border:.5px solid var(--border); border-radius:10px; display:flex; align-items:center; padding:0 10px; }
.msg-input { flex:1; background:transparent; border:none; color:var(--text-1); font-family:var(--sans); font-size:13px; padding:10px 6px; outline:none; resize:none; max-height:100px; }
.msg-input::placeholder { color:var(--text-3); }
.msg-send-btn { width:36px; height:36px; border-radius:50%; background:var(--gold); border:none; cursor:pointer; font-size:16px; color:#000; font-weight:700; flex-shrink:0; }
/* Row clickable */
.row { display:flex; align-items:center; gap:12px; padding:10px 18px; border-bottom:.5px solid var(--border); font-size:12px; color:var(--text-2); }
.row:last-child { border-bottom:none; }
.row.clickable { cursor:pointer; transition:background .12s; text-decoration:none; }
.row.clickable:hover { background:var(--dark-2); color:var(--text-1); }
/* Buttons */
.btn-back { font-family:var(--sans); font-size:12px; color:var(--text-3); background:transparent; border:.5px solid var(--border); padding:6px 14px; border-radius:6px; text-decoration:none; transition:all .15s; display:inline-block; margin-bottom:16px; }
.btn-back:hover { color:var(--text-1); }
.btn-sm { font-family:var(--sans); font-size:11px; padding:6px 12px; border-radius:5px; border:.5px solid var(--border); background:transparent; color:var(--text-3); cursor:pointer; transition:all .15s; text-decoration:none; display:inline-block; }
.btn-sm:hover { color:var(--text-1); }
.btn-gold-sm { font-family:var(--sans); font-size:11px; padding:6px 12px; border-radius:5px; background:rgba(232,184,75,.15); color:var(--gold); border:.5px solid var(--gold-bd); cursor:pointer; transition:all .15s; }
.btn-gold-sm:hover { background:rgba(232,184,75,.25); }
.section-divider { padding:6px 18px; border-bottom:.5px solid var(--border); }
.section-divider-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); }
@media(max-width:900px) { .grid-2 { grid-template-columns:1fr; } }
</style>

<div class="app-wrap">
  <a class="btn-back" href="/overview/projects.php">← Projects</a>

  <!-- Hero -->
  <div class="proj-hero">
    <div class="proj-hero-ey">Project workspace</div>
    <div class="proj-hero-name"><?php echo htmlspecialchars($proj['name']); ?></div>
    <div class="proj-hero-sub"><?php echo e($proj['sub']); ?></div>
    <div class="proj-hero-meta">
      <div class="proj-hero-item">📍 <strong><?php echo e($proj['location']); ?></strong></div>
      <div class="proj-hero-item">🗓 <strong><?php echo e($proj['dates']); ?></strong></div>
      <div class="proj-hero-item">👥 <strong><?php echo e($proj['team']); ?></strong></div>
      <div class="proj-hero-item">💰 <strong><?php echo e($proj['budget']); ?></strong></div>
      <span class="pill <?php echo e($proj['pill']); ?>"><?php echo e($proj['pill_text']); ?></span>
    </div>
  </div>

  <!-- Tabs -->
  <div class="prod-tabs" id="proj-tab-bar">
    <button class="pt active" onclick="switchTab('overview',this)">Overview</button>
    <button class="pt" onclick="switchTab('gantt',this)">Gantt chart</button>
    <button class="pt" onclick="switchTab('tasks',this)">Tasks</button>
    <button class="pt" onclick="switchTab('milestones',this)">Milestones</button>
    <button class="pt" onclick="switchTab('messages',this)">Messages</button>
    <button class="pt" onclick="switchTab('team',this)">Team</button>
    <button class="pt" onclick="switchTab('budget',this)">Budget</button>
    <button class="pt" onclick="switchTab('vendors',this)">Vendors</button>
    <button class="pt" onclick="switchTab('files',this)">Files</button>
  </div>

  <!-- OVERVIEW -->
  <div id="tab-overview" class="tab-content active">
    <div class="grid-2">
      <div>
        <!-- Gantt preview -->
        <div class="card">
          <div class="chd"><div class="cht">Gantt overview</div><button class="cha" onclick="switchTab('gantt')">Full chart →</button></div>
          <div class="gantt-wrap">
            <div class="gantt">
              <div class="gantt-hdr"><div class="gantt-month">Apr</div><div class="gantt-month">May</div><div class="gantt-month">Jun</div><div class="gantt-month">Jul</div><div class="gantt-month">Aug</div><div class="gantt-month">Sep</div></div>
              <div class="gantt-row"><div class="gantt-lbl">AV infrastructure</div><div class="gantt-track"><div class="gantt-bar" style="left:0;width:55%;background:#4DB6AC">In progress · 68%</div><div class="gantt-today" style="left:20%"></div></div></div>
              <div class="gantt-row"><div class="gantt-lbl">Rigging system</div><div class="gantt-track"><div class="gantt-bar" style="left:10%;width:45%;background:#2A8C7A">In progress · 42%</div></div></div>
              <div class="gantt-row"><div class="gantt-lbl">Control room</div><div class="gantt-track"><div class="gantt-bar" style="left:30%;width:50%;background:var(--dark-3);color:var(--text-3)">Upcoming · 12%</div></div></div>
              <div class="gantt-row"><div class="gantt-lbl">Commissioning</div><div class="gantt-track"><div class="gantt-bar" style="left:70%;width:28%;background:var(--dark-3);color:var(--text-3)">Q3 2025</div></div></div>
            </div>
          </div>
        </div>
        <!-- Tasks -->
        <div class="card">
          <div class="chd"><div class="cht">Open tasks</div><button class="cha" onclick="switchTab('tasks')">View all →</button></div>
          <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Submit permit application — loading dock</div><div class="tr-ref">AV Infrastructure · Tom R.</div></div><div class="td over">Apr 28</div></div>
          <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Review structural engineering report</div><div class="tr-ref">Rigging System · Dana K.</div></div><div class="td">May 8</div></div>
          <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Finalize cable routing plan with architect</div><div class="tr-ref">Control Room · Jordan K.</div></div><div class="td">May 15</div></div>
          <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Order Dante switches — 48-port PoE</div><div class="tr-ref">AV Infrastructure · Unassigned</div></div><div class="td">May 20</div></div>
          <div class="task-row"><div class="chk done" onclick="toggleTask(this)"></div><div class="tb"><div class="tn done">Sign Dante network design contract</div><div class="tr-ref">Completed Apr 10</div></div></div>
        </div>
        <!-- Activity -->
        <div class="card">
          <div class="chd"><div class="cht">Recent activity</div></div>
          <div class="act-row"><div class="act-av" style="background:rgba(91,159,224,.15);color:#5B9FE0">TR</div><div><div class="act-text"><strong>Tom R.</strong> uploaded AV spec sheet</div><div class="act-time">14 min ago</div></div></div>
          <div class="act-row"><div class="act-av" style="background:rgba(0,200,212,.15);color:#4DB6AC">DK</div><div><div class="act-text"><strong>Dana K.</strong> added milestone — Rigging sign-off</div><div class="act-time">Yesterday</div></div></div>
          <div class="act-row"><div class="act-av" style="background:rgba(179,157,219,.15);color:#B39DDB">JK</div><div><div class="act-text"><strong>Jordan K.</strong> updated budget — AV equipment line</div><div class="act-time">2 days ago</div></div></div>
        </div>
      </div>

      <!-- Right column -->
      <div>
        <!-- Progress -->
        <div class="card">
          <div class="chd"><div class="cht">Progress</div></div>
          <div style="padding:14px 18px;display:flex;flex-direction:column;gap:12px">
            <div><div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-3);margin-bottom:5px"><span>AV infrastructure</span><span>68%</span></div><div class="prog-bar"><div class="prog-fill" style="width:68%;background:#4DB6AC"></div></div></div>
            <div><div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-3);margin-bottom:5px"><span>Rigging system</span><span>42%</span></div><div class="prog-bar"><div class="prog-fill" style="width:42%;background:#2A8C7A"></div></div></div>
            <div><div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-3);margin-bottom:5px"><span>Control room</span><span>12%</span></div><div class="prog-bar"><div class="prog-fill" style="width:12%;background:#5B9FE0"></div></div></div>
            <div style="padding-top:8px;border-top:.5px solid var(--border)"><div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-2);margin-bottom:5px;font-weight:500"><span>Overall</span><span>48%</span></div><div class="prog-bar" style="height:5px"><div class="prog-fill" style="width:48%"></div></div></div>
          </div>
        </div>
        <!-- Milestones preview -->
        <div class="card">
          <div class="chd"><div class="cht">Milestones</div><button class="cha" onclick="switchTab('milestones')">View all →</button></div>
          <div class="milestone-row">
            <div class="milestone-icon" style="background:rgba(76,175,80,.15)">✅</div>
            <div style="flex:1"><div class="milestone-name">Design approved</div><div class="milestone-detail">AV system design signed off</div></div>
            <div class="milestone-date" style="color:#66BB6A">Mar 15</div>
          </div>
          <div class="milestone-row">
            <div class="milestone-icon" style="background:rgba(232,53,10,.15)">🔴</div>
            <div style="flex:1"><div class="milestone-name">Rigging install complete</div><div class="milestone-detail">All points tested & certified</div></div>
            <div class="milestone-date" style="color:#E8350A">Jun 1</div>
          </div>
          <div class="milestone-row">
            <div class="milestone-icon" style="background:var(--dark-3)">⬜</div>
            <div style="flex:1"><div class="milestone-name">AV infrastructure sign-off</div><div class="milestone-detail">Full system commissioning</div></div>
            <div class="milestone-date">Jun 22</div>
          </div>
        </div>
        <!-- Quick links -->
        <div class="card">
          <div class="chd"><div class="cht">Quick links</div></div>
          <div class="row clickable" onclick="switchTab('gantt')"><span>📊</span><span>Gantt chart</span></div>
          <div class="row clickable" onclick="switchTab('messages')"><span>💬</span><span>Team messages</span></div>
          <a class="row clickable" href="/overview/drive.php?folder=sphere"><span>📁</span><span>Project drive</span></a>
          <div class="row clickable" onclick="switchTab('budget')"><span>💰</span><span>Budget</span></div>
        </div>
      </div>
    </div>
  </div>

  <!-- GANTT -->
  <div id="tab-gantt" class="tab-content">
    <div class="card">
      <div class="chd">
        <div class="cht">Gantt chart — <?php echo e($proj['name']); ?></div>
        <div style="display:flex;gap:6px">
          <button class="btn-sm" onclick="addPhasePrompt()">+ Add phase</button>
          <button class="btn-sm" onclick="window.print()">Export</button>
        </div>
      </div>
      <div class="gantt-wrap">
        <div class="gantt" style="min-width:820px">
          <div class="gantt-hdr" style="padding-left:180px">
            <div class="gantt-month">Mar 25</div><div class="gantt-month">Apr 25</div><div class="gantt-month">May 25</div>
            <div class="gantt-month">Jun 25</div><div class="gantt-month">Jul 25</div><div class="gantt-month">Aug 25</div>
            <div class="gantt-month">Sep 25</div><div class="gantt-month">Q4 25</div>
          </div>
          <div style="height:8px"></div>
          <!-- AV Infrastructure -->
          <div class="gantt-row" style="margin-bottom:6px"><div class="gantt-lbl phase">AV Infrastructure</div><div class="gantt-track tall"><div class="gantt-bar" style="left:0;width:60%;background:#4DB6AC;font-size:11px">In progress · 68%</div><div class="gantt-today" style="left:16%"></div></div></div>
          <div class="gantt-row"><div class="gantt-lbl sub">→ Network design</div><div class="gantt-track"><div class="gantt-bar" style="left:0;width:25%;background:#2A9D8F">Complete</div></div></div>
          <div class="gantt-row"><div class="gantt-lbl sub">→ Cable install</div><div class="gantt-track"><div class="gantt-bar" style="left:20%;width:35%;background:#4DB6AC">In progress</div></div></div>
          <div class="gantt-row" style="margin-bottom:12px"><div class="gantt-lbl sub">→ Termination & test</div><div class="gantt-track"><div class="gantt-bar" style="left:50%;width:20%;background:var(--dark-3);color:var(--text-3)">Upcoming</div></div></div>
          <!-- Rigging -->
          <div class="gantt-row" style="margin-bottom:6px"><div class="gantt-lbl phase">Rigging System</div><div class="gantt-track tall"><div class="gantt-bar" style="left:8%;width:52%;background:#2A8C7A;font-size:11px">In progress · 42%</div></div></div>
          <div class="gantt-row"><div class="gantt-lbl sub">→ Engineering</div><div class="gantt-track"><div class="gantt-bar" style="left:8%;width:20%;background:#2A9D8F">Complete</div></div></div>
          <div class="gantt-row" style="margin-bottom:12px"><div class="gantt-lbl sub">→ Installation</div><div class="gantt-track"><div class="gantt-bar" style="left:25%;width:35%;background:#2A8C7A">In progress</div></div></div>
          <!-- Control room -->
          <div class="gantt-row" style="margin-bottom:12px"><div class="gantt-lbl phase">Control Room</div><div class="gantt-track tall"><div class="gantt-bar" style="left:33%;width:42%;background:#5B9FE0;font-size:11px">Planning · 12%</div></div></div>
          <!-- Commissioning -->
          <div class="gantt-row" style="margin-bottom:12px"><div class="gantt-lbl phase">Commissioning</div><div class="gantt-track tall"><div class="gantt-bar" style="left:72%;width:26%;background:var(--dark-3);color:var(--text-3);font-size:11px">Q3 2025</div></div></div>
          <!-- Today legend -->
          <div style="display:flex;align-items:center;gap:8px;padding-top:12px;border-top:.5px solid var(--border);padding-left:180px">
            <div style="width:1px;height:14px;background:var(--gold);opacity:.7"></div>
            <div style="font-size:11px;color:var(--text-3)">Today</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TASKS -->
  <div id="tab-tasks" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">All tasks</div><button class="btn-gold-sm">+ Add task</button></div>
      <div class="section-divider" style="background:rgba(232,53,10,.04)"><div class="section-divider-label" style="color:rgba(232,53,10,.5)">Overdue</div></div>
      <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Submit permit application — loading dock</div><div class="tr-ref">AV Infrastructure · Tom R.</div></div><div class="td over">Apr 28</div></div>
      <div class="section-divider" style="background:rgba(91,159,224,.04);border-top:.5px solid var(--border)"><div class="section-divider-label" style="color:rgba(91,159,224,.5)">This week</div></div>
      <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Review structural engineering report</div><div class="tr-ref">Rigging System · Dana K.</div></div><div class="td">May 8</div></div>
      <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Finalize cable routing plan with architect</div><div class="tr-ref">Control Room · Jordan K.</div></div><div class="td">May 15</div></div>
      <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Order Dante switches — 48-port PoE</div><div class="tr-ref">AV Infrastructure · Unassigned</div></div><div class="td">May 20</div></div>
      <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Schedule ETCP rigger certification review</div><div class="tr-ref">Rigging System · Tom R.</div></div><div class="td">May 25</div></div>
      <div class="section-divider" style="background:rgba(255,255,255,.02);border-top:.5px solid var(--border)"><div class="section-divider-label">Completed</div></div>
      <div class="task-row"><div class="chk done" onclick="toggleTask(this)"></div><div class="tb"><div class="tn done">Sign Dante network design contract</div><div class="tr-ref">Completed Apr 10</div></div></div>
      <div class="task-row"><div class="chk done" onclick="toggleTask(this)"></div><div class="tb"><div class="tn done">Finalize rigging point map</div><div class="tr-ref">Completed Mar 28</div></div></div>
      <div class="task-row"><div class="chk done" onclick="toggleTask(this)"></div><div class="tb"><div class="tn done">AV system design approved</div><div class="tr-ref">Completed Mar 15</div></div></div>
    </div>
  </div>

  <!-- MILESTONES -->
  <div id="tab-milestones" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Milestones</div><button class="btn-gold-sm">+ Add milestone</button></div>
      <div class="milestone-row"><div class="milestone-icon" style="background:rgba(76,175,80,.15)">✅</div><div style="flex:1"><div class="milestone-name">Design approved</div><div class="milestone-detail">Full AV system design signed off by client and integrator</div></div><div class="milestone-date" style="color:#66BB6A">Mar 15, 2025</div></div>
      <div class="milestone-row"><div class="milestone-icon" style="background:rgba(76,175,80,.15)">✅</div><div style="flex:1"><div class="milestone-name">Network contract signed</div><div class="milestone-detail">Dante networking scope and contract executed</div></div><div class="milestone-date" style="color:#66BB6A">Apr 10, 2025</div></div>
      <div class="milestone-row"><div class="milestone-icon" style="background:rgba(232,53,10,.15)">🔴</div><div style="flex:1"><div class="milestone-name">Rigging install complete</div><div class="milestone-detail">All rigging points installed, tested, and ETCP certified</div></div><div class="milestone-date" style="color:#E8350A">Jun 1, 2025</div></div>
      <div class="milestone-row"><div class="milestone-icon" style="background:var(--dark-3)">⬜</div><div style="flex:1"><div class="milestone-name">AV infrastructure sign-off</div><div class="milestone-detail">Full system commissioning and client acceptance</div></div><div class="milestone-date">Jun 22, 2025</div></div>
      <div class="milestone-row"><div class="milestone-icon" style="background:var(--dark-3)">⬜</div><div style="flex:1"><div class="milestone-name">Control room complete</div><div class="milestone-detail">Control room built out, all systems integrated and tested</div></div><div class="milestone-date">Aug 15, 2025</div></div>
      <div class="milestone-row"><div class="milestone-icon" style="background:var(--dark-3)">⬜</div><div style="flex:1"><div class="milestone-name">Full commissioning</div><div class="milestone-detail">End-to-end system test and final client sign-off</div></div><div class="milestone-date">Sep 22, 2025</div></div>
    </div>
  </div>

  <!-- MESSAGES -->
  <div id="tab-messages" class="tab-content">
    <div style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between">
      <div><div style="font-size:13px;font-weight:500">Sphere Build — Team chat</div><div style="font-size:12px;color:var(--text-3)">8 members · AV infrastructure, rigging, control room teams</div></div>
      <a class="btn-sm" href="/overview/messages.php">All messages →</a>
    </div>
    <div style="background:var(--dark-2);border:.5px solid var(--border);border-radius:12px;overflow:hidden;height:520px;display:flex;flex-direction:column">
      <div class="msg-messages" id="proj-msg-list">
        <div class="msg-date-divider">Yesterday</div>
        <div class="msg-bubble-wrap"><div style="width:24px;height:24px;border-radius:50%;background:rgba(0,200,212,.15);color:#4DB6AC;font-size:9px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-bottom:2px">DK</div><div class="msg-bubble-col"><div class="msg-sender-name">Dana K.</div><div class="msg-bubble other">Permit docs uploaded to drive. Waiting on the city to confirm the inspection date.</div><div class="msg-bubble-time">4:15 PM</div></div></div>
        <div class="msg-bubble-wrap mine"><div class="msg-bubble-col"><div class="msg-bubble mine">Thanks Dana. Let me know the moment that comes through.</div><div class="msg-bubble-time">4:30 PM</div></div></div>
        <div class="msg-date-divider">Today</div>
        <div class="msg-bubble-wrap"><div style="width:24px;height:24px;border-radius:50%;background:rgba(91,159,224,.15);color:#5B9FE0;font-size:9px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-bottom:2px">TR</div><div class="msg-bubble-col"><div class="msg-sender-name">Tom R.</div><div class="msg-bubble other">Updated AV spec sheet uploaded — revised speaker placement for Phase 2.</div><div class="msg-bubble-time">9:30 AM</div></div></div>
      </div>
      <div style="padding:6px 14px 4px;border-top:.5px solid var(--border)">
        <div class="msg-ref-picker">
          <div class="msg-ref-chip" onclick="switchTab('gantt')">📊 Gantt</div>
          <div class="msg-ref-chip" onclick="switchTab('tasks')">✅ Tasks</div>
          <div class="msg-ref-chip" onclick="switchTab('milestones')">🏁 Milestones</div>
          <div class="msg-ref-chip" onclick="switchTab('files')">📁 Files</div>
        </div>
      </div>
      <div class="msg-input-area"><div class="msg-input-row"><div class="msg-input-wrap"><textarea class="msg-input" id="proj-msg-input" placeholder="Message the team…" rows="1" onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendProjMsg()}" oninput="autoResize(this)"></textarea></div><button class="msg-send-btn" onclick="sendProjMsg()">↑</button></div></div>
    </div>
  </div>

  <!-- TEAM -->
  <div id="tab-team" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Project team</div><button class="btn-gold-sm">+ Add member</button></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(179,157,219,.15);color:#B39DDB;font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">JK</div><div class="tb"><div class="tn">Jordan Kim</div><div class="tr-ref">Lead producer · Admin access</div></div><span class="pill pill-gold">Admin</span></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(0,200,212,.15);color:#4DB6AC;font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">TR</div><div class="tb"><div class="tn">Tom R.</div><div class="tr-ref">AV integrator · Full access</div></div><span class="pill pill-teal">Full</span></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(91,159,224,.15);color:#5B9FE0;font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">DK</div><div class="tb"><div class="tn">Dana K.</div><div class="tr-ref">Structural engineer · View + comment</div></div><span class="pill pill-plan">View</span></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(76,175,80,.15);color:#66BB6A;font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">ML</div><div class="tb"><div class="tn">Mara L.</div><div class="tr-ref">Project coordinator · Full access</div></div><span class="pill pill-teal">Full</span></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(232,184,75,.15);color:var(--gold);font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">SC</div><div class="tb"><div class="tn">Sarah Chen</div><div class="tr-ref">Client · View only</div></div><span class="pill pill-draft">View</span></div>
    </div>
  </div>

  <!-- BUDGET -->
  <div id="tab-budget" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Budget overview</div><button class="btn-sm">Export</button></div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:14px 16px;border-bottom:.5px solid var(--border)">
        <div style="background:var(--dark-3);border-radius:8px;padding:13px"><div style="font-size:10px;color:var(--text-3);margin-bottom:4px">Total budget</div><div style="font-size:22px;font-weight:500">$2.4M</div></div>
        <div style="background:var(--dark-3);border-radius:8px;padding:13px"><div style="font-size:10px;color:var(--text-3);margin-bottom:4px">Spent to date</div><div style="font-size:22px;font-weight:500">$980K</div></div>
        <div style="background:var(--dark-3);border-radius:8px;padding:13px"><div style="font-size:10px;color:var(--text-3);margin-bottom:4px">Remaining</div><div style="font-size:22px;font-weight:500;color:#66BB6A">$1.42M</div></div>
      </div>
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead><tr style="background:var(--dark-3)"><th style="text-align:left;padding:9px 18px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-3);font-weight:500">Line item</th><th style="text-align:right;padding:9px 10px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-3);font-weight:500">Budgeted</th><th style="text-align:right;padding:9px 18px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-3);font-weight:500">Actual</th></tr></thead>
        <tbody>
          <tr style="border-bottom:.5px solid var(--border)"><td style="padding:10px 18px">AV equipment</td><td style="padding:10px;text-align:right">$1,200,000</td><td style="padding:10px 18px;text-align:right;color:#66BB6A">$580,000</td></tr>
          <tr style="border-bottom:.5px solid var(--border)"><td style="padding:10px 18px">Rigging & structural</td><td style="padding:10px;text-align:right">$480,000</td><td style="padding:10px 18px;text-align:right;color:#E8350A">$320,000</td></tr>
          <tr style="border-bottom:.5px solid var(--border)"><td style="padding:10px 18px">Labor & installation</td><td style="padding:10px;text-align:right">$520,000</td><td style="padding:10px 18px;text-align:right">$80,000</td></tr>
          <tr><td style="padding:10px 18px">Contingency (10%)</td><td style="padding:10px;text-align:right">$200,000</td><td style="padding:10px 18px;text-align:right;color:var(--text-3)">—</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- VENDORS -->
  <div id="tab-vendors" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Vendors & contractors</div><button class="btn-gold-sm">+ Add vendor</button></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">🔊</div><div class="tb"><div class="tn">Apex AV Integration</div><div class="tr-ref">AV systems · ★★★★★</div></div><span class="pill pill-green">Under contract</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">🏗</div><div class="tb"><div class="tn">SkyRig Structural</div><div class="tr-ref">Rigging & structural · ★★★★★</div></div><span class="pill pill-green">Under contract</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">🌐</div><div class="tb"><div class="tn">NetCore Systems</div><div class="tr-ref">Dante networking · ★★★★☆</div></div><span class="pill pill-green">Under contract</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">⚡</div><div class="tb"><div class="tn">PowerSpec Electric</div><div class="tr-ref">Electrical</div></div><span class="pill pill-draft">Quote pending</span></div>
    </div>
  </div>

  <!-- FILES -->
  <div id="tab-files" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Files</div><div style="display:flex;gap:8px"><a class="btn-sm" href="/overview/drive.php?folder=sphere">Open in Drive →</a><button class="btn-gold-sm">+ Upload</button></div></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">📄</div><div class="tb"><div class="tn">AV_System_Design_v3.pdf</div><div class="tr-ref">Tom R. · Apr 12</div></div><span class="pill pill-gold">Current</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">📊</div><div class="tb"><div class="tn">Rigging_Point_Map_Final.dwg</div><div class="tr-ref">Dana K. · Mar 28</div></div><span class="pill pill-green">Approved</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">📄</div><div class="tb"><div class="tn">Network_Contract_Signed.pdf</div><div class="tr-ref">Jordan K. · Apr 10</div></div></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">📊</div><div class="tb"><div class="tn">Budget_Sphere_v2.xlsx</div><div class="tr-ref">Jordan K. · Mar 20</div></div></div>
    </div>
  </div>

</div>

<script>
function switchTab(id, btn) {
  document.querySelectorAll('.tab-content').forEach(t=>t.classList.remove('active'));
  document.getElementById('tab-'+id)?.classList.add('active');
  document.querySelectorAll('#proj-tab-bar .pt').forEach(t=>t.classList.remove('active'));
  if (btn) { btn.classList.add('active'); }
  else {
    const labels={overview:'Overview',gantt:'Gantt chart',tasks:'Tasks',milestones:'Milestones',messages:'Messages',team:'Team',budget:'Budget',vendors:'Vendors',files:'Files'};
    document.querySelectorAll('#proj-tab-bar .pt').forEach(t=>{if(t.textContent===labels[id])t.classList.add('active');});
  }
}

const urlTab = new URLSearchParams(window.location.search).get('tab');
if (urlTab) switchTab(urlTab);

function toggleTask(el) { el.classList.toggle('done'); el.closest('.task-row')?.querySelector('.tn')?.classList.toggle('done'); }
function addPhasePrompt() { const n=prompt('New phase name:'); if(n) alert(`Phase "${n}" would be added to the Gantt.`); }
function sendProjMsg() {
  const input=document.getElementById('proj-msg-input'); if(!input)return;
  const text=input.value.trim(); if(!text)return;
  const list=document.getElementById('proj-msg-list'); if(!list)return;
  const wrap=document.createElement('div'); wrap.className='msg-bubble-wrap mine';
  wrap.innerHTML=`<div class="msg-bubble-col"><div class="msg-bubble mine">${text}</div><div class="msg-bubble-time">Just now</div></div>`;
  list.appendChild(wrap); list.scrollTop=list.scrollHeight;
  input.value=''; input.style.height='auto';
}
function autoResize(el) { el.style.height='auto'; el.style.height=Math.min(el.scrollHeight,100)+'px'; }
</script>

<?php include __DIR__ . '/../footer.php'; ?>
