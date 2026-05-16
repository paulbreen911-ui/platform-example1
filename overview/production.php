<?php



require_once __DIR__ . '/../config.php';

require_login();

$id = $_GET['id'] ?? 'gala';
$tab = $_GET['tab'] ?? 'overview';

$productions = [
  'gala'   => ['name'=>'SUMMERSET CORPORATE GALA', 'venue'=>'Grand Ballroom, Las Vegas NV', 'dates'=>'Jun 12–15, 2025', 'guests'=>'340 guests', 'budget'=>'$86,000', 'pill'=>'pill-live', 'pill_text'=>'Live tonight'],
  'summit' => ['name'=>'TECH SUMMIT 2025',          'venue'=>'Convention Center, Austin TX',  'dates'=>'Aug 1–4, 2025',  'guests'=>'1,200 guests','budget'=>'$142,000','pill'=>'pill-plan', 'pill_text'=>'Planning'],
  'dinner' => ['name'=>'MERIDIAN HOLIDAY DINNER',   'venue'=>'The Rooftop, Los Angeles CA',   'dates'=>'Dec 5, 2025',    'guests'=>'120 guests', 'budget'=>'$28,000', 'pill'=>'pill-draft','pill_text'=>'Draft'],
];
$prod = $productions[$id] ?? $productions['gala'];
$page_title = ucwords(strtolower($prod['name']));
require_once ROOT_PATH . '/functions.php';
include ROOT_PATH . '/required/header.php';

