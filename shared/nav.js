/* ============================================
   PRODUCTION CENTRAL — SHARED NAV
   shared/nav.js
   ============================================ */

// ── ACTIVE PAGE DETECTION ──
// Each page sets window.PC_PAGE before loading this script
// e.g. <script>window.PC_PAGE = 'dashboard'</script>

const PC_PAGES = {
  dashboard:   { label: 'Dashboard',    href: 'dashboard.html' },
  messages:    { label: 'Messages',     href: 'messages.html' },
  drive:       { label: 'My Drive',     href: 'drive.html' },
  projects:    { label: 'Projects',     href: 'projects.html' },
  productions: { label: 'Productions',  href: 'productions.html' },
  calendar:    { label: 'Calendar',     href: 'calendar.html' },
  tasks:       { label: 'Tasks',        href: 'tasks.html' },
};

// Resolve path prefix — pages in /app/ need '../' to reach /shared/
const IS_APP_PAGE = window.location.pathname.includes('/app/');
const BASE = IS_APP_PAGE ? '../' : './';
const APP  = IS_APP_PAGE ? '' : 'app/';

// ── BUILD NAV ──
function buildNav(breadcrumb) {
  const nav = document.getElementById('pc-nav');
  if (!nav) return;

  nav.innerHTML = `
    <a class="nav-logo" href="test-connection.php">PRODUCTION<span>.</span>CENTRAL</a>
    <div class="nav-bc" id="nav-bc">
      ${breadcrumb || '<span class="cur">' + (PC_PAGES[window.PC_PAGE]?.label || 'Dashboard') + '</span>'}
    </div>
    <div class="nav-search" style="margin-left:16px">
      <svg width="12" height="12" viewBox="0 0 16 16" fill="none">
        <circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="1.5"/>
        <path d="M11 11l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
      Search everything…
    </div>
    <div class="nav-right">
      <a class="nav-ib" href="${APP_PATH('messages.html')}">💬<div class="nav-pip"></div></a>
      <div class="nav-ib">🔔<div class="nav-pip"></div></div>
      <div class="nav-av">JK</div>
    </div>
  `;
}

function APP_PATH(page) {
  return IS_APP_PAGE ? page : 'app/' + page;
}

// ── BUILD SIDEBAR ──
function buildSidebar() {
  const sb = document.getElementById('pc-sidebar');
  if (!sb) return;

  const cur = window.PC_PAGE || '';

  sb.innerHTML = `
    <a class="sb-new" href="${APP_PATH('new-production.html')}">+ New project / production</a>

    <div class="sb-sec">
      <span class="sb-lbl">Workspace</span>
      ${sbLink('dashboard',  '◻',  'Dashboard',   '')}
      ${sbLink('messages',   '💬', 'Messages',    '<span class="sb-badge unread">4</span>')}
      ${sbLink('drive',      '📁', 'My Drive',    '')}
      ${sbLink('projects',   '🏗', 'Projects',    '<span class="sb-badge">2</span>')}
      ${sbLink('productions','🎭', 'Productions', '<span class="sb-badge">3</span>')}
      ${sbLink('calendar',   '📅', 'Calendar',    '')}
      ${sbLink('tasks',      '✅', 'Tasks',       '<span class="sb-badge">11</span>')}
    </div>

    <div class="sb-sec">
      <span class="sb-lbl">Active now</span>
      <div class="sb-item" onclick="location.href='${APP_PATH('production.html?id=gala')}'">
        <div class="sb-dot" style="background:#E24B4A"></div>
        <div class="sb-iname">Summerset Gala</div>
        <span class="sb-live-tag">LIVE</span>
      </div>
      <div class="sb-item" onclick="location.href='${APP_PATH('production.html?id=summit')}'">
        <div class="sb-dot" style="background:#378ADD"></div>
        <div class="sb-iname">Tech Summit 2025</div>
      </div>
      <div class="sb-item" onclick="location.href='${APP_PATH('project.html?id=sphere')}'">
        <div class="sb-dot" style="background:#4DB6AC"></div>
        <div class="sb-iname">Sphere Residency</div>
      </div>
    </div>

    <div class="sb-sec">
      <span class="sb-lbl">Platform</span>
      <a class="sb-link" href="#"><span class="icon">💬</span>Forum</a>
      <a class="sb-link" href="#"><span class="icon">🔧</span>Tools</a>
      <a class="sb-link" href="#"><span class="icon">🎓</span>Education</a>
    </div>

    <div class="sb-bottom">
      <div class="sb-uav">JK</div>
      <div>
        <div class="sb-uname">Jordan Kim</div>
        <div class="sb-urole">Senior producer</div>
      </div>
    </div>
  `;
}

