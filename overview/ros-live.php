<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_login();
$page_title = 'Live ROS';
include __DIR__ . '/../header.php';
?>
<style>
main { padding-top:0; background:#080808; min-height:calc(100vh - var(--nav-h)); }
.app-wrap { max-width:1300px; margin:0 auto; padding:20px 20px 32px; }
/* Header */
.ros-hd { display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.live-badge { display:flex; align-items:center; gap:8px; background:rgba(232,53,10,.12); border:.5px solid rgba(232,53,10,.3); border-radius:6px; padding:6px 14px; }
.live-dot { width:8px; height:8px; border-radius:50%; background:#E8350A; animation:pulse 1.4s ease-in-out infinite; flex-shrink:0; }
@keyframes pulse { 0%,100%{box-shadow:0 0 0 3px rgba(232,53,10,.25)} 50%{box-shadow:0 0 0 7px rgba(232,53,10,.08)} }
.live-badge-txt { font-size:12px; font-weight:500; color:#E8350A; }
/* Layout */
.live-caller-wrap { display:grid; grid-template-columns:1fr 270px; gap:16px; }
/* Cue panel */
.cue-panel { background:#0A0A08; border:.5px solid rgba(255,255,255,.06); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; min-height:600px; }
.cue-panel-hd { padding:13px 18px; border-bottom:.5px solid rgba(255,255,255,.06); display:flex; align-items:center; justify-content:space-between; flex-shrink:0; flex-wrap:wrap; gap:10px; }
.cue-panel-meta { font-size:11px; color:rgba(255,255,255,.35); }
.cue-controls { display:flex; gap:8px; align-items:center; }
.btn-go   { background:#E8350A; color:#fff; border:none; font-family:var(--sans); font-size:15px; font-weight:500; padding:9px 30px; border-radius:7px; cursor:pointer; transition:opacity .15s; }
.btn-go:hover { opacity:.88; }
.btn-hold { background:rgba(196,154,0,.13); color:#C49A00; border:.5px solid rgba(196,154,0,.3); font-family:var(--sans); font-size:13px; padding:8px 16px; border-radius:7px; cursor:pointer; transition:all .15s; }
.btn-ros-back { background:transparent; color:rgba(255,255,255,.4); border:.5px solid rgba(255,255,255,.1); font-family:var(--sans); font-size:12px; padding:8px 14px; border-radius:7px; cursor:pointer; transition:all .15s; }
.btn-ros-back:hover { color:rgba(255,255,255,.7); }
.standing-by { background:rgba(232,53,10,.12); border:.5px solid rgba(232,53,10,.25); border-radius:8px; padding:11px 16px; display:flex; align-items:center; gap:12px; margin:12px 18px 0; }
.sb-label { font-size:10px; font-weight:500; color:#E8350A; text-transform:uppercase; letter-spacing:1.5px; flex-shrink:0; }
.sb-cue-text { font-size:14px; color:#fff; font-weight:500; flex:1; }
.sb-cue-num { font-size:11px; color:rgba(255,255,255,.3); font-family:monospace; }
.cue-list { flex:1; overflow-y:auto; }
.cue-list-row { display:grid; grid-template-columns:52px 1fr auto; border-bottom:.5px solid rgba(255,255,255,.04); cursor:pointer; transition:background .1s; }
.cue-list-row:hover { background:rgba(255,255,255,.02); }
.cue-list-row.current { background:rgba(232,53,10,.1); border-left:3px solid #E8350A; }
.cue-list-row.past { opacity:.28; }
.cue-num { font-family:monospace; font-size:10px; padding:13px 0 13px 18px; color:rgba(255,255,255,.35); }
.cue-body { padding:11px 8px; }
.cue-title { font-size:12px; color:rgba(255,255,255,.85); margin-bottom:2px; }
.cue-note  { font-size:10px; color:rgba(255,255,255,.25); }
.cue-dept  { padding:11px 16px; display:flex; align-items:center; }
.cue-dept span { font-size:10px; padding:2px 6px; border-radius:3px; font-family:monospace; }
.sec-hd-row { padding:7px 18px; border-bottom:.5px solid rgba(255,255,255,.04); background:rgba(232,184,75,.04); }
.sec-hd-text { font-size:10px; letter-spacing:1px; text-transform:uppercase; color:rgba(232,184,75,.4); }
/* Right panel */
.crew-view { background:#0A0A08; border:.5px solid rgba(255,255,255,.06); border-radius:12px; overflow:hidden; }
.crew-view-hd { background:#E8350A; padding:11px 16px; display:flex; align-items:center; justify-content:space-between; }
.crew-view-label { font-size:10px; font-weight:500; color:#fff; letter-spacing:1px; text-transform:uppercase; display:flex; align-items:center; gap:6px; }
.crew-view-dot { width:6px; height:6px; border-radius:50%; background:#fff; animation:pulse 1.4s ease-in-out infinite; }
.crew-clock { font-family:monospace; font-size:11px; color:rgba(255,255,255,.6); }
.crew-view-body { padding:16px; }
.crew-prev { font-size:10px; color:rgba(255,255,255,.22); font-family:monospace; margin-bottom:10px; }
.crew-cur  { font-family:var(--serif); font-size:20px; color:#fff; font-weight:400; line-height:1.25; margin-bottom:7px; }
.crew-detail { font-size:12px; color:rgba(255,255,255,.4); line-height:1.6; margin-bottom:14px; }
.crew-next-lbl { font-size:9px; color:rgba(255,255,255,.22); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:5px; }
.crew-next { font-size:13px; color:rgba(255,255,255,.5); }
.crew-divider { height:.5px; background:rgba(255,255,255,.07); margin:14px 0; }
.connected-card { background:#0A0A08; border:.5px solid rgba(255,255,255,.06); border-radius:12px; overflow:hidden; margin-top:13px; }
.connected-hd { padding:11px 16px; border-bottom:.5px solid rgba(255,255,255,.06); font-size:12px; font-weight:500; color:rgba(255,255,255,.7); display:flex; align-items:center; justify-content:space-between; }
.connected-count { font-size:10px; color:rgba(255,255,255,.3); }
.connected-row { display:flex; align-items:center; gap:10px; padding:10px 16px; border-bottom:.5px solid rgba(255,255,255,.04); }
.connected-row:last-child { border-bottom:none; }
.conn-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.conn-name { font-size:12px; color:rgba(255,255,255,.7); flex:1; }
.conn-role { font-size:10px; color:rgba(255,255,255,.3); }
.quick-msg-card { background:#0A0A08; border:.5px solid rgba(255,255,255,.06); border-radius:12px; overflow:hidden; margin-top:13px; }
.quick-msg-hd { padding:10px 16px; border-bottom:.5px solid rgba(255,255,255,.06); font-size:12px; font-weight:500; color:rgba(255,255,255,.6); }
.quick-msg-preview { padding:10px 16px; font-size:12px; color:rgba(255,255,255,.3); border-bottom:.5px solid rgba(255,255,255,.04); }
.quick-msg-row { display:flex; gap:8px; padding:10px 14px; }
.quick-msg-input { flex:1; background:rgba(255,255,255,.05); border:.5px solid rgba(255,255,255,.1); border-radius:6px; padding:7px 10px; font-family:var(--sans); font-size:12px; color:#fff; outline:none; }
.quick-msg-input::placeholder { color:rgba(255,255,255,.25); }
.quick-msg-send { width:32px; height:32px; border-radius:50%; background:var(--gold); border:none; cursor:pointer; font-size:14px; color:#000; font-weight:700; flex-shrink:0; transition:opacity .15s; }
.quick-msg-send:hover { opacity:.85; }
.btn-sm-ghost { background:rgba(255,255,255,.05); border:.5px solid rgba(255,255,255,.1); color:rgba(255,255,255,.4); font-family:var(--sans); font-size:11px; padding:6px 12px; border-radius:6px; cursor:pointer; text-decoration:none; transition:all .15s; }
.btn-sm-ghost:hover { color:rgba(255,255,255,.7); }
@media(max-width:900px) { .live-caller-wrap { grid-template-columns:1fr; } }
</style>

<div class="app-wrap">
  <!-- Header row -->
  <div class="ros-hd">
    <a class="btn-sm-ghost" href="/overview/production.php?id=gala&tab=ros">← ROS editor</a>
    <div class="live-badge"><div class="live-dot"></div><div class="live-badge-txt" id="live-status-label">LIVE · Cue 14 of 23</div></div>
    <div style="font-size:12px;color:rgba(255,255,255,.35)">Summerset Corporate Gala · Jun 14, 2025 · Show night 1</div>
    <div style="margin-left:auto">
      <button class="btn-sm-ghost" onclick="toggleFullscreen()">⛶ Fullscreen</button>
    </div>
  </div>

  <div class="live-caller-wrap">
    <!-- Cue panel -->
    <div class="cue-panel">
      <div class="cue-panel-hd">
        <div class="cue-panel-meta">Summerset Gala · Jun 14 · Show 1 · Sarah M. calling</div>
        <div class="cue-controls">
          <button class="btn-ros-back" onclick="liveBack()">‹ Back</button>
          <button class="btn-hold" id="hold-btn" onclick="toggleHold()">Hold</button>
          <button class="btn-go"   id="go-btn"   onclick="liveGo()">GO ›</button>
        </div>
      </div>
      <div class="standing-by">
        <div class="sb-label">▶ Standing by</div>
        <div class="sb-cue-text" id="live-callout-txt">Dinner service — music cross to ambient</div>
        <div class="sb-cue-num"  id="live-callout-q">Q12</div>
      </div>
      <div style="height:10px"></div>
      <div class="cue-list" id="live-list"></div>
    </div>

    <!-- Right panel -->
    <div style="display:flex;flex-direction:column;gap:0">
      <!-- Crew view -->
      <div class="crew-view">
        <div class="crew-view-hd">
          <div class="crew-view-label"><div class="crew-view-dot"></div>Crew view</div>
          <div class="crew-clock" id="live-clock">—</div>
        </div>
        <div class="crew-view-body">
          <div class="crew-prev" id="crew-prev-txt">↑ Q11 — Applause sting</div>
          <div class="crew-cur"  id="crew-cur-txt">Dinner service begins</div>
          <div class="crew-detail" id="crew-note-txt">House music cross to ambient. F&amp;B starts. SM holds for awards.</div>
          <div class="crew-divider"></div>
          <div class="crew-next-lbl">Next</div>
          <div class="crew-next" id="crew-next-txt">Q13 — Awards walk-up music</div>
        </div>
      </div>

      <!-- Connected crew -->
      <div class="connected-card">
        <div class="connected-hd">Connected<span class="connected-count">4 online</span></div>
        <div class="connected-row"><div class="conn-dot" style="background:#66BB6A"></div><div class="conn-name">Sarah M.</div><div class="conn-role">SM · Calling</div></div>
        <div class="connected-row"><div class="conn-dot" style="background:#66BB6A"></div><div class="conn-name">Tom R.</div><div class="conn-role">TD · iPad</div></div>
        <div class="connected-row"><div class="conn-dot" style="background:#66BB6A"></div><div class="conn-name">Dana L.</div><div class="conn-role">LD · Phone</div></div>
        <div class="connected-row"><div class="conn-dot" style="background:#66BB6A"></div><div class="conn-name">Marcus B.</div><div class="conn-role">FOH · Laptop</div></div>
        <div class="connected-row"><div class="conn-dot" style="background:rgba(255,255,255,.2)"></div><div class="conn-name">Ray G.</div><div class="conn-role">Crew · Offline</div></div>
      </div>

      <!-- Quick message -->
      <div class="quick-msg-card">
        <div class="quick-msg-hd">Team chat</div>
        <div class="quick-msg-preview" id="quick-msg-preview">Sarah M.: ROS loaded and ready. Cue 1 on standby.</div>
        <div class="quick-msg-row">
          <input type="text" class="quick-msg-input" id="quick-msg-input" placeholder="Quick message…" onkeydown="if(event.key==='Enter')sendQuickMsg()">
          <button class="quick-msg-send" onclick="sendQuickMsg()">↑</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const ROS_CUES = [
  {type:'sec', text:'PRE-SHOW'},
  {type:'cue', q:'Q1',  title:'House open',                    note:'Preset music begins',                       dept:'Audio', deptCls:'background:#1a3a4a;color:#4DB6AC', past:true},
  {type:'cue', q:'Q2',  title:'Welcome video playback',        note:'60s loop',                                  dept:'Video', deptCls:'background:#1a2a4a;color:#5B9FE0', past:true},
  {type:'sec', text:'ARRIVAL & RECEPTION'},
  {type:'cue', q:'Q3',  title:'Doors open',                    note:'Lighting shift to cocktail preset',         dept:'LX',    deptCls:'background:#3a2a0a;color:var(--gold)', past:true},
  {type:'cue', q:'Q4',  title:'Cocktail music — jazz set',     note:'Band starts. FOH 70dB',                     dept:'Audio', deptCls:'background:#1a3a4a;color:#4DB6AC', past:true},
  {type:'cue', q:'Q5',  title:'Welcome remarks — CEO',         note:'Podium mic up. Video to logo',              dept:'All',   deptCls:'background:#3a1a1a;color:#E8350A', past:true},
  {type:'sec', text:'DINNER SERVICE'},
  {type:'cue', q:'Q6',  title:'Transition to dinner seating',  note:'LX shift. Music cross to ambient',          dept:'Audio', deptCls:'background:#1a3a4a;color:#4DB6AC', past:true},
  {type:'cue', q:'Q7',  title:'Table video loops begin',       note:'Panel content — brand reels',               dept:'Video', deptCls:'background:#1a2a4a;color:#5B9FE0', past:true},
  {type:'cue', q:'Q8',  title:'First course service',          note:'F&B standby',                               dept:'SM',    deptCls:'background:#1a2a1a;color:#66BB6A', past:true},
  {type:'cue', q:'Q9',  title:'Brand film playback — 3min',    note:'All screens. Kill ambient audio',           dept:'All',   deptCls:'background:#3a1a1a;color:#E8350A', past:true},
  {type:'cue', q:'Q10', title:'Main course service',           note:'Ambient music back up',                     dept:'Audio', deptCls:'background:#1a3a4a;color:#4DB6AC', past:true},
  {type:'cue', q:'Q11', title:'Applause sting',                note:'30s bump on awards announce',               dept:'Audio', deptCls:'background:#1a3a4a;color:#4DB6AC', past:true},
  {type:'cue', q:'Q12', title:'Dinner service — music cross',  note:'Cross to ambient. F&B starts. Hold for SM', dept:'Audio', deptCls:'background:#1a3a4a;color:#4DB6AC', past:false, current:true},
  {type:'sec', text:'AWARDS CEREMONY'},
  {type:'cue', q:'Q13', title:'Awards walk-up music',          note:'LX shift to awards preset',                 dept:'Audio', deptCls:'background:#1a3a4a;color:#4DB6AC', past:false},
  {type:'cue', q:'Q14', title:'Award 1 — Innovation',         note:'Presenter to podium. Video: nominee reel',  dept:'All',   deptCls:'background:#3a1a1a;color:#E8350A', past:false},
  {type:'cue', q:'Q15', title:'Winner announcement',           note:'Spotlight SR. Music sting',                 dept:'All',   deptCls:'background:#3a1a1a;color:#E8350A', past:false},
  {type:'sec', text:'CLOSING'},
  {type:'cue', q:'Q16', title:'Closing remarks — VP',          note:'Podium mic. LX hold.',                      dept:'SM',    deptCls:'background:#1a2a1a;color:#66BB6A', past:false},
  {type:'cue', q:'Q17', title:'Thank you reel — 90s',          note:'All screens. Ambient audio.',               dept:'Video', deptCls:'background:#1a2a4a;color:#5B9FE0', past:false},
  {type:'cue', q:'Q18', title:'House lights — cocktail preset',note:'LX shift. Music up.',                       dept:'LX',    deptCls:'background:#3a2a0a;color:var(--gold)', past:false},
];

let currentCue = 12;

function renderCueList() {
  const el = document.getElementById('live-list'); if (!el) return;
  el.innerHTML = ROS_CUES.map((c,i) => {
    if (c.type==='sec') return `<div class="sec-hd-row"><div class="sec-hd-text">${c.text}</div></div>`;
    const isCurrent = c.current;
    return `<div class="cue-list-row${c.past?' past':''}${isCurrent?' current':''}" onclick="jumpToCue(${i})">
      <div class="cue-num">${c.q}</div>
      <div class="cue-body"><div class="cue-title">${c.title}</div><div class="cue-note">${c.note}</div></div>
      <div class="cue-dept"><span style="${c.deptCls}">${c.dept}</span></div>
    </div>`;
  }).join('');
  // Scroll current into view
  setTimeout(()=>{const cur=el.querySelector('.current');if(cur)cur.scrollIntoView({block:'center',behavior:'smooth'});},50);
}

function jumpToCue(idx) {
  ROS_CUES.forEach((c,i)=>{ if(c.type==='cue'){c.past=i<idx;c.current=i===idx;} });
  renderCueList(); updateCrewView();
}

function liveGo() {
  const curIdx = ROS_CUES.findIndex(c=>c.current);
  if (curIdx===-1) return;
  ROS_CUES[curIdx].past=true; ROS_CUES[curIdx].current=false;
  let next = curIdx+1;
  while (next<ROS_CUES.length && ROS_CUES[next].type==='sec') next++;
  if (next<ROS_CUES.length) ROS_CUES[next].current=true;
  renderCueList(); updateCrewView();
}

function liveBack() {
  const curIdx = ROS_CUES.findIndex(c=>c.current);
  if (curIdx<=0) return;
  if (ROS_CUES[curIdx]) { ROS_CUES[curIdx].current=false; ROS_CUES[curIdx].past=false; }
  let prev = curIdx-1;
  while (prev>0 && ROS_CUES[prev].type==='sec') prev--;
  if (ROS_CUES[prev]?.type==='cue') { ROS_CUES[prev].current=true; ROS_CUES[prev].past=false; }
  renderCueList(); updateCrewView();
}

function updateCrewView() {
  const curIdx = ROS_CUES.findIndex(c=>c.current);
  const cur  = curIdx>=0 ? ROS_CUES[curIdx] : null;
  let prevCue=null, nextCue=null;
  for (let i=curIdx-1;i>=0;i--)    { if(ROS_CUES[i].type==='cue'){prevCue=ROS_CUES[i];break;} }
  for (let i=curIdx+1;i<ROS_CUES.length;i++) { if(ROS_CUES[i].type==='cue'){nextCue=ROS_CUES[i];break;} }
  if (cur) {
    document.getElementById('crew-cur-txt').textContent  = cur.title;
    document.getElementById('crew-note-txt').textContent = cur.note;
    document.getElementById('live-callout-txt').textContent = cur.title;
    document.getElementById('live-callout-q').textContent   = cur.q;
    const cues = ROS_CUES.filter(c=>c.type==='cue'), total=cues.length, idx=cues.findIndex(c=>c.current)+1;
    document.getElementById('live-status-label').textContent = `LIVE · Cue ${idx} of ${total}`;
  }
  document.getElementById('crew-prev-txt').textContent = prevCue ? `↑ ${prevCue.q} — ${prevCue.title}` : '—';
  document.getElementById('crew-next-txt').textContent = nextCue ? `${nextCue.q} — ${nextCue.title}`   : 'End of show';
}

let holdActive = false;
function toggleHold() {
  holdActive = !holdActive;
  const btn = document.getElementById('hold-btn');
  btn.textContent = holdActive ? 'Release' : 'Hold';
  btn.style.background = holdActive ? 'rgba(232,53,10,.2)' : 'rgba(196,154,0,.13)';
  btn.style.color = holdActive ? '#E8350A' : '#C49A00';
  btn.style.borderColor = holdActive ? 'rgba(232,53,10,.4)' : 'rgba(196,154,0,.3)';
}

function sendQuickMsg() {
  const input=document.getElementById('quick-msg-input'); if(!input)return;
  const text=input.value.trim(); if(!text)return;
  document.getElementById('quick-msg-preview').textContent='You: '+text;
  input.value='';
}

function toggleFullscreen() {
  if (!document.fullscreenElement) document.documentElement.requestFullscreen?.();
  else document.exitFullscreen?.();
}

// Clock
function updateClock() { document.getElementById('live-clock').textContent = new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit',second:'2-digit'}); }
setInterval(updateClock,1000); updateClock();

renderCueList();
updateCrewView();
</script>

<?php include __DIR__ . '/../footer.php'; ?>
