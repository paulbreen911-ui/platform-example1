<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

$page_title = 'Test Pattern Generator';
include __DIR__ . '/../header.php';
?>

<style>
/* ── TOOL PAGE LAYOUT ────────────────────────────────────────────────────── */
main {
  padding-top: 0;
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - var(--nav-h));
}

/* ── TOOL-SCOPED TOKENS — mapped to PC design system ────────────────────── */
:root {
  --tp-bg:      var(--black);
  --tp-panel:   var(--dark);
  --tp-panel2:  var(--dark-2);
  --tp-border:  var(--border);
  --tp-accent:  var(--gold);
  --tp-accent2: var(--gold);
  --tp-accent3: #4caf50;
  --tp-warn:    #ffb300;
  --tp-danger:  #ff4444;
  --tp-text:    var(--text-1);
  --tp-text2:   var(--text-2);
  --tp-text3:   var(--text-3);
  --tp-cyan:    #00c8d4;
  --tp-mono:    'Share Tech Mono', 'DM Sans', monospace;
}

/* ── TOOL HEADER BAR ─────────────────────────────────────────────────────── */
.tool-hd {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 28px;
  border-bottom: .5px solid var(--border);
  background: var(--dark);
  flex-shrink: 0;
  flex-wrap: wrap;
  gap: 12px;
}
.tool-hd-left   { display: flex; align-items: center; gap: 16px; }
.tool-hd-back {
  font-size: 11px; letter-spacing: .5px; color: var(--text-3);
  text-decoration: none; display: inline-flex; align-items: center;
  gap: 5px; transition: color .15s; white-space: nowrap;
}
.tool-hd-back:hover { color: var(--text-1); }
.tool-hd-divider { width: .5px; height: 20px; background: var(--border); }
.tool-hd-title  {
  font-family: var(--disp); font-size: 20px;
  letter-spacing: 1.5px; color: var(--text-1); line-height: 1;
}
.tool-hd-sub {
  font-size: 10px; letter-spacing: 2px; text-transform: uppercase;
  color: var(--text-3); margin-top: 2px;
}
.tool-hd-actions { display: flex; gap: 8px; align-items: center; }
.tc-btn {
  font-family: var(--sans); font-size: 11px; letter-spacing: .5px;
  font-weight: 500; padding: 8px 16px; border-radius: 6px;
  border: .5px solid var(--border); cursor: pointer;
  transition: all .15s; white-space: nowrap;
}
.tc-btn-ghost { background: transparent; color: var(--text-3); }
.tc-btn-ghost:hover { border-color: var(--gold-bd); color: var(--gold); }
.tc-btn-gold  { background: rgba(232,184,75,.15); color: var(--gold); border-color: var(--gold-bd); font-weight: 600; }
.tc-btn-gold:hover  { background: rgba(232,184,75,.25); border-color: var(--gold); }

/* ── TOOL CONTENT WRAP ───────────────────────────────────────────────────── */
.tp-wrap {
  position: relative; z-index: 1;
  max-width: 1000px; margin: 0 auto;
  padding: 28px 24px 48px;
  width: 100%;
}

/* ── Subtle grid background ──────────────────────────────────────────────── */
.tp-wrap::before {
  content: '';
  position: fixed; inset: 0;
  background-image:
    linear-gradient(rgba(232,184,75,0.025) 1px, transparent 1px),
    linear-gradient(90deg, rgba(232,184,75,0.025) 1px, transparent 1px);
  background-size: 40px 40px;
  pointer-events: none; z-index: 0;
}

/* ── SECTIONS ────────────────────────────────────────────────────────────── */
.section {
  background: var(--tp-panel);
  border: .5px solid var(--tp-border);
  border-radius: 8px;
  padding: 20px 24px;
  margin-bottom: 16px;
  position: relative; z-index: 1;
}
.section-title {
  font-family: var(--tp-mono);
  font-size: 10px; color: var(--gold);
  letter-spacing: 2px; text-transform: uppercase;
  margin-bottom: 16px; padding-bottom: 10px;
  border-bottom: .5px solid var(--tp-border);
  display: flex; align-items: center; gap: 8px;
}
.section-title::before {
  content: ''; display: inline-block;
  width: 6px; height: 6px;
  background: var(--gold); border-radius: 50%; flex-shrink: 0;
}

/* ── GRIDS ───────────────────────────────────────────────────────────────── */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
.grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
@media (max-width: 640px) {
  .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr 1fr; }
}

/* ── FORM CONTROLS ───────────────────────────────────────────────────────── */
.field { display: flex; flex-direction: column; gap: 6px; }
label {
  font-size: 11px; color: var(--tp-text2);
  letter-spacing: 1px; text-transform: uppercase; font-weight: 500;
}
select, input[type="number"] {
  background: var(--tp-panel2);
  border: .5px solid var(--tp-border);
  border-radius: 4px; color: var(--tp-text);
  font-family: var(--tp-mono); font-size: 13px;
  padding: 8px 10px; outline: none;
  transition: border-color .15s; width: 100%;
  appearance: none; -webkit-appearance: none;
}
select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23888'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center;
  padding-right: 28px; cursor: pointer;
}
select:focus, input:focus { border-color: var(--gold); }
select option { background: var(--dark-2); }
input[type="number"] { text-align: center; }
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
input[type="number"] { -moz-appearance: textfield; }

/* ── SPINNERS ────────────────────────────────────────────────────────────── */
.spinner-wrap { display: flex; align-items: center; gap: 0; }
.spinner-wrap input { border-radius: 4px 0 0 4px; border-right: none; }
.spin-btn {
  background: var(--tp-panel2); border: .5px solid var(--tp-border);
  color: var(--tp-text2); font-size: 16px; width: 32px;
  cursor: pointer; display: flex; align-items: center;
  justify-content: center; height: 36px; transition: all .1s;
  user-select: none; flex-shrink: 0;
}
.spin-btn:first-of-type { border-radius: 0; }
.spin-btn:last-of-type  { border-radius: 0 4px 4px 0; }
.spin-btn:hover  { background: var(--tp-border); color: var(--gold); }
.spin-btn:active { transform: scale(.95); }

/* ── PILLS ───────────────────────────────────────────────────────────────── */
.stp-pill {
  display: inline-flex; align-items: center; padding: 3px 10px;
  border: .5px solid var(--tp-border); border-radius: 20px;
  font-family: var(--tp-mono); font-size: 10px; color: var(--tp-text3);
  cursor: pointer; user-select: none; white-space: nowrap;
  transition: all .1s; letter-spacing: .3px;
}
.stp-pill-on { border-color: var(--gold); color: var(--gold); background: rgba(232,184,75,.07); }

/* ── ADJ CARDS ───────────────────────────────────────────────────────────── */
.stp-adj-card {
  background: var(--tp-panel2); border: .5px solid var(--tp-border);
  border-radius: 6px; padding: 8px 12px;
  display: flex; flex-direction: column; gap: 7px;
}
.stp-adj-label {
  font-family: var(--tp-mono); font-size: 9px;
  letter-spacing: 1px; text-transform: uppercase; color: var(--tp-text2);
}

/* ── EXPORT BUTTON ───────────────────────────────────────────────────────── */
.pdf-btn {
  background: none; border: .5px solid var(--gold); color: var(--gold);
  font-family: var(--tp-mono); font-size: 11px; letter-spacing: 1.5px;
  text-transform: uppercase; padding: 7px 16px; border-radius: 4px;
  cursor: pointer; transition: all .15s;
  display: flex; align-items: center; gap: 7px;
}
.pdf-btn:hover { background: var(--gold); color: #000; }
.pdf-btn svg   { width: 13px; height: 13px; flex-shrink: 0; }

/* ── TOOLTIP ─────────────────────────────────────────────────────────────── */
.tooltip-wrap {
  display: inline-flex; align-items: center; justify-content: center;
  width: 14px; height: 14px; background: var(--tp-border);
  color: var(--tp-text2); border-radius: 50%; font-size: 9px;
  cursor: pointer; position: relative; margin-left: 4px;
  vertical-align: middle; font-weight: bold; font-style: normal;
}
.tooltip-box {
  display: none; position: absolute;
  bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%);
  background: var(--dark-2); border: .5px solid var(--gold);
  border-radius: 5px; padding: 10px 12px; width: 220px;
  font-size: 11.5px; color: var(--text-1); line-height: 1.6; z-index: 9999;
  font-weight: normal; letter-spacing: 0; text-transform: none;
  white-space: normal; pointer-events: none;
  box-shadow: 0 4px 20px rgba(0,0,0,.5);
}
.tooltip-wrap:hover .tooltip-box { display: block; }

/* ── COLOUR PICKER ───────────────────────────────────────────────────────── */
.stp-picker-wrap { position: relative; display: inline-block; }
.stp-picker-panel {
  display: none; position: absolute; z-index: 100; top: 36px; left: 0;
  background: var(--tp-panel); border: .5px solid var(--tp-border);
  border-radius: 8px; padding: 14px; min-width: 220px;
  box-shadow: 0 4px 20px rgba(0,0,0,.5);
}
.stp-picker-panel.open { display: block; }
.stp-picker-preview { width: 100%; height: 32px; border-radius: 4px; margin-bottom: 10px; border: .5px solid var(--tp-border); }
.stp-picker-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
.stp-picker-label { font-family: var(--tp-mono); font-size: 10px; color: var(--tp-text2); width: 16px; flex-shrink: 0; }
.stp-picker-row input[type=range] { flex: 1; height: 3px; accent-color: var(--gold); }
.stp-picker-val { font-family: var(--tp-mono); font-size: 11px; color: var(--gold); width: 30px; text-align: right; }
.stp-picker-hex {
  width: 100%; background: var(--tp-panel2); border: .5px solid var(--tp-border);
  border-radius: 4px; color: var(--tp-text); font-family: var(--tp-mono); font-size: 12px;
  padding: 6px 8px; outline: none; margin-bottom: 8px; text-transform: uppercase;
}

/* ── POPOUT INFO ─────────────────────────────────────────────────────────── */
.info-box {
  background: rgba(0,200,212,.04); border: .5px solid rgba(0,200,212,.15);
  border-radius: 6px; padding: 12px 14px; font-size: 12px;
  color: var(--tp-text2); line-height: 1.6; margin-top: 12px;
}
.info-box strong { color: var(--gold); font-weight: 600; }
.formula {
  font-family: var(--tp-mono); font-size: 11px; color: var(--tp-text3);
  background: var(--black); padding: 8px 12px; border-radius: 4px;
  border: .5px solid var(--tp-border); margin-top: 10px; line-height: 1.8;
}
.formula span { color: var(--gold); }
.divider { border: none; border-top: .5px solid var(--tp-border); margin: 16px 0; }

/* ── PRINT ───────────────────────────────────────────────────────────────── */
@page { size: A4 landscape; margin: 12mm 8mm; }
@media print {
  * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
  .nav, .tool-hd, .pdf-btn, .tc-btn { display: none !important; }
  main { min-height: auto; }
  .tp-wrap { padding: 8px 12px; }
  .section { break-inside: avoid; margin-bottom: 10px; }
}

/* ── RESPONSIVE ──────────────────────────────────────────────────────────── */
@media (max-width: 860px) {
  .tool-hd-sub { display: none; }
  .tp-wrap { padding: 16px 14px 40px; }
}
</style>

<!-- TOOL HEADER -->
<div class="tool-hd">
  <div class="tool-hd-left">
    <a class="tool-hd-back" href="/tools.php">← Tools</a>
    <div class="tool-hd-divider"></div>
    <div>
      <div class="tool-hd-title">Test Pattern Generator</div>
      <div class="tool-hd-sub">Display · LED · Projection · Alignment & Calibration</div>
    </div>
  </div>
  <div class="tool-hd-actions">
    <button class="tc-btn tc-btn-ghost" onclick="stpClearPattern()">↺ Clear</button>
    <button class="tc-btn tc-btn-gold"  onclick="window.print()">⎙ Print / PDF</button>
  </div>
</div>

<!-- TOOL CONTENT -->
<div class="tp-wrap">

