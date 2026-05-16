<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_login();
$page_title = 'Calendar';
include __DIR__ . '/../header.php';
?>
<style>
main { padding-top:0; background:var(--black); min-height:calc(100vh - var(--nav-h)); }
.app-wrap { max-width:1100px; margin:0 auto; padding:32px 24px 60px; }
.ph-row { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
.ph-ey  { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); margin-bottom:5px; }
.ph-title { font-family:var(--disp); font-size:36px; letter-spacing:1.5px; color:var(--text-1); line-height:1; }
.ph-title em { font-style:normal; color:var(--gold); }
.cal-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:12px; }
.cal-nav-group { display:flex; align-items:center; gap:10px; }
.cal-nav-btn { background:transparent; border:.5px solid var(--border); color:var(--text-2); font-size:18px; width:32px; height:32px; border-radius:6px; cursor:pointer; transition:all .15s; display:flex; align-items:center; justify-content:center; }
.cal-nav-btn:hover { border-color:var(--gold-bd); color:var(--gold); }
.cal-month-title { font-family:var(--disp); font-size:22px; letter-spacing:1px; color:var(--text-1); min-width:160px; }
.cal-legend { display:flex; gap:14px; align-items:center; flex-wrap:wrap; }
.cal-leg-item { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--text-3); }
.cal-leg-dot { width:8px; height:8px; border-radius:50%; }
.cev-load   { background:#4DB6AC; } .cev-tech  { background:#5B9FE0; } .cev-show  { background:#E8350A; }
.cev-strike { background:#FF7043; } .cev-call  { background:#9C27B0; } .cev-mile  { background:var(--gold); } .cev-meet { background:#66BB6A; }
.cal-grid-wrap { background:var(--dark); border:.5px solid var(--border); border-radius:10px; overflow:hidden; margin-bottom:24px; }
.cal-dow-row { display:grid; grid-template-columns:repeat(7,1fr); border-bottom:.5px solid var(--border); }
.cal-dow { padding:10px; text-align:center; font-size:10px; letter-spacing:1px; text-transform:uppercase; color:var(--text-3); }
.cal-week { display:grid; grid-template-columns:repeat(7,1fr); border-bottom:.5px solid var(--border); }
.cal-week:last-child { border-bottom:none; }
.cal-day { min-height:90px; padding:8px 6px; border-right:.5px solid var(--border); position:relative; transition:background .12s; cursor:pointer; }
.cal-day:last-child { border-right:none; }
.cal-day:hover { background:var(--dark-2); }
.cal-day.other { opacity:.35; }
.cal-day.today .cal-day-num { background:var(--gold); color:#000; border-radius:50%; width:22px; height:22px; display:flex; align-items:center; justify-content:center; font-weight:700; }
.cal-day-num { font-size:12px; color:var(--text-2); margin-bottom:4px; }
.cal-event { display:block; font-size:10px; padding:2px 5px; border-radius:3px; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#fff; }
.up-row { display:flex; align-items:center; gap:14px; padding:12px 18px; border-bottom:.5px solid var(--border); }
.up-row:last-child { border-bottom:none; }
.up-date { text-align:center; flex-shrink:0; min-width:30px; }
.up-d { font-family:var(--disp); font-size:20px; color:var(--text-1); line-height:1; }
.up-m { font-size:9px; letter-spacing:1px; text-transform:uppercase; color:var(--text-3); }
.up-div { width:.5px; height:30px; background:var(--border); flex-shrink:0; }
.up-name { font-size:12px; color:var(--text-1); font-weight:500; }
.up-detail { font-size:10px; color:var(--text-3); margin-top:2px; }
.card { background:var(--dark); border:.5px solid var(--border); border-radius:10px; overflow:hidden; }
.btn-sm { font-family:var(--sans); font-size:11px; padding:5px 12px; border-radius:5px; border:.5px solid var(--border); background:transparent; color:var(--text-3); cursor:pointer; transition:all .15s; }
.btn-sm:hover { color:var(--text-1); border-color:rgba(255,255,255,.15); }
.btn-back { font-family:var(--sans); font-size:12px; color:var(--text-3); background:transparent; border:.5px solid var(--border); padding:7px 14px; border-radius:6px; text-decoration:none; transition:all .15s; }
.btn-back:hover { color:var(--text-1); }
.btn-gold { background:rgba(232,184,75,.15); color:var(--gold); border:.5px solid var(--gold-bd); font-family:var(--sans); font-size:12px; font-weight:600; padding:9px 18px; border-radius:6px; cursor:pointer; transition:all .15s; }
.btn-gold:hover { background:rgba(232,184,75,.25); }
/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal { background:var(--dark); border:.5px solid var(--border); border-radius:12px; width:500px; max-width:96vw; overflow:hidden; }
.modal-hd { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:.5px solid var(--border); }
.modal-title { font-size:14px; font-weight:600; color:var(--text-1); }
.modal-close { background:transparent; border:none; color:var(--text-3); font-size:18px; cursor:pointer; }
.modal-body { padding:20px; display:flex; flex-direction:column; gap:14px; }
.form-grp { display:flex; flex-direction:column; gap:6px; }
.form-lbl { font-size:11px; letter-spacing:.5px; text-transform:uppercase; color:var(--text-3); }
.form-inp, .form-sel { background:var(--dark-2); border:.5px solid var(--border); border-radius:6px; color:var(--text-1); font-family:var(--sans); font-size:13px; padding:9px 12px; outline:none; width:100%; }
.form-inp:focus { border-color:var(--gold); }
.form-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.ev-type-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:4px; }
.ev-type-btn { padding:10px 6px; border:.5px solid var(--border); border-radius:8px; text-align:center; cursor:pointer; transition:all .12s; }
.ev-type-btn:hover, .ev-type-btn.selected { border-color:var(--gold-bd); background:rgba(232,184,75,.07); }
.ev-type-icon { font-size:18px; margin-bottom:4px; }
.ev-type-label { font-size:10px; color:var(--text-3); }
.modal-ft { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px; border-top:.5px solid var(--border); }
.btn-ghost { background:transparent; border:.5px solid var(--border); color:var(--text-3); font-family:var(--sans); font-size:12px; padding:8px 16px; border-radius:6px; cursor:pointer; }
.btn-ghost:hover { color:var(--text-1); }
</style>

<div class="app-wrap">
  <div class="ph-row">
    <div>
      <div class="ph-ey">Schedule</div>
      <div class="ph-title">My <em>calendar</em></div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <a class="btn-back" href="/overview.overview.php">← Overview</a>
      <button class="btn-gold" onclick="document.getElementById('modal-add-event').classList.add('open')">+ Add event</button>
    </div>
  </div>

  <div class="cal-toolbar">
    <div class="cal-nav-group">
      <button class="cal-nav-btn" onclick="calNav(-1)">‹</button>
      <div class="cal-month-title" id="cal-title">June 2025</div>
      <button class="cal-nav-btn" onclick="calNav(1)">›</button>
      <button class="btn-sm" onclick="calGoToday()">Today</button>
    </div>
    <div class="cal-legend">
      <div class="cal-leg-item"><div class="cal-leg-dot cev-load"></div>Load-in</div>
      <div class="cal-leg-item"><div class="cal-leg-dot cev-tech"></div>Tech</div>
      <div class="cal-leg-item"><div class="cal-leg-dot cev-show"></div>Show</div>
      <div class="cal-leg-item"><div class="cal-leg-dot cev-strike"></div>Strike</div>
      <div class="cal-leg-item"><div class="cal-leg-dot cev-call"></div>Crew call</div>
      <div class="cal-leg-item"><div class="cal-leg-dot cev-mile"></div>Milestone</div>
      <div class="cal-leg-item"><div class="cal-leg-dot cev-meet"></div>Meeting</div>
    </div>
  </div>

  <div class="cal-grid-wrap">
    <div class="cal-dow-row">
      <div class="cal-dow">Sun</div><div class="cal-dow">Mon</div><div class="cal-dow">Tue</div>
      <div class="cal-dow">Wed</div><div class="cal-dow">Thu</div><div class="cal-dow">Fri</div><div class="cal-dow">Sat</div>
    </div>
    <div id="cal-weeks"></div>
  </div>

  <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--text-3);margin-bottom:14px">Upcoming events</div>
  <div class="card" id="upcoming-list"></div>
</div>

<!-- ADD EVENT MODAL -->
<div class="modal-overlay" id="modal-add-event" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal">
    <div class="modal-hd"><div class="modal-title">Add calendar event</div><button class="modal-close" onclick="document.getElementById('modal-add-event').classList.remove('open')">✕</button></div>
    <div class="modal-body">
      <div class="ev-type-grid">
        <div class="ev-type-btn selected" id="evt-load" onclick="selEvType('load',this)"><div class="ev-type-icon">🚛</div><div class="ev-type-label">Load-in</div></div>
        <div class="ev-type-btn" id="evt-tech"   onclick="selEvType('tech',this)"><div class="ev-type-icon">🔧</div><div class="ev-type-label">Tech</div></div>
        <div class="ev-type-btn" id="evt-show"   onclick="selEvType('show',this)"><div class="ev-type-icon">🔴</div><div class="ev-type-label">Show</div></div>
        <div class="ev-type-btn" id="evt-strike" onclick="selEvType('strike',this)"><div class="ev-type-icon">📦</div><div class="ev-type-label">Strike</div></div>
        <div class="ev-type-btn" id="evt-mile"   onclick="selEvType('mile',this)"><div class="ev-type-icon">🏁</div><div class="ev-type-label">Milestone</div></div>
        <div class="ev-type-btn" id="evt-meet"   onclick="selEvType('meet',this)"><div class="ev-type-icon">💬</div><div class="ev-type-label">Meeting</div></div>
      </div>
      <div class="form-grp"><div class="form-lbl">Event title</div><input class="form-inp" type="text" id="ev-title" placeholder="e.g. Load-in — Summerset Gala"></div>
      <div class="form-2">
        <div class="form-grp"><div class="form-lbl">Date</div><input class="form-inp" type="date" id="ev-date"></div>
        <div class="form-grp"><div class="form-lbl">Time</div><input class="form-inp" type="time" id="ev-time"></div>
      </div>
      <div class="form-grp"><div class="form-lbl">Production / project</div>
        <select class="form-sel" id="ev-prod"><option>Summerset Corporate Gala</option><option>Tech Summit 2025</option><option>Meridian Holiday Dinner</option><option>Sphere Residency Build</option><option>Downtown Venue Buildout</option><option>General</option></select>
      </div>
      <div class="form-grp"><div class="form-lbl">Notes</div><input class="form-inp" type="text" id="ev-notes" placeholder="Optional notes"></div>
    </div>
    <div class="modal-ft">
      <button class="btn-ghost" onclick="document.getElementById('modal-add-event').classList.remove('open')">Cancel</button>
      <button class="btn-gold" onclick="saveEvent()">Add to calendar</button>
    </div>
  </div>
</div>

<script>
const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const SHORT_MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
let calYear = 2025, calMonth = 5; // June 2025

const CAL_EVENTS = {
  '2025-6-12': [{t:'Load-in — Summerset Gala',cls:'cev-load'}],
  '2025-6-13': [{t:'Tech — Summerset Gala',cls:'cev-tech'}],
  '2025-6-14': [{t:'🔴 Show — Summerset Gala',cls:'cev-show'}],
  '2025-6-15': [{t:'Strike — Summerset Gala',cls:'cev-strike'}],
  '2025-6-22': [{t:'Milestone — Sphere Build',cls:'cev-mile'}],
  '2025-8-1':  [{t:'Load-in — Tech Summit',cls:'cev-load'}],
  '2025-8-3':  [{t:'Show — Tech Summit',cls:'cev-show'}],
  '2025-12-5': [{t:'Show — Meridian Dinner',cls:'cev-show'}],
};

function renderCal() {
  const title = document.getElementById('cal-title');
  if (title) title.textContent = `${MONTHS[calMonth]} ${calYear}`;
  const weeksEl = document.getElementById('cal-weeks');
  if (!weeksEl) return;
  const today = new Date();
  const firstDay = new Date(calYear, calMonth, 1).getDay();
  const daysInMonth = new Date(calYear, calMonth+1, 0).getDate();
  const prevDays = new Date(calYear, calMonth, 0).getDate();
  let html = '', dayCount = 0, cell = 0;
  const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
  for (let row = 0; row < totalCells/7; row++) {
    html += '<div class="cal-week">';
    for (let col = 0; col < 7; col++) {
      const idx = row*7 + col;
      let dayNum, isOther = false, m = calMonth, y = calYear;
      if (idx < firstDay) { dayNum = prevDays - firstDay + idx + 1; isOther = true; m = calMonth-1; if(m<0){m=11;y--;} }
      else if (idx >= firstDay + daysInMonth) { dayNum = idx - firstDay - daysInMonth + 1; isOther = true; m = calMonth+1; if(m>11){m=0;y++;} }
      else { dayNum = idx - firstDay + 1; }
      const isToday = !isOther && dayNum === today.getDate() && calMonth === today.getMonth() && calYear === today.getFullYear();
      const evKey = `${y}-${m+1}-${dayNum}`;
      const evs = CAL_EVENTS[evKey] || [];
      html += `<div class="cal-day${isOther?' other':''}${isToday?' today':''}">`;
      html += `<div class="cal-day-num">${dayNum}</div>`;
      evs.forEach(e => { html += `<span class="cal-event ${e.cls}">${e.t}</span>`; });
      html += '</div>';
    }
    html += '</div>';
  }
  weeksEl.innerHTML = html;
  renderUpcoming();
}

function calNav(dir) { calMonth += dir; if(calMonth>11){calMonth=0;calYear++;} if(calMonth<0){calMonth=11;calYear--;} renderCal(); }
function calGoToday() { const t=new Date(); calMonth=t.getMonth(); calYear=t.getFullYear(); renderCal(); }

function renderUpcoming() {
  const el = document.getElementById('upcoming-list');
  if (!el) return;
  const all = [];
  Object.entries(CAL_EVENTS).forEach(([key,evs]) => {
    const p=key.split('-').map(Number);
    evs.forEach(e => all.push({date:new Date(p[0],p[1]-1,p[2]),label:e.t,cls:e.cls}));
  });
  all.sort((a,b)=>a.date-b.date);
  const future = all.filter(e=>e.date>=new Date()).slice(0,8);
  if (!future.length) { el.innerHTML=`<div style="padding:20px;text-align:center;color:var(--text-3);font-size:13px">No upcoming events</div>`; return; }
  el.innerHTML = future.map(e=>`
    <div class="up-row">
      <div class="up-date"><div class="up-d">${e.date.getDate()}</div><div class="up-m">${SHORT_MONTHS[e.date.getMonth()]}</div></div>
      <div class="up-div"></div>
      <div><div class="up-name">${e.label}</div><div class="up-detail">${e.date.toLocaleDateString('en-US',{weekday:'long',year:'numeric',month:'long',day:'numeric'})}</div></div>
      <div style="margin-left:auto"><span class="cal-event ${e.cls}" style="font-size:10px;padding:2px 8px">${e.cls.replace('cev-','').replace('load','Load-in').replace('tech','Tech').replace('show','Show').replace('strike','Strike').replace('call','Crew call').replace('mile','Milestone').replace('meet','Meeting')}</span></div>
    </div>`).join('');
}

let selEvTypeVal = 'load';
function selEvType(type, el) {
  selEvTypeVal = type;
  document.querySelectorAll('.ev-type-btn').forEach(b=>b.classList.remove('selected'));
  el.classList.add('selected');
}

function saveEvent() {
  const title=document.getElementById('ev-title')?.value?.trim(); if(!title)return;
  const date=document.getElementById('ev-date')?.value; if(!date)return;
  const p=date.split('-').map(Number);
  const key=`${p[0]}-${p[1]}-${p[2]}`;
  const clsMap={load:'cev-load',tech:'cev-tech',show:'cev-show',strike:'cev-strike',call:'cev-call',mile:'cev-mile',meet:'cev-meet',other:'cev-meet'};
  if(!CAL_EVENTS[key])CAL_EVENTS[key]=[];
  CAL_EVENTS[key].push({t:title,cls:clsMap[selEvTypeVal]||'cev-meet'});
  document.getElementById('modal-add-event').classList.remove('open');
  document.getElementById('ev-title').value='';
  renderCal();
}

renderCal();
</script>

<?php include __DIR__ . '/../footer.php'; ?>