function sbLink(page, icon, label, extra) {
  const cur = window.PC_PAGE || '';
  const active = cur === page ? ' active' : '';
  const href = APP_PATH(PC_PAGES[page]?.href || page + '.html');
  return `<a class="sb-link${active}" href="${href}"><span class="icon">${icon}</span>${label}${extra}</a>`;
}

// ── INIT ──
// nav.js is loaded with defer or at end of body, so DOM is ready.
// We use a small timeout to ensure any inline page scripts above have also run.
function pcInit() {
  buildNav();
  buildSidebar();
  setDate();

  const init = window.PC_INIT;
  if (!init) return;

  if (init === 'calendar') {
    if (typeof renderCal === 'function') renderCal();
    if (typeof window.renderUpcoming === 'function') {
      const orig = window.saveEvent;
      window.saveEvent = function() { if (typeof orig === 'function') orig(); window.renderUpcoming(); };
    }
  }
  if (init === 'ros-live') {
    if (typeof renderLiveList === 'function') renderLiveList();
    if (typeof updateLiveViews === 'function') updateLiveViews();
    if (typeof updateClock === 'function') { updateClock(); setInterval(updateClock, 15000); }
  }
  if (init === 'productions' && typeof window.renderProds === 'function') window.renderProds();
  if (init === 'projects'    && typeof window.renderProjs === 'function') window.renderProjs();
  if (init === 'tasks'       && typeof window.renderTasks === 'function') window.renderTasks();
  if (init === 'production') {
    if (typeof renderROS === 'function') renderROS();
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab && typeof window.switchTab === 'function') window.switchTab(tab);
  }
  if (init === 'project') {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab && typeof window.switchTab === 'function') window.switchTab(tab);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', pcInit);
} else {
  pcInit();
}

// ── DATE ──
function setDate() {
  const el = document.getElementById('dash-date');
  if (!el) return;
  const DAYS = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const now = new Date();
  el.textContent = `${DAYS[now.getDay()]}, ${MONTHS[now.getMonth()]} ${now.getDate()}`;
}

// ── SHARED UTILITIES ──

// Toggle task checkbox
function toggleTask(el) {
  el.classList.toggle('done');
  el.closest('.task-row')?.querySelector('.tn')?.classList.toggle('done');
}

// Auto-resize textarea
function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = el.scrollHeight + 'px';
}

// Modal open/close
function openModal(id) {
  document.getElementById('modal-' + id)?.classList.add('open');
}
function closeModal(id) {
  document.getElementById('modal-' + id)?.classList.remove('open');
}

// Send message (shared chat behavior)
function sendMessage(inputId, listId, placeholder) {
  const input = document.getElementById(inputId || 'msg-input');
  if (!input) return;
  const text = input.value.trim();
  if (!text) return;

  const list = document.getElementById(listId || 'msg-messages');
  if (!list) return;

  const wrap = document.createElement('div');
  wrap.className = 'msg-bubble-wrap mine';
  wrap.innerHTML = `
    <div class="msg-bubble-col">
      <div class="msg-bubble mine">${text}</div>
      <div class="msg-bubble-time">Just now</div>
    </div>`;
  list.appendChild(wrap);
  list.scrollTop = list.scrollHeight;

  input.value = '';
  input.style.height = 'auto';
}

// Message send on Enter (Shift+Enter for newline)
function msgKeyDown(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
}

// Calendar shared data
const CAL_EVENTS = {
  '2025-6-12': [{ t: '8am Load-in',      cls: 'cev-load' }, { t: 'All crew',       cls: 'cev-call' }],
  '2025-6-13': [{ t: '2pm Tech',          cls: 'cev-tech' }, { t: 'SM call 1pm',    cls: 'cev-call' }],
  '2025-6-14': [{ t: 'Crew call 3pm',     cls: 'cev-call' }, { t: 'Show 6:30pm',    cls: 'cev-show' }],
  '2025-6-15': [{ t: '8am Strike',        cls: 'cev-strike' }],
  '2025-6-22': [{ t: 'Milestone: Sign-off',cls: 'cev-mile' }],
  '2025-8-1':  [{ t: 'Load-in: Tech Summit',cls: 'cev-load' }],
  '2025-8-3':  [{ t: 'Show: Tech Summit',  cls: 'cev-show' }],
};