<!-- TAB: TEST PATTERNS -->
<div id="tab-testpattern" class="tab-panel active">

  <!-- Sub-tabs + lock -->
  <div style="display:flex;gap:0;border-bottom:.5px solid var(--border);margin-bottom:20px;align-items:center">
    <button id="stp-subtab-btn-generic" onclick="stpSetSubtab('generic')"
      style="font-family:var(--tp-mono);font-size:10px;letter-spacing:1px;padding:8px 18px;background:transparent;border:none;border-bottom:2px solid var(--gold);color:var(--gold);cursor:pointer;margin-bottom:-1px">
      GENERIC / PROJECTION
    </button>
    <button id="stp-subtab-btn-led" onclick="stpSetSubtab('led')"
      style="font-family:var(--tp-mono);font-size:10px;letter-spacing:1px;padding:8px 18px;background:transparent;border:none;border-bottom:2px solid transparent;color:var(--text-3);cursor:pointer;margin-bottom:-1px">
      LED
    </button>
    <div style="margin-left:auto;padding-right:4px;margin-bottom:-1px;display:flex;gap:6px;align-items:center">
      <label id="stp-lock-btn" class="stp-pill" onclick="stpToggleLock()"
        title="Lock pattern — prevents accidental overwrite of current settings"
        style="cursor:pointer">
        <input type="checkbox" id="stp-locked" style="display:none">
        🔓 Unlocked
      </label>
    </div>
  </div>

  <div class="section">
    <div class="section-title">Pattern Setup</div>

    <!-- Row 1: main selectors -->
    <div class="grid-4" style="margin-bottom:12px">
      <div class="field">
        <label>Base Pattern</label>
        <select id="stp-type" onchange="calcStandalonePattern()">
          <optgroup label="Alignment &amp; Geometry">
            <option value="grid" selected>Grid</option>
            <option value="checker">Checkerboard</option>
            <option value="focus">Focus chart</option>
            <option value="zones">Zone plate</option>
          </optgroup>
          <optgroup label="Greyscale">
            <option value="greyramp">Grey scale ramp</option>
            <option value="hbars">Horizontal grey bars</option>
          </optgroup>
          <optgroup label="Colour">
            <option value="smpte">SMPTE 75% colour bars (EG 1-1990)</option>
            <option value="bars_h">Colour bars — vertical stripes</option>
            <option value="bars_v">Colour bars — horizontal stripes</option>
            <option value="colchips">Colour chip chart</option>
          </optgroup>
          <optgroup label="Solid">
            <option value="fullwhite">Full white</option>
            <option value="fullblack">Full black</option>
            <option value="red">Full red</option>
            <option value="green">Full green</option>
            <option value="blue">Full blue</option>
          </optgroup>
        </select>
      </div>
      <div class="field" id="stp-width-field">
        <label>Width (px)</label>
        <div class="spinner-wrap">
          <button class="spin-btn" onclick="spinSTP('stp-width',-10)">−</button>
          <input type="number" id="stp-width" value="1920" min="320" max="32768" oninput="calcStandalonePattern()">
          <button class="spin-btn" onclick="spinSTP('stp-width',10)">+</button>
        </div>
      </div>
      <div class="field" id="stp-height-field">
        <label>Height (px)</label>
        <div class="spinner-wrap">
          <button class="spin-btn" onclick="spinSTP('stp-height',-10)">−</button>
          <input type="number" id="stp-height" value="1080" min="240" max="32768" oninput="calcStandalonePattern()">
          <button class="spin-btn" onclick="spinSTP('stp-height',10)">+</button>
        </div>
      </div>
      <div class="field" id="stp-zones-field">
        <label>Zones <span class="tooltip-wrap">?<span class="tooltip-box">Projector or LED processor zones — each gets a high-contrast border.</span></span></label>
        <div class="spinner-wrap">
          <button class="spin-btn" onclick="spinSTP('stp-projectors',-1)">−</button>
          <input type="number" id="stp-projectors" value="1" min="1" max="8" oninput="stpUpdateBlendCard(); calcStandalonePattern()">
          <button class="spin-btn" onclick="spinSTP('stp-projectors',1)">+</button>
        </div>
      </div>
    </div>

    <!-- Row 2: background -->
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-wrap:wrap">
      <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text);min-width:90px;letter-spacing:1px">Background</span>
      <div class="stp-swatch stp-sw-active" onclick="stpSetBg('solid:#000000',this)" style="width:28px;height:28px;border-radius:4px;background:#000000;border:2px solid var(--gold);cursor:pointer" title="Black"></div>
      <div class="stp-swatch" onclick="stpSetBg('solid:#1e1e1e',this)" style="width:28px;height:28px;border-radius:4px;background:#1e1e1e;border:2px solid transparent;cursor:pointer" title="Dark grey"></div>
      <div class="stp-swatch" onclick="stpSetBg('solid:#404040',this)" style="width:28px;height:28px;border-radius:4px;background:#404040;border:2px solid transparent;cursor:pointer" title="Mid grey"></div>
      <div class="stp-swatch" onclick="stpSetBg('solid:#808080',this)" style="width:28px;height:28px;border-radius:4px;background:#808080;border:2px solid transparent;cursor:pointer" title="50% grey"></div>
      <div class="stp-swatch" onclick="stpSetBg('solid:#ffffff',this)" style="width:28px;height:28px;border-radius:4px;background:#ffffff;border:2px solid var(--border);cursor:pointer" title="White"></div>
      <div class="stp-swatch" onclick="stpSetBg('grad:black-blue',this)" style="width:28px;height:28px;border-radius:4px;background:linear-gradient(135deg,#0000ff,#000);border:2px solid transparent;cursor:pointer" title="Blue diagonal"></div>
      <div class="stp-swatch" onclick="stpSetBg('grad:black-red',this)" style="width:28px;height:28px;border-radius:4px;background:linear-gradient(135deg,#ff0000,#000);border:2px solid transparent;cursor:pointer" title="Red diagonal"></div>
      <div class="stp-swatch" onclick="stpSetBg('grad:black-green',this)" style="width:28px;height:28px;border-radius:4px;background:linear-gradient(135deg,#00ff00,#000);border:2px solid transparent;cursor:pointer" title="Green diagonal"></div>
      <div class="stp-swatch" onclick="stpSetBg('grad:grey-black',this)" style="width:28px;height:28px;border-radius:4px;background:linear-gradient(135deg,#fff,#000);border:2px solid transparent;cursor:pointer" title="White diagonal"></div>
      <div class="stp-picker-wrap">
        <div id="stp-bg-custom-swatch" onclick="stpOpenPicker('stp-bg-custom','stpApplyCustomSolid')" style="width:28px;height:28px;border-radius:4px;background:#000000;border:.5px solid var(--border);cursor:pointer" title="Custom colour"></div>
        <input type="hidden" id="stp-bg-custom" value="#000000">
      </div>
      <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">Custom</span>
      <div id="stp-grad-controls" style="display:none;align-items:center;gap:6px;margin-left:8px">
        <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">Colour</span>
        <div class="stp-picker-wrap">
          <div id="stp-grad-a-swatch" onclick="stpOpenPicker('stp-grad-a','stpUpdateGrad')" style="width:28px;height:28px;border-radius:4px;background:#ff0000;border:.5px solid var(--border);cursor:pointer"></div>
          <input type="hidden" id="stp-grad-a" value="#ff0000">
        </div>
        <input type="hidden" id="stp-grad-b" value="">
        <select id="stp-grad-dir" onchange="stpUpdateGrad()" style="background:var(--dark-2);border:.5px solid var(--border);border-radius:4px;color:var(--text-1);font-family:var(--tp-mono);font-size:11px;padding:4px 6px">
          <option value="135">Diagonal ↘</option>
          <option value="180">Top → Bottom</option>
          <option value="90">Left → Right</option>
        </select>
        <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2);margin-left:4px">Spread</span>
        <input type="range" id="stp-grad-spread" min="10" max="100" value="100" step="5"
          style="width:80px;accent-color:var(--gold)"
          oninput="document.getElementById('stp-grad-spread-val').textContent=this.value+'%'; stpUpdateGrad()">
        <span id="stp-grad-spread-val" style="font-family:var(--tp-mono);font-size:11px;color:var(--gold);min-width:36px">100%</span>
      </div>
      <input type="hidden" id="stp-bg" value="solid:#000000">
      <input type="hidden" id="stp-grad-active" value="0">
    </div>

    <!-- Row 3: overlays -->
    <div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;flex-wrap:wrap">
      <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text);min-width:90px;letter-spacing:1px">Overlays</span>
      <label class="stp-pill stp-pill-on" onclick="stpTogglePill(this,'stp-ov-grid')"><input type="checkbox" id="stp-ov-grid" checked style="display:none">Grid</label>
      <label class="stp-pill" onclick="stpTogglePill(this,'stp-ov-markers')"><input type="checkbox" id="stp-ov-markers" style="display:none">Markers</label>
      <label class="stp-pill stp-pill-on" onclick="stpTogglePill(this,'stp-ov-circles')"><input type="checkbox" id="stp-ov-circles" checked style="display:none">Circles</label>
      <label class="stp-pill" onclick="stpTogglePill(this,'stp-ov-hcircles')"><input type="checkbox" id="stp-ov-hcircles" style="display:none">H Circles</label>
      <label class="stp-pill" onclick="stpTogglePill(this,'stp-ov-diag')"><input type="checkbox" id="stp-ov-diag" style="display:none">Diagonals</label>
      <label class="stp-pill stp-pill-on" onclick="stpTogglePill(this,'stp-ov-crosshair')"><input type="checkbox" id="stp-ov-crosshair" checked style="display:none">Crosshair</label>
      <label class="stp-pill" onclick="stpToggleSweep(this)" id="stp-sweep-pill"><input type="checkbox" id="stp-ov-sweep" style="display:none">⟷ Sweep</label>
    </div>

    <!-- Row 4: adjustment cards -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">

      <div id="stp-adj-grid" class="stp-adj-card">
        <span class="stp-adj-label">Grid</span>
        <div style="display:flex;align-items:center;gap:5px">
          <button class="spin-btn" onclick="spinSTP('stp-vcols',-1)">−</button>
          <input type="number" id="stp-vcols" value="16" min="2" max="64" oninput="calcStandalonePattern()"
            style="width:42px;text-align:center;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 4px;outline:none">
          <button class="spin-btn" onclick="spinSTP('stp-vcols',1)">+</button>
          <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">V ×</span>
          <button class="spin-btn" onclick="spinSTP('stp-hrows',-1)">−</button>
          <input type="number" id="stp-hrows" value="9" min="2" max="64" oninput="calcStandalonePattern()"
            style="width:42px;text-align:center;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 4px;outline:none">
          <button class="spin-btn" onclick="spinSTP('stp-hrows',1)">+</button>
          <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">H</span>
        </div>
      </div>

      <div id="stp-adj-circles" class="stp-adj-card">
        <span class="stp-adj-label">Circles</span>
        <div style="display:flex;align-items:center;gap:5px">
          <button class="spin-btn" onclick="spinSTP('stp-circles',-1)">−</button>
          <input type="number" id="stp-circles" value="3" min="1" max="12" oninput="calcStandalonePattern()"
            style="width:42px;text-align:center;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 4px;outline:none">
          <button class="spin-btn" onclick="spinSTP('stp-circles',1)">+</button>
        </div>
      </div>

      <div id="stp-adj-hcircles" class="stp-adj-card" style="display:none">
        <span class="stp-adj-label">H Circles</span>
        <div style="display:flex;align-items:center;gap:5px">
          <button class="spin-btn" onclick="spinSTP('stp-hcircles',-1)">−</button>
          <input type="number" id="stp-hcircles" value="3" min="1" max="12" oninput="calcStandalonePattern()"
            style="width:42px;text-align:center;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 4px;outline:none">
          <button class="spin-btn" onclick="spinSTP('stp-hcircles',1)">+</button>
        </div>
      </div>

      <div id="stp-adj-diag" class="stp-adj-card" style="display:none">
        <span class="stp-adj-label">Diagonals</span>
        <div style="display:flex;align-items:center;gap:5px">
          <button class="spin-btn" onclick="spinSTP('stp-diag',-1)">−</button>
          <input type="number" id="stp-diag" value="1" min="1" max="8" oninput="calcStandalonePattern()"
            style="width:42px;text-align:center;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 4px;outline:none">
          <button class="spin-btn" onclick="spinSTP('stp-diag',1)">+</button>
        </div>
      </div>

      <div class="stp-adj-card">
        <span class="stp-adj-label">Line weight</span>
        <div style="display:flex;align-items:center;gap:5px">
          <button class="spin-btn" onclick="stpSpinFloat('stp-lineweight',-1)">−</button>
          <input type="number" id="stp-lineweight" value="2" min="1" max="20" step="1" oninput="calcStandalonePattern()"
            style="width:42px;text-align:center;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 4px;outline:none">
          <button class="spin-btn" onclick="stpSpinFloat('stp-lineweight',1)">+</button>
          <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">px</span>
        </div>
      </div>

      <div class="stp-adj-card" id="stp-adj-sweep" style="display:none">
        <span class="stp-adj-label">Sweep</span>
        <div style="display:flex;flex-direction:column;gap:6px">
          <div style="display:flex;align-items:center;gap:5px">
            <span style="font-family:var(--tp-mono);font-size:9px;color:var(--tp-text2);width:32px">Weight</span>
            <button class="spin-btn" onclick="stpLEDSpin('stp-sweep-weight',-1)">−</button>
            <input type="number" id="stp-sweep-weight" value="3" min="1" max="20" step="1"
              style="width:42px;text-align:center;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 4px;outline:none"
              oninput="stpSweepUpdateConfig()">
            <button class="spin-btn" onclick="stpLEDSpin('stp-sweep-weight',1)">+</button>
            <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">px</span>
          </div>
          <div style="display:flex;align-items:center;gap:5px">
            <span style="font-family:var(--tp-mono);font-size:9px;color:var(--tp-text2);width:32px">Speed</span>
            <input type="range" id="stp-sweep-speed" min="1" max="10" value="3" step="1"
              style="width:90px;accent-color:var(--gold)"
              oninput="document.getElementById('stp-sweep-speed-val').textContent=this.value; stpSweepUpdateConfig()">
            <span id="stp-sweep-speed-val" style="font-family:var(--tp-mono);font-size:11px;color:var(--gold);min-width:14px">3</span>
          </div>
        </div>
      </div>

      <div class="stp-adj-card" id="stp-adj-blend" style="display:none">
        <span class="stp-adj-label">Blend zone <span class="tooltip-wrap">?<span class="tooltip-box">Enter the blend zone width in pixels — the value you programme into your media server.</span></span></span>
        <div style="display:flex;align-items:center;gap:5px">
          <button class="spin-btn" onclick="spinSTP('stp-blend-px',-10)">−</button>
          <input type="number" id="stp-blend-px" value="0" min="0" max="4000" step="10" oninput="calcStandalonePattern()"
            style="width:56px;text-align:center;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 4px;outline:none"
            placeholder="0">
          <button class="spin-btn" onclick="spinSTP('stp-blend-px',10)">+</button>
          <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">px</span>
        </div>
      </div>

      <div id="stp-zone-labels" style="display:none;flex-wrap:wrap;gap:8px;margin-top:4px;width:100%">
        <div id="stp-zone-label-inputs" style="display:flex;gap:8px;flex-wrap:wrap"></div>
      </div>

    </div><!-- /adj cards -->

    <!-- Export panel -->
    <div style="background:var(--tp-panel2);border:.5px solid var(--tp-border);border-radius:6px;padding:12px 14px;margin-bottom:14px">
      <div style="display:flex;gap:0;align-items:center;flex-wrap:wrap">

        <!-- Quick exports -->
        <div style="padding-right:20px;border-right:.5px solid var(--border);margin-right:20px">
          <div class="stp-adj-label" style="margin-bottom:8px">Quick export <span class="tooltip-wrap">?<span class="tooltip-box">SVG is vector and resolution-independent. PNG 8-bit is an sRGB canvas render suitable for screen preview.</span></span></div>
          <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
            <button class="pdf-btn" onclick="downloadStandalonePattern('png8')" style="border-color:var(--tp-accent3);color:var(--tp-accent3)">
              <svg viewBox="0 0 16 16" fill="none"><path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><path d="M2 12h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>PNG 8-bit
            </button>
            <button class="pdf-btn" onclick="downloadStandalonePattern('svg')">
              <svg viewBox="0 0 16 16" fill="none"><path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><path d="M2 12h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>SVG
            </button>
            <label id="pill-quick-wm" class="stp-pill" onclick="stpToggleWmPill(this,'stp-quick-watermark'); calcStandalonePattern()">
              <input type="checkbox" id="stp-quick-watermark" style="display:none">Burn metadata
            </label>
          </div>
        </div>

        <!-- Professional export -->
        <div style="flex:1;min-width:300px">
          <div class="stp-adj-label" style="margin-bottom:8px">Professional PNG <span class="tooltip-wrap">?<span class="tooltip-box">Pixels computed from pattern math directly — correct colour space primaries, proper transfer function, and true bit depth. Use for calibration and colour science work.</span></span></div>
          <div style="display:flex;gap:8px;align-items:center;margin-bottom:4px">
            <div style="font-family:var(--tp-mono);font-size:9px;color:var(--tp-text3);width:160px">Colour space</div>
            <div style="font-family:var(--tp-mono);font-size:9px;color:var(--tp-text3)">γ override</div>
            <div style="font-family:var(--tp-mono);font-size:9px;color:var(--tp-text3);width:64px">Bit depth</div>
            <div style="font-family:var(--tp-mono);font-size:9px;color:var(--tp-text3);width:56px">Range</div>
          </div>
          <div style="display:flex;gap:8px;align-items:center;flex-wrap:nowrap">
            <select id="stp-export-cs" style="width:160px;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--text-1);font-family:var(--tp-mono);font-size:11px;padding:5px 8px">
              <option value="srgb">sRGB (γ 2.2)</option>
              <option value="rec709">Rec.709</option>
              <option value="p3">P3-D65 (γ 2.6)</option>
              <option value="rec2020">Rec.2020</option>
              <option value="linear">Linear light (γ 1.0)</option>
            </select>
            <div style="display:flex;align-items:center;gap:5px">
              <input type="checkbox" id="stp-gamma-override-on" onchange="stpToggleGammaOverride()"
                style="accent-color:var(--gold);cursor:pointer;flex-shrink:0">
              <button class="spin-btn" id="stp-gamma-minus" onclick="stpSpinFloat('stp-gamma-override-val',-0.1)" disabled style="opacity:.3">−</button>
              <input type="number" id="stp-gamma-override-val" value="2.2" min="0.5" max="4.0" step="0.1"
                disabled
                style="width:40px;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--text-3);font-family:var(--tp-mono);font-size:11px;padding:5px 4px;outline:none;text-align:center;opacity:.4;-moz-appearance:textfield"
                oninput="stpUpdateGammaOverride()">
              <button class="spin-btn" id="stp-gamma-plus" onclick="stpSpinFloat('stp-gamma-override-val',0.1)" disabled style="opacity:.3">+</button>
            </div>
            <select id="stp-export-bd" style="width:64px;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--text-1);font-family:var(--tp-mono);font-size:11px;padding:5px 8px">
              <option value="16">16-bit</option>
              <option value="8">8-bit</option>
            </select>
            <select id="stp-export-range" style="width:56px;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--text-1);font-family:var(--tp-mono);font-size:11px;padding:5px 8px">
              <option value="full">Full</option>
              <option value="legal">Legal</option>
            </select>
            <label id="pill-pro-wm" class="stp-pill" onclick="stpToggleWmPill(this,'stp-export-watermark'); calcStandalonePattern()">
              <input type="checkbox" id="stp-export-watermark" style="display:none">Burn metadata
            </label>
            <button class="pdf-btn" onclick="downloadProPattern()">
              <svg viewBox="0 0 16 16" fill="none"><path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><path d="M2 12h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Export
            </button>
            <span style="display:none" id="stp-export-note"></span>
          </div>
        </div>

      </div>
    </div><!-- /export panel -->

    <!-- LED tile config — shown when LED sub-tab active -->
    <div id="stp-led-config" style="display:none;margin-bottom:14px">
      <div style="background:var(--tp-panel2);border:.5px solid var(--tp-border);border-radius:6px;padding:12px 14px">
        <div class="grid-4" style="margin-bottom:12px">
          <div class="field">
            <label>Tile width (px) <span class="tooltip-wrap">?<span class="tooltip-box">Native pixel resolution of one LED tile/cabinet — width.</span></span></label>
            <div class="spinner-wrap">
              <button class="spin-btn" onclick="stpLEDSpin('stp-tile-w',-1)">−</button>
              <input type="number" id="stp-tile-w" value="192" min="8" max="2048" oninput="stpLEDSyncFromTiles(); calcStandalonePattern()">
              <button class="spin-btn" onclick="stpLEDSpin('stp-tile-w',1)">+</button>
            </div>
          </div>
          <div class="field">
            <label>Tile height (px)</label>
            <div class="spinner-wrap">
              <button class="spin-btn" onclick="stpLEDSpin('stp-tile-h',-1)">−</button>
              <input type="number" id="stp-tile-h" value="192" min="8" max="2048" oninput="stpLEDSyncFromTiles(); calcStandalonePattern()">
              <button class="spin-btn" onclick="stpLEDSpin('stp-tile-h',1)">+</button>
            </div>
          </div>
          <div class="field">
            <label>Tiles wide</label>
            <div class="spinner-wrap">
              <button class="spin-btn" onclick="stpLEDSpin('stp-tiles-w',-1)">−</button>
              <input type="number" id="stp-tiles-w" value="8" min="1" max="100" oninput="stpLEDSyncFromTiles(); calcStandalonePattern()">
              <button class="spin-btn" onclick="stpLEDSpin('stp-tiles-w',1)">+</button>
            </div>
          </div>
          <div class="field">
            <label>Tiles high</label>
            <div class="spinner-wrap">
              <button class="spin-btn" onclick="stpLEDSpin('stp-tiles-h',-1)">−</button>
              <input type="number" id="stp-tiles-h" value="5" min="1" max="100" oninput="stpLEDSyncFromTiles(); calcStandalonePattern()">
              <button class="spin-btn" onclick="stpLEDSpin('stp-tiles-h',1)">+</button>
            </div>
          </div>
        </div>
        <!-- Canvas override -->
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">
            <input type="checkbox" id="stp-led-canvas-override" onchange="stpLEDToggleOverride()" style="accent-color:var(--gold)">
            Output canvas differs from tile grid
          </label>
        </div>
        <div id="stp-led-canvas-override-fields" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;margin-bottom:12px;opacity:.35;pointer-events:none">
          <div class="field">
            <label>Output canvas width (px)</label>
            <div class="spinner-wrap">
              <button class="spin-btn" onclick="stpLEDSpinCanvas('w',-10)">−</button>
              <input type="number" id="stp-led-canvas-w" value="1536" min="320" max="32768" oninput="stpLEDUpdateCanvas()">
              <button class="spin-btn" onclick="stpLEDSpinCanvas('w',10)">+</button>
            </div>
          </div>
          <div class="field">
            <label>Output canvas height (px)</label>
            <div class="spinner-wrap">
              <button class="spin-btn" onclick="stpLEDSpinCanvas('h',-10)">−</button>
              <input type="number" id="stp-led-canvas-h" value="960" min="240" max="32768" oninput="stpLEDUpdateCanvas()">
              <button class="spin-btn" onclick="stpLEDSpinCanvas('h',10)">+</button>
            </div>
          </div>
          <div class="field">
            <label>Justification <span class="tooltip-wrap">?<span class="tooltip-box">Where the tile grid sits within the output canvas when dimensions don't match exactly.</span></span></label>
            <select id="stp-tile-justify" onchange="calcStandalonePattern()" style="background:var(--dark-2);border:.5px solid var(--border);border-radius:4px;color:var(--text-1);font-family:var(--tp-mono);font-size:12px;padding:7px 10px">
              <option value="tl">Top-left</option>
              <option value="tr">Top-right</option>
              <option value="bl">Bottom-left</option>
              <option value="br">Bottom-right</option>
              <option value="c">Centred</option>
            </select>
          </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
          <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)" id="stp-led-info">—</span>
          <label class="stp-pill stp-pill-on" onclick="stpTogglePill(this,'stp-ov-led-tiles')"><input type="checkbox" id="stp-ov-led-tiles" checked style="display:none">Tile borders</label>
          <label class="stp-pill stp-pill-on" onclick="stpTogglePill(this,'stp-ov-led-ids')"><input type="checkbox" id="stp-ov-led-ids" checked style="display:none">Tile IDs</label>
          <div style="display:flex;align-items:center;gap:6px;margin-left:8px">
            <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">Border weight</span>
            <button class="spin-btn" onclick="stpLEDSpin('stp-tile-border-weight',-1)">−</button>
            <input type="number" id="stp-tile-border-weight" value="2" min="1" max="20" step="1"
              style="width:42px;text-align:center;background:var(--black);border:.5px solid var(--border);border-radius:4px;color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 4px;outline:none"
              oninput="calcStandalonePattern()">
            <button class="spin-btn" onclick="stpLEDSpin('stp-tile-border-weight',1)">+</button>
            <span style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2)">px</span>
          </div>
        </div>
      </div>
    </div><!-- /LED tile config -->

    <!-- Popout config panel -->
    <div id="stp-popout-panel" style="display:none;background:var(--tp-panel2);border:.5px solid var(--gold);border-radius:6px;padding:14px;margin-bottom:12px">
      <div style="font-family:var(--tp-mono);font-size:9px;color:var(--gold);letter-spacing:1px;margin-bottom:12px">⤢ SEND TO DISPLAYS</div>
      <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:12px">
        <div class="field">
          <label>Number of outputs</label>
          <div class="spinner-wrap">
            <button class="spin-btn" onclick="stpSpinPopout('stp-popout-outputs',-1)">−</button>
            <input type="number" id="stp-popout-outputs" value="1" min="1" max="16" oninput="stpUpdatePopoutPanel()">
            <button class="spin-btn" onclick="stpSpinPopout('stp-popout-outputs',1)">+</button>
          </div>
        </div>
        <div class="field" id="stp-popout-res-field">
          <label>Output resolution</label>
          <div style="display:flex;gap:6px;align-items:center">
            <div class="spinner-wrap">
              <button class="spin-btn" onclick="stpSpinPopout('stp-popout-res-w',-10)">−</button>
              <input type="number" id="stp-popout-res-w" value="1920" min="320" max="7680" oninput="stpUpdatePopoutPanel()">
              <button class="spin-btn" onclick="stpSpinPopout('stp-popout-res-w',10)">+</button>
            </div>
            <span style="font-family:var(--tp-mono);font-size:11px;color:var(--tp-text2)">×</span>
            <div class="spinner-wrap">
              <button class="spin-btn" onclick="stpSpinPopout('stp-popout-res-h',-10)">−</button>
              <input type="number" id="stp-popout-res-h" value="1080" min="240" max="4320" oninput="stpUpdatePopoutPanel()">
              <button class="spin-btn" onclick="stpSpinPopout('stp-popout-res-h',10)">+</button>
            </div>
          </div>
        </div>
        <div class="field">
          <label>Slice direction</label>
          <select id="stp-popout-slice" onchange="stpUpdatePopoutPanel()"
            style="background:var(--dark-2);border:.5px solid var(--border);border-radius:4px;color:var(--text-1);font-family:var(--tp-mono);font-size:12px;padding:7px 10px">
            <option value="h">Horizontal (left → right)</option>
            <option value="v">Vertical (top → bottom)</option>
          </select>
        </div>
      </div>
      <div id="stp-popout-preview-info" style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2);margin-bottom:12px">—</div>
      <div id="stp-output-buttons" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center"></div>
      <div style="margin-top:8px">
        <button onclick="document.getElementById('stp-popout-panel').style.display='none'"
          style="font-family:var(--tp-mono);font-size:11px;color:var(--tp-text3);background:transparent;border:.5px solid var(--border);border-radius:4px;padding:8px 14px;cursor:pointer">
          Cancel
        </button>
      </div>
    </div><!-- /popout panel -->

    <!-- Pattern message bar -->
    <div id="stp-pattern-msg" style="font-family:var(--tp-mono);font-size:10px;margin-bottom:6px;opacity:0;transition:opacity .3s;min-height:16px"></div>

    <!-- Preview header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
      <div style="display:flex;gap:6px;align-items:center">
        <button id="stp-popout-btn" onclick="stpShowPopoutPanel()"
          style="font-family:var(--tp-mono);font-size:10px;color:var(--gold);background:transparent;border:.5px solid var(--gold);border-radius:4px;padding:4px 12px;cursor:pointer;letter-spacing:.5px"
          title="Send pattern to display outputs">
          ⤢ POPOUT
        </button>
        <button id="stp-fs-all-btn" onclick="stpFullscreenAll()" style="display:none;font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2);background:transparent;border:.5px solid var(--border);border-radius:4px;padding:4px 10px;cursor:pointer">⛶ FS ALL</button>
        <button id="stp-close-all-btn" onclick="stpCloseAll()" style="display:none;font-family:var(--tp-mono);font-size:10px;color:var(--tp-text2);background:transparent;border:.5px solid var(--border);border-radius:4px;padding:4px 10px;cursor:pointer">✕ CLOSE ALL</button>
        <span id="stp-popout-count" style="display:none;font-family:var(--tp-mono);font-size:9px;color:var(--tp-text3)"></span>
      </div>
      <span id="stp-info" style="font-family:var(--tp-mono);font-size:10px;color:var(--tp-text3)"></span>
    </div>

    <!-- Preview -->
    <div style="position:relative;width:100%;border-radius:4px;overflow:hidden;border:.5px solid var(--border);background:#000">
      <div id="stp-preview" style="width:100%;background:#000"></div>
      <canvas id="stp-sweep-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;display:none"></canvas>
    </div>

  </div><!-- /section -->

