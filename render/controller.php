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

/* ─── INPUT DEVICES PANEL ──────────────────────────────────────────── */
#panel-input { padding: 0; overflow-y: auto; }
.input-col { display: flex; flex-direction: column; gap: 0; height: 100%; }

.device-card {
  background: var(--panel); border-bottom: 1px solid var(--border);
  padding: 14px 16px;
}
.device-card-header {
  display: flex; align-items: center; gap: 10px; margin-bottom: 10px;
}
.device-icon { font-size: 18px; }
.device-name { font-family: var(--mono); font-size: 11px; color: var(--text); flex:1; }
.device-status {
  font-family: var(--mono); font-size: 9px; letter-spacing: 0.1em;
  padding: 2px 8px; border-radius: 10px; text-transform: uppercase;
  background: rgba(255,68,102,0.12); color: var(--danger); border: 1px solid rgba(255,68,102,0.2);
}
.device-status.connected {
  background: rgba(68,255,170,0.1); color: var(--success); border-color: rgba(68,255,170,0.25);
}

/* Gamepad axis viz */
.gp-axes { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px; }
.axis-viz {
  position: relative; width: 60px; height: 60px; margin: 0 auto;
  border: 1px solid var(--border); border-radius: 50%; background: var(--bg);
}
.axis-dot {
  position: absolute; width: 8px; height: 8px; border-radius: 50%;
  background: var(--accent); transform: translate(-50%, -50%);
  top: 50%; left: 50%; transition: top 0.05s, left 0.05s;
  box-shadow: 0 0 6px rgba(255,140,60,0.6);
}
.axis-label { text-align: center; font-family: var(--mono); font-size: 9px; color: var(--text-label); margin-top: 2px; }

