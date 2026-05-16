<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_login();
$page_title = 'Projects';
include __DIR__ . '/../header.php';
?>
<style>
main { padding-top:0; background:var(--black); min-height:calc(100vh - var(--nav-h)); }
.app-wrap { max-width:1100px; margin:0 auto; padding:32px 24px 60px; }
.ph-row { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap; }
.ph-ey  { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); margin-bottom:5px; }
.ph-title { font-family:var(--disp); font-size:36px; letter-spacing:1.5px; color:var(--text-1); line-height:1; }
.ph-title em { font-style:normal; color:var(--gold); }
.filter-bar { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:20px; }
.f-btn { font-family:var(--sans); font-size:11px; padding:6px 14px; border-radius:6px; border:.5px solid var(--border); background:transparent; color:var(--text-3); cursor:pointer; transition:all .15s; }
.f-btn:hover, .f-btn.active { background:var(--dark-2); color:var(--text-1); border-color:rgba(255,255,255,.15); }
.proj-card { display:grid; grid-template-columns:5px 1fr; background:var(--dark); border:.5px solid var(--border); border-radius:12px; overflow:hidden; margin-bottom:14px; text-decoration:none; transition:border-color .15s; }
.proj-card:hover { border-color:rgba(255,255,255,.14); }
.proj-card-body { padding:22px 26px; }
.proj-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:6px; }
.proj-card-name { font-size:16px; font-weight:500; color:var(--text-1); margin-bottom:6px; }
.proj-card-meta { font-size:12px; color:var(--text-3); display:flex; gap:16px; flex-wrap:wrap; }
.proj-desc { font-size:12px; color:var(--text-3); margin-bottom:16px; line-height:1.6; }
.gantt-wrap { margin-bottom:14px; }
.gantt-months { display:flex; margin-bottom:6px; padding-left:150px; }
.gantt-month { flex:1; font-size:10px; color:var(--text-3); text-align:center; text-transform:uppercase; letter-spacing:1px; }
.gantt-row { display:flex; align-items:center; gap:10px; margin-bottom:5px; }
.gantt-label { width:140px; flex-shrink:0; font-size:11px; color:var(--text-2); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.gantt-track { flex:1; height:20px; background:var(--dark-3); border-radius:4px; position:relative; overflow:hidden; }
.gantt-bar { position:absolute; height:100%; border-radius:4px; display:flex; align-items:center; padding:0 8px; font-size:10px; color:rgba(255,255,255,.7); }
.gantt-today { position:absolute; top:0; bottom:0; width:1px; background:var(--gold); opacity:.5; }
.proj-footer { display:flex; align-items:center; gap:24px; flex-wrap:wrap; }
.prog-bar { background:var(--dark-2); border-radius:4px; height:5px; overflow:hidden; }
.prog-fill { height:100%; border-radius:4px; }
.stat-num { font-size:16px; font-weight:500; color:var(--text-1); text-align:center; }
.stat-lbl { font-size:10px; color:var(--text-3); text-align:center; }
.tag-chip { font-size:10px; padding:2px 8px; border-radius:4px; background:rgba(255,255,255,.05); color:var(--text-3); border:.5px solid var(--border); }
.pill { font-size:10px; letter-spacing:.5px; padding:2px 8px; border-radius:4px; font-weight:600; white-space:nowrap; }
.pill-teal { background:rgba(0,200,212,.10); color:#00c8d4; border:.5px solid rgba(0,200,212,.25); }
.pill-plan { background:rgba(91,159,224,.10); color:#5B9FE0; border:.5px solid rgba(91,159,224,.25); }
.btn-back { font-family:var(--sans); font-size:12px; color:var(--text-3); background:transparent; border:.5px solid var(--border); padding:7px 14px; border-radius:6px; text-decoration:none; transition:all .15s; }
.btn-back:hover { color:var(--text-1); }
.btn-gold { background:rgba(232,184,75,.15); color:var(--gold); border:.5px solid var(--gold-bd); font-family:var(--sans); font-size:12px; font-weight:600; padding:9px 18px; border-radius:6px; text-decoration:none; transition:all .15s; display:inline-block; }
.btn-gold:hover { background:rgba(232,184,75,.25); }
</style>

<div class="app-wrap">
  <div class="ph-row">
    <div>
      <div class="ph-ey">Long-horizon builds</div>
      <div class="ph-title">My <em>projects</em></div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <a class="btn-back" href="/overview/overview.php">← Overview</a>
      <a class="btn-gold" href="/overview/new-production.php">+ New project</a>
    </div>
  </div>

  <div class="filter-bar">
    <button class="f-btn active" onclick="filterProjs('all',this)">All</button>
    <button class="f-btn" onclick="filterProjs('active',this)">In progress</button>
    <button class="f-btn" onclick="filterProjs('planning',this)">Planning</button>
    <button class="f-btn" onclick="filterProjs('complete',this)">Complete</button>
  </div>

  <div id="proj-list"></div>
</div>

<script>
const PROJECTS = [
  { id:'sphere', name:'Sphere Residency Build — Phase 2', status:'active',   color:'#4DB6AC', location:'Las Vegas, NV',  dates:'Mar 2025 – Sep 2026', team:8,  budget:'$2.4M', progress:48, pill:'pill-teal', pillText:'In progress',
    phases:[{name:'AV infrastructure',pct:68,color:'#4DB6AC',left:'0%',width:'55%'},{name:'Rigging system',pct:42,color:'#2A8C7A',left:'10%',width:'45%'},{name:'Control room',pct:12,color:'#3a3a35',left:'30%',width:'50%'},{name:'Commissioning',pct:0,color:'#3a3a35',left:'70%',width:'28%'}],
    months:['Apr','May','Jun','Jul','Aug','Sep'], todayPct:'20%', tasks:14,
    description:'AV infrastructure, rigging, control room build, and full commissioning for a permanent residency installation.',
    tags:['Install','AV','Rigging','Permanent'], milestones:3 },
  { id:'venue',  name:'Downtown Venue Buildout — AV Infrastructure', status:'planning', color:'#5B9FE0', location:'Chicago, IL', dates:'Jan 2026 – Jun 2026', team:12, budget:'$840K', progress:18, pill:'pill-plan', pillText:'Planning',
    phases:[{name:'Design & engineering',pct:55,color:'#5B9FE0',left:'0%',width:'35%'},{name:'Procurement',pct:0,color:'#3a3a35',left:'25%',width:'30%'},{name:'Installation',pct:0,color:'#3a3a35',left:'45%',width:'40%'},{name:'Commissioning',pct:0,color:'#3a3a35',left:'80%',width:'18%'}],
    months:['Apr','May','Jun','Jul','Aug','Sep'], todayPct:'20%', tasks:22,
    description:'Full AV infrastructure for a new 800-capacity downtown venue — networking, audio, video, lighting control, and system integration.',
    tags:['Install','AV','Venue','New build'], milestones:2 },
];

let activeFilter = 'all';

function filterProjs(f, btn) {
  activeFilter = f;
  document.querySelectorAll('.f-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderProjs();
}

function renderProjs() {
  const el = document.getElementById('proj-list');
  const filtered = PROJECTS.filter(p => activeFilter === 'all' || p.status === activeFilter);
  if (!filtered.length) {
    el.innerHTML = `<div style="padding:48px;text-align:center;color:var(--text-3);background:var(--dark-2);border:.5px solid var(--border);border-radius:12px"><div style="font-size:32px;margin-bottom:12px">🏗</div><div>No projects found</div></div>`;
    return;
  }
  el.innerHTML = filtered.map(p => `
    <a class="proj-card" href="/overview/project.php?id=${p.id}">
      <div style="background:${p.color}"></div>
      <div class="proj-card-body">
        <div class="proj-card-top">
          <div>
            <div class="proj-card-name">${p.name}</div>
            <div class="proj-card-meta"><span>📍 ${p.location}</span><span>🗓 ${p.dates}</span><span>👥 ${p.team} members</span><span>💰 ${p.budget}</span></div>
          </div>
          <span class="pill ${p.pill}">${p.pillText}</span>
        </div>
        <div class="proj-desc">${p.description}</div>
        <div class="gantt-wrap">
          <div class="gantt-months">${p.months.map(m=>`<div class="gantt-month">${m}</div>`).join('')}</div>
          ${p.phases.map(ph=>`<div class="gantt-row"><div class="gantt-label">${ph.name}</div><div class="gantt-track"><div class="gantt-bar" style="left:${ph.left};width:${ph.width};background:${ph.color}">${ph.pct>0?ph.pct+'%':''}</div><div class="gantt-today" style="left:${p.todayPct}"></div></div></div>`).join('')}
        </div>
        <div class="proj-footer">
          <div style="flex:1;min-width:120px">
            <div style="display:flex;justify-content:space-between;font-size:10px;color:var(--text-3);margin-bottom:4px"><span>Overall progress</span><span>${p.progress}%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:${p.progress}%;background:${p.color}"></div></div>
          </div>
          <div style="display:flex;gap:20px;flex-shrink:0">
            <div><div class="stat-num">${p.tasks}</div><div class="stat-lbl">Open tasks</div></div>
            <div><div class="stat-num">${p.milestones}</div><div class="stat-lbl">Milestones left</div></div>
            <div><div class="stat-num">${p.team}</div><div class="stat-lbl">Team members</div></div>
          </div>
          <div style="display:flex;gap:6px;flex-wrap:wrap">${p.tags.map(t=>`<span class="tag-chip">${t}</span>`).join('')}</div>
        </div>
      </div>
    </a>`).join('');
}

renderProjs();
</script>

<?php include __DIR__ . '/../footer.php'; ?>