</div><!-- /tab-testpattern -->

</div><!-- /tp-wrap -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/pako/2.1.0/pako.min.js"></script>
<script>
// ── STANDALONE STUBS (functions only needed by other tabs in the full app) ──
var projMode    = 'single';
var spacingMode = 'auto';
var projUnit    = 'm';
var M_TO_FT     = 3.28084;
function toM(v)           { return v; }
function fromM(v)         { return v; }
function unitLabel()      { return 'm'; }
function getAspectRatio() { return 16/9; }
function switchTab()      {}
function launchLEDTestPattern()      {}
function launchTestPatternFromProj() {}
function stpUpdateLaunchBtnTitles()  {}
function calcLED()   {}
function calcSync()  {}
function calcPower() {}
function calcProj()  {}
function calcMedia() {}
function calc()      {}
function calcDistribution()  {}
function renderPowerItems()  {}
function ledSyncRaster()     {}
function addPowerItem()      {}
function calcIdealSetup()    {}
function syncClipSizeToDistribution() {}

// Sweep state globals
var _sweepActive      = false;
var _sweepRAF         = null;
var _sweepPosH        = 0;
var _sweepPosV        = 0;
var _sweepLastTs      = null;
var _sweepConfig      = { weight: 3, speed: 3 };
var _stpOutputs       = [];
var _stpChannel       = null;
var _stpOutputConfigs = [];
var _stpPopoutWin     = null;
function updateARDisplay() {}
</script>
<script>
// ── ALL ORIGINAL TOOL JAVASCRIPT (unmodified logic, colour refs updated) ────

function spinSTP(id, delta) {
  const el = document.getElementById(id);
  el.value = Math.max(parseInt(el.min), Math.min(parseInt(el.max), parseInt(el.value) + delta));
  if (id === 'stp-projectors') stpUpdateBlendCard();
  calcStandalonePattern();
}

function stpUpdateBlendCard() {
  const nProj = parseInt(document.getElementById('stp-projectors')?.value) || 1;
  const blendCard = document.getElementById('stp-adj-blend');
  if (blendCard) blendCard.style.display = nProj > 1 ? 'flex' : 'none';
  const labelsWrap  = document.getElementById('stp-zone-labels');
  const labelsInner = document.getElementById('stp-zone-label-inputs');
  if (!labelsWrap || !labelsInner) return;
  if (nProj <= 1) { labelsWrap.style.display = 'none'; return; }
  labelsWrap.style.display = 'flex';
  const existing = labelsInner.querySelectorAll('input');
  if (existing.length === nProj) return;
  const oldVals = [...existing].map(i => i.value);
  labelsInner.innerHTML = '';
  for (let p = 0; p < nProj; p++) {
    const card = document.createElement('div');
    card.className = 'stp-adj-card'; card.style.minWidth = '80px';
    card.innerHTML = `
      <span class="stp-adj-label">Zone ${p+1} label</span>
      <input type="text" id="stp-zone-label-${p}" value="${oldVals[p] || 'P'+(p+1)}"
        maxlength="8" oninput="calcStandalonePattern()"
        style="width:80px;background:var(--black);border:.5px solid var(--border);border-radius:4px;
               color:var(--gold);font-family:var(--tp-mono);font-size:13px;padding:5px 6px;outline:none;text-align:center">`;
    labelsInner.appendChild(card);
  }
}