.gp-buttons { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
.gp-btn-indicator {
  width: 20px; height: 20px; border-radius: 4px; border: 1px solid var(--border);
  background: var(--bg); display: flex; align-items: center; justify-content: center;
  font-family: var(--mono); font-size: 8px; color: var(--text-dim); cursor: pointer;
  transition: all 0.08s;
}
.gp-btn-indicator.pressed { background: var(--accent); color: #000; border-color: var(--accent); box-shadow: 0 0 8px rgba(255,140,60,0.5); }

/* Mapping table */
.mapping-table { width: 100%; border-collapse: collapse; }
.mapping-table th {
  font-family: var(--mono); font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;
  color: var(--text-label); padding: 6px 8px; text-align: left;
  border-bottom: 1px solid var(--border); background: var(--panel2);
}
.mapping-table td { padding: 5px 8px; border-bottom: 1px solid rgba(255,255,255,0.03); vertical-align: middle; }
.mapping-table tr:hover td { background: rgba(255,255,255,0.02); }
.mapping-source {
  font-family: var(--mono); font-size: 10px; color: var(--accent3);
  background: rgba(60,240,255,0.07); border: 1px solid rgba(60,240,255,0.15);
  padding: 2px 7px; border-radius: 4px; display: inline-block;
}
.mapping-target { font-family: var(--mono); font-size: 10px; color: var(--text); }
.mapping-scale { font-family: var(--mono); font-size: 10px; color: var(--text-label); }
.mapping-del { color: var(--text-dim); cursor: pointer; font-size: 13px; }
.mapping-del:hover { color: var(--danger); }

.add-mapping-row { display: grid; grid-template-columns: 1fr 1fr 70px auto; gap: 6px; align-items: end; margin-top: 10px; }
.add-mapping-row select, .add-mapping-row input {
  background: var(--bg); border: 1px solid var(--border);
  color: var(--text); font-family: var(--mono); font-size: 10px;
  padding: 5px 6px; border-radius: 4px; width: 100%;
}
.add-mapping-row select:focus, .add-mapping-row input:focus { outline: none; border-color: var(--accent); }

/* Key capture */
.key-capture-btn {
  padding: 5px 10px; font-family: var(--mono); font-size: 10px;
  border: 1px dashed rgba(60,240,255,0.4); background: rgba(60,240,255,0.05);
  color: var(--accent3); border-radius: 4px; cursor: pointer; text-transform: uppercase;
  letter-spacing: 0.08em; white-space: nowrap; min-width: 80px; text-align: center;
  transition: all 0.15s;
}
.key-capture-btn.listening {
  border-color: var(--accent); color: var(--accent); background: rgba(255,140,60,0.1);
  animation: blink 0.6s ease-in-out infinite;
}
@keyframes blink { 0%,100% { opacity:1; } 50% { opacity:0.4; } }

/* Live value bars */
.live-bar-wrap { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
.live-bar-label { font-family: var(--mono); font-size: 9px; color: var(--text-label); min-width: 90px; }
.live-bar { flex:1; height: 4px; background: var(--panel2); border-radius: 2px; overflow: hidden; }
.live-bar-fill { height: 100%; background: var(--accent); border-radius: 2px; width: 0%; transition: width 0.05s; }
.live-bar-val { font-family: var(--mono); font-size: 9px; color: var(--text-dim); min-width: 40px; text-align: right; }

.midi-note-display {
  font-family: var(--mono); font-size: 11px; color: var(--accent2);
  background: var(--bg); border: 1px solid var(--border);
  padding: 4px 10px; border-radius: 4px; min-width: 80px; text-align: center;
}

.section-divider {
  padding: 8px 16px 6px;
  font-family: var(--mono); font-size: 9px; letter-spacing: 0.15em;
  text-transform: uppercase; color: var(--text-label);
  background: var(--panel2); border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
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
      <button class="tab-btn" data-panel="input">⎮ Input</button>
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

          <!-- Rotate drag pad -->
          <div id="rotate-pad"
            style="
              width:100%; height:110px; border-radius:8px; margin-bottom:12px;
              background: radial-gradient(ellipse at center, rgba(255,140,60,0.07) 0%, rgba(255,140,60,0.02) 60%, transparent 100%);
              border: 1px dashed rgba(255,140,60,0.35);
              display:flex; flex-direction:column; align-items:center; justify-content:center;
              cursor:grab; user-select:none; position:relative; overflow:hidden;
            "
          >
            <div style="font-size:22px; opacity:0.5; pointer-events:none;">⊕</div>
            <div style="font-family:var(--mono);font-size:9px;color:rgba(255,140,60,0.5);margin-top:4px;letter-spacing:0.1em;pointer-events:none;">DRAG TO ROTATE</div>
            <div id="rotate-pad-hint" style="font-family:var(--mono);font-size:8px;color:var(--text-label);margin-top:2px;pointer-events:none;">← → tilt · ↑ ↓ pitch</div>
          </div>

          <div style="font-family:var(--mono);font-size:10px;color:var(--text-label);line-height:2">
            <div>W / S — Move Forward / Back</div>
            <div>A / D — Move Left / Right</div>
            <div>Q / E — Move Up / Down</div>
            <div>Scroll — Zoom (in viewer)</div>
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

    <!-- ═══ INPUT DEVICES PANEL ══════════════════════════════════════════ -->
    <div class="panel-tab-content" id="panel-input">
      <div class="input-col">

        <!-- ── GAMEPAD ── -->
        <div class="section-divider">
          🎮 Gamepad / USB Controller
          <button class="btn small" onclick="inputSys.scanGamepads()" style="width:auto">Scan</button>
        </div>
        <div class="device-card">
          <div class="device-card-header">
            <span class="device-icon">🎮</span>
            <span class="device-name" id="gp-name">No gamepad detected</span>
            <span class="device-status" id="gp-status">Disconnected</span>
          </div>
          <div class="gp-axes">
            <div>
              <div class="axis-viz" id="axis-viz-0"><div class="axis-dot" id="axis-dot-0"></div></div>
              <div class="axis-label">Left Stick</div>
            </div>
            <div>
              <div class="axis-viz" id="axis-viz-1"><div class="axis-dot" id="axis-dot-1"></div></div>
              <div class="axis-label">Right Stick</div>
            </div>
          </div>
          <div class="gp-buttons" id="gp-buttons">
            <!-- filled dynamically -->
          </div>
          <!-- Live axis values -->
          <div id="gp-live-bars"></div>
        </div>

        <!-- ── MIDI ── -->
        <div class="section-divider">
          🎹 MIDI
          <button class="btn small" onclick="inputSys.initMIDI()" style="width:auto">Connect MIDI</button>
        </div>
        <div class="device-card">
          <div class="device-card-header">
            <span class="device-icon">🎹</span>
            <span class="device-name" id="midi-name">No MIDI device</span>
            <span class="device-status" id="midi-status">Disconnected</span>
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px">
            <span style="font-family:var(--mono);font-size:10px;color:var(--text-label)">Last event:</span>
            <span class="midi-note-display" id="midi-last">—</span>
            <span style="font-family:var(--mono);font-size:10px;color:var(--text-label)">Ch</span>
            <span class="midi-note-display" id="midi-ch">—</span>
            <span style="font-family:var(--mono);font-size:10px;color:var(--text-label)">Val</span>
            <span class="midi-note-display" id="midi-val">—</span>
          </div>
          <div id="midi-live-bars"></div>
        </div>

        <!-- ── KEYBOARD ── -->
        <div class="section-divider">⌨ Keyboard Bindings</div>
        <div class="device-card">
          <div id="kbd-live-indicator" style="font-family:var(--mono);font-size:10px;color:var(--text-label);margin-bottom:8px">
            Keys held: <span id="kbd-held" style="color:var(--accent3)">none</span>
          </div>
        </div>

        <!-- ── MAPPING TABLE ── -->
        <div class="section-divider">
          ⇌ Input Mappings
          <span style="font-family:var(--mono);font-size:9px;color:var(--text-label)" id="mapping-count">0 bindings</span>
        </div>
        <div class="device-card" style="flex:1;overflow-y:auto">

          <!-- Preset loader -->
          <div style="display:flex;gap:8px;align-items:center;margin-bottom:12px">
            <select id="preset-select" style="flex:1;background:var(--bg);border:1px solid var(--border);color:var(--text);font-family:var(--mono);font-size:10px;padding:5px 8px;border-radius:4px">
              <option value="">— Load Preset —</option>
            </select>
            <button class="btn small" style="white-space:nowrap;padding:5px 12px"
              onclick="const s=document.getElementById('preset-select');if(s.value)inputSys.loadPreset(s.value)">Load</button>
            <button class="btn small danger" style="white-space:nowrap;padding:5px 10px"
              onclick="inputSys.clearAllMappings()">Clear All</button>
          </div>

          <!-- MIDI Learn shortcut -->
          <div style="display:flex;gap:8px;align-items:center;margin-bottom:14px">
            <span style="font-family:var(--mono);font-size:10px;color:var(--text-label)">MIDI Learn:</span>
            <select id="midi-learn-target" style="flex:1;background:var(--bg);border:1px solid var(--border);color:var(--text);font-family:var(--mono);font-size:10px;padding:4px 6px;border-radius:4px">
              <option value="cam.ry">Camera Yaw</option>
              <option value="cam.rx">Camera Pitch</option>
              <option value="cam.z">Camera Z</option>
              <option value="cam.x">Camera X</option>
              <option value="cam.fov">Camera FOV</option>
              <option value="obj.rot.y">Object Rot Y</option>
              <option value="obj.pos.x">Object Pos X</option>
              <option value="obj.scale">Object Scale</option>
              <option value="transport.seek">Timeline Seek</option>
            </select>
            <button id="midi-learn-btn" class="key-capture-btn"
              onclick="inputSys.startMIDILearn(document.getElementById('midi-learn-target').value)">⊕ MIDI Learn</button>
          </div>

          <!-- Add mapping row -->
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px">
            <div>
              <label style="font-family:var(--mono);font-size:9px;color:var(--text-label);display:block;margin-bottom:4px">SOURCE</label>
              <select id="map-source">
                <optgroup label="Gamepad Axes">
                  <option value="gp_axis_0_x">Left Stick X</option>
                  <option value="gp_axis_0_y">Left Stick Y</option>
                  <option value="gp_axis_1_x">Right Stick X</option>
                  <option value="gp_axis_1_y">Right Stick Y</option>
                  <option value="gp_axis_2">Trigger L</option>
                  <option value="gp_axis_3">Trigger R</option>
                </optgroup>
                <optgroup label="Gamepad Buttons">
                  <option value="gp_btn_0">Button 0 (A/Cross)</option>
                  <option value="gp_btn_1">Button 1 (B/Circle)</option>
                  <option value="gp_btn_2">Button 2 (X/Square)</option>
                  <option value="gp_btn_3">Button 3 (Y/Triangle)</option>
                  <option value="gp_btn_12">D-Pad Up</option>
                  <option value="gp_btn_13">D-Pad Down</option>
                  <option value="gp_btn_14">D-Pad Left</option>
                  <option value="gp_btn_15">D-Pad Right</option>
                </optgroup>
                <optgroup label="MIDI CC">
                  <option value="midi_cc_1">MIDI CC 1 (Mod Wheel)</option>
                  <option value="midi_cc_7">MIDI CC 7 (Volume)</option>
                  <option value="midi_cc_10">MIDI CC 10 (Pan)</option>
                  <option value="midi_cc_11">MIDI CC 11 (Expression)</option>
                  <option value="midi_cc_74">MIDI CC 74 (Filter)</option>
                  <option value="midi_note">MIDI Note Velocity</option>
                </optgroup>
                <optgroup label="Keyboard (or capture below)">
                  <option value="kbd_capture">— Capture Key —</option>
                  <option value="kbd_Space">Space</option>
                  <option value="kbd_KeyW">W</option>
                  <option value="kbd_KeyS">S</option>
                  <option value="kbd_KeyA">A</option>
                  <option value="kbd_KeyD">D</option>
                  <option value="kbd_ArrowLeft">Arrow Left</option>
                  <option value="kbd_ArrowRight">Arrow Right</option>
                  <option value="kbd_ArrowUp">Arrow Up</option>
                  <option value="kbd_ArrowDown">Arrow Down</option>
                </optgroup>
              </select>
            </div>
            <div>
              <label style="font-family:var(--mono);font-size:9px;color:var(--text-label);display:block;margin-bottom:4px">TARGET</label>
              <select id="map-target">
                <optgroup label="Camera">
                  <option value="cam.x">Camera X</option>
                  <option value="cam.y">Camera Y</option>
                  <option value="cam.z">Camera Z</option>
                  <option value="cam.rx">Camera Pitch</option>
                  <option value="cam.ry">Camera Yaw</option>
                  <option value="cam.fov">Camera FOV</option>
                </optgroup>
                <optgroup label="Selected Object">
                  <option value="obj.pos.x">Object Pos X</option>
                  <option value="obj.pos.y">Object Pos Y</option>
                  <option value="obj.pos.z">Object Pos Z</option>
                  <option value="obj.rot.x">Object Rot X</option>
                  <option value="obj.rot.y">Object Rot Y</option>
                  <option value="obj.rot.z">Object Rot Z</option>
                  <option value="obj.scale">Object Scale</option>
                </optgroup>
                <optgroup label="Transport">
                  <option value="transport.seek">Timeline Seek</option>
                  <option value="transport.play">Play/Pause Toggle</option>
                </optgroup>
              </select>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:8px;align-items:end;margin-bottom:8px">
            <div>
              <label style="font-family:var(--mono);font-size:9px;color:var(--text-label);display:block;margin-bottom:4px">SCALE</label>
              <input type="number" id="map-scale" value="1" step="0.05"
                style="background:var(--bg);border:1px solid var(--border);color:var(--text);font-family:var(--mono);font-size:10px;padding:5px 6px;border-radius:4px;width:100%;text-align:center">
            </div>
            <div>
              <label style="font-family:var(--mono);font-size:9px;color:var(--text-label);display:block;margin-bottom:4px">DEADZONE</label>
              <input type="number" id="map-deadzone" value="0.05" step="0.01" min="0" max="0.9"
                style="background:var(--bg);border:1px solid var(--border);color:var(--text);font-family:var(--mono);font-size:10px;padding:5px 6px;border-radius:4px;width:100%;text-align:center">
            </div>
            <div>
              <label style="font-family:var(--mono);font-size:9px;color:var(--text-label);display:block;margin-bottom:4px">MODE</label>
              <select id="map-mode" style="background:var(--bg);border:1px solid var(--border);color:var(--text);font-family:var(--mono);font-size:10px;padding:5px 4px;border-radius:4px;width:100%">
                <option value="delta">Delta</option>
                <option value="absolute">Absolute</option>
              </select>
            </div>
            <div style="display:flex;flex-direction:column;gap:4px">
              <label style="font-family:var(--mono);font-size:9px;color:var(--text-label)">KEY</label>
              <button class="key-capture-btn" id="key-capture-btn" onclick="inputSys.startKeyCapture()">Click</button>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
            <label class="checkbox-row" style="margin:0">
              <input type="checkbox" id="map-invert">
              <span style="font-family:var(--mono);font-size:10px;color:var(--text-label)">Invert</span>
            </label>
          </div>
          <button class="btn primary" onclick="inputSys.addMapping()" style="margin-bottom:14px">+ Add Mapping</button>

          <table class="mapping-table" id="mapping-table">
            <thead>
              <tr>
                <th>Source</th>
                <th>Target</th>
                <th>Scale</th>
                <th>Mode</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="mapping-tbody"></tbody>
          </table>
        </div>

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

// Keyboard camera nav — handled via inputSys mappings, but keep legacy WASD for non-mapped cameras
const keys = {};
// NOTE: Raw WASD movement is now handled by inputSys. This block exists as fallback
// when no explicit mappings are set. inputSys keydown/keyup events take priority.


// Mouse drag on the rotate pad
let dragging = false, lastMouseX, lastMouseY;
const rotatePad = document.getElementById('rotate-pad');

rotatePad.addEventListener('mousedown', (e) => {
  dragging = true;
  lastMouseX = e.clientX; lastMouseY = e.clientY;
  rotatePad.style.cursor = 'grabbing';
  rotatePad.style.background = 'radial-gradient(ellipse at center, rgba(255,140,60,0.14) 0%, rgba(255,140,60,0.04) 60%, transparent 100%)';
  e.preventDefault();
});
document.addEventListener('mousemove', (e) => {
  if (!dragging) return;
  const dx = e.clientX - lastMouseX, dy = e.clientY - lastMouseY;
  lastMouseX = e.clientX; lastMouseY = e.clientY;
  state.camera.ry += dx * 0.4;
  state.camera.rx += dy * 0.4;
  // clamp pitch
  state.camera.rx = Math.max(-89, Math.min(89, state.camera.rx));
  document.getElementById('cam-ry').value = state.camera.ry.toFixed(1);
  document.getElementById('cam-rx').value = state.camera.rx.toFixed(1);
  send({ type: 'camera', ...state.camera });
});
document.addEventListener('mouseup', () => {
  if (dragging) {
    dragging = false;
    rotatePad.style.cursor = 'grab';
    rotatePad.style.background = 'radial-gradient(ellipse at center, rgba(255,140,60,0.07) 0%, rgba(255,140,60,0.02) 60%, transparent 100%)';
  }
});

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


// ═══════════════════════════════════════════════════════════════════════════════
// INPUT SYSTEM — Gamepad · MIDI · Keyboard → Scene/Camera realtime mapping
// Features: deadzone, invert, MIDI learn, presets, save/load, rumble feedback
// ═══════════════════════════════════════════════════════════════════════════════
const inputSys = (() => {

  // ── State ────────────────────────────────────────────────────────────────
  let mappings = [];
  let mappingIdCounter = 0;
  const liveValues = {};         // sourceKey → normalised -1..1 or 0..1
  const prevBtnState = {};       // edge detection for buttons

  // Keyboard
  const heldKeys = new Set();
  let capturingKey = false;
  let capturedKey  = null;

  // Gamepad
  let gpIndex = null;
  const GP_LABELS = ['A','B','X','Y','LB','RB','LT','RT','Sel','Strt','LS','RS','↑','↓','←','→'];

  // MIDI
  let midiAccess   = null;
  let midiLearning = false;
  let midiLearnTarget = null;
  const MIDI_NOTES = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'];

  // Camera speed helper
  const camSpeed = () => +(document.getElementById('cam-speed')?.value || 0.05);

  // ── Dispatch ─────────────────────────────────────────────────────────────
  function dispatch(target, raw, scale, mode, invert) {
    const v = raw * scale * (invert ? -1 : 1);

    if (target.startsWith('cam.')) {
      const p = target.slice(4);
      if (mode === 'absolute') {
        const ranges = {x:[-20,20],y:[-20,20],z:[0.2,30],rx:[-89,89],ry:[-180,180],fov:[10,120]};
        const [lo,hi] = ranges[p]||[-10,10];
        state.camera[p] = lo + raw*(hi-lo)*(invert?-1:1);
      } else {
        state.camera[p] = (state.camera[p]||0) + v;
      }
      if (p==='rx') state.camera.rx = Math.max(-89,Math.min(89,state.camera.rx));
      const fm = {x:'cam-x',y:'cam-y',z:'cam-z',rx:'cam-rx',ry:'cam-ry',fov:'cam-fov'};
      const el = fm[p] && document.getElementById(fm[p]);
      if (el) el.value = (+state.camera[p]).toFixed(2);
      send({type:'camera',...state.camera});

    } else if (target.startsWith('obj.')) {
      if (!state.selectedId) return;
      const obj = state.objects[state.selectedId];
      if (!obj) return;
      const sub = target.slice(4);
      if (sub==='scale') {
        obj.scale = Math.max(0.01, mode==='absolute' ? Math.abs(raw*scale*6) : obj.scale + v*0.02);
        const el = document.getElementById('scale-slider');
        if (el) el.value = obj.scale;
      } else if (sub.startsWith('pos.')) {
        const ax=sub.slice(4);
        obj.position[ax] = mode==='absolute' ? v*10 : (obj.position[ax]||0)+v*0.04;
        const el = document.getElementById('t'+ax); if (el) el.value=obj.position[ax].toFixed(3);
      } else if (sub.startsWith('rot.')) {
        const ax=sub.slice(4);
        obj.rotation[ax] = mode==='absolute' ? v*180 : (obj.rotation[ax]||0)+v*2;
        const el = document.getElementById('r'+ax); if (el) el.value=obj.rotation[ax].toFixed(1);
      }
      send({type:'object_transform',id:state.selectedId,
            position:obj.position,rotation:obj.rotation,scale:obj.scale});

    } else if (target==='transport.seek') {
      const t = mode==='absolute'
        ? Math.max(0,Math.min(state.timeline.duration, raw*state.timeline.duration*(invert?-1:1)))
        : Math.max(0,Math.min(state.timeline.duration, state.timeline.currentTime+v*0.06));
      seekTo(t);
    }
    // transport.play is handled via edge detection, not here
  }

  // ── Per-frame run ─────────────────────────────────────────────────────────
  function runMappings() {
    for (const m of mappings) {
      const val = liveValues[m.source] ?? 0;
      const dz  = m.deadzone ?? 0.05;
      if (m.source.startsWith('gp_axis') || m.source.startsWith('midi_cc')) {
        if (Math.abs(val) > dz)
          dispatch(m.target, val, m.scale, m.mode||'delta', m.invert);
      } else if (m.source.startsWith('gp_btn') || m.source.startsWith('kbd_')) {
        if (val > 0.5 && (m.target.startsWith('cam.') || m.target.startsWith('obj.')))
          dispatch(m.target, val, m.scale, 'delta', m.invert);
      }
    }

    // Built-in WASD (only when no conflicting cam mapping and checkbox on)
    if (document.getElementById('cam-keyboard')?.checked) {
      const sp = camSpeed();
      const c  = state.camera;
      const yr = c.ry * Math.PI / 180;
      let moved = false;
      const mv = (dx,dy,dz)=>{ c.x+=dx; c.y+=dy; c.z+=dz; moved=true; };
      if(heldKeys.has('KeyW'))      mv( Math.sin(yr)*sp, 0,-Math.cos(yr)*sp);
      if(heldKeys.has('KeyS'))      mv(-Math.sin(yr)*sp, 0, Math.cos(yr)*sp);
      if(heldKeys.has('KeyA'))      mv(-Math.cos(yr)*sp, 0,-Math.sin(yr)*sp);
      if(heldKeys.has('KeyD'))      mv( Math.cos(yr)*sp, 0, Math.sin(yr)*sp);
      if(heldKeys.has('KeyQ'))      mv(0,-sp,0);
      if(heldKeys.has('KeyE'))      mv(0, sp,0);
      if(heldKeys.has('ArrowLeft')) { c.ry-=sp*50; moved=true; }
      if(heldKeys.has('ArrowRight')){ c.ry+=sp*50; moved=true; }
      if(heldKeys.has('ArrowUp'))   { c.rx=Math.max(-89,c.rx-sp*50); moved=true; }
      if(heldKeys.has('ArrowDown')) { c.rx=Math.min( 89,c.rx+sp*50); moved=true; }
      if (moved) {
        ['x','y','z','rx','ry'].forEach(p=>{
          const el=document.getElementById('cam-'+p); if(el) el.value=(+c[p]).toFixed(2);
        });
        send({type:'camera',...c});
      }
    }
  }

  // ── GAMEPAD ───────────────────────────────────────────────────────────────
  window.addEventListener('gamepadconnected', e=>{
    gpIndex = e.gamepad.index;
    document.getElementById('gp-name').textContent = e.gamepad.id.substring(0,44);
    setStatus('gp',true);
    buildGamepadUI(e.gamepad);
    rumble(120,0.4,0.15);
  });
  window.addEventListener('gamepaddisconnected', e=>{
    if(e.gamepad.index===gpIndex){ gpIndex=null; setStatus('gp',false);
      document.getElementById('gp-name').textContent='No gamepad detected'; }
  });

  function setStatus(prefix, on) {
    const el = document.getElementById(prefix+'-status');
    if(!el) return;
    el.textContent = on ? 'Connected' : 'Disconnected';
    el.classList.toggle('connected', on);
  }

  function rumble(ms, strong=0.4, weak=0.1) {
    if(gpIndex===null) return;
    const gp=(navigator.getGamepads?.()??[])[gpIndex];
    gp?.vibrationActuator?.playEffect?.('dual-rumble',{
      startDelay:0, duration:ms, strongMagnitude:strong, weakMagnitude:weak
    })?.catch(()=>{});
  }

  function buildGamepadUI(gp) {
    const bc = document.getElementById('gp-buttons');
    bc.innerHTML='';
    const n = Math.min(gp.buttons.length,16);
    for(let i=0;i<n;i++){
      const el=document.createElement('div');
      el.className='gp-btn-indicator'; el.textContent=GP_LABELS[i]??i; el.id='gp-btn-'+i;
      el.title=`Btn ${i} — click to prefill source`;
      el.onclick=()=>{ document.getElementById('map-source').value='gp_btn_'+i; };
      bc.appendChild(el);
    }
    const bars=document.getElementById('gp-live-bars');
    bars.innerHTML='';
    const names=['Left X','Left Y','Right X','Right Y','L.Trigger','R.Trigger'];
    for(let i=0;i<Math.min(gp.axes.length,6);i++){
      const sIdx=Math.floor(i/2), ax=i%2===0?'x':'y';
      bars.innerHTML+=`<div class="live-bar-wrap">
        <span class="live-bar-label" style="cursor:pointer;user-select:none"
          title="Click to prefill"
          onclick="document.getElementById('map-source').value='gp_axis_${sIdx}_${ax}'">
          ${names[i]||'Axis '+i}</span>
        <div class="live-bar"><div class="live-bar-fill" id="gp-bar-${i}"></div></div>
        <span class="live-bar-val" id="gp-bar-val-${i}">0.00</span></div>`;
    }
  }

  function pollGamepad() {
    if(gpIndex===null) return;
    const gp=(navigator.getGamepads?.()??[])[gpIndex];
    if(!gp) return;

    gp.axes.forEach((v,i)=>{
      const n=Math.max(-1,Math.min(1,v));
      const sIdx=Math.floor(i/2), ax=i%2===0?'x':'y';
      liveValues[`gp_axis_${sIdx}_${ax}`] = ax==='y'?-n:n;
      // Shoulder triggers on axes 4&5 (some controllers)
      if(i===4) liveValues['gp_axis_2']=(n+1)/2;
      if(i===5) liveValues['gp_axis_3']=(n+1)/2;
      const bar=document.getElementById('gp-bar-'+i);
      const bv=document.getElementById('gp-bar-val-'+i);
      if(bar) bar.style.width=((n+1)/2*100).toFixed(1)+'%';
      if(bv)  bv.textContent=n.toFixed(2);
    });
    updateStick(0,gp.axes[0]??0,gp.axes[1]??0);
    updateStick(1,gp.axes[2]??0,gp.axes[3]??0);

    gp.buttons.forEach((btn,i)=>{
      const pressed=btn.pressed||btn.value>0.5;
      const key='gp_btn_'+i;
      const was=prevBtnState[key]??false;
      liveValues[key]=pressed?btn.value:0;
      if(pressed&&!was){
        rumble(35,0.12,0.04);
        for(const m of mappings){
          if(m.source===key&&m.target==='transport.play') togglePlay();
        }
      }
      prevBtnState[key]=pressed;
      const el=document.getElementById('gp-btn-'+i);
      if(el) el.classList.toggle('pressed',pressed);
    });
  }

  function updateStick(idx,x,y){
    const d=document.getElementById('axis-dot-'+idx);
    if(!d) return;
    d.style.left=(50+x*38)+'%'; d.style.top=(50+y*38)+'%';
  }

  function scanGamepads(){
    const all=navigator.getGamepads?.()??[];
    for(let i=0;i<all.length;i++){
      if(all[i]){ gpIndex=i;
        document.getElementById('gp-name').textContent=all[i].id.substring(0,44);
        setStatus('gp',true); buildGamepadUI(all[i]); rumble(150,0.5,0.2); return; }
    }
    document.getElementById('gp-name').textContent='No gamepad — press any button to connect';
  }

  // ── MIDI ──────────────────────────────────────────────────────────────────
  async function initMIDI(){
    if(!navigator.requestMIDIAccess){
      alert('Web MIDI not available.\nUse Chrome or Edge desktop.'); return;
    }
    try{ midiAccess=await navigator.requestMIDIAccess({sysex:false}); }
    catch(e){ alert('MIDI denied: '+e.message); return; }
    const ins=[...midiAccess.inputs.values()];
    if(!ins.length){ document.getElementById('midi-name').textContent='No MIDI inputs found'; return; }
    document.getElementById('midi-name').textContent=ins.map(i=>i.name).join(' · ').substring(0,44);
    setStatus('midi',true);
    ins.forEach(i=>{ i.onmidimessage=handleMIDI; });
    midiAccess.onstatechange=()=>{
      const ii=[...midiAccess.inputs.values()];
      ii.forEach(i=>{ i.onmidimessage=handleMIDI; });
      document.getElementById('midi-name').textContent=ii.map(i=>i.name).join(' · ').substring(0,44);
      setStatus('midi',ii.length>0);
    };
    buildMidiBars([1,7,10,11,74]);
  }

  function buildMidiBars(ccs){
    const el=document.getElementById('midi-live-bars');
    if(!el) return;
    el.innerHTML=ccs.map(cc=>`<div class="live-bar-wrap">
      <span class="live-bar-label" style="cursor:pointer;user-select:none" title="Click to prefill"
        onclick="document.getElementById('map-source').value='midi_cc_${cc}'">CC ${cc}</span>
      <div class="live-bar"><div class="live-bar-fill" id="midi-bar-${cc}"></div></div>
      <span class="live-bar-val" id="midi-bar-val-${cc}">0</span></div>`).join('');
  }

  function handleMIDI(msg){
    const [st,d1,d2]=msg.data;
    const type=st&0xF0, ch=(st&0x0F)+1;
    const chEl=document.getElementById('midi-ch');
    const valEl=document.getElementById('midi-val');
    const lastEl=document.getElementById('midi-last');
    if(chEl) chEl.textContent=ch;
    if(valEl) valEl.textContent=d2;

    if(type===0xB0){
      liveValues['midi_cc_'+d1]=d2/127;
      if(lastEl) lastEl.textContent='CC'+d1;
      const bar=document.getElementById('midi-bar-'+d1);
      const bv=document.getElementById('midi-bar-val-'+d1);
      if(bar) bar.style.width=(d2/127*100).toFixed(1)+'%';
      if(bv)  bv.textContent=d2;
      if(midiLearning&&midiLearnTarget){ finishLearn('midi_cc_'+d1); return; }
      for(const m of mappings)
        if(m.source==='midi_cc_'+d1) dispatch(m.target,d2/127,m.scale,'absolute',m.invert);

    } else if(type===0x90&&d2>0){
      const name=MIDI_NOTES[d1%12]+Math.floor(d1/12-1);
      liveValues['midi_note']=d2/127;
      if(lastEl) lastEl.textContent=name;
      if(midiLearning&&midiLearnTarget){ finishLearn('midi_note'); return; }
      for(const m of mappings)
        if(m.source==='midi_note') dispatch(m.target,d2/127,m.scale,'absolute',m.invert);

    } else if((type===0x90&&d2===0)||type===0x80){
      liveValues['midi_note']=0;
    }
  }

  function startMIDILearn(target){
    midiLearning=true; midiLearnTarget=target;
    const btn=document.getElementById('midi-learn-btn');
    if(btn){ btn.textContent='● Listening…'; btn.style.color='var(--accent)'; }
  }
  function finishLearn(source){
    midiLearning=false;
    const id=++mappingIdCounter;
    mappings.push({id,source,target:midiLearnTarget,scale:1,mode:'absolute',invert:false,deadzone:0});
    midiLearnTarget=null;
    saveMappings(); renderTable();
    const btn=document.getElementById('midi-learn-btn');
    if(btn){ btn.textContent='⊕ MIDI Learn'; btn.style.color=''; }
  }

  // ── KEYBOARD ──────────────────────────────────────────────────────────────
  document.addEventListener('keydown', e=>{
    if(['INPUT','SELECT','TEXTAREA'].includes(e.target.tagName)) return;
    if(capturingKey){
      e.preventDefault();
      capturedKey=e.code;
      const btn=document.getElementById('key-capture-btn');
      if(btn){ btn.textContent=e.code; btn.classList.remove('listening'); }
      // Auto-add option to source select
      const sel=document.getElementById('map-source');
      let found=false;
      for(const o of sel.options) if(o.value==='kbd_'+e.code){found=true;sel.value=o.value;break;}
      if(!found){
        const o=document.createElement('option');
        o.value='kbd_'+e.code; o.textContent='⌨ '+e.code;
        sel.appendChild(o); sel.value=o.value;
      }
      capturingKey=false; return;
    }
    if(!heldKeys.has(e.code)){
      heldKeys.add(e.code);
      liveValues['kbd_'+e.code]=1;
      updateKbdDisplay();
      // Edge-triggered mappings
      for(const m of mappings){
        if(m.source==='kbd_'+e.code&&m.target==='transport.play') togglePlay();
      }
    }
  });
  document.addEventListener('keyup', e=>{
    heldKeys.delete(e.code);
    liveValues['kbd_'+e.code]=0;
    updateKbdDisplay();
  });
  function updateKbdDisplay(){
    const el=document.getElementById('kbd-held');
    if(!el) return;
    el.textContent=[...heldKeys].map(k=>k.replace('Key','').replace('Digit','').replace('Arrow','')).join(' + ')||'none';
  }
  function startKeyCapture(){
    capturingKey=true; capturedKey=null;
    const btn=document.getElementById('key-capture-btn');
    if(btn){ btn.textContent='Press key…'; btn.classList.add('listening'); }
  }

  // ── PRESETS ───────────────────────────────────────────────────────────────
  const PRESETS={
    'Xbox / Standard — Camera Fly':[
      {source:'gp_axis_0_x',target:'cam.ry',  scale:2.5, mode:'delta',   invert:false,deadzone:0.08},
      {source:'gp_axis_0_y',target:'cam.rx',  scale:1.8, mode:'delta',   invert:false,deadzone:0.08},
      {source:'gp_axis_1_x',target:'cam.x',   scale:0.1, mode:'delta',   invert:false,deadzone:0.08},
      {source:'gp_axis_1_y',target:'cam.z',   scale:0.1, mode:'delta',   invert:false,deadzone:0.08},
      {source:'gp_axis_2',  target:'cam.y',   scale:0.07,mode:'delta',   invert:true, deadzone:0.04},
      {source:'gp_axis_3',  target:'cam.y',   scale:0.07,mode:'delta',   invert:false,deadzone:0.04},
      {source:'gp_btn_0',   target:'transport.play',scale:1,mode:'toggle',invert:false,deadzone:0},
    ],
    'Xbox / Standard — Object Control':[
      {source:'gp_axis_0_x',target:'obj.rot.y',scale:2.5,mode:'delta',invert:false,deadzone:0.08},
      {source:'gp_axis_0_y',target:'obj.rot.x',scale:2.5,mode:'delta',invert:false,deadzone:0.08},
      {source:'gp_axis_1_x',target:'obj.pos.x',scale:0.05,mode:'delta',invert:false,deadzone:0.08},
      {source:'gp_axis_1_y',target:'obj.pos.y',scale:0.05,mode:'delta',invert:false,deadzone:0.08},
      {source:'gp_axis_2',  target:'obj.pos.z',scale:0.04,mode:'delta',invert:false,deadzone:0.04},
      {source:'gp_axis_3',  target:'obj.pos.z',scale:0.04,mode:'delta',invert:true, deadzone:0.04},
      {source:'gp_btn_3',   target:'transport.play',scale:1,mode:'toggle',invert:false,deadzone:0},
    ],
    'MIDI CC — Camera Absolutes':[
      {source:'midi_cc_1', target:'cam.ry', scale:1,mode:'absolute',invert:false,deadzone:0},
      {source:'midi_cc_7', target:'cam.z',  scale:1,mode:'absolute',invert:false,deadzone:0},
      {source:'midi_cc_10',target:'cam.x',  scale:1,mode:'absolute',invert:false,deadzone:0},
      {source:'midi_cc_11',target:'cam.y',  scale:1,mode:'absolute',invert:false,deadzone:0},
      {source:'midi_cc_74',target:'cam.fov',scale:1,mode:'absolute',invert:false,deadzone:0},
    ],
    'MIDI CC — Object Sculpt':[
      {source:'midi_cc_1', target:'obj.rot.y',scale:1,mode:'absolute',invert:false,deadzone:0},
      {source:'midi_cc_7', target:'obj.pos.y',scale:1,mode:'absolute',invert:false,deadzone:0},
      {source:'midi_cc_10',target:'obj.pos.x',scale:1,mode:'absolute',invert:false,deadzone:0},
      {source:'midi_cc_74',target:'obj.scale',scale:1,mode:'absolute',invert:false,deadzone:0},
    ],
    'Keyboard — QWERTY + Space':[
      {source:'kbd_KeyW',  target:'cam.z',   scale:0.1, mode:'delta',invert:true, deadzone:0},
      {source:'kbd_KeyS',  target:'cam.z',   scale:0.1, mode:'delta',invert:false,deadzone:0},
      {source:'kbd_KeyA',  target:'cam.x',   scale:0.1, mode:'delta',invert:true, deadzone:0},
      {source:'kbd_KeyD',  target:'cam.x',   scale:0.1, mode:'delta',invert:false,deadzone:0},
      {source:'kbd_KeyQ',  target:'cam.y',   scale:0.1, mode:'delta',invert:true, deadzone:0},
      {source:'kbd_KeyE',  target:'cam.y',   scale:0.1, mode:'delta',invert:false,deadzone:0},
      {source:'kbd_Space', target:'transport.play',scale:1,mode:'toggle',invert:false,deadzone:0},
    ],
    'Keyboard — Arrow Object Rotate':[
      {source:'kbd_ArrowLeft', target:'obj.rot.y',scale:2,mode:'delta',invert:true, deadzone:0},
      {source:'kbd_ArrowRight',target:'obj.rot.y',scale:2,mode:'delta',invert:false,deadzone:0},
      {source:'kbd_ArrowUp',   target:'obj.rot.x',scale:2,mode:'delta',invert:true, deadzone:0},
      {source:'kbd_ArrowDown', target:'obj.rot.x',scale:2,mode:'delta',invert:false,deadzone:0},
    ],
  };

  function loadPreset(name){
    const p=PRESETS[name]; if(!p) return;
    p.forEach(m=>mappings.push({id:++mappingIdCounter,...m}));
    saveMappings(); renderTable(); rumble(80,0.2,0.08);
  }
  function populatePresets(){
    const sel=document.getElementById('preset-select');
    if(!sel) return;
    sel.innerHTML='<option value="">— Load Preset —</option>'+
      Object.keys(PRESETS).map(k=>`<option value="${k}">${k}</option>`).join('');
  }

  // ── PERSIST ───────────────────────────────────────────────────────────────
  function saveMappings(){
    try{ localStorage.setItem('stage_mappings',JSON.stringify(mappings)); }catch(e){}
  }
  function loadSaved(){
    try{
      const s=localStorage.getItem('stage_mappings');
      if(!s) return;
      mappings=JSON.parse(s);
      mappingIdCounter=mappings.reduce((mx,m)=>Math.max(mx,m.id||0),0);
      renderTable();
    }catch(e){}
  }

  // ── MAPPING CRUD ──────────────────────────────────────────────────────────
  function addMapping(){
    let source=document.getElementById('map-source').value;
    if(!source) return;
    if(source==='kbd_capture'){
      if(!capturedKey){ alert('Click KEY button then press a key first.'); return; }
      source='kbd_'+capturedKey;
    }
    const target  = document.getElementById('map-target').value;
    const scale   = parseFloat(document.getElementById('map-scale').value)||1;
    const deadzone= parseFloat(document.getElementById('map-deadzone')?.value||0.05);
    const invert  = document.getElementById('map-invert')?.checked||false;
    const modeEl  = document.getElementById('map-mode');
    const mode    = modeEl?.value || ((target==='transport.seek'||source.startsWith('midi'))?'absolute':'delta');
    mappings.push({id:++mappingIdCounter,source,target,scale,mode,invert,deadzone});
    saveMappings(); renderTable();
  }
  function removeMapping(id){
    mappings=mappings.filter(m=>m.id!==id);
    saveMappings(); renderTable();
  }
  function clearAllMappings(){
    if(!confirm('Remove all mappings?')) return;
    mappings=[]; saveMappings(); renderTable();
  }

  // ── RENDER TABLE ─────────────────────────────────────────────────────────
  function renderTable(){
    const tbody=document.getElementById('mapping-tbody');
    const cnt=document.getElementById('mapping-count');
    if(!tbody) return;
    if(cnt) cnt.textContent=mappings.length+' binding'+(mappings.length!==1?'s':'');
    if(!mappings.length){
      tbody.innerHTML=`<tr><td colspan="5" style="font-family:var(--mono);font-size:10px;
        color:var(--text-label);text-align:center;padding:14px">
        No mappings — add one or load a preset</td></tr>`;
      return;
    }
    tbody.innerHTML=mappings.map(m=>`<tr>
      <td><span class="mapping-source" title="${m.source}">${m.source.length>17?m.source.slice(0,16)+'…':m.source}</span></td>
      <td><span class="mapping-target">${m.target}</span></td>
      <td><span class="mapping-scale">×${m.scale}</span></td>
      <td style="white-space:nowrap;font-family:var(--mono);font-size:9px;color:var(--text-label)">
        ${m.mode||'delta'}${m.invert?' INV':''}${m.deadzone>0?' dz'+m.deadzone:''}</td>
      <td><span class="mapping-del" onclick="inputSys.removeMapping(${m.id})">✕</span></td>
    </tr>`).join('');
  }

  // ── RAF LOOP ──────────────────────────────────────────────────────────────
  function tick(){ pollGamepad(); runMappings(); requestAnimationFrame(tick); }

  // ── BOOT ─────────────────────────────────────────────────────────────────
  loadSaved();
  populatePresets();
  requestAnimationFrame(tick);

  return { scanGamepads, initMIDI, startMIDILearn,
           addMapping, removeMapping, clearAllMappings,
           loadPreset, startKeyCapture };
})();
</script>
</body>
</html>
