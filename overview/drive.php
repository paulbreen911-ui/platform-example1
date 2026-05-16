<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_login();
$page_title = 'Drive';
include __DIR__ . '/../header.php';
?>
<style>
main { padding-top:0; background:var(--black); min-height:calc(100vh - var(--nav-h)); }
.app-wrap { max-width:1200px; margin:0 auto; padding:24px 24px 0; }
.ph-row { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
.ph-ey  { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); margin-bottom:5px; }
.ph-title { font-family:var(--disp); font-size:36px; letter-spacing:1.5px; color:var(--text-1); line-height:1; }
.ph-title em { font-style:normal; color:var(--gold); }
.drive-layout { display:grid; grid-template-columns:240px 1fr; height:calc(100vh - var(--nav-h) - 110px); border:.5px solid var(--border); border-radius:12px; overflow:hidden; }
/* Sidebar */
.drive-sidebar { background:var(--dark); border-right:.5px solid var(--border); display:flex; flex-direction:column; overflow-y:auto; }
.drive-sidebar-hd { padding:14px 16px; border-bottom:.5px solid var(--border); }
.drive-sidebar-title { font-size:13px; font-weight:600; color:var(--text-1); margin-bottom:2px; }
.drive-folder-row { display:flex; align-items:center; gap:8px; padding:8px 14px; cursor:pointer; transition:background .12s; }
.drive-folder-row:hover, .drive-folder-row.active { background:var(--dark-2); }
.drive-folder-row.active .drive-folder-name { color:var(--gold); }
.drive-sub-row { padding-left:28px; }
.drive-folder-icon { font-size:14px; flex-shrink:0; width:18px; text-align:center; }
.drive-folder-name { font-size:12px; color:var(--text-2); flex:1; }
.drive-folder-count { font-size:10px; color:var(--text-3); }
.drive-section-lbl { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); padding:12px 14px 4px; display:block; }
/* Main */
.drive-main { display:flex; flex-direction:column; overflow:hidden; background:var(--black); }
.drive-main-hd { display:flex; align-items:center; justify-content:space-between; padding:12px 18px; border-bottom:.5px solid var(--border); background:var(--dark); flex-shrink:0; }
.drive-path { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--text-3); flex-wrap:wrap; }
.drive-path span { cursor:pointer; transition:color .12s; }
.drive-path span:hover { color:var(--text-1); }
.drive-path .sep { opacity:.3; cursor:default; }
.drive-view-toggle { display:flex; }
.drive-view-btn { background:transparent; border:.5px solid var(--border); color:var(--text-3); font-size:14px; width:30px; height:30px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .12s; }
.drive-view-btn:first-child { border-radius:5px 0 0 5px; }
.drive-view-btn:last-child  { border-radius:0 5px 5px 0; border-left:none; }
.drive-view-btn.active, .drive-view-btn:hover { background:var(--dark-2); color:var(--text-1); }
#drive-content { flex:1; overflow-y:auto; padding:16px; }
/* Grid view */
.drive-file-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:10px; }
.drive-file-card { background:var(--dark); border:.5px solid var(--border); border-radius:8px; padding:14px 12px; display:flex; flex-direction:column; gap:6px; text-decoration:none; transition:border-color .12s; }
.drive-file-card:hover { border-color:var(--gold-bd); }
.drive-file-icon { font-size:22px; }
.drive-file-name { font-size:12px; color:var(--text-1); font-weight:500; line-height:1.4; }
.drive-file-meta { font-size:10px; color:var(--text-3); }
.drive-file-tag { font-size:9px; padding:2px 6px; border-radius:3px; align-self:flex-start; font-weight:600; }
/* List view */
.drive-file-list { display:flex; flex-direction:column; }
.drive-file-row { display:flex; align-items:center; gap:12px; padding:10px 14px; border-bottom:.5px solid var(--border); text-decoration:none; transition:background .12s; }
.drive-file-row:last-child { border-bottom:none; }
.drive-file-row:hover { background:var(--dark-2); }
.drive-file-row-icon { font-size:18px; flex-shrink:0; width:24px; text-align:center; }
.drive-file-row-name { flex:1; font-size:13px; color:var(--text-1); }
.drive-file-row-meta { font-size:11px; color:var(--text-3); flex-shrink:0; }
/* Tag colours */
.dft-live   { background:rgba(232,53,10,.12);  color:#E8350A; border:.5px solid rgba(232,53,10,.3); }
.dft-gen    { background:rgba(91,159,224,.10); color:#5B9FE0; border:.5px solid rgba(91,159,224,.25); }
.dft-upload { background:rgba(232,184,75,.10); color:var(--gold); border:.5px solid var(--gold-bd); }
.btn-back { font-family:var(--sans); font-size:12px; color:var(--text-3); background:transparent; border:.5px solid var(--border); padding:7px 14px; border-radius:6px; text-decoration:none; transition:all .15s; }
.btn-back:hover { color:var(--text-1); }
.btn-ghost { font-family:var(--sans); font-size:12px; color:var(--text-3); background:transparent; border:.5px solid var(--border); padding:8px 16px; border-radius:6px; cursor:pointer; transition:all .15s; }
.btn-ghost:hover { color:var(--text-1); }
.btn-gold { background:rgba(232,184,75,.15); color:var(--gold); border:.5px solid var(--gold-bd); font-family:var(--sans); font-size:12px; font-weight:600; padding:9px 18px; border-radius:6px; cursor:pointer; transition:all .15s; }
.btn-gold:hover { background:rgba(232,184,75,.25); }
@media(max-width:700px) { .drive-layout { grid-template-columns:1fr; height:auto; } .drive-sidebar { max-height:220px; } }
</style>

<div class="app-wrap">
  <div class="ph-row">
    <div>
      <div class="ph-ey">File storage</div>
      <div class="ph-title">My <em>Drive</em></div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <a class="btn-back" href="/overview/overview.php">← Overview</a>
      <button class="btn-ghost" onclick="triggerUpload()">↑ Upload</button>
      <button class="btn-gold">+ New document</button>
    </div>
  </div>

  <div class="drive-layout">
    <!-- Sidebar -->
    <div class="drive-sidebar">
      <div class="drive-sidebar-hd">
        <div class="drive-sidebar-title">My Drive</div>
        <div style="font-size:11px;color:var(--text-3)">12.4 MB used</div>
      </div>
      <div class="drive-folder-row active" onclick="setFolder('all')"    id="fold-all">    <div class="drive-folder-icon">🗂</div><div class="drive-folder-name">All files</div></div>
      <div class="drive-folder-row"        onclick="setFolder('recent')" id="fold-recent"><div class="drive-folder-icon">🕐</div><div class="drive-folder-name">Recent</div></div>
      <div class="drive-folder-row"        onclick="setFolder('shared')" id="fold-shared"><div class="drive-folder-icon">👥</div><div class="drive-folder-name">Shared with me</div></div>
      <span class="drive-section-lbl">Productions</span>
      <div class="drive-folder-row"       onclick="setFolder('gala')"        id="fold-gala"><div class="drive-folder-icon">📁</div><div class="drive-folder-name">Summerset Gala</div><div class="drive-folder-count">12</div></div>
      <div class="drive-folder-row drive-sub-row" onclick="setFolder('gala-ros')"   id="fold-gala-ros">  <div class="drive-folder-icon">▶</div><div class="drive-folder-name">Run of show</div><div class="drive-folder-count">3</div></div>
      <div class="drive-folder-row drive-sub-row" onclick="setFolder('gala-calls')" id="fold-gala-calls"><div class="drive-folder-icon">📋</div><div class="drive-folder-name">Call sheets</div><div class="drive-folder-count">4</div></div>
      <div class="drive-folder-row drive-sub-row" onclick="setFolder('gala-docs')"  id="fold-gala-docs"> <div class="drive-folder-icon">📄</div><div class="drive-folder-name">Documents</div><div class="drive-folder-count">5</div></div>
      <div class="drive-folder-row drive-sub-row" onclick="setFolder('gala-files')" id="fold-gala-files"><div class="drive-folder-icon">📎</div><div class="drive-folder-name">Uploaded files</div><div class="drive-folder-count">3</div></div>
      <div class="drive-folder-row"       onclick="setFolder('summit')"      id="fold-summit"><div class="drive-folder-icon">📁</div><div class="drive-folder-name">Tech Summit 2025</div><div class="drive-folder-count">7</div></div>
      <div class="drive-folder-row drive-sub-row" onclick="setFolder('summit-ros')"  id="fold-summit-ros"> <div class="drive-folder-icon">▶</div><div class="drive-folder-name">Run of show</div><div class="drive-folder-count">1</div></div>
      <div class="drive-folder-row drive-sub-row" onclick="setFolder('summit-docs')" id="fold-summit-docs"><div class="drive-folder-icon">📄</div><div class="drive-folder-name">Documents</div><div class="drive-folder-count">4</div></div>
      <div class="drive-folder-row"       onclick="setFolder('dinner')"      id="fold-dinner"><div class="drive-folder-icon">📁</div><div class="drive-folder-name">Meridian Dinner</div><div class="drive-folder-count">2</div></div>
      <span class="drive-section-lbl">Projects</span>
      <div class="drive-folder-row"       onclick="setFolder('sphere')"      id="fold-sphere"><div class="drive-folder-icon">📁</div><div class="drive-folder-name">Sphere Residency</div><div class="drive-folder-count">18</div></div>
      <div class="drive-folder-row drive-sub-row" onclick="setFolder('sphere-docs')"  id="fold-sphere-docs"> <div class="drive-folder-icon">📄</div><div class="drive-folder-name">Documents</div><div class="drive-folder-count">6</div></div>
      <div class="drive-folder-row drive-sub-row" onclick="setFolder('sphere-files')" id="fold-sphere-files"><div class="drive-folder-icon">📎</div><div class="drive-folder-name">Uploaded files</div><div class="drive-folder-count">12</div></div>
      <div class="drive-folder-row"       onclick="setFolder('venue-build')" id="fold-venue-build"><div class="drive-folder-icon">📁</div><div class="drive-folder-name">Downtown Venue</div><div class="drive-folder-count">9</div></div>
    </div>

    <!-- Main area -->
    <div class="drive-main">
      <div class="drive-main-hd">
        <div class="drive-path" id="drive-path">
          <span onclick="setFolder('all')">My Drive</span><span class="sep">/</span><span style="color:var(--text-1)">All files</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <div style="font-size:12px;color:var(--text-3)" id="drive-count"></div>
          <div class="drive-view-toggle">
            <button class="drive-view-btn active" id="view-grid" onclick="setView('grid')" title="Grid">⊞</button>
            <button class="drive-view-btn"        id="view-list" onclick="setView('list')" title="List">☰</button>
          </div>
        </div>
      </div>
      <div id="drive-content"></div>
    </div>
  </div>
</div>

<script>
const FILES = {
  all:[
    {icon:'▶',name:'Run of show — Show night 1',    tag:'Live doc',  cls:'dft-live',   meta:'Edited today · Summerset Gala',        href:'/overview/production.php?id=gala&tab=ros'},
    {icon:'📋',name:'Call sheet — Jun 14 (Show day)',tag:'Live doc',  cls:'dft-live',   meta:'Edited today · Summerset Gala',        href:'/overview/production.php?id=gala&tab=callsheet'},
    {icon:'📅',name:'Production schedule',           tag:'Live doc',  cls:'dft-live',   meta:'Edited Jun 8 · Summerset Gala',        href:'/overview/production.php?id=gala&tab=documents'},
    {icon:'👥',name:'Crew contact sheet',            tag:'Auto-gen',  cls:'dft-gen',    meta:'Auto-generated · Summerset Gala',      href:'/overview/production.php?id=gala&tab=documents'},
    {icon:'📄',name:'AV_Contract_Signed.pdf',        tag:'Upload',    cls:'dft-upload', meta:'Tom R. · 2h ago · Summerset Gala',    href:'#'},
    {icon:'📊',name:'Budget_Final.xlsx',             tag:'Upload',    cls:'dft-upload', meta:'Jordan K. · Apr 22 · Summerset Gala', href:'#'},
    {icon:'▶',name:'Run of show — Tech Summit Day 1',tag:'doc',       cls:'dft-gen',    meta:'Edited May 10 · Tech Summit',          href:'/overview/production.php?id=summit&tab=ros'},
    {icon:'📄',name:'AV_System_Design_v3.pdf',       tag:'Upload',    cls:'dft-upload', meta:'Tom R. · Apr 12 · Sphere Build',      href:'#'},
    {icon:'📊',name:'Rigging_Point_Map.dwg',         tag:'Upload',    cls:'dft-upload', meta:'Dana K. · Mar 28 · Sphere Build',     href:'#'},
  ],
  recent:[
    {icon:'▶',name:'Run of show — Show night 1', tag:'Live', cls:'dft-live',   meta:'Edited today',    href:'/overview/production.php?id=gala&tab=ros'},
    {icon:'📋',name:'Call sheet — Jun 14',        tag:'Live', cls:'dft-live',   meta:'Edited today',    href:'/overview/production.php?id=gala&tab=callsheet'},
    {icon:'📄',name:'AV_Contract_Signed.pdf',     tag:'Upload',cls:'dft-upload',meta:'Tom R. · 2h ago', href:'#'},
    {icon:'📅',name:'Project timeline — Sphere',  tag:'doc',  cls:'dft-live',   meta:'Yesterday',       href:'/overview/project.php?id=sphere'},
  ],
  shared:[
    {icon:'📄',name:'AV_System_Design_v3.pdf',   tag:'From Tom R.', cls:'dft-upload',meta:'Shared Apr 12',href:'#'},
    {icon:'📊',name:'Rigging_Point_Map.dwg',      tag:'From Dana K.',cls:'dft-upload',meta:'Shared Mar 28',href:'#'},
    {icon:'📄',name:'Venue_Survey_Austin.pdf',   tag:'From Mara L.',cls:'dft-upload',meta:'Shared May 3', href:'#'},
  ],
  gala:[
    {icon:'▶',name:'Run of show — Show night 1',  tag:'Live · Active',cls:'dft-live',  meta:'Edited today · 23 cues',  href:'/overview/production.php?id=gala&tab=ros'},
    {icon:'▶',name:'Run of show — Tech rehearsal',tag:'doc',          cls:'dft-gen',   meta:'Edited Jun 12 · 18 cues', href:'/overview/production.php?id=gala&tab=ros'},
    {icon:'📋',name:'Call sheet — Jun 14 (Show)', tag:'Live',         cls:'dft-live',  meta:'Edited today',            href:'/overview/production.php?id=gala&tab=callsheet'},
    {icon:'📋',name:'Call sheet — Jun 12 (Load)', tag:'doc',          cls:'dft-gen',   meta:'Edited Jun 11',           href:'/overview/production.php?id=gala&tab=callsheet'},
    {icon:'🏨',name:'Room list',                  tag:'doc',          cls:'dft-live',  meta:'Edited Jun 10',           href:'/overview/production.php?id=gala&tab=documents'},
    {icon:'📅',name:'Production schedule',        tag:'doc',          cls:'dft-live',  meta:'Edited Jun 8',            href:'/overview/production.php?id=gala&tab=documents'},
    {icon:'👥',name:'Crew contact sheet',         tag:'Auto-gen',     cls:'dft-gen',   meta:'Auto-generated',          href:'/overview/production.php?id=gala&tab=documents'},
    {icon:'📄',name:'AV_Contract_Signed.pdf',     tag:'Upload',       cls:'dft-upload',meta:'Tom R. · 2h ago',         href:'#'},
    {icon:'📊',name:'Budget_Final.xlsx',          tag:'Upload',       cls:'dft-upload',meta:'Jordan K. · Apr 22',      href:'#'},
  ],
  'gala-ros':[
    {icon:'▶',name:'Run of show — Show night 1',      tag:'Live · Active',cls:'dft-live',meta:'Edited today · 23 cues',  href:'/overview/production.php?id=gala&tab=ros'},
    {icon:'▶',name:'Run of show — Tech rehearsal',    tag:'doc',          cls:'dft-gen', meta:'Edited Jun 12 · 18 cues', href:'/overview/production.php?id=gala&tab=ros'},
    {icon:'▶',name:'Run of show — Strike / load-out', tag:'draft',        cls:'dft-gen', meta:'Draft · 8 cues',          href:'/overview/production.php?id=gala&tab=ros'},
  ],
  'gala-calls':[
    {icon:'📋',name:'Call sheet — Jun 12 (Load-in)', tag:'doc',  cls:'dft-gen',  meta:'Edited Jun 11', href:'/overview/production.php?id=gala&tab=callsheet'},
    {icon:'📋',name:'Call sheet — Jun 13 (Tech)',    tag:'doc',  cls:'dft-gen',  meta:'Edited Jun 12', href:'/overview/production.php?id=gala&tab=callsheet'},
    {icon:'📋',name:'Call sheet — Jun 14 (Show)',    tag:'Live', cls:'dft-live', meta:'Edited today',  href:'/overview/production.php?id=gala&tab=callsheet'},
    {icon:'📋',name:'Call sheet — Jun 15 (Strike)',  tag:'doc',  cls:'dft-gen',  meta:'Edited Jun 13', href:'/overview/production.php?id=gala&tab=callsheet'},
  ],
  'gala-docs':[
    {icon:'🏨',name:'Room list',           tag:'doc',      cls:'dft-live',meta:'Edited Jun 10', href:'/overview/production.php?id=gala&tab=documents'},
    {icon:'📅',name:'Production schedule', tag:'doc',      cls:'dft-live',meta:'Edited Jun 8',  href:'/overview/production.php?id=gala&tab=documents'},
    {icon:'👥',name:'Crew contact sheet',  tag:'Auto-gen', cls:'dft-gen', meta:'Auto-generated',href:'/overview/production.php?id=gala&tab=documents'},
    {icon:'🏪',name:'Vendor contact sheet',tag:'Auto-gen', cls:'dft-gen', meta:'Auto-generated',href:'/overview/production.php?id=gala&tab=documents'},
    {icon:'☀️',name:'Day-of briefing',    tag:'doc',      cls:'dft-gen', meta:'Edited Jun 13', href:'/overview/production.php?id=gala&tab=documents'},
  ],
  'gala-files':[
    {icon:'📄',name:'AV_Contract_Signed.pdf', tag:'Upload',cls:'dft-upload',meta:'Tom R. · 2h ago',   href:'#'},
    {icon:'📊',name:'Budget_Final.xlsx',       tag:'Upload',cls:'dft-upload',meta:'Jordan K. · Apr 22',href:'#'},
    {icon:'📄',name:'Venue_Contract.pdf',      tag:'Upload',cls:'dft-upload',meta:'Jordan K. · Mar 15',href:'#'},
  ],
  summit:[
    {icon:'▶',name:'Run of show — Day 1',       tag:'doc',      cls:'dft-gen',   meta:'Edited May 10',  href:'/overview/production.php?id=summit&tab=ros'},
    {icon:'📋',name:'Call sheet — Load-in',      tag:'draft',    cls:'dft-gen',   meta:'Draft',          href:'/overview/production.php?id=summit&tab=callsheet'},
    {icon:'📅',name:'Production schedule',       tag:'doc',      cls:'dft-gen',   meta:'Edited May 8',   href:'/overview/production.php?id=summit&tab=documents'},
    {icon:'👥',name:'Crew contact sheet',        tag:'Auto-gen', cls:'dft-gen',   meta:'Auto-generated', href:'/overview/production.php?id=summit&tab=documents'},
    {icon:'📄',name:'Venue_Contract_Austin.pdf', tag:'Upload',   cls:'dft-upload',meta:'Jordan K. · May 2',href:'#'},
    {icon:'📊',name:'Budget_Draft.xlsx',         tag:'Upload',   cls:'dft-upload',meta:'Jordan K. · May 1',href:'#'},
  ],
  'summit-ros':[{icon:'▶',name:'Run of show — Day 1',tag:'doc',cls:'dft-gen',meta:'Edited May 10 · 14 cues',href:'/overview/production.php?id=summit&tab=ros'}],
  'summit-docs':[
    {icon:'📅',name:'Production schedule', tag:'doc',      cls:'dft-gen',meta:'Edited May 8',  href:'/overview/production.php?id=summit&tab=documents'},
    {icon:'👥',name:'Crew contact sheet',  tag:'Auto-gen', cls:'dft-gen',meta:'Auto-generated',href:'/overview/production.php?id=summit&tab=documents'},
    {icon:'📝',name:'Advance sheet',       tag:'doc',      cls:'dft-gen',meta:'Edited Apr 28', href:'/overview/production.php?id=summit&tab=documents'},
    {icon:'🏟',name:'Site survey — Austin',tag:'doc',      cls:'dft-gen',meta:'Draft',         href:'/overview/production.php?id=summit&tab=documents'},
  ],
  dinner:[
    {icon:'📅',name:'Production schedule',tag:'draft',cls:'dft-gen',meta:'Edited Apr 15',href:'/overview/production.php?id=dinner&tab=documents'},
    {icon:'📋',name:'Initial call sheet',  tag:'draft',cls:'dft-gen',meta:'Draft',        href:'/overview/production.php?id=dinner&tab=callsheet'},
  ],
  sphere:[
    {icon:'📄',name:'AV_System_Design_v3.pdf',    tag:'Upload',cls:'dft-upload',meta:'Tom R. · Apr 12',   href:'#'},
    {icon:'📊',name:'Rigging_Point_Map.dwg',       tag:'Upload',cls:'dft-upload',meta:'Dana K. · Mar 28',  href:'#'},
    {icon:'📄',name:'Network_Contract_Signed.pdf', tag:'Upload',cls:'dft-upload',meta:'Jordan K. · Apr 10',href:'#'},
    {icon:'📅',name:'Project timeline',            tag:'doc',   cls:'dft-live',  meta:'Edited yesterday',  href:'/overview/project.php?id=sphere'},
    {icon:'📊',name:'Budget_Sphere_v2.xlsx',       tag:'Upload',cls:'dft-upload',meta:'Jordan K. · Mar 20',href:'#'},
  ],
  'sphere-docs':[
    {icon:'📅',name:'Project timeline',   tag:'doc',    cls:'dft-live',meta:'Edited yesterday',href:'/overview/project.php?id=sphere'},
    {icon:'📝',name:'Phase summary',      tag:'doc',    cls:'dft-gen', meta:'Edited Apr 20',   href:'/overview/project.php?id=sphere'},
    {icon:'🏪',name:'Vendor contact list',tag:'doc',    cls:'dft-gen', meta:'Auto-generated',  href:'/overview/project.php?id=sphere'},
  ],
  'sphere-files':[
    {icon:'📄',name:'AV_System_Design_v3.pdf',    tag:'Upload',cls:'dft-upload',meta:'Tom R. · Apr 12',   href:'#'},
    {icon:'📊',name:'Rigging_Point_Map.dwg',       tag:'Upload',cls:'dft-upload',meta:'Dana K. · Mar 28',  href:'#'},
    {icon:'📄',name:'Network_Contract_Signed.pdf', tag:'Upload',cls:'dft-upload',meta:'Jordan K. · Apr 10',href:'#'},
    {icon:'📊',name:'Budget_Sphere_v2.xlsx',       tag:'Upload',cls:'dft-upload',meta:'Jordan K. · Mar 20',href:'#'},
  ],
  'venue-build':[
    {icon:'📄',name:'Design_Engineering_Brief.pdf',tag:'Upload',cls:'dft-upload',meta:'Dana K. · Apr 5', href:'#'},
    {icon:'📅',name:'Project schedule',            tag:'draft', cls:'dft-gen',   meta:'Draft',           href:'/overview/project.php?id=venue'},
    {icon:'📝',name:'Procurement list',            tag:'draft', cls:'dft-gen',   meta:'Draft',           href:'/overview/project.php?id=venue'},
  ],
};

const FOLDER_PATHS = {
  all:           [{label:'My Drive',folder:'all'},{label:'All files'}],
  recent:        [{label:'My Drive',folder:'all'},{label:'Recent'}],
  shared:        [{label:'My Drive',folder:'all'},{label:'Shared with me'}],
  gala:          [{label:'My Drive',folder:'all'},{label:'Summerset Gala'}],
  'gala-ros':    [{label:'My Drive',folder:'all'},{label:'Summerset Gala',folder:'gala'},{label:'Run of show'}],
  'gala-calls':  [{label:'My Drive',folder:'all'},{label:'Summerset Gala',folder:'gala'},{label:'Call sheets'}],
  'gala-docs':   [{label:'My Drive',folder:'all'},{label:'Summerset Gala',folder:'gala'},{label:'Documents'}],
  'gala-files':  [{label:'My Drive',folder:'all'},{label:'Summerset Gala',folder:'gala'},{label:'Uploaded files'}],
  summit:        [{label:'My Drive',folder:'all'},{label:'Tech Summit 2025'}],
  'summit-ros':  [{label:'My Drive',folder:'all'},{label:'Tech Summit 2025',folder:'summit'},{label:'Run of show'}],
  'summit-docs': [{label:'My Drive',folder:'all'},{label:'Tech Summit 2025',folder:'summit'},{label:'Documents'}],
  dinner:        [{label:'My Drive',folder:'all'},{label:'Meridian Dinner'}],
  sphere:        [{label:'My Drive',folder:'all'},{label:'Sphere Residency'}],
  'sphere-docs': [{label:'My Drive',folder:'all'},{label:'Sphere Residency',folder:'sphere'},{label:'Documents'}],
  'sphere-files':[{label:'My Drive',folder:'all'},{label:'Sphere Residency',folder:'sphere'},{label:'Uploaded files'}],
  'venue-build': [{label:'My Drive',folder:'all'},{label:'Downtown Venue'}],
};

let currentView='grid', currentFolder='all';

function setFolder(id) {
  currentFolder = id;
  document.querySelectorAll('.drive-folder-row').forEach(r=>r.classList.remove('active'));
  document.getElementById('fold-'+id)?.classList.add('active');
  const path = FOLDER_PATHS[id] || [{label:'My Drive',folder:'all'},{label:id}];
  const pathEl = document.getElementById('drive-path');
  if (pathEl) pathEl.innerHTML = path.map((p,i)=>i===path.length-1?`<span style="color:var(--text-1)">${p.label}</span>`:`<span onclick="setFolder('${p.folder||'all'}')">${p.label}</span><span class="sep">/</span>`).join('');
  renderFiles();
}

function setView(v) {
  currentView = v;
  document.getElementById('view-grid')?.classList.toggle('active', v==='grid');
  document.getElementById('view-list')?.classList.toggle('active', v==='list');
  renderFiles();
}

function renderFiles() {
  const files = FILES[currentFolder] || [];
  const el = document.getElementById('drive-content'), countEl = document.getElementById('drive-count');
  if (!el) return;
  if (countEl) countEl.textContent = `${files.length} item${files.length!==1?'s':''}`;
  if (!files.length) { el.innerHTML=`<div style="padding:48px;text-align:center;color:var(--text-3)"><div style="font-size:32px;margin-bottom:12px">📁</div><div style="font-size:13px">This folder is empty</div></div>`; return; }
  if (currentView==='grid') {
    el.innerHTML = `<div class="drive-file-grid">${files.map(f=>`<a class="drive-file-card" href="${f.href}"><div class="drive-file-icon">${f.icon}</div><div class="drive-file-name">${f.name}</div><div class="drive-file-meta">${f.meta}</div><span class="drive-file-tag ${f.cls}">${f.tag}</span></a>`).join('')}</div>`;
  } else {
    el.innerHTML = `<div class="drive-file-list">${files.map(f=>`<a class="drive-file-row" href="${f.href}"><div class="drive-file-row-icon">${f.icon}</div><div class="drive-file-row-name">${f.name}</div><span class="drive-file-tag ${f.cls}" style="margin-right:12px">${f.tag}</span><div class="drive-file-row-meta">${f.meta}</div></a>`).join('')}</div>`;
  }
}

function triggerUpload() {
  const input=document.createElement('input'); input.type='file'; input.multiple=true;
  input.onchange=e=>{const names=Array.from(e.target.files).map(f=>f.name);alert(`Files would upload here:\n\n${names.join('\n')}`);};
  input.click();
}

document.addEventListener('DOMContentLoaded', ()=>renderFiles());
</script>

<?php include __DIR__ . '/../footer.php'; ?>