function stpSpinFloat(id, delta) {
  const el = document.getElementById(id);
  const val = Math.round((parseFloat(el.value) + delta) * 10) / 10;
  el.value = Math.max(parseFloat(el.min), Math.min(parseFloat(el.max), val));
  calcStandalonePattern();
}

function stpSetBg(value, swatchEl) {
  document.getElementById('stp-bg').value = value;
  const isGrad = value.startsWith('grad:');
  const gradCtrl   = document.getElementById('stp-grad-controls');
  const gradActive = document.getElementById('stp-grad-active');
  if (gradCtrl)   gradCtrl.style.display   = isGrad ? 'flex' : 'none';
  if (gradActive) gradActive.value = isGrad ? '1' : '0';
  if (value.startsWith('solid:')) {
    try { document.getElementById('stp-bg-custom').value = value.replace('solid:',''); } catch(e) {}
  }
  if (isGrad && !value.startsWith('grad:custom:')) {
    const presetCols = { 'black-blue':'#0000ff','black-red':'#ff0000','black-green':'#00ff00','grey-black':'#ffffff' };
    const key = value.replace('grad:','');
    const col = presetCols[key] || '#ff0000';
    try {
      document.getElementById('stp-grad-a').value = col;
      document.getElementById('stp-grad-a-swatch').style.background = col;
    } catch(e) {}
    const dir    = document.getElementById('stp-grad-dir')?.value || '135';
    const spread = Math.min(95, Math.max(10, parseInt(document.getElementById('stp-grad-spread')?.value) || 80));
    document.getElementById('stp-bg').value = `grad:custom:${col}:${dir}:${spread}`;
  }
  document.querySelectorAll('.stp-swatch').forEach(s => s.style.borderColor = 'transparent');
  if (swatchEl) swatchEl.style.borderColor = '#00c8d4';
  calcStandalonePattern();
}

function stpUpdateGrad() {
  const col    = document.getElementById('stp-grad-a')?.value || '#ff0000';
  const dir    = document.getElementById('stp-grad-dir')?.value || '135';
  const spread = parseInt(document.getElementById('stp-grad-spread')?.value) || 80;
  const safe   = Math.min(95, Math.max(10, spread));
  document.getElementById('stp-bg').value = `grad:custom:${col}:${dir}:${safe}`;
  calcStandalonePattern();
}

function stpParseBg(value, W, H) {
  if (!value || value.startsWith('solid:')) {
    const col = (value || 'solid:#000000').replace('solid:','');
    return { bgSvg: `<rect width="${W}" height="${H}" fill="${col}"/>`, hex: col };
  }
  if (value.startsWith('grad:custom:')) {
    const p   = value.split(':');
    const hex = p[2] || '#ff0000';
    const dir = p[3] || '135';
    const spr = Math.min(95, Math.max(10, parseInt(p[4]) || 80));
    const r=isNaN(parseInt(hex.slice(1,3),16))?0:parseInt(hex.slice(1,3),16);
    const g=isNaN(parseInt(hex.slice(3,5),16))?0:parseInt(hex.slice(3,5),16);
    const b=isNaN(parseInt(hex.slice(5,7),16))?0:parseInt(hex.slice(5,7),16);
    let x1='0',y1='0',x2,y2;
    if      (dir==='90')  { x2='1'; y2='0'; }
    else if (dir==='180') { x2='0'; y2='1'; }
    else                  { x2='1'; y2='1'; }
    const uid = 'g' + Math.random().toString(36).slice(2,8);
    const bgSvg =
      `<defs><linearGradient id="${uid}" gradientUnits="objectBoundingBox" x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}">` +
      `<stop offset="0%" stop-color="rgb(0,0,0)"/>` +
      `<stop offset="${spr}%" stop-color="rgb(${r},${g},${b})"/>` +
      `<stop offset="100%" stop-color="rgb(${r},${g},${b})"/>` +
      `</linearGradient></defs>` +
      `<rect width="${W}" height="${H}" fill="url(#${uid})"/>`;
    return { bgSvg, hex: '#000000' };
  }
  const presets = { 'black-blue':{hex:'#0000ff',dir:'135'},'black-red':{hex:'#ff0000',dir:'135'},'black-green':{hex:'#00ff00',dir:'135'},'grey-black':{hex:'#ffffff',dir:'135'} };
  const key = value.replace('grad:','');
  const gp  = presets[key] || presets['black-blue'];
  const r2=parseInt(gp.hex.slice(1,3),16), g2=parseInt(gp.hex.slice(3,5),16), b2=parseInt(gp.hex.slice(5,7),16);
  let x1='0',y1='0',x2,y2;
  if      (gp.dir==='90')  { x2='1'; y2='0'; }
  else if (gp.dir==='180') { x2='0'; y2='1'; }
  else                     { x2='1'; y2='1'; }
  const uid2 = 'g' + Math.random().toString(36).slice(2,8);
  const bgSvg =
    `<defs><linearGradient id="${uid2}" gradientUnits="objectBoundingBox" x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}">` +
    `<stop offset="0%" stop-color="rgb(0,0,0)"/>` +
    `<stop offset="80%" stop-color="rgb(${r2},${g2},${b2})"/>` +
    `<stop offset="100%" stop-color="rgb(${r2},${g2},${b2})"/>` +
    `</linearGradient></defs>` +
    `<rect width="${W}" height="${H}" fill="url(#${uid2})"/>`;
  return { bgSvg, hex: '#000000' };
}

function stpTogglePill(pill, checkId) {
  const cb = document.getElementById(checkId);
  cb.checked = !cb.checked;
  pill.classList.toggle('stp-pill-on', cb.checked);
  const adjMap = { 'stp-ov-grid':'stp-adj-grid','stp-ov-circles':'stp-adj-circles','stp-ov-hcircles':'stp-adj-hcircles','stp-ov-diag':'stp-adj-diag' };
  const adjId = adjMap[checkId];
  if (adjId) { const adjEl = document.getElementById(adjId); if (adjEl) adjEl.style.display = cb.checked ? 'flex' : 'none'; }
  calcStandalonePattern();
}

