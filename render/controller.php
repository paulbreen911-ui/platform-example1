<?php
// controller.php - Stage Director Control Panel
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAGE — Controller</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@300;400;500&family=Space+Grotesk:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
/* ─── RESET & BASE ─────────────────────────────────────────────────── */
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
:root {
  --bg:        #0d0e10;
  --panel:     #13151a;
  --panel2:    #1a1d24;
  --border:    rgba(255,255,255,0.07);
  --accent:    #ff8c3c;
  --accent2:   #ffcc44;
  --accent3:   #3cf0ff;
  --text:      #d0d2d8;
  --text-dim:  rgba(208,210,216,0.35);
  --text-label:#8a8f9e;
  --danger:    #ff4466;
  --success:   #44ffaa;
  --mono: 'IBM Plex Mono', monospace;
  --sans: 'Space Grotesk', sans-serif;
  --track-h: 32px;
}
html, body { width: 100%; height: 100%; overflow: hidden; background: var(--bg); color: var(--text); font-family: var(--sans); }

/* ─── LAYOUT ───────────────────────────────────────────────────────── */
#app {
  display: grid;
  grid-template-columns: 240px 1fr;
  grid-template-rows: 48px 1fr 220px 56px;
  grid-template-areas:
    "topbar  topbar"
    "sidebar mainarea"
    "sidebar timeline"
    "sidebar transport";
  width: 100vw; height: 100vh;
}

/* ─── TOPBAR ───────────────────────────────────────────────────────── */
#topbar {
  grid-area: topbar;
  background: var(--panel);
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center;
  padding: 0 16px;
  gap: 12px;
}
#topbar .logo {
  font-family: var(--mono);
  font-size: 13px; font-weight: 500;
  color: var(--accent);
  letter-spacing: 0.15em;
  text-transform: uppercase;
  margin-right: 8px;
}
#topbar .logo span { color: var(--text-dim); }
.tab-group { display: flex; gap: 2px; background: var(--bg); border-radius: 6px; padding: 3px; }
.tab-btn {
  padding: 4px 14px;
  font-family: var(--mono); font-size: 10px; letter-spacing: 0.08em;
  color: var(--text-label); background: transparent; border: none; cursor: pointer;
  border-radius: 4px; text-transform: uppercase; transition: all 0.15s;
}
.tab-btn.active { background: var(--panel2); color: var(--text); }
.tab-btn:hover:not(.active) { color: var(--text); }

.spacer { flex: 1; }

.conn-badge {
  display: flex; align-items: center; gap: 6px;
  font-family: var(--mono); font-size: 10px; color: var(--text-label);
  padding: 5px 12px; border-radius: 4px; border: 1px solid var(--border);
}
.conn-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--danger); }
.conn-dot.live { background: var(--success); animation: pulse 2s ease-in-out infinite; }

.topbar-btn {
  padding: 6px 14px; font-family: var(--mono); font-size: 10px; text-transform: uppercase;
  letter-spacing: 0.1em; border: 1px solid var(--border); background: transparent;
  color: var(--text-dim); cursor: pointer; border-radius: 4px; transition: all 0.15s;
}
.topbar-btn:hover { border-color: var(--accent); color: var(--accent); }
.topbar-btn.danger:hover { border-color: var(--danger); color: var(--danger); }

/* ─── SIDEBAR ──────────────────────────────────────────────────────── */
#sidebar {
  grid-area: sidebar;
  background: var(--panel);
  border-right: 1px solid var(--border);
  overflow-y: auto;
  overflow-x: hidden;
  display: flex; flex-direction: column;
}
#sidebar::-webkit-scrollbar { width: 4px; }
#sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

.sidebar-section { border-bottom: 1px solid var(--border); }
.sidebar-section-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 14px 8px;
  font-family: var(--mono); font-size: 9px; letter-spacing: 0.15em;
  text-transform: uppercase; color: var(--text-label);
  cursor: pointer; user-select: none;
}
.sidebar-section-header:hover { color: var(--text); }
.sidebar-section-header .chevron { transition: transform 0.2s; }
.sidebar-section-header.collapsed .chevron { transform: rotate(-90deg); }
.sidebar-body { padding: 8px 14px 12px; }
.sidebar-body.hidden { display: none; }

/* Form elements */
.field-row { display: flex; flex-direction: column; gap: 4px; margin-bottom: 10px; }
.field-row label { font-size: 10px; color: var(--text-label); font-family: var(--mono); text-transform: uppercase; letter-spacing: 0.08em; }
.field-row input[type=text],
.field-row input[type=number],
.field-row select {
  background: var(--bg); border: 1px solid var(--border);
  color: var(--text); font-family: var(--mono); font-size: 11px;
  padding: 5px 8px; border-radius: 4px; width: 100%;
  transition: border-color 0.15s;
}
.field-row input:focus, .field-row select:focus { outline: none; border-color: var(--accent); }
.field-row input[type=range] {
  -webkit-appearance: none; width: 100%; height: 4px;
  background: var(--panel2); border-radius: 2px; outline: none;
}
.field-row input[type=range]::-webkit-slider-thumb {
  -webkit-appearance: none; width: 12px; height: 12px;
  border-radius: 50%; background: var(--accent); cursor: pointer;
}
.field-row input[type=color] { width: 100%; height: 28px; padding: 2px; border-radius: 4px; border: 1px solid var(--border); background: var(--bg); cursor: pointer; }

.xyz-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; }
.xyz-row input { text-align: center; padding: 4px 4px; }

