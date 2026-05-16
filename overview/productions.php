<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_login();
$page_title = 'Productions';
include __DIR__ . '/../header.php';
?>
<style>
main { padding-top: 0; background: var(--black); min-height: calc(100vh - var(--nav-h)); }
.app-wrap { max-width: 1100px; margin: 0 auto; padding: 32px 24px 60px; }
.ph-row { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap; }
.ph-ey  { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); margin-bottom:5px; }
.ph-title { font-family:var(--disp); font-size:36px; letter-spacing:1.5px; color:var(--text-1); line-height:1; }
.ph-title em { font-style:normal; color:var(--gold); }
.filter-bar { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:20px; }
.f-btn { font-family:var(--sans); font-size:11px; padding:6px 14px; border-radius:6px; border:.5px solid var(--border); background:transparent; color:var(--text-3); cursor:pointer; transition:all .15s; }
.f-btn:hover, .f-btn.active { background:var(--dark-2); color:var(--text-1); border-color:rgba(255,255,255,.15); }
.prod-card { display:grid; grid-template-columns:5px 1fr; background:var(--dark); border:.5px solid var(--border); border-radius:12px; overflow:hidden; margin-bottom:10px; text-decoration:none; transition:border-color .15s; }
.prod-card:hover { border-color:rgba(255,255,255,.14); }
.prod-card-bar { /* background set inline */ }
.prod-card-body { padding:20px 24px; }
.prod-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:14px; }
.prod-card-name { font-size:16px; font-weight:500; color:var(--text-1); margin-bottom:6px; }
.prod-card-meta { font-size:12px; color:var(--text-3); display:flex; gap:16px; flex-wrap:wrap; }
.prod-card-stats { display:flex; align-items:center; gap:24px; flex-wrap:wrap; }
.prog-bar { background:var(--dark-2); border-radius:4px; height:5px; overflow:hidden; }
.prog-fill { height:100%; border-radius:4px; }
.stat-num { font-size:16px; font-weight:500; color:var(--text-1); text-align:center; }
.stat-lbl { font-size:10px; color:var(--text-3); text-align:center; }
.tag-chip { font-size:10px; padding:2px 8px; border-radius:4px; background:rgba(255,255,255,.05); color:var(--text-3); border:.5px solid var(--border); }
.pill { font-size:10px; letter-spacing:.5px; padding:2px 8px; border-radius:4px; font-weight:600; white-space:nowrap; }
.pill-live  { background:rgba(232,53,10,.12);  color:#E8350A; border:.5px solid rgba(232,53,10,.3); }
.pill-teal  { background:rgba(0,200,212,.10);  color:#00c8d4; border:.5px solid rgba(0,200,212,.25); }
.pill-plan  { background:rgba(91,159,224,.10); color:#5B9FE0; border:.5px solid rgba(91,159,224,.25); }
.pill-draft { background:rgba(255,255,255,.06); color:var(--text-3); border:.5px solid var(--border); }
.pill-wrap  { background:rgba(76,175,80,.10);  color:#66BB6A; border:.5px solid rgba(76,175,80,.25); }
.empty-state { padding:48px; text-align:center; color:var(--text-3); background:var(--dark-2); border:.5px solid var(--border); border-radius:12px; }
.btn-back { font-family:var(--sans); font-size:12px; color:var(--text-3); background:transparent; border:.5px solid var(--border); padding:7px 14px; border-radius:6px; text-decoration:none; transition:all .15s; }
.btn-back:hover { color:var(--text-1); border-color:rgba(255,255,255,.15); }
.btn-gold { background:rgba(232,184,75,.15); color:var(--gold); border:.5px solid var(--gold-bd); font-family:var(--sans); font-size:12px; font-weight:600; padding:9px 18px; border-radius:6px; text-decoration:none; transition:all .15s; display:inline-block; }
.btn-gold:hover { background:rgba(232,184,75,.25); }
</style>

<div class="app-wrap">
  <div class="ph-row">
    <div>
      <div class="ph-ey">Show management</div>
      <div class="ph-title">My <em>productions</em></div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <a class="btn-back" href="/overview/overview.php">← Overview</a>
      <a class="btn-gold" href="/overview/new-production.php">+ New production</a>
    </div>
  </div>

  <div class="filter-bar">
    <button class="f-btn active" onclick="filterProds('all',this)">All</button>
    <button class="f-btn" onclick="filterProds('live',this)">Live now</button>
    <button class="f-btn" onclick="filterProds('active',this)">Active</button>
    <button class="f-btn" onclick="filterProds('planning',this)">Planning</button>
    <button class="f-btn" onclick="filterProds('draft',this)">Draft</button>
    <button class="f-btn" onclick="filterProds('wrapped',this)">Wrapped</button>
  </div>

  <div id="prod-list"></div>
</div>

<script>
const PRODUCTIONS = [
  { id:'gala',   name:'Summerset Corporate Gala',      status:'live',     color:'#E24B4A', venue:'Grand Ballroom, Las Vegas NV', dates:'Jun 12–15, 2025', guests:'340',   budget:'$86K',  progress:72,  progressLabel:'Load-in Jun 12', pill:'pill-live', pillText:'Live tonight', tags:['Corporate event','Las Vegas'],   team:6, tasks:'2 open' },
  { id:'summit', name:'Tech Summit 2025',               status:'planning', color:'#378ADD', venue:'Convention Center, Austin TX',  dates:'Aug 1–4, 2025',  guests:'1,200', budget:'$142K', progress:34,  progressLabel:'96 days out',    pill:'pill-plan', pillText:'Planning',     tags:['Conference','Austin TX'],       team:8, tasks:'8 open' },
  { id:'dinner', name:'Meridian Holiday Dinner',        status:'draft',    color:'#1D9E75', venue:'The Rooftop, Los Angeles CA',   dates:'Dec 5, 2025',    guests:'120',   budget:'$28K',  progress:8,   progressLabel:'Draft stage',    pill:'pill-draft',pillText:'Draft',       tags:['Private event','Los Angeles'], team:3, tasks:'1 open' },
  { id:'apex',   name:'Product Launch — Apex Series',  status:'wrapped',  color:'#7F77DD', venue:'Studio B, New York City',       dates:'May 22, 2025',   guests:'80',    budget:'$44K',  progress:100, progressLabel:'Wrapped May 22', pill:'pill-wrap', pillText:'Wrapped',      tags:['Product launch','New York'],   team:5, tasks:'0 open' },
];

let activeFilter = 'all';

function filterProds(f, btn) {
  activeFilter = f;
  document.querySelectorAll('.f-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderProds();
}

function renderProds() {
  const el = document.getElementById('prod-list');
  const filtered = PRODUCTIONS.filter(p => {
    if (activeFilter === 'all')      return true;
    if (activeFilter === 'live')     return p.status === 'live';
    if (activeFilter === 'active')   return p.status === 'live' || p.status === 'active';
    if (activeFilter === 'planning') return p.status === 'planning';
    if (activeFilter === 'draft')    return p.status === 'draft';
    if (activeFilter === 'wrapped')  return p.status === 'wrapped';
    return true;
  });
  if (!filtered.length) {
    el.innerHTML = `<div class="empty-state"><div style="font-size:32px;margin-bottom:12px">🎭</div><div style="font-size:13px">No productions found</div></div>`;
    return;
  }
  el.innerHTML = filtered.map(p => `
    <a class="prod-card" href="/overview/production.php?id=${p.id}">
      <div class="prod-card-bar" style="background:${p.color}"></div>
      <div class="prod-card-body">
        <div class="prod-card-top">
          <div>
            <div class="prod-card-name">${p.name}</div>
            <div class="prod-card-meta">
              <span>📍 ${p.venue}</span><span>🗓 ${p.dates}</span>
              <span>👥 ${p.guests} guests</span><span>💰 ${p.budget}</span>
            </div>
          </div>
          <span class="pill ${p.pill}">${p.pillText}</span>
        </div>
        <div class="prod-card-stats">
          <div style="flex:1;min-width:120px">
            <div style="display:flex;justify-content:space-between;font-size:10px;color:var(--text-3);margin-bottom:4px"><span>Tasks complete</span><span>${p.progress}%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:${p.progress}%;background:${p.color}"></div></div>
          </div>
          <div style="display:flex;gap:20px;flex-shrink:0">
            <div><div class="stat-num">${p.team}</div><div class="stat-lbl">Team</div></div>
            <div><div class="stat-num">${p.tasks}</div><div class="stat-lbl">Tasks</div></div>
            <div><div class="stat-num" style="font-size:12px">${p.progressLabel}</div><div class="stat-lbl">Status</div></div>
          </div>
          <div style="display:flex;gap:6px;flex-wrap:wrap">${p.tags.map(t=>`<span class="tag-chip">${t}</span>`).join('')}</div>
        </div>
      </div>
    </a>`).join('');
}

renderProds();
</script>

<?php include __DIR__ . '/../footer.php'; ?>