function buildStandalonePatternSVG() {
  const W        = parseInt(document.getElementById('stp-width').value)  || 1920;
  const H        = parseInt(document.getElementById('stp-height').value) || 1080;
  const type     = document.getElementById('stp-type').value;
  const nProj    = parseInt(document.getElementById('stp-projectors').value) || 1;
  const blendPx  = parseInt(document.getElementById('stp-blend-px')?.value) || 0;
  const vcols    = parseInt(document.getElementById('stp-vcols').value) || 16;
  const hrows    = parseInt(document.getElementById('stp-hrows').value) || 9;
  const nCircles = parseInt(document.getElementById('stp-circles')?.value) || 3;
  const nHCircles= parseInt(document.getElementById('stp-hcircles')?.value) || 3;
  const nDiag    = parseInt(document.getElementById('stp-diag')?.value) || 1;
  const lw       = parseFloat(document.getElementById('stp-lineweight')?.value) || 2;
  const bgValue  = document.getElementById('stp-bg')?.value || 'solid:#000000';
  const { bgSvg, hex: bgHex } = stpParseBg(bgValue, W, H);
  const ovGrid    = document.getElementById('stp-ov-grid')?.checked !== false;
  const ovMarkers = document.getElementById('stp-ov-markers')?.checked || false;
  const ovCirc    = document.getElementById('stp-ov-circles')?.checked !== false;
  const ovHCirc   = document.getElementById('stp-ov-hcircles')?.checked || false;
  const ovDiag    = document.getElementById('stp-ov-diag')?.checked || false;
  const ovCross   = document.getElementById('stp-ov-crosshair')?.checked !== false;

  const bgBrightness = parseInt((bgHex||'#000000').replace('#','').slice(0,2), 16) || 0;
  const lineCol   = bgBrightness > 128 ? '#000000' : '#ffffff';
  const accentCol = '#00c8d4';
  const PROJ_COLS = ['#ffffff','#ffff00','#ff3300','#00ff88','#ff00ff','#00ccff','#ff8800','#88ff00'];
  let body = bgSvg;

  const solidMap = { fullwhite:'#ffffff', fullblack:'#000000', red:'#ff0000', green:'#00ff00', blue:'#0000ff' };
  if (solidMap[type]) {
    body = `<rect width="${W}" height="${H}" fill="${solidMap[type]}"/>`;
  }
  else if (type === 'checker') {
    body = `<rect width="${W}" height="${H}" fill="${bgHex}"/>`;
    const cs = Math.round(W / vcols), fill2 = bgBrightness > 128 ? '#000' : '#fff';
    for (let r = 0; r * cs < H + cs; r++)
      for (let c = 0; c * cs < W + cs; c++)
        if ((r+c)%2===0) body += `<rect x="${c*cs}" y="${r*cs}" width="${cs}" height="${cs}" fill="${fill2}"/>`;
  }
  else if (type === 'greyramp') {
    body = `<rect width="${W}" height="${H}" fill="${bgHex}"/>`;
    const steps = 11, sw = Math.round(W/steps);
    for (let i=0;i<steps;i++) {
      const pct = Math.round((i/(steps-1))*100), v = Math.round((i/(steps-1))*255), h2 = v.toString(16).padStart(2,'0');
      body += `<rect x="${i*sw}" y="0" width="${sw}" height="${H}" fill="#${h2}${h2}${h2}"/>`;
      const fs = Math.max(12,Math.round(H/40));
      body += `<text x="${i*sw+sw/2}" y="${H/2}" text-anchor="middle" font-family="monospace" font-size="${fs}" fill="${v>128?'#000':'#fff'}" opacity="0.8">${pct}%</text>`;
      body += `<text x="${i*sw+sw/2}" y="${H/2+fs*1.4}" text-anchor="middle" font-family="monospace" font-size="${Math.round(fs*0.8)}" fill="${v>128?'#000':'#fff'}" opacity="0.6">${pct} IRE</text>`;
    }
  }
  else if (type === 'hbars') {
    body = `<rect width="${W}" height="${H}" fill="${bgHex}"/>`;
    const bh=Math.round(H/hrows);
    for (let i=0;i<hrows;i++) {
      const pct=Math.round((i/(hrows-1))*100), v=Math.round((i/(hrows-1))*255), hx=v.toString(16).padStart(2,'0');
      body += `<rect x="0" y="${i*bh}" width="${W}" height="${bh}" fill="#${hx}${hx}${hx}"/>`;
      const fs=Math.max(11,Math.round(bh/3));
      if (bh>20) {
        body += `<text x="16" y="${i*bh+bh/2+fs/3}" font-family="monospace" font-size="${fs}" fill="${v>128?'#000':'#fff'}" opacity="0.75">${pct}% / ${pct} IRE</text>`;
        body += `<text x="${W-16}" y="${i*bh+bh/2+fs/3}" text-anchor="end" font-family="monospace" font-size="${fs}" fill="${v>128?'#000':'#fff'}" opacity="0.5">#${hx}${hx}${hx}</text>`;
      }
    }
  }
  else if (type === 'smpte') {
    body = `<rect width="${W}" height="${H}" fill="#000"/>`;
    const bars=[{c:'#bfbfbf',l:'75%W'},{c:'#bfbf00',l:'YEL'},{c:'#00bfbf',l:'CYN'},{c:'#00bf00',l:'GRN'},{c:'#bf00bf',l:'MAG'},{c:'#bf0000',l:'RED'},{c:'#0000bf',l:'BLU'}];
    const bw=Math.round(W/bars.length), h1=Math.round(H*0.67), h2=Math.round(H*0.75);
    bars.forEach((b,i)=>{
      body+=`<rect x="${i*bw}" y="0" width="${bw}" height="${h1}" fill="${b.c}"/>`;
      const fs=Math.max(12,Math.round(bw/7)), dark=['#bfbfbf','#bfbf00','#00bfbf','#00bf00'].includes(b.c);
      body+=`<text x="${i*bw+bw/2}" y="${h1-fs}" text-anchor="middle" font-family="monospace" font-size="${fs}" fill="${dark?'#000':'#fff'}" opacity="0.75">${b.l}</text>`;
    });
    const revBars=[{c:'#0000bf'},{c:'#bfbfbf'},{c:'#bf00bf'},{c:'#000000'},{c:'#00bf00'},{c:'#bfbfbf'},{c:'#bfbfbf'}];
    revBars.forEach((b,i)=>{body+=`<rect x="${i*bw}" y="${h1}" width="${bw}" height="${h2-h1}" fill="${b.c}"/>`;});
    const plugeW=Math.round(W/7);
    const pluge=[{c:'#070707'},{c:'#000000'},{c:'#0b0b0b'},{c:'#000000'},{c:'#ffffff'},{c:'#000000'},{c:'#000000'}];
    pluge.forEach((b,i)=>{body+=`<rect x="${i*plugeW}" y="${h2}" width="${plugeW}" height="${H-h2}" fill="${b.c}"/>`;});
  }
  else if (type === 'colchips') {
    body = `<rect width="${W}" height="${H}" fill="#1a1a1a"/>`;
    const chips=['#735244','#c29682','#627a9d','#576c43','#8580b1','#67bdaa','#d67e2c','#505ba6','#c15a63','#5e3c6c','#9dbc40','#e0a32e','#383d96','#469449','#af363c','#e7c71f','#bb5695','#0885a1','#f3f3f2','#c8c8c8','#a0a0a0','#7a7a79','#555555','#343434'];
    const cols=6, rows=4, cw=Math.round(W/cols), ch=Math.round(H/rows);
    chips.forEach((c,i)=>{
      const col=i%cols, row=Math.floor(i/cols);
      body+=`<rect x="${col*cw}" y="${row*ch}" width="${cw}" height="${ch}" fill="${c}"/>`;
      const fs=Math.max(10,Math.round(ch/6)), r2=parseInt(c.slice(1,3),16), g2=parseInt(c.slice(3,5),16), b2=parseInt(c.slice(5,7),16);
      const lum=0.299*r2+0.587*g2+0.114*b2;
      body+=`<text x="${col*cw+cw/2}" y="${row*ch+ch-fs*0.6}" text-anchor="middle" font-family="monospace" font-size="${fs}" fill="${lum>128?'#000000':'#ffffff'}" opacity="0.6">${c.toUpperCase()}</text>`;
    });
  }
  else if (type === 'focus') {
    body = `<rect width="${W}" height="${H}" fill="${bgHex}"/>`;
    const cx=W/2, cy=H/2, minDim=Math.min(W,H);
    const zs=Math.round(minDim*0.18), lineSpacing=Math.max(2,Math.round(zs/20));
    for(let i=0;i<=20;i++){
      const x=cx-zs+i*lineSpacing*2, y=cy-zs+i*lineSpacing*2;
      if(x>=cx-zs&&x<=cx+zs) body+=`<line x1="${x}" y1="${cy-zs}" x2="${x}" y2="${cy+zs}" stroke="${lineCol}" stroke-width="${lw}" opacity="0.9"/>`;
      if(y>=cy-zs&&y<=cy+zs) body+=`<line x1="${cx-zs}" y1="${y}" x2="${cx+zs}" y2="${y}" stroke="${lineCol}" stroke-width="${lw}" opacity="0.9"/>`;
    }
    body+=`<rect x="${cx-zs}" y="${cy-zs}" width="${zs*2}" height="${zs*2}" fill="none" stroke="${lineCol}" stroke-width="${lw*1.5}"/>`;
    [0.15,0.3,0.45].forEach(f=>{const rad=Math.round(minDim*f);body+=`<circle cx="${cx}" cy="${cy}" r="${rad}" fill="none" stroke="${lineCol}" stroke-width="${lw}" opacity="0.4"/>`;});
    [[0,0],[W,0],[0,H],[W,H]].forEach(([bx,by])=>{
      const dx=bx===0?1:-1, dy=by===0?1:-1;
      const bw2=Math.round(minDim*0.12), bh2=Math.round(minDim*0.08);
      const ox=bx===0?Math.round(W*0.04):bx-bw2-Math.round(W*0.04);
      const oy=by===0?Math.round(H*0.06):by-bh2-Math.round(H*0.06);
      for(let i=0;i<10;i++){const spacing=Math.max(1,Math.round(bh2*(1-i/12)));const yy=oy+i*spacing;if(yy<oy+bh2)body+=`<line x1="${ox}" y1="${yy}" x2="${ox+bw2}" y2="${yy}" stroke="${lineCol}" stroke-width="${lw}" opacity="0.85"/>`;}
      const ox2=bx===0?Math.round(W*0.04)+bw2+4:bx-Math.round(W*0.04)-bw2-4;
      for(let i=0;i<10;i++){const spacing=Math.max(1,Math.round(bw2*(1-i/12)));const xx=ox2+i*spacing*dx;if(xx>Math.min(ox2,ox2+bw2*dx)&&xx<Math.max(ox2,ox2+bw2*dx))body+=`<line x1="${xx}" y1="${oy}" x2="${xx}" y2="${oy+bh2}" stroke="${lineCol}" stroke-width="${lw}" opacity="0.85"/>`;}
    });
    body+=`<line x1="${cx-20}" y1="${cy}" x2="${cx+20}" y2="${cy}" stroke="#00c8d4" stroke-width="${lw*2}"/>`;
    body+=`<line x1="${cx}" y1="${cy-20}" x2="${cx}" y2="${cy+20}" stroke="#00c8d4" stroke-width="${lw*2}"/>`;
  }
  else if (type === 'zones') {
    body = `<rect width="${W}" height="${H}" fill="#808080"/>`;
    const cx=W/2, cy=H/2, N=80, maxR=Math.sqrt(cx*cx+cy*cy), bands=200;
    for(let i=bands;i>=0;i--){
      const r=maxR*i/bands, dx=r/W*2, dy=r/H*2, phase=Math.PI*(dx*dx+dy*dy)*N*N;
      const v=Math.round(128+127*Math.cos(phase)), h2=v.toString(16).padStart(2,'0');
      body+=`<circle cx="${cx}" cy="${cy}" r="${r.toFixed(1)}" fill="#${h2}${h2}${h2}" stroke="none"/>`;
    }
    body+=`<circle cx="${cx}" cy="${cy}" r="${Math.round(Math.min(W,H)*0.01)}" fill="none" stroke="#00c8d4" stroke-width="${lw*1.5}"/>`;
  }
  else if (type === 'bars_h') {
    body = `<rect width="${W}" height="${H}" fill="#000"/>`;
    const bars100h=[{c:'#ffffff',l:'WHT'},{c:'#ffff00',l:'YEL'},{c:'#00ffff',l:'CYN'},{c:'#00ff00',l:'GRN'},{c:'#ff00ff',l:'MAG'},{c:'#ff0000',l:'RED'},{c:'#0000ff',l:'BLU'},{c:'#000000',l:'BLK'}];
    const bwh=Math.round(W/bars100h.length);
    bars100h.forEach((b,i)=>{
      body+=`<rect x="${i*bwh}" y="0" width="${bwh}" height="${H}" fill="${b.c}"/>`;
      const fs=Math.max(12,Math.round(bwh/6)), bright=['#ffffff','#ffff00','#00ffff','#00ff00'].includes(b.c);
      body+=`<text x="${i*bwh+bwh/2}" y="${H-Math.round(fs*1.5)}" text-anchor="middle" font-family="monospace" font-size="${fs}" fill="${bright?'#000000':'#ffffff'}" opacity="0.7">${b.l}</text>`;
    });
  }
  else if (type === 'bars_v') {
    body = `<rect width="${W}" height="${H}" fill="#000"/>`;
    const bars100v=[{c:'#ffffff',l:'WHT'},{c:'#ffff00',l:'YEL'},{c:'#00ffff',l:'CYN'},{c:'#00ff00',l:'GRN'},{c:'#ff00ff',l:'MAG'},{c:'#ff0000',l:'RED'},{c:'#0000ff',l:'BLU'},{c:'#000000',l:'BLK'}];
    const bh=Math.round(H/bars100v.length);
    bars100v.forEach((b,i)=>{
      body+=`<rect x="0" y="${i*bh}" width="${W}" height="${bh}" fill="${b.c}"/>`;
      const fs=Math.max(12,Math.round(bh/3)), bright=['#ffffff','#ffff00','#00ffff','#00ff00'].includes(b.c);
      body+=`<text x="${Math.round(W*0.02)}" y="${i*bh+bh/2+fs/3}" font-family="monospace" font-size="${fs}" fill="${bright?'#000000':'#ffffff'}" opacity="0.7">${b.l}</text>`;
    });
  }

  // ── ZONE BORDERS ──────────────────────────────────────────────────────────
  if (nProj > 1) {
    const resW2  = Math.round((W + blendPx * (nProj - 1)) / nProj);
    const imageW = resW2 - blendPx;
    const lblFs  = Math.max(12, Math.round(H / 55));
    for(let p = 0; p < nProj; p++) {
      const x0 = p * imageW, x1 = x0 + resW2, col = PROJ_COLS[p % PROJ_COLS.length];
      body += `<rect x="${x0+lw}" y="${lw}" width="${resW2-lw*2}" height="${H-lw*2}" fill="none" stroke="${col}" stroke-width="${Math.max(3,lw*2)}" opacity="0.7"/>`;
      if (p > 0) body += `<line x1="${x0}" y1="0" x2="${x0}" y2="${H}" stroke="${col}" stroke-width="${Math.max(2,lw)}" opacity="0.9"/>`;
      const mk=Math.round(Math.min(resW2,H)*0.05);
      [[x0,0],[x1,0],[x0,H],[x1,H]].forEach(([cx2,cy2],idx)=>{
        const sx=idx===1||idx===3?-1:1, sy=idx>=2?-1:1;
        body+=`<line x1="${cx2}" y1="${cy2}" x2="${cx2+sx*mk}" y2="${cy2}" stroke="${col}" stroke-width="${Math.max(2,lw*1.5)}" opacity="0.95"/>`;
        body+=`<line x1="${cx2}" y1="${cy2}" x2="${cx2}" y2="${cy2+sy*mk}" stroke="${col}" stroke-width="${Math.max(2,lw*1.5)}" opacity="0.95"/>`;
      });
      const lblSize=Math.round(Math.min(resW2,H)*0.15);
      const zoneLblEl=document.getElementById('stp-zone-label-'+p);
      const zoneLbl=zoneLblEl?(zoneLblEl.value||'P'+(p+1)):'P'+(p+1);
      body+=`<text x="${x0+resW2/2}" y="${H/2+lblSize*0.35}" text-anchor="middle" font-family="monospace" font-size="${lblSize}" fill="${col}" opacity="0.2" font-weight="bold">${zoneLbl}</text>`;
      if (blendPx>0&&p<nProj-1) {
        const bx=x0+imageW;
        body+=`<rect x="${bx}" y="0" width="${blendPx}" height="${H}" fill="#00c8d4" fill-opacity="0.12" stroke="#00c8d4" stroke-width="${lw}" stroke-dasharray="${lw*5},${lw*3}"/>`;
        body+=`<text x="${bx+blendPx/2}" y="${lblFs+8}" text-anchor="middle" font-family="monospace" font-size="${lblFs}" fill="#00c8d4">◀ ${blendPx}px blend ▶</text>`;
      }
    }
  }

  // ── EFFECTIVE OVERLAY BOUNDS ───────────────────────────────────────────────
  const isLEDMode      = document.getElementById('stp-subtab-btn-led')?.style.borderBottom?.includes('var(--gold)') || document.getElementById('stp-subtab-btn-led')?.style.color === 'var(--gold)';
  const canvasOverride = document.getElementById('stp-led-canvas-override')?.checked;
  let eX=0,eY=0,eW=W,eH=H;
  if (isLEDMode && canvasOverride) {
    const tileW2=parseInt(document.getElementById('stp-tile-w')?.value)||192;
    const tileH2=parseInt(document.getElementById('stp-tile-h')?.value)||192;
    const tilesW2=parseInt(document.getElementById('stp-tiles-w')?.value)||8;
    const tilesH2=parseInt(document.getElementById('stp-tiles-h')?.value)||5;
    const just2=document.getElementById('stp-tile-justify')?.value||'tl';
    eW=tileW2*tilesW2; eH=tileH2*tilesH2;
    if(just2==='tr'||just2==='br') eX=W-eW;
    else if(just2==='c') eX=Math.round((W-eW)/2);
    if(just2==='bl'||just2==='br') eY=H-eH;
    else if(just2==='c') eY=Math.round((H-eH)/2);
  }
  const eCX=eX+eW/2, eCY=eY+eH/2;

  // ── OVERLAYS ───────────────────────────────────────────────────────────────
  if (ovGrid) {
    for(let c=0;c<=vcols;c++){const x=Math.round(eX+c*eW/vcols),isE=c===0||c===vcols;body+=`<line x1="${x}" y1="${eY}" x2="${x}" y2="${eY+eH}" stroke="${lineCol}" stroke-width="${isE?lw*2:lw}" opacity="${isE?0.5:0.18}"/>`;}
    for(let r=0;r<=hrows;r++){const y=Math.round(eY+r*eH/hrows),isE=r===0||r===hrows;body+=`<line x1="${eX}" y1="${y}" x2="${eX+eW}" y2="${y}" stroke="${lineCol}" stroke-width="${isE?lw*2:lw}" opacity="${isE?0.5:0.18}"/>`;}
  }
  if (ovMarkers) {
    const cs2=Math.round(Math.min(eW/vcols,eH/hrows)*0.28);
    for(let c=0;c<=vcols;c++)for(let r=0;r<=hrows;r++){const x=Math.round(eX+c*eW/vcols),y=Math.round(eY+r*eH/hrows);body+=`<line x1="${x-cs2}" y1="${y}" x2="${x+cs2}" y2="${y}" stroke="${lineCol}" stroke-width="${lw*2}" opacity="0.65"/>`;body+=`<line x1="${x}" y1="${y-cs2}" x2="${x}" y2="${y+cs2}" stroke="${lineCol}" stroke-width="${lw*2}" opacity="0.65"/>`;}
  }
  if (ovCirc) {
    const cx2=eCX,cy2=eCY,maxR=Math.min(eW,eH)*0.48;
    for(let c=1;c<=nCircles;c++){const r=maxR*(c/nCircles),op=0.25+(c/nCircles)*0.4;body+=`<circle cx="${cx2}" cy="${cy2}" r="${r.toFixed(1)}" fill="none" stroke="${lineCol}" stroke-width="${lw}" opacity="${op.toFixed(2)}"/>`;}
  }
  if (ovHCirc) {
    const cy2=eCY, colW=eW/nHCircles, circR=Math.min(eH*0.48,colW/2);
    for(let i=0;i<nHCircles;i++){
      const cx2=eX+colW*i+colW/2;
      for(let c=1;c<=nCircles;c++){const r=circR*(c/nCircles),op=(0.25+(c/nCircles)*0.4).toFixed(2);body+=`<circle cx="${cx2.toFixed(1)}" cy="${cy2}" r="${r.toFixed(1)}" fill="none" stroke="${lineCol}" stroke-width="${lw}" opacity="${op}"/>`;}
    }
  }
  if (ovDiag) {
    const dColW=eW/nDiag;
    for(let d=0;d<nDiag;d++){const x0=eX+d*dColW,x1=x0+dColW;body+=`<line x1="${x0}" y1="${eY}" x2="${x1}" y2="${eY+eH}" stroke="${lineCol}" stroke-width="${lw}" opacity="0.28"/>`;body+=`<line x1="${x1}" y1="${eY}" x2="${x0}" y2="${eY+eH}" stroke="${lineCol}" stroke-width="${lw}" opacity="0.28"/>`;}
  }
  if (ovCross) {
    const cx2=eCX,cy2=eCY,cs3=Math.round(Math.min(eW,eH)*0.04);
    body+=`<line x1="${cx2-cs3}" y1="${cy2}" x2="${cx2+cs3}" y2="${cy2}" stroke="${accentCol}" stroke-width="${lw*2}"/>`;
    body+=`<line x1="${cx2}" y1="${cy2-cs3}" x2="${cx2}" y2="${cy2+cs3}" stroke="${accentCol}" stroke-width="${lw*2}"/>`;
    body+=`<circle cx="${cx2}" cy="${cy2}" r="${Math.round(cs3*0.35)}" fill="none" stroke="${accentCol}" stroke-width="${lw*1.5}"/>`;
  }

  // LED tile overlay if in LED sub-tab
  const activeSubtab = document.getElementById('stp-subtab-btn-led')?.style.color === 'var(--gold)' ||
    (document.getElementById('stp-subtab-btn-led')?.style.borderBottom || '').includes('var(--gold)');
  if (activeSubtab) body += buildLEDTileOverlay(W, H, lw);

  // ── WATERMARK ──────────────────────────────────────────────────────────────
  const wfs=Math.max(14,Math.round(H/60)), wPad=Math.round(wfs*1.0);
  const mixedPatterns=['smpte','colchips','bars_h','bars_v','greyramp','hbars','checker','focus','zones'];
  const isMixed=mixedPatterns.includes(type), isLight=type==='fullwhite'||bgBrightness>160;
  const stripH=Math.round(wfs*2.2);
  body+=`<rect x="0" y="${H-stripH}" width="${W}" height="${stripH}" fill="rgba(0,0,0,0.5)"/>`;
  const typeNames={grid:'Grid',checker:'Checkerboard',greyramp:'Grey ramp',hbars:'H grey bars',smpte:'SMPTE bars',bars_h:'Colour bars H',bars_v:'Colour bars V',colchips:'Colour chips',focus:'Focus chart',zones:'Zone plate',fullwhite:'Full white',fullblack:'Full black',red:'Full red',green:'Full green',blue:'Full blue'};
  body+=`<text x="${wPad}" y="${H-wPad}" font-family="monospace" font-size="${wfs}" font-weight="bold" fill="rgba(255,255,255,0.7)">Production Central // ${typeNames[type]||type}</text>`;
  const showQWm=document.getElementById('stp-quick-watermark')?.checked||false;
  const showProWm=document.getElementById('stp-export-watermark')?.checked||false;
  if (showQWm||showProWm) {
    let rightInfo;
    if (showProWm) {
      const csEl=document.getElementById('stp-export-cs'), bdEl=document.getElementById('stp-export-bd');
      const goOn=document.getElementById('stp-gamma-override-on')?.checked, goVal=document.getElementById('stp-gamma-override-val')?.value;
      const csLabels={srgb:'sRGB',rec709:'Rec.709',p3:'P3-D65',rec2020:'Rec.2020',linear:'Linear'};
      const gammaMap={srgb:'γ2.2',rec709:'BT.709',p3:'γ2.6',rec2020:'BT.709',linear:'γ1.0'};
      const gLabel=goOn?`γ${parseFloat(goVal).toFixed(1)}`:gammaMap[csEl?.value||'srgb'];
      const rangeLabel=document.getElementById('stp-export-range')?.value==='legal'?'legal':'full';
      rightInfo=`${W}×${H}${nProj>1?' · '+nProj+'P':''} · ${csLabels[csEl?.value||'srgb']} · ${gLabel} · ${bdEl?.value||'16'}-bit · ${rangeLabel}`;
    } else {
      rightInfo=`${W}×${H}${nProj>1?' · '+nProj+'P':''} · sRGB · 8-bit`;
    }
    body+=`<text x="${W-wPad}" y="${H-wPad}" text-anchor="end" font-family="monospace" font-size="${wfs}" fill="rgba(255,255,255,0.55)">${rightInfo}</text>`;
  }

  return { body, W, H };
}