.btn {
  display: block; width: 100%; padding: 7px; text-align: center;
  font-family: var(--mono); font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
  border-radius: 4px; cursor: pointer; transition: all 0.15s;
  border: 1px solid var(--border); background: transparent; color: var(--text);
}
.btn:hover { border-color: var(--accent); color: var(--accent); }
.btn.primary { background: var(--accent); border-color: var(--accent); color: #000; font-weight: 500; }
.btn.primary:hover { background: var(--accent2); border-color: var(--accent2); }
.btn.danger { border-color: rgba(255,68,102,0.3); color: var(--danger); }
.btn.danger:hover { background: rgba(255,68,102,0.1); }
.btn.small { padding: 4px 8px; font-size: 9px; width: auto; }

.checkbox-row {
  display: flex; align-items: center; gap: 8px;
  font-family: var(--mono); font-size: 10px; color: var(--text-label);
  cursor: pointer; margin-bottom: 8px;
}
.checkbox-row input { accent-color: var(--accent); }

/* Object list */
.obj-list { display: flex; flex-direction: column; gap: 4px; }
.obj-item {
  display: flex; align-items: center; gap: 8px;
  padding: 6px 8px; border-radius: 4px;
  background: var(--bg); border: 1px solid transparent;
  cursor: pointer; transition: all 0.15s; font-size: 11px;
}
.obj-item:hover { border-color: var(--border); }
.obj-item.selected { border-color: var(--accent); background: rgba(255,140,60,0.08); }
.obj-item .obj-icon { color: var(--text-dim); font-size: 12px; }
.obj-item .obj-name { flex: 1; font-family: var(--mono); font-size: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.obj-item .obj-del { color: var(--text-dim); cursor: pointer; font-size: 14px; }
.obj-item .obj-del:hover { color: var(--danger); }

/* Drop zone */
.drop-zone {
  border: 1px dashed rgba(255,140,60,0.3);
  border-radius: 6px; padding: 16px 10px; text-align: center;
  cursor: pointer; transition: all 0.2s;
  font-family: var(--mono); font-size: 10px; color: var(--text-label);
  line-height: 1.6;
}
.drop-zone:hover, .drop-zone.drag-over { border-color: var(--accent); color: var(--accent); background: rgba(255,140,60,0.05); }
.drop-zone .icon { font-size: 20px; margin-bottom: 6px; }
input[type=file].hidden { display: none; }

/* ─── MAIN AREA ────────────────────────────────────────────────────── */
#mainarea {
  grid-area: mainarea;
  background: var(--bg);
  position: relative;
  overflow: hidden;
  display: flex; flex-direction: column;
}

.panel-tab-content { display: none; flex: 1; overflow-y: auto; }
.panel-tab-content.active { display: flex; flex-direction: column; }
.panel-tab-content::-webkit-scrollbar { width: 4px; }
.panel-tab-content::-webkit-scrollbar-thumb { background: var(--border); }

/* Camera panel */
#cam-panel {
  padding: 20px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  align-content: start;
}
.prop-group {
  background: var(--panel); border: 1px solid var(--border);
  border-radius: 8px; padding: 16px;
}
.prop-group h4 {
  font-family: var(--mono); font-size: 9px; letter-spacing: 0.15em;
  text-transform: uppercase; color: var(--text-label); margin-bottom: 14px;
  padding-bottom: 8px; border-bottom: 1px solid var(--border);
}

/* Viewport mini-preview */
#viewport-preview {
  grid-column: 1 / -1;
  height: 160px;
  background: var(--panel);
  border: 1px solid var(--border);
  border-radius: 8px;
  position: relative;
  overflow: hidden;
  display: flex; align-items: center; justify-content: center;
}
#viewport-canvas {
  position: absolute; inset: 0; width: 100%; height: 100%;
}
.viewport-label {
  position: absolute; top: 8px; left: 12px;
  font-family: var(--mono); font-size: 9px; color: var(--text-label);
  letter-spacing: 0.12em; text-transform: uppercase;
  pointer-events: none;
}
.viewport-crosshair {
  position: absolute; width: 20px; height: 20px;
  border: 1px solid rgba(255,140,60,0.5); border-radius: 50%;
  pointer-events: none;
}

/* ─── TIMELINE ─────────────────────────────────────────────────────── */
#timeline-panel {
  grid-area: timeline;
  background: var(--panel);
  border-top: 1px solid var(--border);
  display: flex; flex-direction: column;
  overflow: hidden;
}
#timeline-header {
  display: flex; align-items: center;
  padding: 0 12px;
  height: 32px;
  border-bottom: 1px solid var(--border);
  gap: 12px; flex-shrink: 0;
}
#timeline-header .tl-label {
  font-family: var(--mono); font-size: 9px; letter-spacing: 0.15em;
  text-transform: uppercase; color: var(--text-label);
}
#timeline-header .tl-time {
  font-family: var(--mono); font-size: 11px; color: var(--accent);
}
#timeline-header .tl-dur {
  font-family: var(--mono); font-size: 10px; color: var(--text-dim);
}

#timeline-body {
  flex: 1; display: flex; overflow: hidden;
}
#track-labels {
  width: 140px; flex-shrink: 0;
  border-right: 1px solid var(--border);
  overflow-y: auto; overflow-x: hidden;
}
#track-labels::-webkit-scrollbar { display: none; }
.track-label-header {
  height: 24px; display: flex; align-items: center;
  padding: 0 10px;
  font-family: var(--mono); font-size: 9px; letter-spacing: 0.1em;
  text-transform: uppercase; color: var(--text-label);
  border-bottom: 1px solid var(--border);
  background: var(--panel2);
}
.track-label {
  height: var(--track-h); display: flex; align-items: center;
  padding: 0 10px; gap: 6px;
  font-family: var(--mono); font-size: 10px; color: var(--text);
  border-bottom: 1px solid rgba(255,255,255,0.03);
  overflow: hidden;
}
.track-label .tl-icon { color: var(--text-dim); font-size: 11px; }

#track-area {
  flex: 1; overflow: auto; position: relative;
}
#track-area::-webkit-scrollbar { height: 6px; width: 6px; }
#track-area::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

#track-ruler {
  height: 24px; position: sticky; top: 0; z-index: 2;
  background: var(--panel2); border-bottom: 1px solid var(--border);
}
#track-ruler canvas { display: block; }
#tracks-container { position: relative; }
.track-row {
  height: var(--track-h); position: relative;
  border-bottom: 1px solid rgba(255,255,255,0.03);
  background: rgba(255,255,255,0.01);
}
.track-row:hover { background: rgba(255,255,255,0.025); }
.keyframe-diamond {
  position: absolute; top: 50%; transform: translateY(-50%) rotate(45deg);
  width: 10px; height: 10px;
  background: var(--accent); cursor: pointer;
  border: 1px solid rgba(0,0,0,0.5);
  border-radius: 2px;
  transition: background 0.1s;
}
.keyframe-diamond:hover { background: var(--accent2); }
.playhead-line {
  position: absolute; top: 0; bottom: 0; width: 2px;
  background: var(--accent); pointer-events: none; z-index: 5;
  box-shadow: 0 0 8px rgba(255,140,60,0.6);
}

/* ─── TRANSPORT ────────────────────────────────────────────────────── */
#transport {
  grid-area: transport;
  background: var(--panel2);
  border-top: 1px solid var(--border);
  display: flex; align-items: center;
  padding: 0 20px; gap: 16px;
}
.transport-btn {
  width: 36px; height: 36px; border-radius: 50%;
  background: var(--panel); border: 1px solid var(--border);
  color: var(--text); cursor: pointer; font-size: 14px;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.15s;
}
.transport-btn:hover { border-color: var(--accent); color: var(--accent); }
.transport-btn.play-btn {
  width: 42px; height: 42px;
  background: var(--accent); border-color: var(--accent); color: #000;
  font-size: 16px;
}
.transport-btn.play-btn:hover { background: var(--accent2); border-color: var(--accent2); }
.transport-btn.active { border-color: var(--accent3); color: var(--accent3); }