let calYear = 2025, calMonth = 5, selectedEvType = 'load';

function renderCal(targetId) {
  const el = document.getElementById(targetId || 'cal-weeks');
  if (!el) return;
  const titleEl = document.getElementById('cal-title');
  const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  if (titleEl) titleEl.textContent = `${MONTHS[calMonth]} ${calYear}`;

  const first = new Date(calYear, calMonth, 1).getDay();
  const days  = new Date(calYear, calMonth + 1, 0).getDate();
  const prev  = new Date(calYear, calMonth, 0).getDate();
  const today = new Date();

  let html = '', day = 1, nextDay = 1;
  for (let w = 0; w < 6; w++) {
    if (w > 0 && day > days) break;
    html += '<div class="cal-week">';
    for (let d = 0; d < 7; d++) {
      const cell = w * 7 + d;
      if (cell < first) {
        html += `<div class="cal-day other-month"><div class="cal-day-num">${prev - first + cell + 1}</div></div>`;
      } else if (day > days) {
        html += `<div class="cal-day other-month"><div class="cal-day-num">${nextDay++}</div></div>`;
      } else {
        const isToday = today.getFullYear() === calYear && today.getMonth() === calMonth && today.getDate() === day;
        const key  = `${calYear}-${calMonth + 1}-${day}`;
        const evs  = CAL_EVENTS[key] || [];
        html += `<div class="cal-day${isToday ? ' today' : ''}" onclick="calDayClick('${key}', ${day})">`;
        html += isToday
          ? `<div class="cal-day-num"><div style="background:var(--text-1);color:var(--black);width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px">${day}</div></div>`
          : `<div class="cal-day-num">${day}</div>`;
        evs.forEach(e => html += `<div class="cal-event ${e.cls}">${e.t}</div>`);
        html += '</div>';
        day++;
      }
    }
    html += '</div>';
  }
  el.innerHTML = html;
}

function calNav(dir) {
  calMonth += dir;
  if (calMonth > 11) { calMonth = 0; calYear++; }
  if (calMonth < 0)  { calMonth = 11; calYear--; }
  renderCal();
}

function calGoToday() {
  const t = new Date();
  calYear = t.getFullYear();
  calMonth = t.getMonth();
  renderCal();
}

