<?php

$page_title = 'Tasks';

require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/functions.php';
include ROOT_PATH . '/required/header.php';
require_login();

?>
<style>
main { padding-top:0; background:var(--black); min-height:calc(100vh - var(--nav-h)); }
.app-wrap { max-width:1000px; margin:0 auto; padding:32px 24px 60px; }
.ph-row { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap; }
.ph-ey  { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); margin-bottom:5px; }
.ph-title { font-family:var(--disp); font-size:36px; letter-spacing:1.5px; color:var(--text-1); line-height:1; }
.ph-title em { font-style:normal; color:var(--gold); }
.stat-row { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:22px; }
.stat { background:var(--dark); border:.5px solid var(--border); border-radius:10px; padding:16px 18px; }
.stat-lbl { font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:var(--text-3); margin-bottom:8px; }
.stat-val { font-family:var(--disp); font-size:36px; letter-spacing:1px; color:var(--text-1); line-height:1; }
.stat-val.warn { color:#ffb300; }
.stat-sub { font-size:11px; color:var(--text-3); margin-top:5px; }
.stat-sub.up { color:#4caf50; }
.toolbar { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
.filter-bar { display:flex; gap:6px; flex-wrap:wrap; }
.f-btn { font-family:var(--sans); font-size:11px; padding:6px 14px; border-radius:6px; border:.5px solid var(--border); background:transparent; color:var(--text-3); cursor:pointer; transition:all .15s; }
.f-btn:hover, .f-btn.active { background:var(--dark-2); color:var(--text-1); border-color:rgba(255,255,255,.15); }
.card { background:var(--dark); border:.5px solid var(--border); border-radius:10px; overflow:hidden; margin-bottom:12px; }
.section-divider { padding:7px 18px; background:var(--dark-2); border-bottom:.5px solid var(--border); }
.section-divider-label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); }
.sd-overdue .section-divider-label { color:#ff6b6b; }
.sd-week    .section-divider-label { color:#ffb300; }
.task-row { display:flex; align-items:flex-start; gap:12px; padding:11px 18px; border-bottom:.5px solid var(--border); transition:background .12s; }
.task-row:last-child { border-bottom:none; }
.task-row:hover { background:var(--dark-2); }
.chk { width:16px; height:16px; border-radius:4px; border:.5px solid var(--border); flex-shrink:0; cursor:pointer; margin-top:2px; transition:all .15s; display:flex; align-items:center; justify-content:center; }
.chk:hover { border-color:var(--gold); }
.chk.done { background:rgba(76,175,80,.2); border-color:rgba(76,175,80,.4); }
.chk.done::after { content:'✓'; font-size:9px; color:#4caf50; }
.tb { flex:1; min-width:0; }
.tn  { font-size:12px; color:var(--text-1); }
.tn.done { text-decoration:line-through; color:var(--text-3); }
.tr-ref { font-size:10px; color:var(--text-3); margin-top:2px; display:flex; align-items:center; gap:8px; }
.td { font-size:11px; color:var(--text-3); flex-shrink:0; }
.td.over { color:#ff6b6b; }
.type-tag { font-size:9px; padding:1px 6px; border-radius:3px; }
.type-proj { background:rgba(0,200,212,.1); color:#4DB6AC; border:.5px solid rgba(42,140,122,.3); }
.type-prod { background:rgba(91,159,224,.1); color:#5B9FE0; border:.5px solid rgba(42,92,140,.3); }
.sort-sel { background:var(--dark-2); border:.5px solid var(--border); border-radius:6px; color:var(--text-1); font-family:var(--sans); font-size:12px; padding:6px 10px; outline:none; cursor:pointer; }
.btn-back { font-family:var(--sans); font-size:12px; color:var(--text-3); background:transparent; border:.5px solid var(--border); padding:7px 14px; border-radius:6px; text-decoration:none; transition:all .15s; }
.btn-back:hover { color:var(--text-1); }
.btn-gold { background:rgba(232,184,75,.15); color:var(--gold); border:.5px solid var(--gold-bd); font-family:var(--sans); font-size:12px; font-weight:600; padding:9px 18px; border-radius:6px; cursor:pointer; transition:all .15s; }
.btn-gold:hover { background:rgba(232,184,75,.25); }
/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal { background:var(--dark); border:.5px solid var(--border); border-radius:12px; width:480px; max-width:96vw; overflow:hidden; }
.modal-hd { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:.5px solid var(--border); }
.modal-title { font-size:14px; font-weight:600; color:var(--text-1); }
.modal-close { background:transparent; border:none; color:var(--text-3); font-size:18px; cursor:pointer; }
.modal-body { padding:20px; display:flex; flex-direction:column; gap:14px; }
.form-grp { display:flex; flex-direction:column; gap:6px; }
.form-lbl { font-size:11px; letter-spacing:.5px; text-transform:uppercase; color:var(--text-3); }
.form-inp, .form-sel { background:var(--dark-2); border:.5px solid var(--border); border-radius:6px; color:var(--text-1); font-family:var(--sans); font-size:13px; padding:9px 12px; outline:none; width:100%; }
.form-inp:focus, .form-sel:focus { border-color:var(--gold); }
.form-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.modal-ft { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px; border-top:.5px solid var(--border); }
.btn-ghost { background:transparent; border:.5px solid var(--border); color:var(--text-3); font-family:var(--sans); font-size:12px; padding:8px 16px; border-radius:6px; cursor:pointer; transition:all .15s; }
.btn-ghost:hover { color:var(--text-1); }
@media(max-width:640px) { .stat-row { grid-template-columns:1fr 1fr; } }
</style>

<div class="app-wrap">
  <div class="ph-row">
    <div>
      <div class="ph-ey">All work</div>
      <div class="ph-title">My <em>tasks</em></div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <a class="btn-back" href="/overview/overview.php">← Overview</a>
      <button class="btn-gold" onclick="document.getElementById('modal-add-task').classList.add('open')">+ Add task</button>
    </div>
  </div>

  <div class="stat-row">
    <div class="stat"><div class="stat-lbl">Total open</div><div class="stat-val" id="count-open">9</div><div class="stat-sub">across all work</div></div>
    <div class="stat"><div class="stat-lbl">Overdue</div><div class="stat-val warn" id="count-overdue">3</div><div class="stat-sub warn">need attention now</div></div>
    <div class="stat"><div class="stat-lbl">Due this week</div><div class="stat-val" id="count-week">3</div></div>
    <div class="stat"><div class="stat-lbl">Completed</div><div class="stat-val" id="count-done" style="color:#66BB6A">3</div><div class="stat-sub up">this week</div></div>
  </div>

  <div class="toolbar">
    <div class="filter-bar">
      <button class="f-btn active" onclick="setFilter('all',this)">All</button>
      <button class="f-btn" onclick="setFilter('overdue',this)">Overdue</button>
      <button class="f-btn" onclick="setFilter('productions',this)">Productions</button>
      <button class="f-btn" onclick="setFilter('projects',this)">Projects</button>
      <button class="f-btn" onclick="setFilter('done',this)">Completed</button>
    </div>
    <select class="sort-sel" onchange="setSortBy(this.value)">
      <option value="due">Sort by due date</option>
      <option value="prod">Sort by production</option>
      <option value="alpha">Sort alphabetically</option>
    </select>
  </div>

  <div id="task-list"></div>
</div>

<!-- ADD TASK MODAL -->
<div class="modal-overlay" id="modal-add-task" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal">
    <div class="modal-hd"><div class="modal-title">Add task</div><button class="modal-close" onclick="document.getElementById('modal-add-task').classList.remove('open')">✕</button></div>
    <div class="modal-body">
      <div class="form-grp"><div class="form-lbl">Task name</div><input class="form-inp" type="text" id="new-task-name" placeholder="What needs to get done?"></div>
      <div class="form-2">
        <div class="form-grp"><div class="form-lbl">Due date</div><input class="form-inp" type="date" id="new-task-date"></div>
        <div class="form-grp"><div class="form-lbl">Assign to</div><select class="form-sel" id="new-task-assign"><option>Jordan Kim (me)</option><option>Sarah M.</option><option>Tom R.</option><option>Dana L.</option><option>Mara L.</option><option>Unassigned</option></select></div>
      </div>
      <div class="form-grp"><div class="form-lbl">Production / project</div><select class="form-sel" id="new-task-prod"><option>Summerset Corporate Gala</option><option>Tech Summit 2025</option><option>Meridian Holiday Dinner</option><option>Sphere Residency Build</option><option>Downtown Venue Buildout</option><option>General</option></select></div>
      <div class="form-grp"><div class="form-lbl">Notes</div><textarea class="form-inp" rows="2" placeholder="Optional notes…" style="resize:vertical"></textarea></div>
    </div>
    <div class="modal-ft">
      <button class="btn-ghost" onclick="document.getElementById('modal-add-task').classList.remove('open')">Cancel</button>
      <button class="btn-gold" onclick="addTask()">Add task</button>
    </div>
  </div>
</div>

<script>
const TASKS = [
  { id:1,  name:'Confirm AV vendor contract',                    ref:'Tech Summit 2025',  type:'production', due:'Apr 27', overdue:true,  done:false, assign:'Jordan K.' },
  { id:2,  name:'Submit permit application — loading dock',      ref:'Sphere Residency',  type:'project',    due:'Apr 28', overdue:true,  done:false, assign:'Tom R.' },
  { id:3,  name:'Review insurance certificates from all vendors',ref:'Summerset Gala',    type:'production', due:'May 1',  overdue:true,  done:false, assign:'Jordan K.' },
  { id:4,  name:'Send runsheet to venue coordinator',            ref:'Summerset Gala',    type:'production', due:'May 3',  overdue:false, done:false, assign:'Jordan K.' },
  { id:5,  name:'Review structural engineering report',          ref:'Downtown Venue',    type:'project',    due:'May 8',  overdue:false, done:false, assign:'Dana K.' },
  { id:6,  name:'Get F&B headcount from client',                ref:'Meridian Dinner',   type:'production', due:'May 10', overdue:false, done:false, assign:'Jordan K.' },
  { id:7,  name:'Finalize cable routing plan with architect',    ref:'Sphere Residency',  type:'project',    due:'May 15', overdue:false, done:false, assign:'Jordan K.' },
  { id:8,  name:'Order Dante switches — 48-port PoE',           ref:'Sphere Residency',  type:'project',    due:'May 20', overdue:false, done:false, assign:'Unassigned' },
  { id:9,  name:'Confirm stage dimensions with venue',          ref:'Tech Summit 2025',  type:'production', due:'May 25', overdue:false, done:false, assign:'Mara L.' },
  { id:10, name:'Finalize menu selections with catering',        ref:'Summerset Gala',    type:'production', due:'Apr 22', overdue:false, done:true,  assign:'Jordan K.' },
  { id:11, name:'Sign Dante network design contract',            ref:'Sphere Residency',  type:'project',    due:'Apr 10', overdue:false, done:true,  assign:'Jordan K.' },
  { id:12, name:'Confirm final guest count with client',         ref:'Summerset Gala',    type:'production', due:'Apr 20', overdue:false, done:true,  assign:'Jordan K.' },
];

let activeFilter = 'all', sortBy = 'due';

function setFilter(f, btn) { activeFilter = f; document.querySelectorAll('.f-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); renderTasks(); }
function setSortBy(v) { sortBy = v; renderTasks(); }

function renderTasks() {
  const el = document.getElementById('task-list');
  let tasks = TASKS.filter(t => {
    if (activeFilter==='all')         return !t.done;
    if (activeFilter==='overdue')     return t.overdue && !t.done;
    if (activeFilter==='productions') return t.type==='production' && !t.done;
    if (activeFilter==='projects')    return t.type==='project' && !t.done;
    if (activeFilter==='done')        return t.done;
    return true;
  });
  if (sortBy==='alpha') tasks=[...tasks].sort((a,b)=>a.name.localeCompare(b.name));
  if (sortBy==='prod')  tasks=[...tasks].sort((a,b)=>a.ref.localeCompare(b.ref));
  if (!tasks.length) { el.innerHTML=`<div style="padding:48px;text-align:center;color:var(--text-3);background:var(--dark-2);border:.5px solid var(--border);border-radius:12px"><div style="font-size:32px;margin-bottom:12px">✅</div><div>All clear</div></div>`; return; }
  if (activeFilter==='all') {
    const overdue=tasks.filter(t=>t.overdue), week=tasks.filter(t=>!t.overdue&&['May 3','May 8','May 10'].some(d=>t.due===d)), upcoming=tasks.filter(t=>!t.overdue&&!['May 3','May 8','May 10'].some(d=>t.due===d));
    el.innerHTML = renderGroup('Overdue',overdue,'sd-overdue') + renderGroup('This week',week,'sd-week') + renderGroup('Upcoming',upcoming,'sd-upcoming');
  } else {
    el.innerHTML = `<div class="card">${tasks.map(renderTaskRow).join('')}</div>`;
  }
  updateCounts();
}

function renderGroup(label,tasks,cls) {
  if (!tasks.length) return '';
  return `<div class="card"><div class="section-divider ${cls}"><div class="section-divider-label">${label}</div></div>${tasks.map(renderTaskRow).join('')}</div>`;
}

function renderTaskRow(t) {
  const tt = t.type==='project'
    ? `<span class="type-tag type-proj">Project</span>`
    : `<span class="type-tag type-prod">Production</span>`;
  return `<div class="task-row" id="tr-${t.id}">
    <div class="chk${t.done?' done':''}" onclick="toggleTask(${t.id},this)"></div>
    <div class="tb"><div class="tn${t.done?' done':''}">${t.name}</div><div class="tr-ref">${tt}<span>${t.ref}</span><span>· ${t.assign}</span></div></div>
    <div class="td${t.overdue&&!t.done?' over':''}">${t.due}</div>
  </div>`;
}

function toggleTask(id, el) {
  const t = TASKS.find(t=>t.id===id); if (!t) return;
  t.done = !t.done; el.classList.toggle('done');
  el.closest('.task-row')?.querySelector('.tn')?.classList.toggle('done');
  updateCounts();
}

function updateCounts() {
  const open=TASKS.filter(t=>!t.done).length, over=TASKS.filter(t=>t.overdue&&!t.done).length, week=TASKS.filter(t=>!t.done&&['May 3','May 8','May 10'].includes(t.due)).length, done=TASKS.filter(t=>t.done).length;
  document.getElementById('count-open').textContent=open;
  document.getElementById('count-overdue').textContent=over;
  document.getElementById('count-week').textContent=week;
  document.getElementById('count-done').textContent=done;
}

function addTask() {
  const name=document.getElementById('new-task-name')?.value?.trim(); if(!name) return;
  const date=document.getElementById('new-task-date')?.value||'TBD', prod=document.getElementById('new-task-prod')?.value||'General', assign=document.getElementById('new-task-assign')?.value||'Unassigned';
  TASKS.unshift({id:Date.now(),name,ref:prod,type:['Sphere Residency Build','Downtown Venue Buildout'].includes(prod)?'project':'production',due:date,overdue:false,done:false,assign});
  document.getElementById('modal-add-task').classList.remove('open');
  document.getElementById('new-task-name').value='';
  renderTasks();
}

renderTasks();
</script>

<?php include ROOT_PATH . '/required/footer.php'; ?>