.transport-time {
  font-family: var(--mono); font-size: 18px; font-weight: 300;
  color: var(--text); letter-spacing: 0.08em; min-width: 90px;
}
.transport-time .secs { color: var(--accent); }
.transport-time .frac { color: var(--text-dim); font-size: 13px; }

.transport-scrubber { flex: 1; position: relative; height: 6px; }
.scrubber-track {
  position: absolute; inset: 0; height: 6px; top: 50%; transform: translateY(-50%);
  background: var(--panel); border-radius: 3px; cursor: pointer;
  border: 1px solid var(--border);
}
.scrubber-fill {
  height: 100%; background: linear-gradient(90deg, var(--accent), var(--accent2));
  border-radius: 3px; width: 0%; transition: width 0.05s linear; pointer-events: none;
}
.scrubber-thumb {
  position: absolute; top: 50%; transform: translate(-50%, -50%);
  width: 14px; height: 14px; border-radius: 50%;
  background: var(--accent); border: 2px solid var(--bg);
  cursor: grab; left: 0%;
}
.scrubber-thumb:active { cursor: grabbing; }

.transport-duration {
  font-family: var(--mono); font-size: 11px; color: var(--text-label);
}
.transport-loop-btn {
  font-family: var(--mono); font-size: 10px; padding: 5px 10px;
  border-radius: 4px; border: 1px solid var(--border);
  background: transparent; color: var(--text-label); cursor: pointer;
  transition: all 0.15s; text-transform: uppercase; letter-spacing: 0.08em;
}
.transport-loop-btn.active { border-color: var(--accent3); color: var(--accent3); background: rgba(60,240,255,0.08); }

/* ─── MISC ─────────────────────────────────────────────────────────── */
@keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.3; } }

.light-row {
  display: flex; flex-direction: column; gap: 8px;
  padding: 8px; background: var(--bg); border-radius: 6px; margin-bottom: 8px;
  border: 1px solid var(--border);
}
.light-row-header {
  display: flex; align-items: center; gap: 8px;
  font-family: var(--mono); font-size: 10px;
}
.light-row-header .light-icon { font-size: 14px; }
.light-row-header span { flex: 1; }
.light-row-header button {
  background: none; border: none; cursor: pointer; color: var(--text-dim); font-size: 14px;
}
.light-row-header button:hover { color: var(--danger); }

.divider { height: 1px; background: var(--border); margin: 8px 0; }

.add-kf-btn {
  background: rgba(255,140,60,0.1); border: 1px solid rgba(255,140,60,0.25);
  color: var(--accent); border-radius: 4px; padding: 5px 10px;
  font-family: var(--mono); font-size: 10px; cursor: pointer; text-transform: uppercase;
  letter-spacing: 0.08em; transition: all 0.15s;
}
.add-kf-btn:hover { background: rgba(255,140,60,0.2); }