function calcStandalonePattern() {
  stpUpdateLaunchBtnTitles();
  try {
    const { body, W, H } = buildStandalonePatternSVG();
    const svgStr = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${W} ${H}" style="position:absolute;top:0;left:0;width:100%;height:100%">${body}</svg>`;
    const preview = document.getElementById('stp-preview');
    if (preview) {
      const ar = W/H;
      preview.style.paddingBottom = (1/ar*100).toFixed(2)+'%';
      preview.style.position='relative'; preview.style.height='0';
      preview.innerHTML = svgStr;
    }
    const infoEl = document.getElementById('stp-info');
    if (infoEl) infoEl.textContent = `${W.toLocaleString()} × ${H.toLocaleString()}px`;
    if (window._stpPopoutWin && !window._stpPopoutWin.closed) stpPushToPopout();
  } catch(e) { console.warn('Pattern error:', e); }
}

function downloadStandalonePattern(fmt) {
  const { body, W, H } = buildStandalonePatternSVG();
  const type  = document.getElementById('stp-type').value;
  const nProj = document.getElementById('stp-projectors').value;
  const fname = `Production Central_${type}_${W}x${H}_${nProj}P`;
  const svgStr = `<svg xmlns="http://www.w3.org/2000/svg" width="${W}" height="${H}" viewBox="0 0 ${W} ${H}">${body}</svg>`;
  if (fmt === 'png8') fmt = 'png';
  if (fmt === 'svg') {
    const blob=new Blob([svgStr],{type:'image/svg+xml'});
    const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=fname+'.svg';a.click();return;
  }
  const canvas=document.createElement('canvas');canvas.width=W;canvas.height=H;
  const ctx=canvas.getContext('2d');const img=new Image();
  img.onload=()=>{ctx.drawImage(img,0,0);const a=document.createElement('a');a.href=canvas.toDataURL('image/png');a.download=fname+'.png';a.click();};
  img.src=URL.createObjectURL(new Blob([svgStr],{type:'image/svg+xml'}));
}

// ── COLOUR PICKER ──────────────────────────────────────────────────────────
var _stpPickerTarget=null, _stpPickerCallback=null, _stpPickerPanel=null;
function hexToRgb(hex){const h=hex.replace('#','');return{r:parseInt(h.slice(0,2),16),g:parseInt(h.slice(2,4),16),b:parseInt(h.slice(4,6),16)};}
function rgbToHex(r,g,b){return '#'+[r,g,b].map(v=>Math.max(0,Math.min(255,Math.round(v))).toString(16).padStart(2,'0')).join('');}
function stpApplyCustomSolid(){const col=document.getElementById('stp-bg-custom')?.value||'#000000';stpSetBg('solid:'+col,null);}
function stpOpenPicker(targetId,callbackName){
  const old=document.getElementById('stp-picker-panel');if(old)old.remove();
  if(_stpPickerTarget===targetId&&_stpPickerPanel){_stpPickerTarget=null;_stpPickerPanel=null;return;}
  _stpPickerTarget=targetId;_stpPickerCallback=callbackName;
  const el=document.getElementById(targetId), swatch=document.getElementById(targetId+'-swatch');
  const currentHex=el?.value||'#000000', rgb=hexToRgb(currentHex);
  const panel=document.createElement('div');panel.id='stp-picker-panel';panel.className='stp-picker-panel open';
  panel.innerHTML=`<div class="stp-picker-preview" id="stp-picker-preview" style="background:${currentHex}"></div><input class="stp-picker-hex" id="stp-picker-hex" value="${currentHex.toUpperCase()}" maxlength="7" oninput="stpPickerHexInput(this.value)"><div class="stp-picker-row"><span class="stp-picker-label" style="color:#ff6666">R</span><input type="range" min="0" max="255" value="${rgb.r}" id="stp-pr" oninput="stpPickerRgbInput()"><span class="stp-picker-val" id="stp-pr-v">${rgb.r}</span></div><div class="stp-picker-row"><span class="stp-picker-label" style="color:#66ff88">G</span><input type="range" min="0" max="255" value="${rgb.g}" id="stp-pg" oninput="stpPickerRgbInput()"><span class="stp-picker-val" id="stp-pg-v">${rgb.g}</span></div><div class="stp-picker-row"><span class="stp-picker-label" style="color:#6699ff">B</span><input type="range" min="0" max="255" value="${rgb.b}" id="stp-pb" oninput="stpPickerRgbInput()"><span class="stp-picker-val" id="stp-pb-v">${rgb.b}</span></div><div style="display:flex;gap:6px;margin-top:8px"><button onclick="stpPickerApply()" style="flex:1;font-family:var(--tp-mono);font-size:11px;padding:6px;background:var(--gold);color:#000;border:none;border-radius:4px;cursor:pointer">Apply</button><button onclick="document.getElementById('stp-picker-panel').remove()" style="flex:1;font-family:var(--tp-mono);font-size:11px;padding:6px;background:var(--dark-2);color:var(--text-1);border:.5px solid var(--border);border-radius:4px;cursor:pointer">Cancel</button></div>`;
  if(swatch)swatch.parentNode.appendChild(panel);_stpPickerPanel=panel;
}
function stpPickerHexInput(val){if(val.length===7&&val.startsWith('#')){const rgb=hexToRgb(val);document.getElementById('stp-pr').value=rgb.r;document.getElementById('stp-pg').value=rgb.g;document.getElementById('stp-pb').value=rgb.b;document.getElementById('stp-pr-v').textContent=rgb.r;document.getElementById('stp-pg-v').textContent=rgb.g;document.getElementById('stp-pb-v').textContent=rgb.b;document.getElementById('stp-picker-preview').style.background=val;}}
function stpPickerRgbInput(){const r=parseInt(document.getElementById('stp-pr').value),g=parseInt(document.getElementById('stp-pg').value),b=parseInt(document.getElementById('stp-pb').value);document.getElementById('stp-pr-v').textContent=r;document.getElementById('stp-pg-v').textContent=g;document.getElementById('stp-pb-v').textContent=b;const hex=rgbToHex(r,g,b);document.getElementById('stp-picker-hex').value=hex.toUpperCase();document.getElementById('stp-picker-preview').style.background=hex;}
function stpPickerApply(){const hex=document.getElementById('stp-picker-hex').value,el=document.getElementById(_stpPickerTarget),swatch=document.getElementById(_stpPickerTarget+'-swatch');if(el)el.value=hex;if(swatch)swatch.style.background=hex;document.getElementById('stp-picker-panel')?.remove();if(_stpPickerCallback&&window[_stpPickerCallback])window[_stpPickerCallback]();}
document.addEventListener('click',function(e){const panel=document.getElementById('stp-picker-panel');if(panel&&!panel.contains(e.target)&&!e.target.id?.includes('swatch')){panel.remove();_stpPickerTarget=null;}});

// ── PROFESSIONAL PNG EXPORT ────────────────────────────────────────────────
function stpToggleWmPill(pill,checkId){const cb=document.getElementById(checkId);if(!cb)return;cb.checked=!cb.checked;pill.classList.toggle('stp-pill-on',cb.checked);}
function stpToggleGammaOverride(){const on=document.getElementById('stp-gamma-override-on')?.checked,inp=document.getElementById('stp-gamma-override-val'),cs=document.getElementById('stp-export-cs'),bm=document.getElementById('stp-gamma-minus'),bp=document.getElementById('stp-gamma-plus');if(!inp)return;inp.disabled=!on;inp.style.opacity=on?'1':'0.4';inp.style.color=on?'var(--gold)':'var(--text-3)';if(bm){bm.disabled=!on;bm.style.opacity=on?'1':'0.3';}if(bp){bp.disabled=!on;bp.style.opacity=on?'1':'0.3';}if(on&&cs){const gm={srgb:2.2,rec709:2.4,p3:2.6,rec2020:2.4,linear:1.0};inp.value=gm[cs.value]||2.2;}}
function stpUpdateGammaOverride(){}
function stpGetExportGamma(){const on=document.getElementById('stp-gamma-override-on')?.checked,val=parseFloat(document.getElementById('stp-gamma-override-val')?.value);if(on&&val>=0.5&&val<=4.0)return val;const cs=document.getElementById('stp-export-cs')?.value||'srgb';return{srgb:2.2,rec709:2.4,p3:2.6,rec2020:2.4,linear:1.0}[cs]||2.2;}
function stpGammaEncode(linear,gamma,cs){const v=Math.max(0,Math.min(1,linear));if(gamma===1.0)return v;if(cs==='srgb'||gamma===2.2)return v<=0.0031308?12.92*v:1.055*Math.pow(v,1/2.4)-0.055;if(cs==='rec709'||cs==='rec2020')return v<0.018?4.5*v:1.0993*Math.pow(v,0.45)-0.0993;if(cs==='p3')return Math.pow(v,1/2.6);return Math.pow(v,1/gamma);}
function stpPrimaryMatrix(cs){if(cs==='srgb'||cs==='linear')return(r,g,b)=>[r,g,b];if(cs==='rec709')return(r,g,b)=>[r,g,b];if(cs==='p3')return(r,g,b)=>[Math.max(0,0.8225*r+0.1774*g),Math.max(0,0.0332*r+0.9669*g),Math.max(0,0.0171*r+0.0724*g+0.9108*b)];if(cs==='rec2020')return(r,g,b)=>[Math.max(0,0.6274*r+0.3293*g+0.0433*b),Math.max(0,0.0691*r+0.9195*g+0.0114*b),Math.max(0,0.0164*r+0.0880*g+0.8956*b)];return(r,g,b)=>[r,g,b];}
function stpHexToLinear(hex){const r=parseInt(hex.slice(1,3),16)/255,g=parseInt(hex.slice(3,5),16)/255,b=parseInt(hex.slice(5,7),16)/255;const toL=v=>v<=0.04045?v/12.92:Math.pow((v+0.055)/1.055,2.4);return[toL(r),toL(g),toL(b)];}
function stpEncodePixel(hexCol,cs,gamma,maxVal){const[rl,gl,bl]=stpHexToLinear(hexCol),conv=stpPrimaryMatrix(cs),[tr,tg,tb]=conv(rl,gl,bl),enc=v=>Math.round(stpGammaEncode(v,gamma,cs)*maxVal);return[enc(tr),enc(tg),enc(tb)];}

function stpRenderPatternPixels(W,H,type,cs,gamma,maxVal,vcols,hrows,legalRange){
  const pixels=new Array(W*H);
  const encode=hex=>stpEncodePixel(hex,cs,gamma,maxVal);
  const BLACK=encode('#000000'),WHITE=encode('#ffffff');
  if(type==='greyramp'){const steps=11,sw=Math.round(W/steps);for(let y=0;y<H;y++)for(let x=0;x<W;x++){const step=Math.min(steps-1,Math.floor(x/sw)),lv=step/(steps-1),enc=v=>Math.round(stpGammaEncode(v,gamma,cs)*maxVal),conv=stpPrimaryMatrix(cs),[r,g,b]=conv(lv,lv,lv);pixels[y*W+x]=[enc(r),enc(g),enc(b)];}}
  else if(type==='hbars'){const bh=Math.round(H/hrows);for(let y=0;y<H;y++){const bar=Math.min(hrows-1,Math.floor(y/bh)),lv=bar/(hrows-1),enc=v=>Math.round(stpGammaEncode(v,gamma,cs)*maxVal),conv=stpPrimaryMatrix(cs),[r,g,b]=conv(lv,lv,lv),px=[enc(r),enc(g),enc(b)];for(let x=0;x<W;x++)pixels[y*W+x]=px;}}
  else if(type==='fullwhite')pixels.fill(WHITE);
  else if(type==='fullblack')pixels.fill(BLACK);
  else if(type==='red'){const px=encode('#ff0000');pixels.fill(px);}
  else if(type==='green'){const px=encode('#00ff00');pixels.fill(px);}
  else if(type==='blue'){const px=encode('#0000ff');pixels.fill(px);}
  else if(type==='bars_h'){const barCols=['#ffffff','#ffff00','#00ffff','#00ff00','#ff00ff','#ff0000','#0000ff','#000000'],bw=Math.round(W/barCols.length),encoded=barCols.map(c=>encode(c));for(let y=0;y<H;y++)for(let x=0;x<W;x++)pixels[y*W+x]=encoded[Math.min(barCols.length-1,Math.floor(x/bw))];}
  else if(type==='bars_v'){const barCols=['#ffffff','#ffff00','#00ffff','#00ff00','#ff00ff','#ff0000','#0000ff','#000000'],bh=Math.round(H/barCols.length),encoded=barCols.map(c=>encode(c));for(let y=0;y<H;y++){const px=encoded[Math.min(barCols.length-1,Math.floor(y/bh))];for(let x=0;x<W;x++)pixels[y*W+x]=px;}}
  else if(type==='checker'){const cs2=Math.round(W/vcols);for(let y=0;y<H;y++)for(let x=0;x<W;x++){const col=Math.floor(x/cs2),row=Math.floor(y/cs2);pixels[y*W+x]=(col+row)%2===0?WHITE:BLACK;}}
  else if(type==='zones'){const cx=W/2,cy=H/2,N=80;for(let y=0;y<H;y++)for(let x=0;x<W;x++){const dx=(x-cx)/W*2,dy=(y-cy)/H*2,phase=Math.PI*(dx*dx+dy*dy)*N*N,lv=0.5+0.5*Math.cos(phase),enc=v=>Math.round(stpGammaEncode(v,gamma,cs)*maxVal),conv=stpPrimaryMatrix(cs),[r,g,b]=conv(lv,lv,lv);pixels[y*W+x]=[enc(r),enc(g),enc(b)];}}
  else if(type==='colchips'){const chips=['#735244','#c29682','#627a9d','#576c43','#8580b1','#67bdaa','#d67e2c','#505ba6','#c15a63','#5e3c6c','#9dbc40','#e0a32e','#383d96','#469449','#af363c','#e7c71f','#bb5695','#0885a1','#f3f3f2','#c8c8c8','#a0a0a0','#7a7a79','#555555','#343434'],cols=6,cw=Math.round(W/cols),ch=Math.round(H/4),encoded=chips.map(c=>encode(c));for(let y=0;y<H;y++)for(let x=0;x<W;x++){const col=Math.floor(x/cw),row=Math.floor(y/ch),idx=row*cols+col;pixels[y*W+x]=idx<chips.length?encoded[idx]:BLACK;}}
  else pixels.fill(BLACK);
  return pixels;
}