function calDayClick(key, day) {
  const dateInput = document.getElementById('ev-date');
  if (dateInput) dateInput.value = `${calYear}-${String(calMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
  openModal('add-event');
}

function selectEvType(t) {
  selectedEvType = t;
  document.querySelectorAll('.ev-type-btn').forEach(b => b.classList.remove('selected'));
  document.getElementById('evt-' + t)?.classList.add('selected');
}

function saveEvent() {
  const title = document.getElementById('ev-title')?.value;
  const date  = document.getElementById('ev-date')?.value;
  if (!title || !date) { closeModal('add-event'); return; }
  const [y, m, d] = date.split('-').map(Number);
  const key = `${y}-${m}-${d}`;
  const CLS = { load:'cev-load',tech:'cev-tech',reh:'cev-reh',show:'cev-show',strike:'cev-strike',call:'cev-call',mile:'cev-mile',meet:'cev-meet',other:'cev-call' };
  const time = document.getElementById('ev-time')?.value || '';
  if (!CAL_EVENTS[key]) CAL_EVENTS[key] = [];
  CAL_EVENTS[key].push({ t: time ? `${time} ${title}` : title, cls: CLS[selectedEvType] || 'cev-meet' });
  closeModal('add-event');
  renderCal();
  if (document.getElementById('ev-title')) document.getElementById('ev-title').value = '';
}

// ── ROS SHARED DATA ──
const CUES = [
  { q:'Q1',  time:'6:00p', title:'House open — pre-show music',          dept:'all', dur:'—',    notes:'Ambient playlist 75%.', past:true,  sec:false },
  { q:'Q2',  time:'6:30p', title:'Doors open / cocktail reception',      dept:'sm',  dur:'60m',  notes:'House music up.',        past:true,  sec:false },
  { q:'Q3',  time:'6:30p', title:'LX: Cocktail hour wash',               dept:'lx',  dur:'—',    notes:'Warm amber, no specials.',past:true, sec:false },
  { q:'Q4',  time:'7:25p', title:'Announce: Guests to seats',            dept:'sm',  dur:'5m',   notes:'MC on SM cue.',          past:true,  sec:false },
  { q:'Q5',  time:'7:30p', title:'House music fade to 20%',              dept:'snd', dur:'30s',  notes:'Slow cross.',             past:true,  sec:false },
  { q:'Q6',  time:'7:30p', title:'LX: Dinner wash',                      dept:'lx',  dur:'—',    notes:'Chandelier 60%.',         past:true,  sec:false },
  { q:'Q7',  time:'7:35p', title:'Video: Welcome reel',                  dept:'vid', dur:'2m',   notes:'Full screen. Audio 85%.',past:true,  sec:false },
  { q:'Q8',  time:'7:38p', title:'CEO walk-up music',                    dept:'snd', dur:'—',    notes:'SM cues on CEO standing.',past:true, sec:false },
  { q:'Q9',  time:'7:39p', title:'Mic open — CEO remarks',               dept:'snd', dur:'~10m', notes:'Open podium mic.',        past:true,  sec:false },
  { q:'Q10', time:'7:49p', title:'LX: House back to dinner',             dept:'lx',  dur:'8ct',  notes:'Return to dinner wash.',  past:true,  sec:false },
  { q:'Q11', time:'7:50p', title:'Applause sting',                       dept:'snd', dur:'—',    notes:'SM calls on CEO step-back.',past:true,sec:false },
  { q:'',    time:'',      title:'DINNER SERVICE',                       dept:'',    dur:'',     notes:'',                        past:false, sec:true  },
  { q:'Q12', time:'7:52p', title:'Dinner service — music cross to ambient',dept:'snd',dur:'~75m',notes:'House to ambient. SM holds.',past:false,current:true,sec:false},
  { q:'Q13', time:'~9:10p',title:'Awards walk-up music — Award 1',       dept:'snd', dur:'—',    notes:'Upbeat sting on GO.',     past:false, sec:false },
  { q:'Q14', time:'~9:10p',title:'LX: Awards stage wash',                dept:'lx',  dur:'—',    notes:'Stage up, tables down.',  past:false, sec:false },
  { q:'',    time:'',      title:'LIVE MUSIC & RECEPTION',               dept:'',    dur:'',     notes:'',                        past:false, sec:true  },
  { q:'Q15', time:'~9:45p',title:'Band walk-up',                         dept:'sm',  dur:'—',    notes:'Band on stage. SM cues.', past:false, sec:false },
  { q:'Q16', time:'~9:48p',title:'LX: Live music scene',                 dept:'lx',  dur:'—',    notes:'Full stage wash, dance floor up.',past:false,sec:false},
  { q:'Q17', time:'11:10p',title:'Last call announcement',               dept:'sm',  dur:'—',    notes:'MC to podium.',           past:false, sec:false },
  { q:'Q18', time:'11:15p',title:'Closing remarks + walk-out music',     dept:'snd', dur:'—',    notes:'Exit music. House lights up.',past:false,sec:false},
  { q:'Q19', time:'11:25p',title:'House lights full — event close',      dept:'lx',  dur:'—',    notes:'Full house. Strike standby.',past:false,sec:false},
];

let liveIdx = 12;
const DL = { sm:'SM', lx:'LX', snd:'SND', vid:'VID', rig:'RIG', all:'ALL' };

function renderROS() {
  const tb = document.getElementById('ros-body');
  if (!tb) return;
  tb.innerHTML = '';
  CUES.forEach((c, i) => {
    if (c.sec) {
      const tr = document.createElement('tr');
      tr.className = 'sec-header-row';
      tr.innerHTML = `<td colspan="7"><input class="sec-header-input" value="${c.title}" placeholder="Section header…" onchange="CUES[${i}].title=this.value"><button onclick="CUES.splice(${i},1);renderROS()" style="background:none;border:none;color:var(--text-3);cursor:pointer;padding:6px 12px;opacity:.5">✕</button></td>`;
      tb.appendChild(tr);
      return;
    }
    const tr = document.createElement('tr');
    tr.className = 'ros-row-tr' + (c.current ? ' cur-cue' : '') + (c.past ? ' past' : '');
    tr.draggable = true;
    tr.dataset.i = i;
    tr.innerHTML = `
      <td><div class="ros-drag">⠿</div></td>
      <td><input class="ros-cell" value="${c.time}" style="width:55px" onchange="CUES[${i}].time=this.value"></td>
      <td><input class="ros-cell title-c" value="${c.title}" style="width:100%" onchange="CUES[${i}].title=this.value"></td>
      <td><select class="dept-sel d-${c.dept}" onchange="this.className='dept-sel d-'+this.value;CUES[${i}].dept=this.value">
        <option value="sm"  ${c.dept==='sm' ?'selected':''}>SM</option>
        <option value="lx"  ${c.dept==='lx' ?'selected':''}>LX</option>
        <option value="snd" ${c.dept==='snd'?'selected':''}>SND</option>
        <option value="vid" ${c.dept==='vid'?'selected':''}>VID</option>
        <option value="rig" ${c.dept==='rig'?'selected':''}>RIG</option>
        <option value="all" ${c.dept==='all'?'selected':''}>ALL</option>
      </select></td>
      <td><input class="ros-cell" value="${c.dur}" style="width:55px" onchange="CUES[${i}].dur=this.value"></td>
      <td><input class="ros-cell note-c" value="${c.notes}" style="width:100%" onchange="CUES[${i}].notes=this.value"></td>
      <td><button onclick="CUES.splice(${i},1);renderROS()" style="background:none;border:none;color:var(--text-3);cursor:pointer;padding:8px;font-size:11px;opacity:.4">✕</button></td>`;
    tb.appendChild(tr);
  });
  setupDrag();
}

function addCue() {
  CUES.push({ q:`Q${CUES.filter(c=>!c.sec).length+1}`, time:'', title:'', dept:'sm', dur:'', notes:'', past:false, sec:false });
  renderROS();
  setTimeout(() => document.getElementById('ros-body')?.lastElementChild?.querySelector('.title-c')?.focus(), 50);
}

function addSection() {
  CUES.push({ q:'', time:'', title:'NEW SECTION', dept:'', dur:'', notes:'', past:false, sec:true });
  renderROS();
}

function setupDrag() {
  let di = null;
  document.querySelectorAll('.ros-row-tr').forEach(r => {
    r.addEventListener('dragstart', () => { di = +r.dataset.i; r.style.opacity = '.4'; });
    r.addEventListener('dragend',   () => { r.style.opacity = ''; di = null; });
    r.addEventListener('dragover',  e => { e.preventDefault(); r.style.background = 'rgba(201,168,76,.06)'; });
    r.addEventListener('dragleave', () => r.style.background = '');
    r.addEventListener('drop', e => {
      e.preventDefault(); r.style.background = '';
      const dj = +r.dataset.i;
      if (di === null || di === dj) return;
      const [m] = CUES.splice(di, 1);
      CUES.splice(dj, 0, m);
      renderROS();
    });
  });
}

function renderPrint() {
  const el = document.getElementById('print-content');
  if (!el) return;
  el.innerHTML = `
    <div class="print-hd">
      <div class="print-show-name">SUMMERSET CORPORATE GALA</div>
      <div class="print-meta">
        <span>June 14, 2025</span><span>Grand Ballroom, Las Vegas NV</span>
        <span>SM: Sarah M.</span><span>Printed: ${new Date().toLocaleString()}</span>
      </div>
    </div>
    <table class="print-tbl">
      <thead><tr><th>Cue</th><th>Time</th><th>Item</th><th>Dept</th><th>Dur</th><th>Notes</th></tr></thead>
      <tbody>
        ${CUES.map(c => c.sec
          ? `<tr><td colspan="6" class="print-sec-hd" style="padding:8px 9px">${c.title}</td></tr>`
          : `<tr>
              <td style="font-family:monospace;font-size:10px;color:var(--lt-3)">${c.q}</td>
              <td style="font-family:monospace;font-size:11px">${c.time}</td>
              <td class="pitem">${c.title}</td>
              <td><span class="print-dept">${DL[c.dept]||''}</span></td>
              <td>${c.dur}</td>
              <td style="font-size:11px;color:var(--lt-3)">${c.notes}</td>
            </tr>`
        ).join('')}
      </tbody>
    </table>`;
}

// Live ROS caller
let onHold = false;

function renderLiveList() {
  const el = document.getElementById('live-list');
  if (!el) return;
  el.innerHTML = CUES.map((c, i) => {
    if (c.sec) return `<div style="padding:8px 18px;background:rgba(201,168,76,.05);border-bottom:0.5px solid rgba(255,255,255,.04)"><div style="font-size:10px;letter-spacing:1px;text-transform:uppercase;color:rgba(201,168,76,.4)">${c.title}</div></div>`;
    const isCur = i === liveIdx, isPast = i < liveIdx;
    return `<div onclick="liveJump(${i})" style="display:grid;grid-template-columns:52px 1fr auto;border-bottom:0.5px solid rgba(255,255,255,.04);cursor:pointer;${isCur?'background:rgba(232,53,10,.1);border-left:3px solid var(--live)':''}${isPast?'opacity:.3':''}">
      <div style="font-family:var(--mono);font-size:10px;color:${isCur?'var(--live)':'rgba(255,255,255,.22)'};padding:12px 0 12px 18px">${c.q}</div>
      <div style="padding:11px 8px"><div style="font-size:12px;color:${isCur?'#fff':'rgba(255,255,255,.7)'};font-weight:${isCur?'500':'400'};margin-bottom:2px">${c.title}</div><div style="font-size:10px;color:rgba(255,255,255,.25)">${c.notes}</div></div>
      <div style="padding:11px 16px"><span style="font-size:10px;padding:2px 6px;border-radius:3px;font-family:var(--mono);background:${c.dept==='lx'?'rgba(255,214,0,.12)':c.dept==='snd'?'rgba(0,188,212,.12)':c.dept==='sm'?'rgba(232,53,10,.12)':'rgba(255,255,255,.06)'};color:${c.dept==='lx'?'#FFD600':c.dept==='snd'?'#4DD0E1':c.dept==='sm'?'var(--live)':'rgba(255,255,255,.4)'}">${DL[c.dept]||''}</span></div>
    </div>`;
  }).join('');
  setTimeout(() => {
    const rows = el.querySelectorAll('[onclick]');
    rows[liveIdx]?.scrollIntoView({ block: 'center', behavior: 'smooth' });
  }, 80);
}

function updateLiveViews() {
  const c = CUES[liveIdx], p = CUES[liveIdx-1], n = CUES[liveIdx+1];
  const nreal = n?.sec ? CUES[liveIdx+2] : n;
  const g = id => document.getElementById(id);
  if (g('live-callout-txt')) g('live-callout-txt').textContent = c?.title || '';
  if (g('live-callout-q'))   g('live-callout-q').textContent   = c?.q    || '';
  if (g('crew-prev-txt')) g('crew-prev-txt').textContent = p?.sec ? (CUES[liveIdx-2] ? `↑ ${CUES[liveIdx-2].q} — ${CUES[liveIdx-2].title}` : '— Show start') : p ? `↑ ${p.q} — ${p.title}` : '— Show start';
  if (g('crew-cur-txt'))  g('crew-cur-txt').textContent  = c?.title || '';
  if (g('crew-note-txt')) g('crew-note-txt').textContent = c?.notes || '';
  if (g('crew-next-txt')) g('crew-next-txt').textContent = nreal ? `${nreal.q} — ${nreal.title}` : '— End of show';
}

function liveGo() {
  if (onHold) { toggleHold(); return; }
  if (liveIdx < CUES.length - 1) {
    CUES[liveIdx].past = true; CUES[liveIdx].current = false;
    liveIdx++;
    if (CUES[liveIdx].sec) liveIdx++;
    CUES[liveIdx].current = true;
    renderLiveList(); updateLiveViews();
  }
}

function liveBack() {
  if (liveIdx > 0) {
    CUES[liveIdx].current = false;
    liveIdx--;
    if (CUES[liveIdx].sec && liveIdx > 0) liveIdx--;
    CUES[liveIdx].past = false; CUES[liveIdx].current = true;
    renderLiveList(); updateLiveViews();
  }
}

function liveJump(i) {
  CUES[liveIdx].current = false;
  for (let j = 0; j < CUES.length; j++) { CUES[j].past = j < i; CUES[j].current = false; }
  liveIdx = i; CUES[i].current = true;
  renderLiveList(); updateLiveViews();
}

function toggleHold() {
  onHold = !onHold;
  const b = document.getElementById('hold-btn'), g = document.getElementById('go-btn');
  if (b) { b.textContent = onHold ? 'Release' : 'Hold'; b.style.background = onHold ? 'rgba(196,154,0,.25)' : 'rgba(196,154,0,.13)'; }
  if (g) g.textContent = onHold ? 'GO (release) ›' : 'GO ›';
}

function updateClock() {
  const el = document.getElementById('live-clock');
  if (el) el.textContent = new Date().toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', hour12:true });
}

// Wizard helpers
function selectWizType(t) {
  const p = document.getElementById('wtype-prod'), j = document.getElementById('wtype-proj');
  if (!p || !j) return;
  if (t === 'production') {
    p.style.border = '0.5px solid rgba(42,92,140,.4)'; p.style.background = 'rgba(42,92,140,.1)';
    j.style.border = '0.5px solid var(--border)';      j.style.background = 'var(--dark-3)';
  } else {
    j.style.border = '0.5px solid rgba(42,140,122,.4)'; j.style.background = 'rgba(42,140,122,.1)';
    p.style.border = '0.5px solid var(--border)';       p.style.background = 'var(--dark-3)';
  }
}

function updateWizPreview() {
  const name  = document.getElementById('wiz-name')?.value  || 'YOUR PRODUCTION NAME';
  const venue = document.getElementById('wiz-venue')?.value || 'Venue TBD';
  const city  = document.getElementById('wiz-city')?.value  || '';
  const date  = document.getElementById('wiz-date')?.value  || 'Date TBD';
  const pn = document.getElementById('wiz-preview-name');
  const pm = document.getElementById('wiz-preview-meta');
  if (pn) pn.textContent = name.toUpperCase();
  if (pm) pm.innerHTML = `<span>📅 ${date}</span><span>📍 ${[venue,city].filter(Boolean).join(', ')}</span>`;
}

function wizNext(step) {
  document.querySelectorAll('.wiz-panel').forEach((p, i) => p.classList.toggle('active', i === step - 1));
  document.querySelectorAll('.wiz-step').forEach((s, i) => {
    s.classList.toggle('active', i === step - 1);
    s.classList.toggle('done',   i <  step - 1);
  });
}

function addPhase() {
  const colors = ['#4DB6AC','#FFD600','#CE93D8','#E24B4A','#FF7043','#5B9FE0','#81C784'];
  const c = colors[document.querySelectorAll('.phase-row').length % colors.length];
  const row = document.createElement('div');
  row.className = 'phase-row';
  row.innerHTML = `<div class="phase-color" style="background:${c}"></div><input class="phase-name-inp" placeholder="Phase name"><div class="phase-dates"><input class="phase-date-inp" type="date"><span style="color:var(--text-3);font-size:11px">→</span><input class="phase-date-inp" type="date"></div><button onclick="this.closest('.phase-row').remove()" style="background:none;border:none;color:var(--text-3);cursor:pointer;font-size:14px;padding:4px">✕</button>`;
  document.getElementById('phase-list')?.appendChild(row);
}

function addCrewRow() {
  const row = document.createElement('div');
  row.className = 'crew-inv-row';
  row.innerHTML = `<input class="form-inp" type="email" placeholder="Email address" style="flex:2"><select class="form-sel" style="flex:1"><option>Stage manager</option><option>Technical director</option><option>Lighting designer</option><option>FOH engineer</option><option>Stagehand</option><option>Client</option><option>Other</option></select><button onclick="this.closest('.crew-inv-row').remove()" style="background:none;border:none;color:var(--text-3);cursor:pointer;font-size:16px;padding:0 4px">✕</button>`;
  document.getElementById('crew-list')?.appendChild(row);
}

function selectROSTemplate(t) {
  ['blank','corp','conc'].forEach(id => {
    const el = document.getElementById('rt-' + id);
    if (!el) return;
    const isActive = id === t || (t === 'corporate' && id === 'corp') || (t === 'concert' && id === 'conc');
    el.style.border     = isActive ? '0.5px solid rgba(201,168,76,.4)' : '0.5px solid var(--border)';
    el.style.background = isActive ? 'var(--gold-dim)' : 'var(--dark-3)';
  });
}