.no-items {
  font-family: var(--mono); font-size: 10px; color: var(--text-label);
  text-align: center; padding: 12px; line-height: 1.6;
}
</style>
</head>
<body>
<div id="app">

  <!-- TOPBAR -->
  <header id="topbar">
    <div class="logo">STAGE <span>DIRECTOR</span></div>
    <div class="tab-group">
      <button class="tab-btn active" data-panel="objects">Objects</button>
      <button class="tab-btn" data-panel="camera">Camera</button>
      <button class="tab-btn" data-panel="lights">Lights</button>
      <button class="tab-btn" data-panel="env">Environment</button>
    </div>
    <div class="spacer"></div>
    <div class="conn-badge">
      <div class="conn-dot" id="conn-dot"></div>
      <span id="conn-label">Viewer offline</span>
    </div>
    <button class="topbar-btn" onclick="openViewer()">Open Viewer ↗</button>
    <button class="topbar-btn danger" onclick="clearScene()">Clear Scene</button>
  </header>

  <!-- SIDEBAR -->
  <aside id="sidebar">

    <!-- Scene Objects -->
    <div class="sidebar-section">
      <div class="sidebar-section-header" onclick="toggleSection(this)">
        Scene Objects <span class="chevron">▾</span>
      </div>
      <div class="sidebar-body" id="obj-section">
        <div class="drop-zone" id="obj-dropzone" onclick="document.getElementById('obj-file-input').click()">
          <div class="icon">⬡</div>
          Click or drop .OBJ file
          <br><span style="opacity:0.5;font-size:9px">Wavefront OBJ format</span>
        </div>
        <input type="file" accept=".obj" class="hidden" id="obj-file-input" multiple>
        <div style="height:10px"></div>
        <div class="obj-list" id="obj-list">
          <div class="no-items">No objects loaded</div>
        </div>
      </div>
    </div>

    <!-- Transform -->
    <div class="sidebar-section">
      <div class="sidebar-section-header" onclick="toggleSection(this)">
        Transform <span class="chevron">▾</span>
      </div>
      <div class="sidebar-body" id="transform-section">
        <div class="field-row">
          <label>Position X Y Z</label>
          <div class="xyz-row">
            <input type="number" id="tx" value="0" step="0.1" placeholder="X">
            <input type="number" id="ty" value="0" step="0.1" placeholder="Y">
            <input type="number" id="tz" value="0" step="0.1" placeholder="Z">
          </div>
        </div>
        <div class="field-row">
          <label>Rotation X Y Z (°)</label>
          <div class="xyz-row">
            <input type="number" id="rx" value="0" step="1" placeholder="X">
            <input type="number" id="ry" value="0" step="1" placeholder="Y">
            <input type="number" id="rz" value="0" step="1" placeholder="Z">
          </div>
        </div>
        <div class="field-row">
          <label>Uniform Scale</label>
          <input type="range" id="scale-slider" min="0.01" max="10" step="0.01" value="1">
        </div>
        <div style="display:flex;gap:6px">
          <button class="btn primary" onclick="applyTransform()" style="flex:2">Apply</button>
          <button class="add-kf-btn" onclick="addKeyframe()" style="flex:1">+ KF</button>
        </div>
      </div>
    </div>

    <!-- Material -->
    <div class="sidebar-section">
      <div class="sidebar-section-header" onclick="toggleSection(this)">
        Material <span class="chevron">▾</span>
      </div>
      <div class="sidebar-body">
        <div class="field-row">
          <label>Texture Map</label>
          <div class="drop-zone" style="padding:10px" onclick="document.getElementById('tex-file-input').click()">
            Drop or click image
          </div>
          <input type="file" accept="image/*" class="hidden" id="tex-file-input">
        </div>
        <div class="field-row">
          <label>Base Color</label>
          <input type="color" id="mat-color" value="#cccccc">
        </div>
        <div class="field-row">
          <label>Roughness <span id="roughness-val">0.5</span></label>
          <input type="range" id="mat-roughness" min="0" max="1" step="0.01" value="0.5">
        </div>
        <div class="field-row">
          <label>Metalness <span id="metalness-val">0.1</span></label>
          <input type="range" id="mat-metalness" min="0" max="1" step="0.01" value="0.1">
        </div>
        <div class="checkbox-row">
          <input type="checkbox" id="mat-wireframe">
          <label for="mat-wireframe">Wireframe</label>
        </div>
        <button class="btn" onclick="applyMaterial()">Apply Material</button>
      </div>
    </div>

  </aside>

  <!-- MAIN PANELS -->
  <main id="mainarea">

    <!-- Objects Panel -->
    <div class="panel-tab-content active" id="panel-objects" style="padding:20px; gap:16px; flex-direction:column;">
      <div class="prop-group">
        <h4>Selected Object Properties</h4>
        <div id="obj-props-empty" style="font-family:var(--mono);font-size:10px;color:var(--text-label)">
          Select an object from the sidebar to edit properties.
        </div>
        <div id="obj-props-detail" style="display:none">
          <div class="field-row">
            <label>Object ID</label>
            <input type="text" id="detail-id" readonly style="opacity:0.4">
          </div>
          <div class="field-row">
            <label>Name</label>
            <input type="text" id="detail-name">
          </div>
          <div style="display:flex;gap:8px">
            <button class="btn primary" onclick="applyTransform()" style="flex:1">Update Transform</button>
            <button class="btn" onclick="addKeyframe()">Add Keyframe</button>
          </div>
        </div>
      </div>

      <div class="prop-group">
        <h4>Keyframes for Selected Object</h4>
        <div id="kf-list" class="obj-list">
          <div class="no-items">No object selected</div>
        </div>
      </div>
    </div>

    <!-- Camera Panel -->
    <div class="panel-tab-content" id="panel-camera">
      <div id="cam-panel">
        <div class="prop-group" style="grid-column:1/-1">
          <h4>Camera Position</h4>
          <div class="field-row">
            <label>Position X Y Z</label>
            <div class="xyz-row">
              <input type="number" id="cam-x" value="0" step="0.1">
              <input type="number" id="cam-y" value="0" step="0.1">
              <input type="number" id="cam-z" value="5" step="0.1">
            </div>
          </div>
          <div class="field-row">
            <label>Rotation X Y (°)</label>
            <div class="xyz-row">
              <input type="number" id="cam-rx" value="0" step="1">
              <input type="number" id="cam-ry" value="0" step="1">
              <div></div>
            </div>
          </div>
          <div class="field-row">
            <label>Field of View: <span id="fov-val">60</span>°</label>
            <input type="range" id="cam-fov" min="10" max="120" value="60">
          </div>
          <button class="btn primary" onclick="sendCamera()">Apply Camera</button>
        </div>

        <div class="prop-group">
          <h4>Camera Presets</h4>
          <div style="display:flex;flex-direction:column;gap:6px">
            <button class="btn" onclick="setCamPreset(0,0,5,0,0)">Front</button>
            <button class="btn" onclick="setCamPreset(5,0,0,0,90)">Right</button>
            <button class="btn" onclick="setCamPreset(-5,0,0,0,-90)">Left</button>
            <button class="btn" onclick="setCamPreset(0,5,0,-90,0)">Top</button>
            <button class="btn" onclick="setCamPreset(3,3,3,-30,45)">Isometric</button>
          </div>
        </div>

        <div class="prop-group">
          <h4>Navigation Controls</h4>
          <div style="font-family:var(--mono);font-size:10px;color:var(--text-label);line-height:2">
            <div>W / S — Move Forward / Back</div>
            <div>A / D — Move Left / Right</div>
            <div>Q / E — Move Up / Down</div>
            <div>Mouse drag — Rotate</div>
            <div>Scroll — Zoom</div>
          </div>
          <div class="divider"></div>
          <div class="checkbox-row">
            <input type="checkbox" id="cam-keyboard" checked>
            <label for="cam-keyboard">Keyboard Navigation</label>
          </div>
          <div class="field-row">
            <label>Move Speed</label>
            <input type="range" id="cam-speed" min="0.01" max="2" step="0.01" value="0.1">
          </div>
        </div>
      </div>
    </div>

    <!-- Lights Panel -->
    <div class="panel-tab-content" id="panel-lights" style="padding:20px;gap:16px;flex-direction:column;overflow-y:auto;">
      <div class="prop-group">
        <h4>Add Light Source</h4>
        <div class="field-row">
          <label>Type</label>
          <select id="new-light-type">
            <option value="ambient">Ambient</option>
            <option value="directional">Directional</option>
            <option value="point" selected>Point</option>
            <option value="spot">Spot</option>
            <option value="hemisphere">Hemisphere</option>
          </select>
        </div>
        <div class="field-row">
          <label>Color</label>
          <input type="color" id="new-light-color" value="#ffffff">
        </div>
        <div class="field-row">
          <label>Intensity: <span id="light-int-val">1.0</span></label>
          <input type="range" id="new-light-intensity" min="0" max="5" step="0.1" value="1">
        </div>
        <div id="light-pos-group">
          <div class="field-row">
            <label>Position X Y Z</label>
            <div class="xyz-row">
              <input type="number" id="lx" value="3" step="0.5">
              <input type="number" id="ly" value="5" step="0.5">
              <input type="number" id="lz" value="3" step="0.5">
            </div>
          </div>
        </div>
        <button class="btn primary" onclick="addLight()">Add Light</button>
      </div>

      <div class="prop-group">
        <h4>Active Lights</h4>
        <div id="lights-list">
          <div class="no-items">No lights added</div>
        </div>
      </div>
    </div>

    <!-- Environment Panel -->
    <div class="panel-tab-content" id="panel-env" style="padding:20px;gap:16px;flex-direction:column;overflow-y:auto;">
      <div class="prop-group">
        <h4>Background</h4>
        <div class="field-row">
          <label>Background Color</label>
          <input type="color" id="bg-color" value="#0a0a0a">
        </div>
        <div class="field-row">
          <label>Tone Mapping Exposure: <span id="exposure-val">1.0</span></label>
          <input type="range" id="exposure" min="0" max="4" step="0.05" value="1">
        </div>
        <div class="checkbox-row">
          <input type="checkbox" id="env-fog">
          <label for="env-fog">Enable Fog</label>
        </div>
        <div id="fog-controls" style="display:none">
          <div class="field-row">
            <label>Fog Color</label>
            <input type="color" id="fog-color" value="#000000">
          </div>
          <div class="field-row">
            <label>Fog Density: <span id="fog-density-val">0.02</span></label>
            <input type="range" id="fog-density" min="0.001" max="0.2" step="0.001" value="0.02">
          </div>
        </div>
        <button class="btn primary" onclick="applyEnvironment()">Apply Environment</button>
      </div>

      <div class="prop-group">
        <h4>Timeline Settings</h4>
        <div class="field-row">
          <label>Duration (seconds)</label>
          <input type="number" id="tl-duration" value="10" min="1" max="300">
        </div>
        <div class="checkbox-row">
          <input type="checkbox" id="tl-loop">
          <label for="tl-loop">Loop Playback</label>
        </div>
        <button class="btn" onclick="applyTimelineSettings()">Apply Timeline</button>
      </div>
    </div>

  </main>

  <!-- TIMELINE -->
  <div id="timeline-panel">
    <div id="timeline-header">
      <span class="tl-label">Timeline</span>
      <span class="tl-time" id="tl-curr-time">0.000s</span>
      <span class="tl-dur">/ <span id="tl-total-time">10.000s</span></span>
      <div style="flex:1"></div>
      <span style="font-family:var(--mono);font-size:9px;color:var(--text-label)">Click ruler to seek · Diamond = keyframe</span>
    </div>
    <div id="timeline-body">
      <div id="track-labels">
        <div class="track-label-header">Tracks</div>
        <div id="track-label-list"></div>
      </div>
      <div id="track-area">
        <div id="track-ruler">
          <canvas id="ruler-canvas" height="24"></canvas>
        </div>
        <div id="tracks-container" style="position:relative">
          <div class="playhead-line" id="playhead-el" style="left:0px"></div>
          <div id="track-rows-container"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- TRANSPORT -->
  <div id="transport">
    <button class="transport-btn" onclick="transportStop()" title="Stop">⏹</button>
    <button class="transport-btn" onclick="transportRewind()" title="Rewind">⏮</button>
    <button class="transport-btn play-btn" id="play-btn" onclick="togglePlay()" title="Play/Pause">▶</button>
    <button class="transport-btn" onclick="transportForward()" title="Skip to end">⏭</button>

    <div class="transport-time">
      <span class="secs" id="trans-secs">0</span>:<span id="trans-frac" class="frac">000</span>
    </div>

    <div class="transport-scrubber" id="scrubber-wrap">
      <div class="scrubber-track" id="scrubber-track">
        <div class="scrubber-fill" id="scrubber-fill"></div>
      </div>
      <div class="scrubber-thumb" id="scrubber-thumb"></div>
    </div>

    <div class="transport-duration" id="trans-dur">10.000s</div>
    <button class="transport-loop-btn" id="loop-btn" onclick="toggleLoop()">⟳ LOOP</button>
  </div>