function stpWritePng(pixels,W,H,bitDepth){
  const bytesPerChannel=bitDepth<=8?1:2,rowBytes=1+W*3*bytesPerChannel,raw=new Uint8Array(H*rowBytes);
  for(let y=0;y<H;y++){raw[y*rowBytes]=0;for(let x=0;x<W;x++){const[r,g,b]=pixels[y*W+x],di=y*rowBytes+1+x*3*bytesPerChannel;if(bytesPerChannel===1){raw[di]=r&0xff;raw[di+1]=g&0xff;raw[di+2]=b&0xff;}else{raw[di]=(r>>8)&0xff;raw[di+1]=r&0xff;raw[di+2]=(g>>8)&0xff;raw[di+3]=g&0xff;raw[di+4]=(b>>8)&0xff;raw[di+5]=b&0xff;}}}
  function u32(v){return[(v>>>24)&0xff,(v>>>16)&0xff,(v>>>8)&0xff,v&0xff];}
  function crc32(buf){let c=0xffffffff;for(const b of buf){let x=(c^b)&0xff;for(let i=0;i<8;i++)x=x&1?(x>>>1)^0xEDB88320:x>>>1;c=(c>>>8)^x;}return(c^0xffffffff)>>>0;}
  function chunk(type,data){const tb=[...type].map(c=>c.charCodeAt(0)),len=u32(data.length),cd=[...tb,...data];return[...len,...tb,...data,...u32(crc32(cd))];}
  const ihdr=[...u32(W),...u32(H),bitDepth<=8?8:16,2,0,0,0];
  return new Promise(resolve=>{
    const cs2=new CompressionStream('deflate'),w=cs2.writable.getWriter();w.write(raw);w.close();
    const chunks=[],reader=cs2.readable.getReader();
    function read(){reader.read().then(({done,value})=>{if(done){const total=chunks.reduce((s,c)=>s+c.length,0),comp=new Uint8Array(total);let off=0;for(const c of chunks){comp.set(c,off);off+=c.length;}const zlib=new Uint8Array(comp.length+6);zlib[0]=0x78;zlib[1]=0x9c;zlib.set(comp,2);let a=1,bv=0;for(const v of raw){a=(a+v)%65521;bv=(bv+a)%65521;}const ad=(bv<<16|a)>>>0;zlib[zlib.length-4]=(ad>>24)&0xff;zlib[zlib.length-3]=(ad>>16)&0xff;zlib[zlib.length-2]=(ad>>8)&0xff;zlib[zlib.length-1]=ad&0xff;const sig=[137,80,78,71,13,10,26,10],png=new Uint8Array([...sig,...chunk('IHDR',ihdr),...chunk('IDAT',Array.from(zlib)),...chunk('IEND',[])]);resolve(png);}else{chunks.push(value);read();}});}read();});
}

async function downloadProPattern(){
  const cs=document.getElementById('stp-export-cs')?.value||'srgb',bd=parseInt(document.getElementById('stp-export-bd')?.value)||16,W=parseInt(document.getElementById('stp-width').value)||1920,H=parseInt(document.getElementById('stp-height').value)||1080,type=document.getElementById('stp-type').value,vcols=parseInt(document.getElementById('stp-vcols').value)||16,hrows=parseInt(document.getElementById('stp-hrows').value)||9,gamma=stpGetExportGamma(),legalRange=document.getElementById('stp-export-range')?.value==='legal',maxVal=(1<<bd)-1;
  const csLabels={srgb:'sRGB',rec709:'Rec709',p3:'P3-D65',rec2020:'Rec2020',linear:'Linear'};
  await new Promise(r=>setTimeout(r,10));
  try{
    const pixels=stpRenderPatternPixels(W,H,type,cs,gamma,maxVal,vcols,hrows,legalRange);
    const png=await stpWritePng(pixels,W,H,bd);
    const rangeSuffix=legalRange?'_legal':'_full',fname=`Production Central_${type}_${W}x${H}_${csLabels[cs]}_${bd}bit${rangeSuffix}.png`;
    const blob=new Blob([png],{type:'image/png'}),a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=fname;a.click();
  }catch(e){console.error(e);}
}

// ── LED TILE FUNCTIONS ─────────────────────────────────────────────────────
function stpLEDUpdateCanvas(){const on=document.getElementById('stp-led-canvas-override')?.checked;if(!on)return;const w=document.getElementById('stp-led-canvas-w')?.value,h=document.getElementById('stp-led-canvas-h')?.value;if(w)document.getElementById('stp-width').value=w;if(h)document.getElementById('stp-height').value=h;calcStandalonePattern();}
function stpLEDSpinCanvas(axis,delta){const on=document.getElementById('stp-led-canvas-override')?.checked;if(!on)return;const wEl=document.getElementById('stp-led-canvas-w'),hEl=document.getElementById('stp-led-canvas-h');if(axis==='w'&&wEl){wEl.value=Math.max(320,Math.min(32768,parseInt(wEl.value)+delta));document.getElementById('stp-width').value=wEl.value;}else if(axis==='h'&&hEl){hEl.value=Math.max(240,Math.min(32768,parseInt(hEl.value)+delta));document.getElementById('stp-height').value=hEl.value;}calcStandalonePattern();}
function stpLEDSpin(id,delta){const el=document.getElementById(id);if(!el)return;const step=parseFloat(el.step)||1,isFloat=step<1,current=parseFloat(el.value)||0,newVal=Math.max(parseFloat(el.min)||0.5,Math.min(parseFloat(el.max)||9999,Math.round((current+delta)/step)*step));el.value=isFloat?newVal.toFixed(1):Math.round(newVal);if(['stp-tiles-w','stp-tiles-h','stp-tile-w','stp-tile-h'].includes(id)){stpLEDSyncFromTiles();}else{calcStandalonePattern();}}
function stpLEDToggleOverride(){const on=document.getElementById('stp-led-canvas-override')?.checked,fields=document.getElementById('stp-led-canvas-override-fields');if(fields){fields.style.opacity=on?'1':'0.35';fields.style.pointerEvents=on?'auto':'none';}if(!on)stpLEDSyncFromTiles();calcStandalonePattern();}
function stpLEDSyncFromTiles(){const tileW=parseInt(document.getElementById('stp-tile-w')?.value)||192,tileH=parseInt(document.getElementById('stp-tile-h')?.value)||192,tilesW=parseInt(document.getElementById('stp-tiles-w')?.value)||8,tilesH=parseInt(document.getElementById('stp-tiles-h')?.value)||5,totalW=tileW*tilesW,totalH=tileH*tilesH,on=document.getElementById('stp-led-canvas-override')?.checked;if(!on){document.getElementById('stp-width').value=totalW;document.getElementById('stp-height').value=totalH;const cW=document.getElementById('stp-led-canvas-w'),cH=document.getElementById('stp-led-canvas-h');if(cW)cW.value=totalW;if(cH)cH.value=totalH;}calcStandalonePattern();}

function buildLEDTileOverlay(W,H,lw){
  const tileW=parseInt(document.getElementById('stp-tile-w')?.value)||192,tileH=parseInt(document.getElementById('stp-tile-h')?.value)||192,tilesWc=parseInt(document.getElementById('stp-tiles-w')?.value)||10,tilesHc=parseInt(document.getElementById('stp-tiles-h')?.value)||6,just=document.getElementById('stp-tile-justify')?.value||'tl',showBorders=document.getElementById('stp-ov-led-tiles')?.checked!==false,showIDs=document.getElementById('stp-ov-led-ids')?.checked!==false;
  const totalTileW=tileW*tilesWc,totalTileH=tileH*tilesHc;
  let offX=0,offY=0;
  if(just==='tr'||just==='br')offX=W-totalTileW;else if(just==='c')offX=Math.round((W-totalTileW)/2);
  if(just==='bl'||just==='br')offY=H-totalTileH;else if(just==='c')offY=Math.round((H-totalTileH)/2);
  const infoEl=document.getElementById('stp-led-info');
  if(infoEl){const match=totalTileW===W&&totalTileH===H;infoEl.textContent=`${tilesWc}×${tilesHc} tiles · ${tileW}×${tileH}px each · ${totalTileW.toLocaleString()}×${totalTileH.toLocaleString()}px total${match?'':' · offset: '+offX+','+offY}`;}
  const TILE_COLS=['#00c8d4','#ff8800','#ff4466','#44ff88'];
  function tileColour(col,row){return TILE_COLS[(col%2)+(row%2)*2];}
  const tileBorderWeight=parseFloat(document.getElementById('stp-tile-border-weight')?.value)||lw*1.5,bw=Math.max(0.5,tileBorderWeight),fs=Math.max(10,Math.min(Math.round(tileW/6),Math.round(tileH/4),28));
  let body='';
  function colToLetters(n){let s='',c=n+1;while(c>0){c--;s=String.fromCharCode(65+(c%26))+s;c=Math.floor(c/26);}return s;}
  for(let row=0;row<tilesHc;row++){for(let col=0;col<tilesWc;col++){const x=offX+col*tileW,y=offY+row*tileH,colour=tileColour(col,row),tileID=`${colToLetters(col)}${String(row+1).padStart(2,'0')}`;if(showBorders)body+=`<rect x="${x+bw/2}" y="${y+bw/2}" width="${tileW-bw}" height="${tileH-bw}" fill="none" stroke="${colour}" stroke-width="${bw}" opacity="0.9"/>`;if(showIDs){const cx2=x+tileW/2,cy2=y+tileH/2+fs*0.35;body+=`<text x="${cx2}" y="${cy2}" text-anchor="middle" font-family="monospace" font-size="${fs}" fill="${colour}" opacity="0.85" font-weight="bold">${tileID}</text>`;}}}
  if(totalTileW!==W||totalTileH!==H)body+=`<rect x="${offX}" y="${offY}" width="${totalTileW}" height="${totalTileH}" fill="none" stroke="#ffffff" stroke-width="${bw}" stroke-dasharray="${bw*4},${bw*3}" opacity="0.4"/>`;
  return body;
}

// ── SUB-TAB SWITCHING ──────────────────────────────────────────────────────
function stpSetSubtab(tab){
  ['generic','led'].forEach(t=>{
    const btn=document.getElementById('stp-subtab-btn-'+t);if(!btn)return;
    const active=t===tab||(t==='generic'&&tab==='projection');
    btn.style.borderBottom=active?'2px solid var(--gold)':'2px solid transparent';
    btn.style.color=active?'var(--gold)':'var(--text-3)';
  });
  const ledConfig=document.getElementById('stp-led-config'),zonesField=document.getElementById('stp-zones-field'),adjBlend=document.getElementById('stp-adj-blend'),zoneLabels=document.getElementById('stp-zone-labels'),widthField=document.getElementById('stp-width-field'),heightField=document.getElementById('stp-height-field');
  if(tab==='led'){const lwEl=document.getElementById('stp-lineweight');if(lwEl){lwEl.dataset.genericLw=lwEl.value;lwEl.value=lwEl.dataset.ledLw||'2';}if(ledConfig)ledConfig.style.display='block';if(zonesField)zonesField.style.display='none';if(adjBlend)adjBlend.style.display='none';if(zoneLabels)zoneLabels.style.display='none';if(widthField)widthField.style.display='none';if(heightField)heightField.style.display='none';stpLEDSyncFromTiles();}
  else{const lwEl2=document.getElementById('stp-lineweight');if(lwEl2){lwEl2.dataset.ledLw=lwEl2.value;lwEl2.value=lwEl2.dataset.genericLw||'2';}if(ledConfig)ledConfig.style.display='none';if(zonesField)zonesField.style.display='flex';if(widthField)widthField.style.display='flex';if(heightField)heightField.style.display='flex';stpUpdateBlendCard();}
  calcStandalonePattern();
}