?>
<style>
main { padding-top:0; background:var(--black); min-height:calc(100vh - var(--nav-h)); }
.app-wrap { max-width:1200px; margin:0 auto; padding:20px 24px 60px; }
.tab-content { display:none; } .tab-content.active { display:block; }
/* Hero */
.prod-hero { background:var(--dark); border:.5px solid var(--border); border-radius:10px; padding:22px 24px; margin-bottom:16px; }
.prod-hero-ey   { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); margin-bottom:6px; }
.prod-hero-name { font-family:var(--disp); font-size:28px; letter-spacing:1.5px; color:var(--text-1); margin-bottom:10px; }
.prod-hero-meta { display:flex; align-items:center; gap:16px; flex-wrap:wrap; font-size:12px; color:var(--text-3); }
.prod-hero-item strong { color:var(--text-1); }
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
.pill-amber { background:rgba(255,179,0,.10);  color:#ffb300; border:.5px solid rgba(255,179,0,.25); }
/* Live banner */
.live-banner { background:rgba(232,53,10,.08); border:.5px solid rgba(232,53,10,.3); border-radius:10px; padding:14px 20px; display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:12px; }
.live-l { display:flex; align-items:center; gap:14px; }
.live-dot { width:10px; height:10px; border-radius:50%; background:#E8350A; animation:livepulse 1.6s ease-in-out infinite; flex-shrink:0; }
@keyframes livepulse { 0%,100%{box-shadow:0 0 0 3px rgba(232,53,10,.25)} 50%{box-shadow:0 0 0 7px rgba(232,53,10,.08)} }
.live-title { font-size:14px; font-weight:600; color:var(--text-1); }
.live-sub { font-size:11px; color:var(--text-3); margin-top:2px; }
.btn-danger { background:rgba(232,53,10,.15); border:.5px solid rgba(232,53,10,.4); color:#E8350A; font-family:var(--sans); font-size:11px; font-weight:600; padding:8px 16px; border-radius:6px; text-decoration:none; transition:all .15s; }
.btn-danger:hover { background:rgba(232,53,10,.25); }
/* ROS */
.ros-day-selector { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px; align-items:center; }
.ros-day-btn { background:transparent; border:.5px solid var(--border); color:var(--text-3); font-family:var(--sans); font-size:11px; padding:7px 14px; border-radius:6px; cursor:pointer; display:flex; align-items:center; gap:6px; transition:all .15s; }
.ros-day-btn:hover, .ros-day-btn.active { border-color:var(--gold-bd); color:var(--gold); }
.ros-day-btn.live-day.active { border-color:rgba(232,53,10,.4); color:#E8350A; background:rgba(232,53,10,.08); }
.ros-day-dot { width:6px; height:6px; border-radius:50%; background:currentColor; flex-shrink:0; }
.ros-toolbar { display:flex; align-items:center; gap:10px; margin-bottom:14px; flex-wrap:wrap; }
.ros-status { display:flex; align-items:center; gap:6px; font-size:11px; padding:4px 10px; border-radius:4px; }
.ros-status.live { background:rgba(232,53,10,.1); color:#E8350A; border:.5px solid rgba(232,53,10,.25); }
.ros-status .ros-dot { width:6px; height:6px; border-radius:50%; background:#E8350A; animation:livepulse 1.4s ease-in-out infinite; flex-shrink:0; }
.ros-status.pre { background:rgba(255,255,255,.05); color:var(--text-3); border:.5px solid var(--border); }
.ros-tbl { width:100%; border-collapse:collapse; font-size:12px; }
.ros-tbl th { background:var(--dark-2); padding:9px 12px; text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:1px; color:var(--text-3); font-weight:500; white-space:nowrap; }
.ros-tbl td { padding:10px 12px; border-bottom:.5px solid var(--border); color:var(--text-1); vertical-align:middle; }
.ros-tbl tr:last-child td { border-bottom:none; }
.ros-tbl tr:hover td { background:var(--dark-2); }
.ros-tbl tr.sec-row td { background:rgba(232,184,75,.04); padding:6px 12px; font-size:10px; letter-spacing:1px; text-transform:uppercase; color:rgba(232,184,75,.5); font-weight:600; }
.ros-add { padding:11px 18px; text-align:center; color:var(--text-3); font-size:12px; cursor:pointer; border-top:.5px solid var(--border); transition:background .12s; }
.ros-add:hover { background:var(--dark-2); }
.dept-tag { font-size:9px; padding:2px 6px; border-radius:3px; font-family:monospace; }
/* Call sheet */
.ns-hd { padding:16px 18px; border-bottom:.5px solid var(--border); }
.ns-ey { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); margin-bottom:5px; }
.ns-name { font-family:var(--disp); font-size:22px; letter-spacing:1px; color:var(--text-1); }
.ns-date { font-size:11px; color:var(--text-3); margin-top:4px; }
.ns-body { padding:14px 18px; }
.ns-row { display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:.5px solid var(--border); }
.ns-row:last-child { border-bottom:none; }
.ns-lbl { font-size:10px; letter-spacing:1px; text-transform:uppercase; color:var(--text-3); }
.ns-val { font-size:12px; color:var(--text-1); font-weight:500; }
/* Calendar */
.cal-grid-wrap { background:var(--dark); border:.5px solid var(--border); border-radius:10px; overflow:hidden; }
.cal-dow-row { display:grid; grid-template-columns:repeat(7,1fr); border-bottom:.5px solid var(--border); }
.cal-dow  { padding:9px; text-align:center; font-size:10px; letter-spacing:1px; text-transform:uppercase; color:var(--text-3); }
.cal-week { display:grid; grid-template-columns:repeat(7,1fr); border-bottom:.5px solid var(--border); }
.cal-week:last-child { border-bottom:none; }
.cal-day  { min-height:72px; padding:7px 5px; border-right:.5px solid var(--border); }
.cal-day:last-child { border-right:none; }
.cal-day.other-month { opacity:.3; }
.cal-day-num { font-size:11px; color:var(--text-2); margin-bottom:3px; }
.cal-event { display:block; font-size:9px; padding:2px 5px; border-radius:3px; margin-bottom:2px; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cev-load { background:#4DB6AC; } .cev-tech { background:#5B9FE0; } .cev-show { background:#E8350A; }
.cev-call { background:#9C27B0; } .cev-strike { background:#FF7043; }
/* Documents */
.doc-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:10px; }
.doc-template { background:var(--dark-2); border:.5px solid var(--border); border-radius:8px; padding:14px; cursor:pointer; transition:border-color .12s; }
.doc-template:hover { border-color:var(--gold-bd); }
.doc-icon { font-size:20px; margin-bottom:8px; }
.doc-name { font-size:13px; font-weight:500; color:var(--text-1); margin-bottom:4px; }
.doc-desc { font-size:11px; color:var(--text-3); line-height:1.5; margin-bottom:8px; }
.doc-tag  { font-size:9px; padding:2px 7px; border-radius:4px; background:rgba(91,159,224,.1); color:#5B9FE0; border:.5px solid rgba(91,159,224,.25); }
.doc-section { border:.5px solid var(--border); border-radius:8px; overflow:hidden; margin-bottom:10px; }
.doc-section-hd { display:flex; align-items:center; justify-content:space-between; padding:11px 14px; background:var(--dark-2); cursor:pointer; }
.doc-section-title { font-size:12px; font-weight:600; color:var(--text-1); }
.doc-section-body { padding:14px; display:flex; flex-direction:column; gap:10px; }
.doc-field { display:grid; grid-template-columns:140px 1fr; gap:8px; align-items:center; }
.doc-field-label { font-size:11px; color:var(--text-3); }
.doc-field-input { background:var(--dark-2); border:.5px solid var(--border); border-radius:5px; color:var(--text-1); font-family:var(--sans); font-size:13px; padding:7px 10px; outline:none; }
.doc-field-input:focus { border-color:var(--gold); }
.doc-field-textarea { background:var(--dark-2); border:.5px solid var(--border); border-radius:5px; color:var(--text-1); font-family:var(--sans); font-size:12px; padding:9px 12px; outline:none; width:100%; resize:vertical; line-height:1.7; min-height:90px; }
.doc-field-textarea:focus { border-color:var(--gold); }
.doc-title-input { background:transparent; border:none; border-bottom:.5px solid var(--border); color:var(--text-1); font-family:var(--disp); font-size:22px; letter-spacing:1px; padding:0 0 10px; outline:none; width:100%; margin-bottom:16px; }
.room-list-table { width:100%; border-collapse:collapse; font-size:12px; }
.room-list-table th { background:var(--dark-2); padding:9px 14px; text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:1px; color:var(--text-3); font-weight:500; }
.room-list-table td { border-bottom:.5px solid var(--border); }
.room-input { background:transparent; border:none; color:var(--text-1); font-family:var(--sans); font-size:12px; padding:9px 12px; outline:none; width:100%; }
.room-input:focus { background:var(--dark-2); }
/* Messages */
.msg-messages { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:12px; }
.msg-date-divider { text-align:center; font-size:10px; color:var(--text-3); }
.msg-system { text-align:center; font-size:11px; color:var(--text-3); background:var(--dark-2); border-radius:4px; padding:4px 10px; align-self:center; }
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
.prog-bar  { background:var(--dark-2); border-radius:4px; height:5px; overflow:hidden; }
.prog-fill { height:100%; border-radius:4px; background:linear-gradient(90deg,#5B9FE0,#00c8d4); }
/* Row clickable */
.row { display:flex; align-items:center; gap:12px; padding:10px 18px; border-bottom:.5px solid var(--border); font-size:12px; color:var(--text-2); }
.row:last-child { border-bottom:none; }
.row.clickable { cursor:pointer; transition:background .12s; text-decoration:none; }
.row.clickable:hover { background:var(--dark-2); color:var(--text-1); }
/* Buttons */
.btn-back { font-family:var(--sans); font-size:12px; color:var(--text-3); background:transparent; border:.5px solid var(--border); padding:6px 14px; border-radius:6px; text-decoration:none; transition:all .15s; display:inline-block; margin-bottom:16px; }
.btn-back:hover { color:var(--text-1); }
.btn-sm { font-family:var(--sans); font-size:11px; padding:6px 12px; border-radius:5px; border:.5px solid var(--border); background:transparent; color:var(--text-3); cursor:pointer; transition:all .15s; text-decoration:none; display:inline-block; }
.btn-sm:hover { color:var(--text-1); border-color:rgba(255,255,255,.15); }
.btn-gold-sm { font-family:var(--sans); font-size:11px; padding:6px 12px; border-radius:5px; background:rgba(232,184,75,.15); color:var(--gold); border:.5px solid var(--gold-bd); cursor:pointer; transition:all .15s; }
.btn-gold-sm:hover { background:rgba(232,184,75,.25); }
@media(max-width:900px) { .grid-2 { grid-template-columns:1fr; } .prod-tabs { flex-wrap:nowrap; } }
</style>

<div class="app-wrap">
  <a class="btn-back" href="/overview/productions.php">← Productions</a>

  <!-- Hero -->
  <div class="prod-hero">
    <div class="prod-hero-ey">Production workspace</div>
    <div class="prod-hero-name"><?php echo htmlspecialchars($prod['name']); ?></div>
    <div class="prod-hero-meta">
      <div class="prod-hero-item">📍 <strong><?php echo e($prod['venue']); ?></strong></div>
      <div class="prod-hero-item">🗓 <strong><?php echo e($prod['dates']); ?></strong></div>
      <div class="prod-hero-item">👥 <strong><?php echo e($prod['guests']); ?></strong></div>
      <div class="prod-hero-item">💰 <strong><?php echo e($prod['budget']); ?></strong></div>
      <span class="pill <?php echo e($prod['pill']); ?>"><?php echo e($prod['pill_text']); ?></span>
    </div>
  </div>

  <!-- Tabs -->
  <div class="prod-tabs" id="prod-tab-bar">
    <button class="pt active" onclick="switchTab('overview',this)">Overview</button>
    <button class="pt" onclick="switchTab('ros',this)">Run of show</button>
    <button class="pt" onclick="switchTab('callsheet',this)">Call sheets</button>
    <button class="pt" onclick="switchTab('messages',this)">Messages</button>
    <button class="pt" onclick="switchTab('documents',this)">Documents</button>
    <button class="pt" onclick="switchTab('calendar',this)">Calendar</button>
    <button class="pt" onclick="switchTab('tasks',this)">Tasks</button>
    <button class="pt" onclick="switchTab('team',this)">Team</button>
    <button class="pt" onclick="switchTab('budget',this)">Budget</button>
    <button class="pt" onclick="switchTab('vendors',this)">Vendors</button>
    <button class="pt" onclick="switchTab('files',this)">Files</button>
  </div>

  <!-- OVERVIEW -->
  <div id="tab-overview" class="tab-content active">
    <div class="live-banner">
      <div class="live-l"><div class="live-dot"></div><div><div class="live-title">Show is live now</div><div class="live-sub">Sarah M. calling · Q14 of 23 · 5 crew connected</div></div></div>
      <a class="btn-danger" href="/overview/ros-live.php">Open live caller →</a>
    </div>
    <div class="grid-2">
      <div>
        <div class="card">
          <div class="chd"><div class="cht">Production phases</div></div>
          <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;padding:14px 16px">
            <div style="text-align:center;padding:10px 6px;border-radius:8px;background:rgba(76,175,80,.1);border:.5px solid rgba(76,175,80,.25)"><div style="font-size:9px;color:#66BB6A;font-weight:500;margin-bottom:3px">✓ Done</div><div style="font-size:11px;color:#66BB6A">Pre-prod</div></div>
            <div style="text-align:center;padding:10px 6px;border-radius:8px;background:rgba(76,175,80,.1);border:.5px solid rgba(76,175,80,.25)"><div style="font-size:9px;color:#66BB6A;font-weight:500;margin-bottom:3px">✓ Done</div><div style="font-size:11px;color:#66BB6A">Load-in</div></div>
            <div style="text-align:center;padding:10px 6px;border-radius:8px;background:rgba(76,175,80,.1);border:.5px solid rgba(76,175,80,.25)"><div style="font-size:9px;color:#66BB6A;font-weight:500;margin-bottom:3px">✓ Done</div><div style="font-size:11px;color:#66BB6A">Tech</div></div>
            <div style="text-align:center;padding:10px 6px;border-radius:8px;background:rgba(232,53,10,.1);border:.5px solid rgba(232,53,10,.25)"><div style="font-size:9px;color:#E8350A;font-weight:500;margin-bottom:3px">🔴 Live</div><div style="font-size:11px;color:#E8350A">Show</div></div>
            <div style="text-align:center;padding:10px 6px;border-radius:8px;background:var(--dark-3);border:.5px solid var(--border)"><div style="font-size:9px;color:var(--text-3);font-weight:500;margin-bottom:3px">Upcoming</div><div style="font-size:11px;color:var(--text-3)">Strike</div></div>
          </div>
        </div>
        <div class="card">
          <div class="chd"><div class="cht">Open tasks</div><button class="cha" onclick="switchTab('tasks')">View all →</button></div>
          <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Send runsheet to venue coordinator</div><div class="tr-ref">Due May 3</div></div><div class="td">May 3</div></div>
          <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Review insurance certificates</div><div class="tr-ref">Due May 1</div></div><div class="td over">May 1</div></div>
          <div class="task-row"><div class="chk done" onclick="toggleTask(this)"></div><div class="tb"><div class="tn done">Finalize menu selections</div><div class="tr-ref">Completed Apr 22</div></div></div>
        </div>
        <div class="card">
          <div class="chd"><div class="cht">Recent activity</div></div>
          <div class="act-row"><div class="act-av" style="background:rgba(232,53,10,.15);color:#FF7043">SM</div><div><div class="act-text"><strong>Sarah M.</strong> started the run of show</div><div class="act-time">1h ago</div></div></div>
          <div class="act-row"><div class="act-av" style="background:rgba(91,159,224,.15);color:#5B9FE0">TR</div><div><div class="act-text"><strong>Tom R.</strong> uploaded AV contract PDF</div><div class="act-time">2h ago</div></div></div>
          <div class="act-row"><div class="act-av" style="background:rgba(232,184,75,.15);color:var(--gold)">DL</div><div><div class="act-text"><strong>Dana L.</strong> updated lighting notes in ROS</div><div class="act-time">3h ago</div></div></div>
        </div>
      </div>
      <div>
        <div class="card">
          <div class="ns-hd"><div class="ns-ey">Show info</div><div class="ns-name">Tonight</div><div class="ns-date">Jun 14, 2025 · Night 1</div></div>
          <div class="ns-body">
            <div class="ns-row"><span class="ns-lbl">Crew call</span><span class="ns-val">3:00 PM</span></div>
            <div class="ns-row"><span class="ns-lbl">Doors</span><span class="ns-val">6:30 PM</span></div>
            <div class="ns-row"><span class="ns-lbl">Show end</span><span class="ns-val">~11:30 PM</span></div>
            <div class="ns-row"><span class="ns-lbl">Strike</span><span class="ns-val">Jun 15 · 8 AM</span></div>
          </div>
        </div>
        <div class="card">
          <div class="chd"><div class="cht">Quick links</div></div>
          <div class="row clickable" onclick="switchTab('ros')"><span>▶</span><span>Run of show</span></div>
          <div class="row clickable" onclick="switchTab('callsheet')"><span>📋</span><span>Call sheets</span></div>
          <div class="row clickable" onclick="switchTab('messages')"><span>💬</span><span>Team messages</span></div>
          <a class="row clickable" href="/overview/drive.php?folder=gala"><span>📁</span><span>Production drive</span></a>
        </div>
        <div class="card">
          <div class="chd"><div class="cht">Team tonight</div></div>
          <div class="task-row"><div style="width:28px;height:28px;border-radius:50%;background:rgba(232,53,10,.15);color:#FF7043;font-size:10px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">SM</div><div class="tb"><div class="tn">Sarah M.</div><div class="tr-ref">SM · Calling</div></div><span class="pill pill-live">Live</span></div>
          <div class="task-row"><div style="width:28px;height:28px;border-radius:50%;background:rgba(91,159,224,.15);color:#5B9FE0;font-size:10px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">TR</div><div class="tb"><div class="tn">Tom R.</div><div class="tr-ref">TD · Connected</div></div><span class="pill pill-green">Online</span></div>
          <div class="task-row"><div style="width:28px;height:28px;border-radius:50%;background:rgba(232,184,75,.15);color:var(--gold);font-size:10px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">DL</div><div class="tb"><div class="tn">Dana L.</div><div class="tr-ref">LD · Connected</div></div><span class="pill pill-green">Online</span></div>
        </div>
      </div>
    </div>
  </div>

  <!-- RUN OF SHOW -->
  <div id="tab-ros" class="tab-content">
    <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-3);margin-bottom:6px">Select show day</div>
    <div class="ros-day-selector" id="ros-day-selector">
      <button class="ros-day-btn" onclick="selectROSDay(this,'Tech rehearsal · Jun 13, 2025','pre')"><div class="ros-day-dot"></div>Jun 13 — Tech rehearsal</button>
      <button class="ros-day-btn live-day active" onclick="selectROSDay(this,'Show night 1 · Jun 14, 2025','live')"><div class="ros-day-dot"></div>Jun 14 — Show night 1 · Live</button>
      <button class="ros-day-btn" onclick="selectROSDay(this,'Strike · Jun 15, 2025','pre')"><div class="ros-day-dot"></div>Jun 15 — Strike</button>
      <button class="btn-sm" onclick="addROSDay()">+ Add day</button>
    </div>
    <div class="ros-toolbar">
      <div class="ros-status live" id="ros-status"><div class="ros-dot"></div>Live · Cue 14 of 23</div>
      <div style="font-size:12px;color:var(--text-3);margin-left:4px" id="ros-day-label">Show night 1 · Jun 14, 2025</div>
      <div style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap">
        <button class="btn-sm" onclick="window.print()">🖨 Print</button>
        <button class="btn-sm" onclick="addCue()">+ Cue</button>
        <a class="btn-gold-sm" href="/overview/ros-live.php">▶ Live caller</a>
      </div>
    </div>
    <div style="overflow-x:auto;border-radius:10px;border:.5px solid var(--border)">
      <table class="ros-tbl"><thead><tr><th>Time</th><th>Cue</th><th>Item</th><th>Dept</th><th>Duration</th><th>Notes</th></tr></thead>
      <tbody id="ros-body"></tbody></table>
    </div>
    <div class="ros-add" onclick="addCue()">+ Add cue row</div>
  </div>

  <!-- CALL SHEETS -->
  <div id="tab-callsheet" class="tab-content">
    <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-3);margin-bottom:6px">Select day</div>
    <div class="ros-day-selector" id="call-day-selector">
      <button class="ros-day-btn" onclick="selectCallDay(this,'Load-in · Jun 12, 2025')"><div class="ros-day-dot"></div>Jun 12 — Load-in</button>
      <button class="ros-day-btn" onclick="selectCallDay(this,'Tech day · Jun 13, 2025')"><div class="ros-day-dot"></div>Jun 13 — Tech</button>
      <button class="ros-day-btn live-day active" onclick="selectCallDay(this,'Show day · Jun 14, 2025')"><div class="ros-day-dot"></div>Jun 14 — Show day · Tonight</button>
      <button class="ros-day-btn" onclick="selectCallDay(this,'Strike · Jun 15, 2025')"><div class="ros-day-dot"></div>Jun 15 — Strike</button>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <div><div style="font-size:13px;font-weight:500" id="callsheet-title">Call sheet — Jun 14, 2025 (Show day)</div><div style="font-size:12px;color:var(--text-3)"><?php echo e($prod['venue']); ?></div></div>
      <div style="display:flex;gap:8px"><button class="btn-sm" onclick="window.print()">Export PDF</button><button class="btn-gold-sm">Send to crew</button></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
      <div class="card">
        <div class="chd"><div class="cht">Crew calls</div></div>
        <div class="task-row"><div style="font-family:monospace;font-size:11px;color:var(--text-3);width:44px;flex-shrink:0">3:00p</div><div class="tb"><div class="tn">General crew call — all departments</div></div></div>
        <div class="task-row"><div style="font-family:monospace;font-size:11px;color:var(--text-3);width:44px;flex-shrink:0">3:30p</div><div class="tb"><div class="tn">Lighting focus & programming</div><div class="tr-ref">LD, board op</div></div></div>
        <div class="task-row"><div style="font-family:monospace;font-size:11px;color:var(--text-3);width:44px;flex-shrink:0">4:30p</div><div class="tb"><div class="tn">Soundcheck</div><div class="tr-ref">FOH, monitors, RF</div></div></div>
        <div class="task-row"><div style="font-family:monospace;font-size:11px;color:var(--text-3);width:44px;flex-shrink:0">5:00p</div><div class="tb"><div class="tn">SM crew walkthrough</div></div></div>
        <div class="task-row"><div style="font-family:monospace;font-size:11px;color:var(--text-3);width:44px;flex-shrink:0">6:00p</div><div class="tb"><div class="tn">Crew ready / places</div></div></div>
      </div>
      <div class="card">
        <div class="chd"><div class="cht">Show schedule</div></div>
        <div class="task-row"><div style="font-family:monospace;font-size:11px;color:var(--text-3);width:44px;flex-shrink:0">6:30p</div><div class="tb"><div class="tn">Doors open / cocktail reception</div></div></div>
        <div class="task-row"><div style="font-family:monospace;font-size:11px;color:var(--text-3);width:44px;flex-shrink:0">7:40p</div><div class="tb"><div class="tn">Welcome remarks — CEO (~10 min)</div></div></div>
        <div class="task-row" style="background:rgba(232,53,10,.05)"><div style="font-family:monospace;font-size:11px;color:#E8350A;width:44px;flex-shrink:0">NOW</div><div class="tb"><div class="tn" style="color:#E8350A">Dinner service</div></div></div>
        <div class="task-row"><div style="font-family:monospace;font-size:11px;color:var(--text-3);width:44px;flex-shrink:0">~9:10p</div><div class="tb"><div class="tn">Awards presentation (~35 min)</div></div></div>
        <div class="task-row"><div style="font-family:monospace;font-size:11px;color:var(--text-3);width:44px;flex-shrink:0">11:15p</div><div class="tb"><div class="tn">Last call / closing</div></div></div>
      </div>
    </div>
    <div class="card">
      <div class="chd"><div class="cht">Key contacts tonight</div></div>
      <div style="display:grid;grid-template-columns:repeat(4,1fr)">
        <div style="padding:12px 16px;border-right:.5px solid var(--border)"><div style="font-size:10px;color:var(--text-3);margin-bottom:3px">Lead producer</div><div style="font-weight:500">Jordan K.</div><div style="font-size:11px;color:var(--text-3)">(702) 555-0101</div></div>
        <div style="padding:12px 16px;border-right:.5px solid var(--border)"><div style="font-size:10px;color:var(--text-3);margin-bottom:3px">Stage manager</div><div style="font-weight:500">Sarah M.</div><div style="font-size:11px;color:var(--text-3)">(702) 555-0192</div></div>
        <div style="padding:12px 16px;border-right:.5px solid var(--border)"><div style="font-size:10px;color:var(--text-3);margin-bottom:3px">TD / AV</div><div style="font-weight:500">Tom R.</div><div style="font-size:11px;color:var(--text-3)">(702) 555-0233</div></div>
        <div style="padding:12px 16px"><div style="font-size:10px;color:var(--text-3);margin-bottom:3px">Venue coordinator</div><div style="font-weight:500">Lisa T.</div><div style="font-size:11px;color:var(--text-3)">(702) 555-0344</div></div>
      </div>
    </div>
  </div>

  <!-- MESSAGES -->
  <div id="tab-messages" class="tab-content">
    <div style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between">
      <div><div style="font-size:13px;font-weight:500">Summerset Gala — Team chat</div><div style="font-size:12px;color:var(--text-3)">5 members · All production team</div></div>
      <a class="btn-sm" href="/overview/messages.php">All messages →</a>
    </div>
    <div style="background:var(--dark-2);border:.5px solid var(--border);border-radius:12px;overflow:hidden;height:520px;display:flex;flex-direction:column">
      <div class="msg-messages" id="prod-msg-list">
        <div class="msg-date-divider">Today · June 14, 2025</div>
        <div class="msg-bubble-wrap"><div style="width:24px;height:24px;border-radius:50%;background:rgba(232,53,10,.15);color:#FF7043;font-size:9px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-bottom:2px">SM</div><div class="msg-bubble-col"><div class="msg-sender-name">Sarah M.</div><div class="msg-bubble other">Morning team — load-in running smooth. AV set up, waiting on lighting focus.</div><div class="msg-bubble-time">8:14 AM</div></div></div>
        <div class="msg-bubble-wrap mine"><div class="msg-bubble-col"><div class="msg-bubble mine">Great. Client arrives at 4pm for a walkthrough.</div><div class="msg-bubble-time">8:45 AM</div></div></div>
        <div class="msg-system">Sarah M. started the run of show · 5:58 PM</div>
        <div class="msg-bubble-wrap"><div style="width:24px;height:24px;border-radius:50%;background:rgba(232,53,10,.15);color:#FF7043;font-size:9px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-bottom:2px">SM</div><div class="msg-bubble-col"><div class="msg-sender-name">Sarah M.</div><div class="msg-bubble other">ROS loaded and ready. Cue 1 on standby.</div><div class="msg-bubble-time">5:59 PM</div></div></div>
      </div>
      <div style="padding:6px 14px 4px;border-top:.5px solid var(--border)"><div class="msg-ref-picker"><div class="msg-ref-chip" onclick="switchTab('ros')">▶ Run of show</div><div class="msg-ref-chip" onclick="switchTab('callsheet')">📋 Call sheet</div><div class="msg-ref-chip" onclick="switchTab('tasks')">✅ Tasks</div></div></div>
      <div class="msg-input-area"><div class="msg-input-row"><div class="msg-input-wrap"><textarea class="msg-input" id="prod-msg-input" placeholder="Message the team…" rows="1" onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendProdMsg()}" oninput="autoResize(this)"></textarea></div><button class="msg-send-btn" onclick="sendProdMsg()">↑</button></div></div>
    </div>
  </div>

  <!-- DOCUMENTS -->
  <div id="tab-documents" class="tab-content">
    <div id="doc-library-view">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px"><div><div style="font-size:13px;font-weight:500;margin-bottom:3px">Documents</div><div style="font-size:12px;color:var(--text-3)">All production docs — always current, printable anytime.</div></div><button class="btn-gold-sm">+ New document</button></div>
      <div style="margin-bottom:22px">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:1.5px;color:var(--text-3);margin-bottom:10px">Saved documents</div>
        <div style="display:flex;flex-direction:column;gap:8px">
          <div class="card" style="cursor:pointer;padding:0" onclick="switchTab('ros')"><div style="display:flex;align-items:center;gap:14px;padding:14px 18px"><span style="font-size:18px">▶</span><div style="flex:1"><div style="font-size:13px;font-weight:500">Run of show</div><div style="font-size:11px;color:var(--text-3)">3 show days · Sarah M. calling · Jun 14 live</div></div><div style="display:flex;gap:8px"><a class="btn-sm" href="/overview/ros-live.php" onclick="event.stopPropagation()">Live caller</a><button class="btn-sm" onclick="event.stopPropagation();switchTab('ros')">Open →</button></div></div></div>
          <div class="card" style="cursor:pointer;padding:0" onclick="openDoc('callsheet')"><div style="display:flex;align-items:center;gap:14px;padding:14px 18px"><span style="font-size:18px">📋</span><div style="flex:1"><div style="font-size:13px;font-weight:500">Call sheet — Jun 14, 2025</div><div style="font-size:11px;color:var(--text-3)">Last edited today · Jordan K.</div></div><button class="btn-sm" onclick="event.stopPropagation();openDoc('callsheet')">Open →</button></div></div>
          <div class="card" style="cursor:pointer;padding:0" onclick="openDoc('room-list')"><div style="display:flex;align-items:center;gap:14px;padding:14px 18px"><span style="font-size:18px">🏨</span><div style="flex:1"><div style="font-size:13px;font-weight:500">Room list</div><div style="font-size:11px;color:var(--text-3)">Last edited Jun 10 · Jordan K.</div></div><button class="btn-sm" onclick="event.stopPropagation();openDoc('room-list')">Open →</button></div></div>
          <div class="card" style="cursor:pointer;padding:0" onclick="openDoc('prod-schedule')"><div style="display:flex;align-items:center;gap:14px;padding:14px 18px"><span style="font-size:18px">📅</span><div style="flex:1"><div style="font-size:13px;font-weight:500">Production schedule</div><div style="font-size:11px;color:var(--text-3)">Last edited Jun 8 · Jordan K.</div></div><button class="btn-sm" onclick="event.stopPropagation();openDoc('prod-schedule')">Open →</button></div></div>
        </div>
      </div>
      <div style="font-size:10px;text-transform:uppercase;letter-spacing:1.5px;color:var(--text-3);margin-bottom:10px">Templates</div>
      <div class="doc-grid">
        <div class="doc-template" onclick="openDoc('crew-contact')"><div class="doc-icon">👥</div><div class="doc-name">Crew contact sheet</div><div class="doc-desc">Everyone on the team with roles and numbers.</div><span class="doc-tag">Auto-gen</span></div>
        <div class="doc-template" onclick="openDoc('vendor-list')"><div class="doc-icon">🏪</div><div class="doc-name">Vendor contact sheet</div><div class="doc-desc">All confirmed vendors with contacts and contract status.</div><span class="doc-tag">Auto-gen</span></div>
        <div class="doc-template" onclick="openDoc('site-survey')"><div class="doc-icon">🏟</div><div class="doc-name">Site survey</div><div class="doc-desc">Venue details, power specs, loading access, rigging notes.</div><span class="doc-tag">Template</span></div>
        <div class="doc-template" onclick="openDoc('day-of')"><div class="doc-icon">☀️</div><div class="doc-name">Day-of briefing</div><div class="doc-desc">Show overview, timeline, emergency contacts for full crew.</div><span class="doc-tag">Template</span></div>
        <div class="doc-template" onclick="openDoc('advance')"><div class="doc-icon">📝</div><div class="doc-name">Advance sheet</div><div class="doc-desc">Pre-show requirements sent to venue.</div><span class="doc-tag">Template</span></div>
      </div>
    </div>
    <div id="doc-editor-view" style="display:none">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px"><button class="btn-sm" onclick="closeDoc()">← Documents</button><div style="flex:1;font-size:13px;font-weight:500" id="doc-editor-title">Document</div><div style="display:flex;gap:8px"><button class="btn-sm" onclick="window.print()">🖨 Print</button><button class="btn-gold-sm">Save</button></div></div>
      <div id="doc-editor-content"></div>
    </div>
  </div>

  <!-- CALENDAR -->
  <div id="tab-calendar" class="tab-content">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px"><div style="font-size:13px;font-weight:500">Production calendar</div><button class="btn-gold-sm">+ Add event</button></div>
    <div class="cal-grid-wrap">
      <div class="cal-dow-row"><div class="cal-dow">Sun</div><div class="cal-dow">Mon</div><div class="cal-dow">Tue</div><div class="cal-dow">Wed</div><div class="cal-dow">Thu</div><div class="cal-dow">Fri</div><div class="cal-dow">Sat</div></div>
      <div class="cal-week">
        <div class="cal-day other-month"><div class="cal-day-num">1</div></div><div class="cal-day other-month"><div class="cal-day-num">2</div></div><div class="cal-day other-month"><div class="cal-day-num">3</div></div><div class="cal-day other-month"><div class="cal-day-num">4</div></div><div class="cal-day other-month"><div class="cal-day-num">5</div></div><div class="cal-day other-month"><div class="cal-day-num">6</div></div><div class="cal-day other-month"><div class="cal-day-num">7</div></div>
      </div>
      <div class="cal-week">
        <div class="cal-day"><div class="cal-day-num">8</div></div><div class="cal-day"><div class="cal-day-num">9</div></div><div class="cal-day"><div class="cal-day-num">10</div></div><div class="cal-day"><div class="cal-day-num">11</div></div>
        <div class="cal-day"><div class="cal-day-num">12</div><span class="cal-event cev-load">8am Load-in</span></div>
        <div class="cal-day"><div class="cal-day-num">13</div><span class="cal-event cev-tech">2pm Tech</span></div>
        <div class="cal-day" style="background:rgba(232,53,10,.06)"><div class="cal-day-num" style="color:#E8350A">14 🔴</div><span class="cal-event cev-call">Crew 3pm</span><span class="cal-event cev-show">Show 6:30pm</span></div>
      </div>
      <div class="cal-week">
        <div class="cal-day"><div class="cal-day-num">15</div><span class="cal-event cev-strike">8am Strike</span></div>
        <div class="cal-day"><div class="cal-day-num">16</div></div><div class="cal-day"><div class="cal-day-num">17</div></div><div class="cal-day"><div class="cal-day-num">18</div></div><div class="cal-day"><div class="cal-day-num">19</div></div><div class="cal-day"><div class="cal-day-num">20</div></div><div class="cal-day"><div class="cal-day-num">21</div></div>
      </div>
    </div>
  </div>

  <!-- TASKS -->
  <div id="tab-tasks" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Production tasks</div><button class="btn-gold-sm">+ Add task</button></div>
      <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Send runsheet to venue coordinator</div><div class="tr-ref">Due May 3 · Jordan K.</div></div><div class="td">May 3</div></div>
      <div class="task-row"><div class="chk" onclick="toggleTask(this)"></div><div class="tb"><div class="tn">Review insurance certificates from all vendors</div><div class="tr-ref">Due May 1 · Jordan K.</div></div><div class="td over">May 1</div></div>
      <div class="task-row"><div class="chk done" onclick="toggleTask(this)"></div><div class="tb"><div class="tn done">Confirm final guest count with client</div><div class="tr-ref">Completed Apr 20</div></div></div>
      <div class="task-row"><div class="chk done" onclick="toggleTask(this)"></div><div class="tb"><div class="tn done">Finalize menu selections</div><div class="tr-ref">Completed Apr 22</div></div></div>
    </div>
  </div>

  <!-- TEAM -->
  <div id="tab-team" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Production team</div><button class="btn-gold-sm">+ Add member</button></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(179,157,219,.15);color:#B39DDB;font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">JK</div><div class="tb"><div class="tn">Jordan Kim</div><div class="tr-ref">Lead producer · All access</div></div><span class="pill pill-gold">Admin</span></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(232,53,10,.15);color:#FF7043;font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">SM</div><div class="tb"><div class="tn">Sarah M.</div><div class="tr-ref">Stage manager · All access</div></div><span class="pill pill-gold">Admin</span></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(91,159,224,.15);color:#5B9FE0;font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">TR</div><div class="tb"><div class="tn">Tom R.</div><div class="tr-ref">Technical director · AV dept</div></div><span class="pill pill-plan">Dept head</span></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(232,184,75,.15);color:var(--gold);font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">DL</div><div class="tb"><div class="tn">Dana L.</div><div class="tr-ref">Lighting designer · LX dept</div></div><span class="pill pill-plan">Dept head</span></div>
      <div class="task-row"><div style="width:30px;height:30px;border-radius:50%;background:rgba(76,175,80,.15);color:#66BB6A;font-size:11px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0">PN</div><div class="tb"><div class="tn">Priya N.</div><div class="tr-ref">Stagehand · Call times + ROS view</div></div><span class="pill pill-draft">Crew</span></div>
    </div>
  </div>

  <!-- BUDGET -->
  <div id="tab-budget" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Budget tracker</div><button class="btn-sm">Export</button></div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:14px 16px;border-bottom:.5px solid var(--border)">
        <div style="background:var(--dark-3);border-radius:8px;padding:13px"><div style="font-size:10px;color:var(--text-3);margin-bottom:4px">Total budget</div><div style="font-size:22px;font-weight:500">$86,000</div></div>
        <div style="background:var(--dark-3);border-radius:8px;padding:13px"><div style="font-size:10px;color:var(--text-3);margin-bottom:4px">Committed</div><div style="font-size:22px;font-weight:500">$85,600</div></div>
        <div style="background:var(--dark-3);border-radius:8px;padding:13px"><div style="font-size:10px;color:var(--text-3);margin-bottom:4px">Remaining</div><div style="font-size:22px;font-weight:500;color:#66BB6A">+$400</div></div>
      </div>
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead><tr style="background:var(--dark-3)"><th style="text-align:left;padding:9px 18px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-3);font-weight:500">Line item</th><th style="text-align:right;padding:9px 10px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-3);font-weight:500">Budgeted</th><th style="text-align:right;padding:9px 10px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-3);font-weight:500">Actual</th><th style="text-align:right;padding:9px 18px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-3);font-weight:500">Variance</th></tr></thead>
        <tbody>
          <tr style="border-bottom:.5px solid var(--border)"><td style="padding:10px 18px">Venue rental</td><td style="padding:10px;text-align:right">$22,000</td><td style="padding:10px;text-align:right">$22,000</td><td style="padding:10px 18px;text-align:right;color:#66BB6A">$0</td></tr>
          <tr style="border-bottom:.5px solid var(--border)"><td style="padding:10px 18px">Food & beverage</td><td style="padding:10px;text-align:right">$28,000</td><td style="padding:10px;text-align:right">$29,400</td><td style="padding:10px 18px;text-align:right;color:#E8350A">-$1,400</td></tr>
          <tr style="border-bottom:.5px solid var(--border)"><td style="padding:10px 18px">AV & production</td><td style="padding:10px;text-align:right">$14,000</td><td style="padding:10px;text-align:right">$13,200</td><td style="padding:10px 18px;text-align:right;color:#66BB6A">+$800</td></tr>
          <tr style="border-bottom:.5px solid var(--border)"><td style="padding:10px 18px">Décor & florals</td><td style="padding:10px;text-align:right">$8,500</td><td style="padding:10px;text-align:right">$8,500</td><td style="padding:10px 18px;text-align:right;color:#66BB6A">$0</td></tr>
          <tr><td style="padding:10px 18px">Contingency</td><td style="padding:10px;text-align:right">$3,000</td><td style="padding:10px;text-align:right">$1,700</td><td style="padding:10px 18px;text-align:right;color:#66BB6A">+$1,300</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- VENDORS -->
  <div id="tab-vendors" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Vendors</div><button class="btn-gold-sm">+ Add vendor</button></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">🔊</div><div class="tb"><div class="tn">Apex Sound & Light</div><div class="tr-ref">AV / Production · ★★★★★</div></div><span class="pill pill-green">Confirmed</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">🌸</div><div class="tb"><div class="tn">Desert Bloom Florals</div><div class="tr-ref">Décor · ★★★★☆</div></div><span class="pill pill-green">Confirmed</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">🍽</div><div class="tb"><div class="tn">MGM Grand Catering</div><div class="tr-ref">Food & Beverage · ★★★★★</div></div><span class="pill pill-green">Confirmed</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">🎵</div><div class="tb"><div class="tn">The Rhythm Section</div><div class="tr-ref">Entertainment · ★★★★☆</div></div><span class="pill pill-green">Confirmed</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">👥</div><div class="tb"><div class="tn">ProStaff Events</div><div class="tr-ref">Staffing</div></div><span class="pill pill-draft">Quote pending</span></div>
    </div>
  </div>

  <!-- FILES -->
  <div id="tab-files" class="tab-content">
    <div class="card">
      <div class="chd"><div class="cht">Files</div><div style="display:flex;gap:8px"><a class="btn-sm" href="/overview/drive.php?folder=gala">Open in Drive →</a><button class="btn-gold-sm">+ Upload</button></div></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">📄</div><div class="tb"><div class="tn">AV_Contract_Signed.pdf</div><div class="tr-ref">Tom R. · 2h ago</div></div><span class="pill pill-green">New</span></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">📊</div><div class="tb"><div class="tn">Budget_Final.xlsx</div><div class="tr-ref">Jordan K. · Apr 22</div></div></div>
      <div class="task-row"><div style="font-size:18px;flex-shrink:0">📄</div><div class="tb"><div class="tn">Venue_Contract.pdf</div><div class="tr-ref">Jordan K. · Mar 15</div></div></div>
    </div>
  </div>

</div>

<script>
// Tab switching
function switchTab(id, btn) {
  document.querySelectorAll('.tab-content').forEach(t=>t.classList.remove('active'));
  document.getElementById('tab-'+id)?.classList.add('active');
  document.querySelectorAll('#prod-tab-bar .pt').forEach(t=>t.classList.remove('active'));
  if (btn) { btn.classList.add('active'); }
  else {
    const map={overview:'Overview',ros:'Run of show',callsheet:'Call sheets',messages:'Messages',documents:'Documents',calendar:'Calendar',tasks:'Tasks',team:'Team',budget:'Budget',vendors:'Vendors',files:'Files'};
    document.querySelectorAll('#prod-tab-bar .pt').forEach(t=>{if(t.textContent===map[id])t.classList.add('active');});
  }
  if (id==='ros') renderROS();
  if (id==='documents') { document.getElementById('doc-library-view').style.display='block'; document.getElementById('doc-editor-view').style.display='none'; }
}

// Check URL param for initial tab
const urlTab = new URLSearchParams(window.location.search).get('tab');
if (urlTab) switchTab(urlTab);

// ROS data
const ROS_ROWS = [
  {sec:'PRE-SHOW'},
  {q:'Q1',  time:'6:00p',  title:'House open',             dur:'∞',   dept:'Audio', dc:'background:#1a3a4a;color:#4DB6AC', note:'Preset music begins'},
  {q:'Q2',  time:'6:00p',  title:'Welcome video loop',     dur:'60s', dept:'Video', dc:'background:#1a2a4a;color:#5B9FE0', note:'Background branding'},
  {sec:'ARRIVAL & RECEPTION'},
  {q:'Q3',  time:'6:30p',  title:'Doors open',             dur:'—',   dept:'LX',    dc:'background:#3a2a0a;color:var(--gold)',  note:'Shift to cocktail preset'},
  {q:'Q4',  time:'6:30p',  title:'Cocktail music — jazz',  dur:'70m', dept:'Audio', dc:'background:#1a3a4a;color:#4DB6AC', note:'Band starts · FOH 70dB'},
  {q:'Q5',  time:'7:40p',  title:'Welcome remarks — CEO',  dur:'10m', dept:'All',   dc:'background:#3a1a1a;color:#E8350A', note:'Podium mic up'},
  {sec:'DINNER SERVICE'},
  {q:'Q6',  time:'7:52p',  title:'Dinner seating',         dur:'—',   dept:'Audio', dc:'background:#1a3a4a;color:#4DB6AC', note:'LX shift · music cross to ambient'},
  {q:'Q7',  time:'7:52p',  title:'Table video loops',      dur:'∞',   dept:'Video', dc:'background:#1a2a4a;color:#5B9FE0', note:'Brand reels · all panels'},
  {q:'Q8',  time:'~8:00p', title:'Brand film — 3min',      dur:'3m',  dept:'All',   dc:'background:#3a1a1a;color:#E8350A', note:'All screens · kill ambient audio'},
  {q:'Q9',  time:'NOW',    title:'Dinner service continues',dur:'∞',  dept:'SM',    dc:'background:#1a2a1a;color:#66BB6A', note:'SM holds for awards readiness', current:true},
  {sec:'AWARDS CEREMONY'},
  {q:'Q10', time:'~9:10p', title:'Awards walk-up music',   dur:'—',   dept:'Audio', dc:'background:#1a3a4a;color:#4DB6AC', note:'LX shift to awards preset'},
  {q:'Q11', time:'~9:15p', title:'Award 1 — Innovation',   dur:'8m',  dept:'All',   dc:'background:#3a1a1a;color:#E8350A', note:'Presenter to podium · nominee reel'},
  {sec:'CLOSING'},
  {q:'Q12', time:'~11:00p',title:'Closing remarks — VP',   dur:'5m',  dept:'SM',    dc:'background:#1a2a1a;color:#66BB6A', note:'Podium mic · LX hold'},
  {q:'Q13', time:'~11:05p',title:'Thank you reel — 90s',   dur:'90s', dept:'Video', dc:'background:#1a2a4a;color:#5B9FE0', note:'All screens · ambient audio'},
  {q:'Q14', time:'~11:15p',title:'House lights up',        dur:'—',   dept:'LX',    dc:'background:#3a2a0a;color:var(--gold)',  note:'Music up · last call'},
];

function renderROS() {
  const tbody=document.getElementById('ros-body'); if(!tbody)return;
  tbody.innerHTML = ROS_ROWS.map(r=>{
    if(r.sec) return `<tr class="sec-row"><td colspan="6">${r.sec}</td></tr>`;
    return `<tr${r.current?' style="background:rgba(232,53,10,.08);border-left:2px solid #E8350A"':''}>
      <td style="font-family:monospace;font-size:11px;color:${r.current?'#E8350A':'var(--text-3)'}">${r.time}</td>
      <td style="font-family:monospace;font-size:11px;color:var(--text-3)">${r.q}</td>
      <td style="font-weight:${r.current?'600':'400'};color:${r.current?'#E8350A':'var(--text-1)'}">${r.title}</td>
      <td><span class="dept-tag" style="${r.dc}">${r.dept}</span></td>
      <td style="font-family:monospace;font-size:11px;color:var(--text-3)">${r.dur}</td>
      <td style="font-size:11px;color:var(--text-3)">${r.note}</td>
    </tr>`;
  }).join('');
}
renderROS();

function selectROSDay(btn, label, status) {
  document.querySelectorAll('#ros-day-selector .ros-day-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  const statusEl=document.getElementById('ros-status'), labelEl=document.getElementById('ros-day-label');
  if(labelEl) labelEl.textContent=label;
  if(statusEl) {
    statusEl.className='ros-status '+(status==='live'?'live':'pre');
    statusEl.innerHTML=status==='live'?'<div class="ros-dot"></div>Live · Cue 14 of 23':'<div class="ros-dot"></div>Pre-show';
  }
}
function addROSDay() { const n=prompt('New show day name:'); if(!n)return; const sel=document.getElementById('ros-day-selector'),add=sel?.querySelector('.btn-sm'); const btn=document.createElement('button'); btn.className='ros-day-btn'; btn.innerHTML=`<div class="ros-day-dot"></div>${n}`; btn.onclick=()=>{document.querySelectorAll('#ros-day-selector .ros-day-btn').forEach(b=>b.classList.remove('active'));btn.classList.add('active');}; sel?.insertBefore(btn,add); }
function selectCallDay(btn, label) { document.querySelectorAll('#call-day-selector .ros-day-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); const el=document.getElementById('callsheet-title'); if(el) el.textContent='Call sheet — '+label; }
function addCue() { const tbody=document.getElementById('ros-body'); if(!tbody)return; const tr=document.createElement('tr'); tr.innerHTML=`<td style="font-family:monospace;font-size:11px"><input style="background:transparent;border:none;color:var(--text-3);font-family:monospace;font-size:11px;width:50px;outline:none" placeholder="Time"></td><td style="font-family:monospace;font-size:11px"><input style="background:transparent;border:none;color:var(--text-3);font-family:monospace;font-size:11px;width:40px;outline:none" placeholder="Q#"></td><td><input style="background:transparent;border:none;color:var(--text-1);font-size:12px;width:100%;outline:none" placeholder="Cue description"></td><td></td><td></td><td><input style="background:transparent;border:none;color:var(--text-3);font-size:11px;width:100%;outline:none" placeholder="Notes"></td>`; tbody.appendChild(tr); tr.querySelector('input').focus(); }
function toggleTask(el) { el.classList.toggle('done'); el.closest('.task-row')?.querySelector('.tn')?.classList.toggle('done'); }
function sendProdMsg() { const input=document.getElementById('prod-msg-input'); if(!input)return; const text=input.value.trim(); if(!text)return; const list=document.getElementById('prod-msg-list'); if(!list)return; const wrap=document.createElement('div'); wrap.className='msg-bubble-wrap mine'; wrap.innerHTML=`<div class="msg-bubble-col"><div class="msg-bubble mine">${text}</div><div class="msg-bubble-time">Just now</div></div>`; list.appendChild(wrap); list.scrollTop=list.scrollHeight; input.value=''; input.style.height='auto'; }
function autoResize(el) { el.style.height='auto'; el.style.height=Math.min(el.scrollHeight,100)+'px'; }

// Document system
const DOCS = {
  callsheet:{'title':'Call sheet — Jun 14, 2025','sections':[{title:'Show information',fields:[{l:'Production',v:'Summerset Corporate Gala'},{l:'Date',v:'June 14, 2025'},{l:'Venue',v:'Grand Ballroom, Las Vegas NV'},{l:'SM',v:'Sarah M.'},{l:'Producer',v:'Jordan Kim'}]},{title:'Crew calls',textarea:'3:00 PM — General crew call\n3:30 PM — Lighting focus & programming\n4:30 PM — Soundcheck\n5:00 PM — SM walkthrough\n6:00 PM — Crew ready / places'},{title:'Show schedule',textarea:'6:30 PM — Doors open\n7:40 PM — Welcome remarks\n~9:10 PM — Awards presentation\n11:15 PM — Closing'},{title:'Key contacts',textarea:'Jordan Kim (Producer) — (702) 555-0101\nSarah M. (SM) — (702) 555-0192\nTom R. (TD) — (702) 555-0233\nLisa T. (Venue) — (702) 555-0344'}]},
  'room-list':{title:'Room list — Summerset Gala',roomList:true},
  'prod-schedule':{title:'Production schedule — Summerset Gala','sections':[{title:'Dates & venue',fields:[{l:'Production',v:'Summerset Gala'},{l:'Venue',v:'Grand Ballroom, Las Vegas'},{l:'Dates',v:'Jun 12–15, 2025'}]},{title:'Phase schedule',textarea:'Jun 12 — Load-in\n  8:00 AM — All crew\nJun 13 — Tech\n  2:00 PM — Tech begins\nJun 14 — Show day\n  3:00 PM — Crew call\n  6:30 PM — Doors / show\nJun 15 — Strike\n  8:00 AM — All crew'}]},
  'crew-contact':{title:'Crew contact sheet',sections:[{title:'Production leadership',textarea:'Jordan Kim — Lead producer\n  (702) 555-0101\n\nSarah M. — Stage manager\n  (702) 555-0192'},{title:'Technical',textarea:'Tom R. — TD/AV — (702) 555-0233\nDana L. — LD — (702) 555-0244\nMarcus B. — FOH — (702) 555-0255'}]},
  'vendor-list':{title:'Vendor contact sheet',sections:[{title:'Confirmed vendors',textarea:'Apex Sound & Light — AV\n  (702) 555-9001\n\nDesert Bloom Florals — Décor\n  (702) 555-9002\n\nMGM Grand Catering — F&B\n  (702) 555-9003'}]},
  'site-survey':{title:'Site survey — Grand Ballroom',sections:[{title:'Venue details',fields:[{l:'Venue',v:'Grand Ballroom'},{l:'Ceiling',v:'32 ft'},{l:'Contact',v:'Lisa T. · (702) 555-0344'}]},{title:'Power & access',textarea:'Main power: 400A 3-phase — dock level\nLoading dock: North side, Harmon Ave\nFreight elevator: 10x12 ft, 5,000 lb'}]},
  'day-of':{title:'Day-of briefing — Jun 14, 2025',sections:[{title:'Show info',fields:[{l:'Show',v:'Summerset Corporate Gala'},{l:'Guests',v:'340'},{l:'SM',v:'Sarah M.'}]},{title:'Emergency contacts',textarea:'Emergency: 911\nVenue security: (702) 555-0300\nSM radio: Channel 3'}]},
  advance:{title:'Advance sheet',sections:[{title:'Overview',fields:[{l:'Event',v:'Summerset Gala'},{l:'Date',v:'June 14, 2025'}]},{title:'Requirements',textarea:'Power: 400A 3-phase\nCrew access: 8:00 AM\nParking: 6 vendor passes'}]},
};

function openDoc(id) {
  const doc=DOCS[id]; if(!doc)return;
  document.getElementById('doc-library-view').style.display='none';
  document.getElementById('doc-editor-view').style.display='block';
  document.getElementById('doc-editor-title').textContent=doc.title;
  if(doc.roomList){renderRoomList();return;}
  let html=`<input class="doc-title-input" value="${doc.title}">`;
  doc.sections.forEach(s=>{
    html+=`<div class="doc-section"><div class="doc-section-hd" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'flex':'none'"><div class="doc-section-title">${s.title}</div><div style="font-size:12px;color:var(--text-3)">▾</div></div><div class="doc-section-body">`;
    if(s.fields) s.fields.forEach(f=>{html+=`<div class="doc-field"><div class="doc-field-label">${f.l}</div><input class="doc-field-input" value="${f.v}"></div>`;});
    if(s.textarea) html+=`<textarea class="doc-field-textarea">${s.textarea}</textarea>`;
    html+=`</div></div>`;
  });
  document.getElementById('doc-editor-content').innerHTML=html;
}
function closeDoc() { document.getElementById('doc-library-view').style.display='block'; document.getElementById('doc-editor-view').style.display='none'; }
let roomCount=5;
function renderRoomList() {
  document.getElementById('doc-editor-content').innerHTML=`<input class="doc-title-input" value="Room list — Summerset Corporate Gala"><table class="room-list-table"><thead><tr><th>#</th><th>Name</th><th>Role</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Notes</th><th></th></tr></thead><tbody id="room-tbody">${roomRow(1,'Jordan Kim','Lead producer','2214','Jun 12','Jun 15')}${roomRow(2,'Sarah M.','Stage manager','2216','Jun 12','Jun 15')}${roomRow(3,'Tom R.','Technical director','2218','Jun 12','Jun 15')}${roomRow(4,'Dana L.','Lighting designer','','Jun 12','Jun 15')}</tbody></table><div style="padding:12px 14px;border-top:.5px solid var(--border)"><button class="btn-sm" onclick="addRoomRow()">+ Add crew member</button></div>`;
}
function roomRow(n,name,role,room,cin,cout) { return `<tr><td style="padding:10px 14px;color:var(--text-3);font-size:11px">${n}</td><td><input class="room-input" value="${name}"></td><td><input class="room-input" value="${role}" style="color:var(--text-2)"></td><td><input class="room-input" value="${room}" placeholder="TBD" style="font-family:monospace"></td><td><input class="room-input" value="${cin}"></td><td><input class="room-input" value="${cout}"></td><td><input class="room-input" placeholder="Notes" style="color:var(--text-3)"></td><td style="padding:0 8px"><button onclick="this.closest('tr').remove()" style="background:none;border:none;color:var(--text-3);cursor:pointer;font-size:12px;opacity:.4">✕</button></td></tr>`; }
function addRoomRow() { roomCount++; const tb=document.getElementById('room-tbody'); if(!tb)return; const tr=document.createElement('tr'); tr.innerHTML=roomRow(roomCount,'','','','',''); tb.appendChild(tr); }
</script>

<?php include ROOT_PATH . '/required/footer.php'; ?>