</div>

<script>
// ─── STATE ────────────────────────────────────────────────────────────────────
const state = {
  objects: {},       // id → { id, name, position, rotation, scale, keyframes }
  lights: {},        // id → { id, lightType, color, intensity, position }
  selectedId: null,
  camera: { x:0, y:0, z:5, rx:0, ry:0, fov:60 },
  timeline: { duration:10, currentTime:0, playing:false, loop:false },
  viewerConnected: false,
};

let objCounter = 0, lightCounter = 0;
const pxPerSec = 60; // pixels per second in timeline

// ─── BROADCAST CHANNEL ────────────────────────────────────────────────────────
const bc = new BroadcastChannel('stage_channel');
bc.onmessage = (evt) => {
  const msg = evt.data;
  if (msg.type === 'viewer_ready' || msg.type === 'pong') {
    state.viewerConnected = true;
    document.getElementById('conn-dot').classList.add('live');
    document.getElementById('conn-label').textContent = 'Viewer live';
  }
  if (msg.type === 'time_update') {
    state.timeline.currentTime = msg.time;
    updateTransportDisplay(msg.time);
  }
};

// Ping viewer periodically
setInterval(() => bc.postMessage({ type: 'ping' }), 3000);
bc.postMessage({ type: 'ping' });

function send(msg) { bc.postMessage(msg); }

// ─── TAB SWITCHING ────────────────────────────────────────────────────────────
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.panel-tab-content').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + btn.dataset.panel).classList.add('active');
  });
});

function toggleSection(header) {
  const body = header.nextElementSibling;
  header.classList.toggle('collapsed');
  body.classList.toggle('hidden');
}

// ─── OBJECT LOADING ───────────────────────────────────────────────────────────
const objInput = document.getElementById('obj-file-input');
const dropzone = document.getElementById('obj-dropzone');

dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('drag-over'); });
dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
dropzone.addEventListener('drop', (e) => {
  e.preventDefault(); dropzone.classList.remove('drag-over');
  [...e.dataTransfer.files].forEach(f => { if (f.name.endsWith('.obj')) loadOBJ(f); });
});
objInput.addEventListener('change', () => {
  [...objInput.files].forEach(f => loadOBJ(f));
  objInput.value = '';
});

function loadOBJ(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    const id = 'obj_' + (++objCounter);
    const name = file.name.replace('.obj','');
    state.objects[id] = {
      id, name,
      position: {x:0, y:0, z:0},
      rotation: {x:0, y:0, z:0},
      scale: 1,
      keyframes: []
    };
    send({ type: 'add_object', id, objData: e.target.result, name });
    renderObjList();
    selectObject(id);
    addTrack(id, name);
  };
  reader.readAsText(file);
}

// ─── TEXTURE LOADING ──────────────────────────────────────────────────────────
document.getElementById('tex-file-input').addEventListener('change', (e) => {
  if (!state.selectedId) { alert('Select an object first'); return; }
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (ev) => send({ type: 'apply_texture', id: state.selectedId, dataUrl: ev.target.result });
  reader.readAsDataURL(file);
  e.target.value = '';
});

// ─── OBJECT LIST UI ───────────────────────────────────────────────────────────
function renderObjList() {
  const list = document.getElementById('obj-list');
  const ids = Object.keys(state.objects);
  if (!ids.length) { list.innerHTML = '<div class="no-items">No objects loaded</div>'; return; }
  list.innerHTML = '';
  ids.forEach(id => {
    const obj = state.objects[id];
    const el = document.createElement('div');
    el.className = 'obj-item' + (id === state.selectedId ? ' selected' : '');
    el.innerHTML = `<span class="obj-icon">⬡</span><span class="obj-name">${obj.name}</span><span class="obj-del" data-id="${id}" onclick="removeObject('${id}')">✕</span>`;
    el.onclick = (e) => { if (!e.target.classList.contains('obj-del')) selectObject(id); };
    list.appendChild(el);
  });
}