// ── POPOUT SYSTEM ──────────────────────────────────────────────────────────
function stpShowPopoutPanel(){const panel=document.getElementById('stp-popout-panel');if(!panel)return;const showing=panel.style.display!=='none';panel.style.display=showing?'none':'block';if(!showing)stpUpdatePopoutPanel();}
function stpSpinPopout(id,delta){const el=document.getElementById(id);if(!el)return;el.value=Math.max(parseInt(el.min)||1,Math.min(parseInt(el.max)||9999,parseInt(el.value||1)+delta));stpUpdatePopoutPanel();}
function stpUpdatePopoutPanel(){
  const{W,H}=buildStandalonePatternSVG(),nOut=parseInt(document.getElementById('stp-popout-outputs')?.value)||1,slice=document.getElementById('stp-popout-slice')?.value||'h';
  const sliceW=slice==='h'?Math.round(W/nOut):W,sliceH=slice==='v'?Math.round(H/nOut):H;
  const rW=document.getElementById('stp-popout-res-w'),rH=document.getElementById('stp-popout-res-h');
  if(rW&&!rW.dataset.userSet)rW.value=sliceW;if(rH&&!rH.dataset.userSet)rH.value=sliceH;
  const infoEl=document.getElementById('stp-popout-preview-info');
  if(infoEl)infoEl.textContent=nOut===1?`1 output · full canvas · ${W}×${H}px`:`${nOut} outputs · each ${sliceW}×${sliceH}px · direction: ${slice==='h'?'left → right':'top → bottom'}`;
  const btnContainer=document.getElementById('stp-output-buttons');if(!btnContainer)return;btnContainer.innerHTML='';
  window._stpOutputConfigs=[];
  for(let i=0;i<nOut;i++){let vx=0,vy=0,vw=W,vh=H;if(nOut>1){if(slice==='h'){vw=Math.round(W/nOut);vx=i*vw;}else{vh=Math.round(H/nOut);vy=i*vh;}}const label=nOut>1?`Output ${i+1} of ${nOut}`:'Pattern Output';window._stpOutputConfigs.push({vx,vy,vw,vh,label,index:i,nOut,outW:parseInt(document.getElementById('stp-popout-res-w')?.value)||W,outH:parseInt(document.getElementById('stp-popout-res-h')?.value)||H});}
  window._stpOutputConfigs.forEach((cfg,i)=>{const btn=document.createElement('button');btn.textContent=cfg.nOut===1?'⤢ Open Output':`⤢ Output ${i+1}`;btn.style.cssText=`font-family:var(--tp-mono);font-size:11px;color:var(--gold);background:var(--dark-2);border:.5px solid var(--gold);border-radius:4px;padding:8px 16px;cursor:pointer;letter-spacing:.5px`;btn.onclick=()=>stpOpenSingleOutput(i);btnContainer.appendChild(btn);});
}
function stpOpenSingleOutput(index){
  const cfg=window._stpOutputConfigs?.[index];if(!cfg)return;
  const{body,W,H}=buildStandalonePatternSVG();
  const features=`width=${Math.min(cfg.outW,960)},height=${Math.min(cfg.outH,540)},menubar=no,toolbar=no,location=no,status=no,scrollbars=no`;
  const pw=window.open('about:blank',`Production Central_output_${index}`,features);
  if(!pw){alert('Popup blocked — please allow popups for this page and try again.');return;}
  if(!window._stpOutputs)window._stpOutputs=[];
  const existing=window._stpOutputs[index];if(existing&&!existing.closed)existing.close();
  window._stpOutputs[index]=pw;
  if(!window._stpChannel)window._stpChannel=new BroadcastChannel('Production Central_pattern');
  setTimeout(()=>stpFillOutputWindow(pw,body,W,H,cfg.vx,cfg.vy,cfg.vw,cfg.vh,cfg.outW,cfg.outH,cfg.label,cfg.index,cfg.nOut),100);
  stpUpdateOutputControls();
}
function stpGetPopupHTML(){var c=[];c.push('<!DOCTYPE html><html><head><meta charset="UTF-8">');c.push('<style>*{margin:0;padding:0;box-sizing:border-box}html,body{width:100%;height:100%;background:#000;overflow:hidden}#pc{width:100%;height:100%}#pc svg{width:100%;height:100%;display:block}#ui{position:fixed;top:0;left:0;right:0;display:flex;justify-content:space-between;align-items:center;padding:8px 14px;background:linear-gradient(rgba(0,0,0,.8),transparent);opacity:1;transition:opacity .4s;z-index:100}#ui.hidden{opacity:0;pointer-events:none}.btn{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.25);color:rgba(255,255,255,.8);font-family:monospace;font-size:10px;padding:4px 10px;border-radius:4px;cursor:pointer}#inf{font-family:monospace;font-size:10px;color:rgba(255,255,255,.4)}#hnt{font-family:monospace;font-size:9px;color:rgba(255,255,255,.25)}</style></head><body>');c.push('<div id="ui"><span id="inf">Production Central</span><span id="hnt">F = fullscreen</span><div><button class="btn" id="fsb">Fullscreen</button> <button class="btn" onclick="window.close()">Close</button></div></div>');c.push('<div id="pc" style="position:relative"><svg id="svg" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:100%;display:block"></svg><canvas id="sc" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none"></canvas></div>');c.push('<scr'+'ipt>var t,ui=document.getElementById("ui");function show(){ui.classList.remove("hidden");clearTimeout(t);t=setTimeout(function(){ui.classList.add("hidden");},3000);}document.addEventListener("mousemove",show);show();function fs(){if(!document.fullscreenElement){document.documentElement.requestFullscreen();}else{document.exitFullscreen();}}document.getElementById("fsb").onclick=fs;document.addEventListener("keydown",function(e){if(e.key=="f"||e.key=="F")fs();});window.addEventListener("message",function(e){if(!e||!e.data)return;var d=e.data;if(d.type=="label"&&d.text)document.getElementById("inf").textContent=d.text;if(d.type=="pattern"){var s=document.getElementById("svg");if(!s)return;s.innerHTML=d.body||"";if(d.vx!=null)s.setAttribute("viewBox",d.vx+" "+d.vy+" "+d.vw+" "+d.vh);}if(d.type=="fullscreen")fs();if(d.type=="sweep"){var sc=document.getElementById("sc"),sv=document.getElementById("svg");if(!sc||!sv)return;var W=sv.clientWidth||window.innerWidth,H=sv.clientHeight||window.innerHeight;if(sc.width!==W)sc.width=W;if(sc.height!==H)sc.height=H;var ctx=sc.getContext("2d");ctx.clearRect(0,0,W,H);if(d.posH<0){return;}var w=d.weight||3;ctx.save();ctx.strokeStyle="#00c8d4";ctx.lineWidth=w;ctx.beginPath();ctx.moveTo(0,d.posH*H);ctx.lineTo(W,d.posH*H);ctx.stroke();ctx.beginPath();ctx.moveTo(d.posV*W,0);ctx.lineTo(d.posV*W,H);ctx.stroke();ctx.restore();}});window.addEventListener("load",function(){if(window.opener)window.opener.postMessage({type:"output-ready"},"*");});<'+'/script></body></html>');return c.join('');}
function stpFillOutputWindow(pw,body,W,H,vx,vy,vw,vh,outW,outH,label,index,total){if(!pw||pw.closed)return;window._stpPopoutWin=pw;var html=stpGetPopupHTML(),blob=new Blob([html],{type:'text/html'}),url=URL.createObjectURL(blob);pw.location.href=url;var attempts=0,pushInterval=setInterval(function(){attempts++;try{var svg=pw.document&&pw.document.getElementById('svg');if(svg){clearInterval(pushInterval);URL.revokeObjectURL(url);pw.postMessage({type:'label',text:label},'*');var msg={type:'pattern',body,W,H,vx,vy,vw,vh};pw.postMessage(msg,'*');if(window._stpChannel)window._stpChannel.postMessage(msg);return;}}catch(e){}if(attempts===5){pw.postMessage({type:'label',text:label},'*');var msg2={type:'pattern',body,W,H,vx,vy,vw,vh};pw.postMessage(msg2,'*');}if(attempts>20)clearInterval(pushInterval);},200);}
window.addEventListener('message',function(e){if(e.data&&e.data.type==='output-ready')setTimeout(stpPushToPopout,100);});
function stpPushToPopout(){try{const{body,W,H}=buildStandalonePatternSVG(),nOut=parseInt(document.getElementById('stp-popout-outputs')?.value)||1,slice=document.getElementById('stp-popout-slice')?.value||'h';window._stpOutputs=(window._stpOutputs||[]).filter(w=>w&&!w.closed);window._stpOutputs.forEach((pw,i)=>{if(!pw||pw.closed)return;const vx=slice==='h'?Math.round(i*W/nOut):0,vy=slice==='v'?Math.round(i*H/nOut):0,vw=slice==='h'?Math.round(W/nOut):W,vh=slice==='v'?Math.round(H/nOut):H,msg={type:'pattern',body,W,H,vx,vy,vw,vh};pw.postMessage(msg,'*');if(window._stpChannel)window._stpChannel.postMessage(msg);});stpUpdateOutputControls();}catch(e){console.warn('Popout push error:',e);}}
function stpUpdateOutputControls(){window._stpOutputs=(window._stpOutputs||[]).filter(w=>w&&!w.closed);const n=window._stpOutputs.length,fsBtn=document.getElementById('stp-fs-all-btn'),closeBtn=document.getElementById('stp-close-all-btn'),countEl=document.getElementById('stp-popout-count');if(fsBtn)fsBtn.style.display=n>0?'block':'none';if(closeBtn)closeBtn.style.display=n>0?'block':'none';if(countEl){countEl.style.display=n>0?'inline':'none';countEl.textContent=n===1?'1 output open':`${n} outputs open`;}}
function stpFullscreenAll(){var outputs=(window._stpOutputs||[]).filter(function(w){return w&&!w.closed;});outputs.forEach(function(pw){try{pw.postMessage({type:'fullscreen'},'*');}catch(e){}});var countEl=document.getElementById('stp-popout-count');if(countEl){var orig=countEl.textContent,origColor=countEl.style.color;countEl.textContent=outputs.length>1?'Click each output window then press F':'Click the output window then press F';countEl.style.color='var(--gold)';setTimeout(function(){countEl.textContent=orig;countEl.style.color=origColor;},4000);}}
function stpCloseAll(){(window._stpOutputs||[]).forEach(pw=>{try{if(!pw.closed)pw.close();}catch(e){}});window._stpOutputs=[];stpUpdateOutputControls();}

// ── LOCK & CLEAR ──────────────────────────────────────────────────────────
function stpIsLocked(){return document.getElementById('stp-locked')?.checked===true;}
function stpToggleLock(){
  const cb=document.getElementById('stp-locked'),btn=document.getElementById('stp-lock-btn');if(!cb||!btn)return;
  const locked=cb.checked;cb.checked=!locked;btn.classList.toggle('stp-pill-on',!locked);
  btn.innerHTML=(locked?'🔓 Unlocked':'🔒 Locked')+'<input type="checkbox" id="stp-locked" style="display:none"'+(locked?'':' checked')+'>';
  stpShowPatternMsg(locked?'Pattern unlocked':'Pattern locked — other tabs cannot overwrite settings',locked?'var(--text-2)':'var(--gold)');
}
function stpShowPatternMsg(msg,color){const el=document.getElementById('stp-pattern-msg');if(!el)return;el.textContent=msg;el.style.color=color||'var(--gold)';el.style.opacity='1';clearTimeout(el._msgTimer);el._msgTimer=setTimeout(()=>{el.style.opacity='0';},4000);}
function stpClearPattern(){
  stpSetSubtab('generic');
  document.getElementById('stp-type').value='grid';
  document.getElementById('stp-width').value=1920;
  document.getElementById('stp-height').value=1080;
  document.getElementById('stp-projectors').value=1;
  const blendEl=document.getElementById('stp-blend-px');if(blendEl)blendEl.value=0;
  document.querySelectorAll('.stp-swatch').forEach(s=>s.style.borderColor='transparent');
  document.querySelectorAll('.stp-swatch').forEach(s=>{if(s.onclick&&s.onclick.toString().includes("'#000000'"))s.style.borderColor='#00c8d4';});
  const bgEl=document.getElementById('stp-bg');if(bgEl)bgEl.value='solid:#000000';
  const gradControls=document.getElementById('stp-grad-controls');if(gradControls)gradControls.style.display='none';
  const gradActive=document.getElementById('stp-grad-active');if(gradActive)gradActive.value='0';
  const defaults={'stp-ov-grid':true,'stp-ov-markers':false,'stp-ov-circles':false,'stp-ov-hcircles':false,'stp-ov-diag':false,'stp-ov-crosshair':true};
  Object.entries(defaults).forEach(([id,on])=>{const cb=document.getElementById(id),pill=cb?.closest('label');if(cb)cb.checked=on;if(pill)pill.classList.toggle('stp-pill-on',on);});
  const vcols=document.getElementById('stp-vcols');if(vcols)vcols.value=16;
  const hrows=document.getElementById('stp-hrows');if(hrows)hrows.value=9;
  const lw=document.getElementById('stp-lineweight');if(lw)lw.value=2;
  stpUpdateBlendCard();calcStandalonePattern();
  stpShowPatternMsg('Pattern cleared','var(--text-2)');
}

// ── SWEEP ANIMATION ────────────────────────────────────────────────────────
function stpToggleSweep(pill){const cb=document.getElementById('stp-ov-sweep');cb.checked=!cb.checked;_sweepActive=cb.checked;pill.classList.toggle('stp-pill-on',_sweepActive);const adjCard=document.getElementById('stp-adj-sweep');if(adjCard)adjCard.style.display=_sweepActive?'flex':'none';const canvas=document.getElementById('stp-sweep-canvas');if(canvas)canvas.style.display=_sweepActive?'block':'none';if(_sweepActive){_sweepLastTs=null;_sweepRAF=requestAnimationFrame(stpSweepLoop);}else{if(_sweepRAF){cancelAnimationFrame(_sweepRAF);_sweepRAF=null;}if(canvas){const ctx=canvas.getContext('2d');ctx.clearRect(0,0,canvas.width,canvas.height);}stpSweepSendToPopouts(-1,-1);}}
function stpSweepUpdateConfig(){_sweepConfig.weight=parseInt(document.getElementById('stp-sweep-weight')?.value)||3;_sweepConfig.speed=parseInt(document.getElementById('stp-sweep-speed')?.value)||3;}
function stpSweepLoop(ts){if(!_sweepActive)return;if(ts&&_sweepLastTs!==null){const dt=Math.min(ts-_sweepLastTs,100),cyclesPerSec=_sweepConfig.speed*0.05,delta=(dt/1000)*cyclesPerSec;_sweepPosH=(_sweepPosH+delta)%1;_sweepPosV=(_sweepPosV+delta)%1;}_sweepLastTs=ts||performance.now();const canvas=document.getElementById('stp-sweep-canvas'),container=canvas?canvas.parentElement:null;if(canvas&&container){const W=container.offsetWidth,H=container.offsetHeight;if(canvas.width!==W)canvas.width=W;if(canvas.height!==H)canvas.height=H;if(W>0&&H>0){const ctx=canvas.getContext('2d');ctx.clearRect(0,0,W,H);stpDrawSweepLines(ctx,W,H,_sweepPosH,_sweepPosV,_sweepConfig.weight);}}stpSweepSendToPopouts(_sweepPosH,_sweepPosV);_sweepRAF=requestAnimationFrame(stpSweepLoop);}
function stpDrawSweepLines(ctx,W,H,posH,posV,weight){const lw=parseFloat(document.getElementById('stp-sweep-weight')?.value)||weight;ctx.save();ctx.strokeStyle='#00c8d4';ctx.lineWidth=lw;ctx.beginPath();ctx.moveTo(0,posH*H);ctx.lineTo(W,posH*H);ctx.stroke();ctx.beginPath();ctx.moveTo(posV*W,0);ctx.lineTo(posV*W,H);ctx.stroke();ctx.restore();}
function stpSweepSendToPopouts(posH,posV){const outputs=(window._stpOutputs||[]).filter(w=>w&&!w.closed);if(!outputs.length)return;const msg={type:'sweep',posH,posV,weight:_sweepConfig.weight};outputs.forEach(pw=>{try{pw.postMessage(msg,'*');}catch(e){}});if(window._stpChannel){try{window._stpChannel.postMessage(msg);}catch(e){}}}

// ── INIT ───────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  try { calcStandalonePattern(); } catch(e) { console.error('Pattern init:', e); }
});
</script>

<?php include __DIR__ . '/../footer.php'; ?>