function selectObject(id) {
  state.selectedId = id;
  renderObjList();
  const obj = state.objects[id];
  if (!obj) return;
  // Fill transform
  document.getElementById('tx').value = obj.position.x;
  document.getElementById('ty').value = obj.position.y;
  document.getElementById('tz').value = obj.position.z;
  document.getElementById('rx').value = obj.rotation.x;
  document.getElementById('ry').value = obj.rotation.y;
  document.getElementById('rz').value = obj.rotation.z;
  document.getElementById('scale-slider').value = obj.scale;
  // Fill detail panel
  document.getElementById('obj-props-empty').style.display = 'none';
  document.getElementById('obj-props-detail').style.display = '';
  document.getElementById('detail-id').value = id;
  document.getElementById('detail-name').value = obj.name;
  renderKFList();
}

function removeObject(id) {
  event.stopPropagation();
  delete state.objects[id];
  send({ type: 'remove_object', id });
  if (state.selectedId === id) {
    state.selectedId = null;
    document.getElementById('obj-props-empty').style.display = '';
    document.getElementById('obj-props-detail').style.display = 'none';
  }
  delete state.timeline.keyframesByObj?.[id];
  renderObjList();
  removeTrack(id);
}

// ─── TRANSFORM ────────────────────────────────────────────────────────────────
function applyTransform() {
  if (!state.selectedId) return;
  const obj = state.objects[state.selectedId];
  obj.position = { x: +document.getElementById('tx').value, y: +document.getElementById('ty').value, z: +document.getElementById('tz').value };
  obj.rotation = { x: +document.getElementById('rx').value, y: +document.getElementById('ry').value, z: +document.getElementById('rz').value };
  obj.scale = +document.getElementById('scale-slider').value;
  send({ type: 'object_transform', id: state.selectedId, position: obj.position, rotation: obj.rotation, scale: obj.scale });
}

// ─── MATERIAL ─────────────────────────────────────────────────────────────────
document.getElementById('mat-roughness').addEventListener('input', (e) => document.getElementById('roughness-val').textContent = (+e.target.value).toFixed(2));
document.getElementById('mat-metalness').addEventListener('input', (e) => document.getElementById('metalness-val').textContent = (+e.target.value).toFixed(2));

function applyMaterial() {
  if (!state.selectedId) return;
  send({
    type: 'object_transform',
    id: state.selectedId,
    color: document.getElementById('mat-color').value,
    roughness: +document.getElementById('mat-roughness').value,
    metalness: +document.getElementById('mat-metalness').value,
    wireframe: document.getElementById('mat-wireframe').checked,
  });
}

// ─── CAMERA ───────────────────────────────────────────────────────────────────
document.getElementById('cam-fov').addEventListener('input', (e) => document.getElementById('fov-val').textContent = e.target.value);

function sendCamera() {
  const cam = {
    x: +document.getElementById('cam-x').value,
    y: +document.getElementById('cam-y').value,
    z: +document.getElementById('cam-z').value,
    rx: +document.getElementById('cam-rx').value,
    ry: +document.getElementById('cam-ry').value,
    fov: +document.getElementById('cam-fov').value,
  };
  Object.assign(state.camera, cam);
  send({ type: 'camera', ...cam });
}

function setCamPreset(x, y, z, rx, ry) {
  document.getElementById('cam-x').value = x;
  document.getElementById('cam-y').value = y;
  document.getElementById('cam-z').value = z;
  document.getElementById('cam-rx').value = rx;
  document.getElementById('cam-ry').value = ry;
  sendCamera();
}

// Keyboard camera nav
const keys = {};
document.addEventListener('keydown', (e) => {
  if (e.target.tagName === 'INPUT') return;
  keys[e.key.toLowerCase()] = true;
});
document.addEventListener('keyup', (e) => { keys[e.key.toLowerCase()] = false; });

setInterval(() => {
  if (!document.getElementById('cam-keyboard').checked) return;
  const speed = +document.getElementById('cam-speed').value;
  let moved = false;
  const cam = state.camera;
  const ryRad = cam.ry * Math.PI / 180;
  if (keys['w']) { cam.x += Math.sin(ryRad) * speed; cam.z -= Math.cos(ryRad) * speed; moved = true; }
  if (keys['s']) { cam.x -= Math.sin(ryRad) * speed; cam.z += Math.cos(ryRad) * speed; moved = true; }
  if (keys['a']) { cam.x -= Math.cos(ryRad) * speed; cam.z -= Math.sin(ryRad) * speed; moved = true; }
  if (keys['d']) { cam.x += Math.cos(ryRad) * speed; cam.z += Math.sin(ryRad) * speed; moved = true; }
  if (keys['q']) { cam.y -= speed; moved = true; }
  if (keys['e']) { cam.y += speed; moved = true; }
  if (moved) {
    document.getElementById('cam-x').value = cam.x.toFixed(3);
    document.getElementById('cam-y').value = cam.y.toFixed(3);
    document.getElementById('cam-z').value = cam.z.toFixed(3);
    send({ type: 'camera', ...cam });
  }
}, 50);

// Mouse drag on viewport (if implemented)
let dragging = false, lastMouseX, lastMouseY;
document.addEventListener('mousedown', (e) => {
  if (e.target.id === 'viewport-canvas') { dragging = true; lastMouseX = e.clientX; lastMouseY = e.clientY; }
});
document.addEventListener('mousemove', (e) => {
  if (!dragging) return;
  const dx = e.clientX - lastMouseX, dy = e.clientY - lastMouseY;
  lastMouseX = e.clientX; lastMouseY = e.clientY;
  state.camera.ry += dx * 0.3;
  state.camera.rx += dy * 0.3;
  document.getElementById('cam-ry').value = state.camera.ry.toFixed(1);
  document.getElementById('cam-rx').value = state.camera.rx.toFixed(1);
  send({ type: 'camera', ...state.camera });
});
document.addEventListener('mouseup', () => dragging = false);

// ─── LIGHTS ───────────────────────────────────────────────────────────────────
document.getElementById('new-light-intensity').addEventListener('input', (e) => document.getElementById('light-int-val').textContent = (+e.target.value).toFixed(1));
document.getElementById('new-light-type').addEventListener('change', (e) => {
  document.getElementById('light-pos-group').style.display = e.target.value === 'ambient' ? 'none' : '';
});

function addLight() {
  const id = 'light_' + (++lightCounter);
  const lightType = document.getElementById('new-light-type').value;
  const color = document.getElementById('new-light-color').value;
  const intensity = +document.getElementById('new-light-intensity').value;
  const position = { x: +document.getElementById('lx').value, y: +document.getElementById('ly').value, z: +document.getElementById('lz').value };
  state.lights[id] = { id, lightType, color, intensity, position };
  send({ type: 'light', id, lightType, color, intensity, position });
  renderLightList();
}

function removeLight(id) {
  delete state.lights[id];
  send({ type: 'light', id, action: 'remove' });
  renderLightList();
}

function renderLightList() {
  const list = document.getElementById('lights-list');
  const ids = Object.keys(state.lights);
  if (!ids.length) { list.innerHTML = '<div class="no-items">No lights added</div>'; return; }
  list.innerHTML = '';
  ids.forEach(id => {
    const l = state.lights[id];
    const icons = { ambient:'☀', directional:'↘', point:'💡', spot:'🔦', hemisphere:'🌓' };
    const el = document.createElement('div');
    el.className = 'light-row';
    el.innerHTML = `
      <div class="light-row-header">
        <span class="light-icon">${icons[l.lightType] || '💡'}</span>
        <span>${l.lightType} — ${l.intensity}x</span>
        <span style="width:16px;height:16px;border-radius:3px;background:${l.color};display:inline-block;border:1px solid rgba(255,255,255,0.2)"></span>
        <button onclick="removeLight('${id}')">✕</button>
      </div>
    `;
    list.appendChild(el);
  });
}

// ─── ENVIRONMENT ──────────────────────────────────────────────────────────────
document.getElementById('exposure').addEventListener('input', (e) => document.getElementById('exposure-val').textContent = (+e.target.value).toFixed(2));
document.getElementById('fog-density').addEventListener('input', (e) => document.getElementById('fog-density-val').textContent = (+e.target.value).toFixed(3));
document.getElementById('env-fog').addEventListener('change', (e) => {
  document.getElementById('fog-controls').style.display = e.target.checked ? '' : 'none';
});

function applyEnvironment() {
  const hasFog = document.getElementById('env-fog').checked;
  send({
    type: 'background',
    color: document.getElementById('bg-color').value,
    exposure: +document.getElementById('exposure').value,
    fog: hasFog,
    fogColor: document.getElementById('fog-color').value,
    fogDensity: +document.getElementById('fog-density').value,
  });
}

// ─── KEYFRAMES ────────────────────────────────────────────────────────────────
function addKeyframe() {
  if (!state.selectedId) return;
  const obj = state.objects[state.selectedId];
  const t = state.timeline.currentTime;
  const kf = {
    id: 'kf_' + Date.now(),
    time: t,
    position: { ...obj.position },
    rotation: { ...obj.rotation },
    scale: { x: obj.scale, y: obj.scale, z: obj.scale },
  };
  // Remove existing at same time
  obj.keyframes = obj.keyframes.filter(k => Math.abs(k.time - t) > 0.001);
  obj.keyframes.push(kf);
  obj.keyframes.sort((a, b) => a.time - b.time);
  syncKeyframes();
  renderKFList();
  renderTimelineTracks();
}

function syncKeyframes() {
  // Build keyframes map for viewer
  const map = {};
  for (const [id, obj] of Object.entries(state.objects)) {
    if (obj.keyframes.length) map[id] = obj.keyframes;
  }
  send({ type: 'timeline_update', duration: state.timeline.duration, loop: state.timeline.loop, keyframes: map });
}

function renderKFList() {
  const list = document.getElementById('kf-list');
  if (!state.selectedId) { list.innerHTML = '<div class="no-items">No object selected</div>'; return; }
  const obj = state.objects[state.selectedId];
  if (!obj || !obj.keyframes.length) { list.innerHTML = '<div class="no-items">No keyframes</div>'; return; }
  list.innerHTML = '';
  obj.keyframes.forEach((kf, i) => {
    const el = document.createElement('div');
    el.className = 'obj-item';
    el.innerHTML = `
      <span class="obj-icon" style="color:var(--accent)">◆</span>
      <span class="obj-name">t=${kf.time.toFixed(3)}s</span>
      <span style="font-family:var(--mono);font-size:9px;color:var(--text-label)">${kf.position.x.toFixed(1)}, ${kf.position.y.toFixed(1)}, ${kf.position.z.toFixed(1)}</span>
      <span class="obj-del" onclick="removeKF('${state.selectedId}',${i})">✕</span>
    `;
    el.onclick = () => seekTo(kf.time);
    list.appendChild(el);
  });
}

function removeKF(objId, idx) {
  event.stopPropagation();
  state.objects[objId].keyframes.splice(idx, 1);
  syncKeyframes();
  renderKFList();
  renderTimelineTracks();
}

// ─── TIMELINE UI ──────────────────────────────────────────────────────────────
const trackLabelList = document.getElementById('track-label-list');
const trackRowsContainer = document.getElementById('track-rows-container');
const tracks = {}; // id → { label el, row el }

function addTrack(id, name) {
  const labelEl = document.createElement('div');
  labelEl.className = 'track-label';
  labelEl.innerHTML = `<span class="tl-icon">⬡</span>${name.substring(0,12)}`;
  labelEl.id = 'tl-label-' + id;
  trackLabelList.appendChild(labelEl);

  const rowEl = document.createElement('div');
  rowEl.className = 'track-row';
  rowEl.id = 'tl-row-' + id;
  rowEl.setAttribute('data-id', id);
  rowEl.addEventListener('click', (e) => {
    const rect = rowEl.getBoundingClientRect();
    const trackArea = document.getElementById('track-area');
    const scrollX = trackArea.scrollLeft;
    const x = e.clientX - rect.left + scrollX;
    seekTo(x / pxPerSec);
  });
  trackRowsContainer.appendChild(rowEl);
  tracks[id] = { labelEl, rowEl };
  updateTrackWidth();
}

function removeTrack(id) {
  if (tracks[id]) {
    tracks[id].labelEl.remove();
    tracks[id].rowEl.remove();
    delete tracks[id];
  }
}

function updateTrackWidth() {
  const w = state.timeline.duration * pxPerSec;
  Object.values(tracks).forEach(({ rowEl }) => { rowEl.style.width = w + 'px'; });
  drawRuler(w);
}

function renderTimelineTracks() {
  // Clear and re-render all keyframe diamonds
  for (const [id, { rowEl }] of Object.entries(tracks)) {
    // Remove old diamonds
    rowEl.querySelectorAll('.keyframe-diamond').forEach(el => el.remove());
    const obj = state.objects[id];
    if (!obj) continue;
    obj.keyframes.forEach(kf => {
      const diamond = document.createElement('div');
      diamond.className = 'keyframe-diamond';
      diamond.style.left = (kf.time * pxPerSec - 5) + 'px';
      diamond.title = `t=${kf.time.toFixed(3)}s`;
      diamond.onclick = () => { selectObject(id); seekTo(kf.time); };
      rowEl.appendChild(diamond);
    });
  }
}

// Ruler
function drawRuler(width) {
  const canvas = document.getElementById('ruler-canvas');
  canvas.width = width;
  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, width, 24);
  ctx.fillStyle = 'rgba(255,255,255,0.06)';
  ctx.fillRect(0, 0, width, 24);

  const dur = state.timeline.duration;
  const step = dur > 30 ? 5 : dur > 10 ? 2 : 1;
  ctx.font = '9px IBM Plex Mono, monospace';
  ctx.fillStyle = 'rgba(255,255,255,0.3)';
  ctx.strokeStyle = 'rgba(255,255,255,0.1)';
  for (let t = 0; t <= dur; t += step) {
    const x = t * pxPerSec;
    ctx.beginPath(); ctx.moveTo(x, 12); ctx.lineTo(x, 24); ctx.stroke();
    ctx.fillText(t + 's', x + 2, 10);
  }
  // Ticks
  ctx.strokeStyle = 'rgba(255,255,255,0.05)';
  for (let t = 0; t <= dur; t += 0.5) {
    const x = t * pxPerSec;
    ctx.beginPath(); ctx.moveTo(x, 18); ctx.lineTo(x, 24); ctx.stroke();
  }
}

// Ruler click to seek
document.getElementById('track-ruler').addEventListener('click', (e) => {
  const rect = document.getElementById('ruler-canvas').getBoundingClientRect();
  const scrollX = document.getElementById('track-area').scrollLeft;
  const x = e.clientX - rect.left + scrollX;
  seekTo(Math.max(0, Math.min(x / pxPerSec, state.timeline.duration)));
});

// Playhead
function updatePlayhead(t) {
  const x = t * pxPerSec;
  document.getElementById('playhead-el').style.left = x + 'px';
  // scroll into view
  const area = document.getElementById('track-area');
  if (x < area.scrollLeft || x > area.scrollLeft + area.clientWidth - 20) {
    area.scrollLeft = x - 40;
  }
}

// ─── TRANSPORT ────────────────────────────────────────────────────────────────
let isPlaying = false, isLoop = false;

function togglePlay() {
  if (isPlaying) {
    isPlaying = false;
    document.getElementById('play-btn').textContent = '▶';
    send({ type: 'transport_pause' });
  } else {
    isPlaying = true;
    document.getElementById('play-btn').textContent = '⏸';
    send({ type: 'transport_play' });
  }
}

function transportStop() {
  isPlaying = false;
  document.getElementById('play-btn').textContent = '▶';
  state.timeline.currentTime = 0;
  send({ type: 'transport_stop' });
  updateTransportDisplay(0);
}

function transportRewind() { seekTo(0); }
function transportForward() { seekTo(state.timeline.duration); }

function seekTo(t) {
  t = Math.max(0, Math.min(t, state.timeline.duration));
  state.timeline.currentTime = t;
  send({ type: 'transport_seek', time: t });
  updateTransportDisplay(t);
}

function toggleLoop() {
  isLoop = !isLoop;
  state.timeline.loop = isLoop;
  document.getElementById('loop-btn').classList.toggle('active', isLoop);
  send({ type: 'timeline_update', duration: state.timeline.duration, loop: isLoop, keyframes: buildKFMap() });
}

function buildKFMap() {
  const map = {};
  for (const [id, obj] of Object.entries(state.objects)) {
    if (obj.keyframes.length) map[id] = obj.keyframes;
  }
  return map;
}

function updateTransportDisplay(t) {
  const secs = Math.floor(t);
  const frac = Math.round((t - secs) * 1000).toString().padStart(3, '0');
  document.getElementById('trans-secs').textContent = secs;
  document.getElementById('trans-frac').textContent = frac;
  document.getElementById('tl-curr-time').textContent = t.toFixed(3) + 's';
  const pct = ((t / state.timeline.duration) * 100).toFixed(2);
  document.getElementById('scrubber-fill').style.width = pct + '%';
  document.getElementById('scrubber-thumb').style.left = pct + '%';
  updatePlayhead(t);
}

// Scrubber drag
let scrubDragging = false;
document.getElementById('scrubber-thumb').addEventListener('mousedown', () => { scrubDragging = true; });
document.addEventListener('mousemove', (e) => {
  if (!scrubDragging) return;
  const track = document.getElementById('scrubber-track');
  const rect = track.getBoundingClientRect();
  const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
  seekTo(pct * state.timeline.duration);
});
document.addEventListener('mouseup', () => { scrubDragging = false; });

document.getElementById('scrubber-track').addEventListener('click', (e) => {
  const rect = e.currentTarget.getBoundingClientRect();
  const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
  seekTo(pct * state.timeline.duration);
});

// ─── TIMELINE SETTINGS ────────────────────────────────────────────────────────
function applyTimelineSettings() {
  state.timeline.duration = +document.getElementById('tl-duration').value;
  state.timeline.loop = document.getElementById('tl-loop').checked;
  document.getElementById('tl-total-time').textContent = state.timeline.duration.toFixed(3) + 's';
  document.getElementById('trans-dur').textContent = state.timeline.duration.toFixed(3) + 's';
  updateTrackWidth();
  syncKeyframes();
}

// ─── SCENE CLEAR ─────────────────────────────────────────────────────────────
function clearScene() {
  if (!confirm('Clear all scene objects, lights and keyframes?')) return;
  send({ type: 'scene_clear' });
  for (const id of Object.keys(state.objects)) removeTrack(id);
  for (const id of Object.keys(state.lights)) delete state.lights[id];
  Object.keys(state.objects).forEach(k => delete state.objects[k]);
  state.selectedId = null;
  renderObjList();
  renderLightList();
  renderKFList();
  renderTimelineTracks();
  transportStop();
}

// ─── OPEN VIEWER ─────────────────────────────────────────────────────────────
function openViewer() {
  window.open('viewer.php', '_blank', 'width=1280,height=720');
}

// ─── INIT ─────────────────────────────────────────────────────────────────────
drawRuler(state.timeline.duration * pxPerSec);
document.getElementById('tl-total-time').textContent = state.timeline.duration.toFixed(3) + 's';
document.getElementById('trans-dur').textContent = state.timeline.duration.toFixed(3) + 's';
</script>
</body>
</html>
