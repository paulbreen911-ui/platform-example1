<?php

$page_title = 'CIE Chromaticity Plotter';

require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/functions.php';
include ROOT_PATH . '/required/header.php';
?>

<style>
/* ── TOOL PAGE OVERRIDES ────────────────────────────────────────── */
/*
   The tool canvas needs full-height below the nav. We pull <main>
   back to edge-to-edge and override its default padding here rather
   than in the global stylesheet so nothing else is affected.
*/
main {
  padding-top: 0;
  display: flex;
  flex-direction: column;
  height: calc(100vh - var(--nav-h));
  overflow: hidden;
}

/* ── CSS VARIABLES — bridge CIE tool palette to PC design tokens ── */
:root {
  --cie-bg:        var(--black);        /* #0E0E0C */
  --cie-surface:   var(--dark);         /* #1A1A17 */
  --cie-panel:     var(--dark-2);       /* #242420 */
  --cie-border:    var(--border);       /* rgba(255,255,255,.07) */
  --cie-accent:    var(--gold);         /* #C9A84C */
  --cie-accent2:   #00c8d4;
  --cie-text:      var(--text-1);       /* #E8E6DC */
  --cie-dim:       var(--text-3);
  --cie-danger:    #e85050;
  --cie-mono:      'DM Sans', system-ui, sans-serif;
}

/* ── TOOL HEADER BAR ─────────────────────────────────────────────── */
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
.tool-hd-left {
  display: flex;
  align-items: center;
  gap: 16px;
}
.tool-hd-back {
  font-size: 11px;
  letter-spacing: .5px;
  color: var(--text-3);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: color .15s;
  white-space: nowrap;
}
.tool-hd-back:hover { color: var(--text-1); }
.tool-hd-divider {
  width: .5px; height: 20px;
  background: var(--border);
}
.tool-hd-title {
  font-family: var(--disp);
  font-size: 20px;
  letter-spacing: 1.5px;
  color: var(--text-1);
  line-height: 1;
}
.tool-hd-sub {
  font-size: 10px;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--text-3);
  margin-top: 2px;
}
.tool-hd-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* ── BUTTONS — use PC token colours ─────────────────────────────── */
.tc-btn {
  font-family: var(--sans);
  font-size: 11px;
  letter-spacing: .5px;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 6px;
  border: .5px solid var(--border);
  cursor: pointer;
  transition: all .15s;
  white-space: nowrap;
}
.tc-btn-ghost {
  background: transparent;
  color: var(--text-3);
}
.tc-btn-ghost:hover {
  border-color: var(--gold-bd);
  color: var(--gold);
}
.tc-btn-gold {
  background: rgba(201,168,76,.15);
  color: var(--gold);
  border-color: var(--gold-bd);
  font-weight: 600;
}
.tc-btn-gold:hover {
  background: rgba(201,168,76,.25);
  border-color: var(--gold);
}
.tc-btn-teal {
  background: rgba(0,200,212,.12);
  color: #00c8d4;
  border-color: rgba(0,200,212,.3);
  font-weight: 600;
}
.tc-btn-teal:hover {
  background: rgba(0,200,212,.22);
}

/* ── MAIN APP LAYOUT ─────────────────────────────────────────────── */
.cie-app {
  display: grid;
  grid-template-columns: 320px 1fr;
  flex: 1;
  overflow: hidden;
}

/* ── SIDEBAR ─────────────────────────────────────────────────────── */
.cie-sidebar {
  border-right: .5px solid var(--border);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: var(--dark);
}
.cie-sidebar-hd {
  padding: 11px 16px;
  border-bottom: .5px solid var(--border);
  font-size: 10px;
  letter-spacing: 2px;
  color: var(--text-3);
  text-transform: uppercase;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.cie-color-list {
  flex: 1;
  overflow-y: auto;
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 7px;
}
.cie-color-list::-webkit-scrollbar { width: 3px; }
.cie-color-list::-webkit-scrollbar-track { background: transparent; }
.cie-color-list::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,.1);
  border-radius: 2px;
}

/* ── COLOR CARD ──────────────────────────────────────────────────── */
.color-card {
  background: var(--dark-2);
  border: .5px solid var(--border);
  border-radius: 8px;
  padding: 11px 12px;
  position: relative;
  transition: border-color .15s;
  animation: slideIn .18s ease;
}
.color-card:hover { border-color: rgba(255,255,255,.12); }
.color-card.active { border-color: var(--gold-bd); }

@keyframes slideIn {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: translateY(0); }
}
.card-top {
  display: flex;
  align-items: center;
  gap: 9px;
  margin-bottom: 9px;
}
.swatch {
  width: 32px; height: 32px;
  border-radius: 5px;
  border: .5px solid rgba(255,255,255,.12);
  flex-shrink: 0;
  cursor: pointer;
  position: relative;
}
.swatch input[type="color"] {
  opacity: 0;
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
  border: none;
}
.card-name {
  flex: 1;
  font-size: 12px;
  font-weight: 500;
  color: var(--text-1);
  background: transparent;
  border: none;
  border-bottom: .5px solid var(--border);
  padding: 2px 0;
  outline: none;
  font-family: var(--sans);
}
.card-name:focus { border-bottom-color: var(--gold); }
.card-delete {
  background: transparent;
  border: none;
  color: var(--text-3);
  cursor: pointer;
  font-size: 16px;
  line-height: 1;
  padding: 2px;
  transition: color .15s;
}
.card-delete:hover { color: var(--cie-danger); }

.input-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px;
}
.input-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.input-group label {
  font-size: 9px;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--text-3);
}
.input-group input,
.input-group select {
  background: var(--black);
  border: .5px solid var(--border);
  border-radius: 4px;
  color: var(--text-1);
  font-family: var(--sans);
  font-size: 11px;
  padding: 5px 7px;
  outline: none;
  transition: border-color .15s;
  width: 100%;
}
.input-group input:focus { border-color: #00c8d4; }
.input-group input.error { border-color: var(--cie-danger); }

.card-coords {
  margin-top: 7px;
  font-size: 10px;
  color: var(--text-3);
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.card-coords span { color: #00c8d4; }

/* Gamut badges */
.gamut-badge {
  display: inline-block;
  font-size: 9px;
  letter-spacing: 1px;
  padding: 2px 7px;
  border-radius: 3px;
  margin-top: 5px;
  text-transform: uppercase;
}
.in709   { background: rgba(0,200,80,.10);  color: #00c850; border: .5px solid rgba(0,200,80,.25); }
.out709  { background: rgba(201,168,76,.10); color: var(--gold); border: .5px solid var(--gold-bd); }
.inP3    { background: rgba(0,200,212,.10);  color: #00c8d4; border: .5px solid rgba(0,200,212,.25); }
.out2020 { background: rgba(232,80,80,.10);  color: var(--cie-danger); border: .5px solid rgba(232,80,80,.25); }

/* ── ADD FORM ────────────────────────────────────────────────────── */
.cie-add-form {
  padding: 13px 14px;
  border-top: .5px solid var(--border);
  background: var(--black);
  flex-shrink: 0;
}
.cie-add-form-hd {
  font-size: 9px;
  letter-spacing: 2px;
  color: var(--text-3);
  text-transform: uppercase;
  margin-bottom: 9px;
}
.quick-presets {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-top: 8px;
}
.preset-btn {
  font-size: 10px;
  padding: 3px 8px;
  border-radius: 4px;
  border: .5px solid var(--border);
  background: transparent;
  color: var(--text-3);
  cursor: pointer;
  transition: all .12s;
  font-family: var(--sans);
}
.preset-btn:hover {
  border-color: var(--gold-bd);
  color: var(--gold);
}

/* ── CHART AREA ──────────────────────────────────────────────────── */
.cie-chart-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 16px;
  position: relative;
  overflow: hidden;
  background: var(--black);
}
canvas#cie-canvas {
  max-width: 100%;
  max-height: calc(100vh - var(--nav-h) - 90px);
  border-radius: 8px;
  display: block;
}

/* ── INFO / LEGEND BAR ───────────────────────────────────────────── */
.cie-info-bar {
  border-top: .5px solid var(--border);
  padding: 9px 20px;
  display: flex;
  gap: 20px;
  align-items: center;
  font-size: 10px;
  color: var(--text-3);
  flex-wrap: wrap;
  background: var(--dark);
  flex-shrink: 0;
}
.cie-legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
}
.cie-legend-line {
  width: 18px; height: 2px;
  border-radius: 1px;
}

/* ── TOOLTIP ─────────────────────────────────────────────────────── */
#cie-tooltip {
  position: fixed;
  background: var(--dark-2);
  border: .5px solid var(--border);
  border-radius: 8px;
  padding: 9px 13px;
  font-size: 11px;
  color: var(--text-1);
  pointer-events: none;
  display: none;
  z-index: 600;
  box-shadow: 0 8px 24px rgba(0,0,0,.6);
  max-width: 210px;
  line-height: 1.7;
}

/* ── PRINT ───────────────────────────────────────────────────────── */
@media print {
  .nav, .tool-hd, .cie-add-form, .tc-btn { display: none !important; }
  main { height: auto; overflow: visible; }
  .cie-app { display: block; }
  .cie-sidebar { border: none; width: 100%; }
  .color-card { background: #f5f5f5; border-color: #ccc; break-inside: avoid; }
  canvas#cie-canvas { max-width: 100%; page-break-before: always; }
  #cie-tooltip { display: none !important; }
}

/* ── RESPONSIVE ──────────────────────────────────────────────────── */
@media (max-width: 860px) {
  .cie-app { grid-template-columns: 1fr; grid-template-rows: auto 1fr; }
  .cie-sidebar {
    max-height: 280px;
    border-right: none;
    border-bottom: .5px solid var(--border);
  }
  main { height: auto; overflow: auto; }
  canvas#cie-canvas { max-height: 55vw; }
  .tool-hd-sub { display: none; }
}
</style>

<!-- TOOL HEADER -->
<div class="tool-hd">
  <div class="tool-hd-left">
    <a class="tool-hd-back" href="/tools.php">← Tools</a>
    <div class="tool-hd-divider"></div>
    <div>
      <div class="tool-hd-title">Chromaticity Plotter</div>
      <div class="tool-hd-sub">CIE 1931 xy — Brand Color Analysis</div>
    </div>
  </div>
  <div class="tool-hd-actions">
    <button class="tc-btn tc-btn-ghost" onclick="clearAll()">Clear All</button>
    <button class="tc-btn tc-btn-teal" onclick="addColorFromForm()">+ Add Color</button>
    <button class="tc-btn tc-btn-gold" onclick="window.print()">⎙ Print / PDF</button>
  </div>
</div>

<!-- APP LAYOUT -->
<div class="cie-app">

  <!-- SIDEBAR -->
  <aside class="cie-sidebar">
    <div class="cie-sidebar-hd">
      <span>Color Entries</span>
      <span id="count-badge">0 colors</span>
    </div>
    <div class="cie-color-list" id="color-list"></div>

    <div class="cie-add-form">
      <div class="cie-add-form-hd">Add Color</div>
      <div class="input-grid">
        <div class="input-group">
          <label>Name</label>
          <input type="text" id="new-name" placeholder="e.g. T-Mobile Magenta">
        </div>
        <div class="input-group">
          <label>Input Format</label>
          <select id="new-format" onchange="toggleFormatInput()">
            <option value="hex">HEX</option>
            <option value="rgb">RGB</option>
            <option value="pantone">Pantone (approx)</option>
            <option value="lab">CIE L*a*b*</option>
            <option value="xy">CIE xy direct</option>
          </select>
        </div>

        <div class="input-group" id="input-hex" style="grid-column:1/-1">
          <label>Hex Code</label>
          <input type="text" id="new-hex" placeholder="#E20074" maxlength="9">
        </div>
        <div class="input-group" id="input-r" style="display:none"><label>R (0–255)</label><input type="number" id="new-r" placeholder="226" min="0" max="255"></div>
        <div class="input-group" id="input-g" style="display:none"><label>G (0–255)</label><input type="number" id="new-g" placeholder="0"   min="0" max="255"></div>
        <div class="input-group" id="input-b" style="display:none"><label>B (0–255)</label><input type="number" id="new-b" placeholder="122" min="0" max="255"></div>
        <div class="input-group" id="input-pantone" style="display:none;grid-column:1/-1">
          <label>Pantone PMS Code</label>
          <input type="text" id="new-pantone" placeholder="e.g. 219 C or 2685 C">
        </div>
        <div class="input-group" id="input-L"     style="display:none"><label>L* (0–100)</label>    <input type="number" id="new-L"     placeholder="50" min="0"    max="100"></div>
        <div class="input-group" id="input-a"     style="display:none"><label>a* (−128–127)</label> <input type="number" id="new-a"     placeholder="0"  min="-128" max="127"></div>
        <div class="input-group" id="input-bstar" style="display:none"><label>b* (−128–127)</label> <input type="number" id="new-bstar" placeholder="0"  min="-128" max="127"></div>
        <div class="input-group" id="input-cx"    style="display:none"><label>x (0.0–0.8)</label>   <input type="number" id="new-cx"    placeholder="0.31" min="0" max="0.8" step="0.001"></div>
        <div class="input-group" id="input-cy"    style="display:none"><label>y (0.0–0.9)</label>   <input type="number" id="new-cy"    placeholder="0.33" min="0" max="0.9" step="0.001"></div>
      </div>

      <div style="margin-top:9px;display:flex;gap:6px;align-items:flex-end;">
        <div class="input-group" style="flex:0 0 auto;">
          <label>Marker Color</label>
          <input type="color" id="new-markercolor" value="#C9A84C"
                 style="width:46px;height:28px;padding:2px;background:var(--black);border:.5px solid var(--border);border-radius:4px;cursor:pointer;">
        </div>
        <button class="tc-btn tc-btn-teal" style="flex:1;margin-bottom:0" onclick="addColorFromForm()">+ Add</button>
      </div>

      <!-- Quick presets -->
      <div style="margin-top:11px">
        <div style="font-size:9px;letter-spacing:2px;color:var(--text-3);text-transform:uppercase;margin-bottom:5px;">Quick Load Brands</div>
        <div class="quick-presets">
          <button class="preset-btn" onclick="addPreset('T-Mobile Magenta','#E20074','#E20074')">T-Mobile</button>
          <button class="preset-btn" onclick="addPreset('Home Depot Orange','#F96302','#F96302')">Home Depot</button>
          <button class="preset-btn" onclick="addPreset('John Deere Green','#367C2B','#5abf3a')">John Deere</button>
          <button class="preset-btn" onclick="addPreset('Cadbury Purple','#3D1152','#9b59b6')">Cadbury</button>
          <button class="preset-btn" onclick="addPreset('FedEx Purple','#4D148C','#8e44ad')">FedEx</button>
          <button class="preset-btn" onclick="addPreset('Tiffany Blue','#81D8D0','#81D8D0')">Tiffany</button>
          <button class="preset-btn" onclick="addPreset('Barbie Pink','#E0218A','#E0218A')">Barbie</button>
          <button class="preset-btn" onclick="addPreset('D65 White','#FFFFFF','#ffffff')">D65 White</button>
        </div>
      </div>
    </div><!-- /add-form -->
  </aside>

  <!-- CHART -->
  <div class="cie-chart-wrap">
    <canvas id="cie-canvas"></canvas>
  </div>

</div><!-- /cie-app -->

<!-- LEGEND / INFO BAR -->
<div class="cie-info-bar">
  <span style="color:var(--text-1);font-weight:500">Gamut boundaries:</span>
  <div class="cie-legend-item"><div class="cie-legend-line" style="background:#ffffff;opacity:.55"></div><span>Rec.709 / sRGB</span></div>
  <div class="cie-legend-item"><div class="cie-legend-line" style="background:#00c8d4"></div><span>DCI-P3</span></div>
  <div class="cie-legend-item"><div class="cie-legend-line" style="background:var(--gold)"></div><span>Rec.2020</span></div>
  <div class="cie-legend-item"><div class="cie-legend-line" style="background:#ff6060;width:6px;height:6px;border-radius:50%"></div><span>D65 White Point</span></div>
  <span style="margin-left:auto;color:var(--text-3)">Hover points for details · Click swatch to change color</span>
</div>

<div id="cie-tooltip"></div>

<script>
// ─────────────────────────────────────────────────────────────────
//  DATA
// ─────────────────────────────────────────────────────────────────
let colors = [];
let idCounter = 0;

// ─────────────────────────────────────────────────────────────────
//  CIE 1931 SPECTRAL LOCUS
// ─────────────────────────────────────────────────────────────────
const spectralLocus = [
  [0.1741,0.0050],[0.1740,0.0050],[0.1738,0.0049],[0.1736,0.0049],
  [0.1733,0.0048],[0.1730,0.0048],[0.1726,0.0048],[0.1721,0.0048],
  [0.1714,0.0051],[0.1703,0.0058],[0.1689,0.0069],[0.1669,0.0086],
  [0.1644,0.0109],[0.1611,0.0138],[0.1566,0.0177],[0.1510,0.0227],
  [0.1440,0.0297],[0.1355,0.0399],[0.1241,0.0578],[0.1096,0.0868],
  [0.0913,0.1327],[0.0687,0.2007],[0.0454,0.2950],[0.0235,0.4127],
  [0.0082,0.5384],[0.0039,0.6548],[0.0139,0.7502],[0.0389,0.8120],
  [0.0743,0.8338],[0.1142,0.8262],[0.1547,0.8059],[0.1929,0.7816],
  [0.2296,0.7543],[0.2658,0.7243],[0.3016,0.6923],[0.3373,0.6589],
  [0.3731,0.6245],[0.4087,0.5896],[0.4441,0.5547],[0.4788,0.5202],
  [0.5125,0.4866],[0.5448,0.4544],[0.5752,0.4242],[0.6029,0.3965],
  [0.6270,0.3725],[0.6482,0.3514],[0.6658,0.3340],[0.6801,0.3197],
  [0.6915,0.3083],[0.7006,0.2993],[0.7079,0.2920],[0.7140,0.2859],
  [0.7190,0.2809],[0.7230,0.2770],[0.7260,0.2740],[0.7283,0.2717],
  [0.7300,0.2700],[0.7311,0.2689],[0.7320,0.2680],[0.7327,0.2673],
  [0.1741,0.0050]
];

// Gamut primaries in xy
const REC709  = { r:[0.64,0.33],  g:[0.30,0.60],  b:[0.15,0.06],  w:[0.3127,0.3290] };
const P3      = { r:[0.680,0.320],g:[0.265,0.690], b:[0.150,0.060],w:[0.3127,0.3290] };
const REC2020 = { r:[0.708,0.292],g:[0.170,0.797], b:[0.131,0.046],w:[0.3127,0.3290] };

// ─────────────────────────────────────────────────────────────────
//  COLOR MATH
// ─────────────────────────────────────────────────────────────────
function hexToRgb(hex) {
  hex = hex.replace(/^#/, '');
  if (hex.length === 3) hex = hex.split('').map(c=>c+c).join('');
  const n = parseInt(hex, 16);
  return { r:(n>>16)&255, g:(n>>8)&255, b:n&255 };
}
function srgbLinear(c) {
  c /= 255;
  return c <= 0.04045 ? c/12.92 : Math.pow((c+0.055)/1.055, 2.4);
}
function rgbToXYZ(r,g,b) {
  const rl=srgbLinear(r), gl=srgbLinear(g), bl=srgbLinear(b);
  return {
    X: 0.4124564*rl + 0.3575761*gl + 0.1804375*bl,
    Y: 0.2126729*rl + 0.7151522*gl + 0.0721750*bl,
    Z: 0.0193339*rl + 0.1191920*gl + 0.9503041*bl
  };
}
function xyzToXY(X,Y,Z) {
  const s = X+Y+Z;
  if (s===0) return {x:0.3127,y:0.3290};
  return {x:X/s, y:Y/s};
}
function labToXYZ(L,a,b) {
  const Xn=0.95047, Yn=1.00000, Zn=1.08883;
  const fy=(L+16)/116, fx=a/500+fy, fz=fy-b/500;
  const x=fx>0.206897?fx**3:(fx-16/116)/7.787;
  const y=L>7.9996?((L+16)/116)**3:L/903.3;
  const z=fz>0.206897?fz**3:(fz-16/116)/7.787;
  return {X:x*Xn, Y:y*Yn, Z:z*Zn};
}

const pantoneMap = {
  '219c':'#E20074','219 c':'#E20074','219':'#D4006A',
  '165c':'#F96302','165 c':'#F96302','165':'#F96302',
  '364c':'#367C2B','364 c':'#367C2B','364':'#367C2B',
  '012c':'#FDE100','012 c':'#FDE100','012':'#FDE100',
  '2685c':'#3D1152','2685 c':'#3D1152','2685':'#3D1152',
  '1837':'#81D8D0','1837c':'#81D8D0',
  '17-5641':'#009473','661c':'#003087',
  '485c':'#DA291C','485':'#DA291C',
  '021c':'#FF6B35','021':'#FF6B35',
  '032c':'#EF3340','032':'#EF3340',
  '286c':'#0032A0','355c':'#009A44',
  '2736c':'#3C1874','2736':'#3C1874',
  'reflex blue':'#001489','warm red':'#F9423A',
  'rubine red':'#CE0058','rhodamine red':'#E10098',
  'process blue':'#0085CA','green':'#00AB84',
  'yellow':'#FEDD00','orange 021':'#FE5000',
  'black':'#2B2B2B'
};

function parsePantone(code) {
  return pantoneMap[code.toLowerCase().trim()] || null;
}

function resolveToXY(entry) {
  let rgb = null;
  switch(entry.format) {
    case 'hex':
      try { rgb = hexToRgb(entry.hex); } catch(e){}
      break;
    case 'rgb':
      rgb = {r:+entry.r, g:+entry.g, b:+entry.b};
      break;
    case 'pantone': {
      const h = parsePantone(entry.pantone||'');
      if (h) rgb = hexToRgb(h);
      break;
    }
    case 'lab': {
      const xyz = labToXYZ(+entry.L, +entry.a, +entry.bstar);
      return xyzToXY(xyz.X, xyz.Y, xyz.Z);
    }
    case 'xy':
      return {x:+entry.cx, y:+entry.cy};
  }
  if (!rgb) return null;
  const xyz = rgbToXYZ(rgb.r, rgb.g, rgb.b);
  return xyzToXY(xyz.X, xyz.Y, xyz.Z);
}

// ─────────────────────────────────────────────────────────────────
//  GAMUT TESTS
// ─────────────────────────────────────────────────────────────────
function sign(p1,p2,p3) {
  return (p1[0]-p3[0])*(p2[1]-p3[1]) - (p2[0]-p3[0])*(p1[1]-p3[1]);
}
function inTriangle(pt,v1,v2,v3) {
  const d1=sign(pt,v1,v2), d2=sign(pt,v2,v3), d3=sign(pt,v3,v1);
  return !((d1<0||d2<0||d3<0) && (d1>0||d2>0||d3>0));
}
function getGamutStatus(x,y) {
  const pt=[x,y];
  if (inTriangle(pt,REC709.r,REC709.g,REC709.b))    return {label:'Within Rec.709',              cls:'in709'};
  if (inTriangle(pt,P3.r,P3.g,P3.b))                return {label:'Outside Rec.709 · Within P3', cls:'inP3'};
  if (inTriangle(pt,REC2020.r,REC2020.g,REC2020.b)) return {label:'Outside P3 · Within Rec.2020',cls:'out709'};
  return {label:'Outside Rec.2020', cls:'out2020'};
}

// ─────────────────────────────────────────────────────────────────
//  CANVAS RENDERING
// ─────────────────────────────────────────────────────────────────
const canvas = document.getElementById('cie-canvas');
const ctx    = canvas.getContext('2d');

function resizeCanvas() {
  const wrap = canvas.parentElement;
  const size = Math.min(wrap.clientWidth - 32, wrap.clientHeight - 32, 740);
  canvas.width  = size;
  canvas.height = size;
  drawChart();
}

function toCanvas(x,y,W,H) {
  const pad = W * 0.09;
  const cx = pad + (x/0.85)*(W-2*pad);
  const cy = (H-pad) - (y/0.95)*(H-2*pad);
  return [cx,cy];
}

function drawGamutTriangle(gamut,color,lw,alpha) {
  const [W,H] = [canvas.width,canvas.height];
  const pts = [gamut.r,gamut.g,gamut.b].map(([x,y])=>toCanvas(x,y,W,H));
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(pts[0][0],pts[0][1]);
  pts.slice(1).forEach(p=>ctx.lineTo(p[0],p[1]));
  ctx.closePath();
  ctx.strokeStyle=color; ctx.globalAlpha=alpha;
  ctx.lineWidth=lw; ctx.setLineDash([]);
  ctx.stroke(); ctx.restore();
}

function pointInLocus(x,y) {
  let inside=false;
  const poly=spectralLocus;
  for(let i=0,j=poly.length-1;i<poly.length;j=i++){
    const [xi,yi]=poly[i],[xj,yj]=poly[j];
    if(((yi>y)!=(yj>y))&&(x<(xj-xi)*(y-yi)/(yj-yi)+xi)) inside=!inside;
  }
  return inside;
}

function drawChart() {
  const W=canvas.width, H=canvas.height;
  ctx.clearRect(0,0,W,H);

  // Background — use PC dark token
  ctx.fillStyle='#0E0E0C';
  ctx.fillRect(0,0,W,H);

  const pts=spectralLocus.map(([x,y])=>toCanvas(x,y,W,H));

  // Locus fill (radial gradient tint)
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(pts[0][0],pts[0][1]);
  for(let i=1;i<pts.length;i++) ctx.lineTo(pts[i][0],pts[i][1]);
  ctx.closePath();
  const grad=ctx.createRadialGradient(W*.35,H*.45,10,W*.35,H*.45,W*.45);
  grad.addColorStop(0,'rgba(255,255,255,0.10)');
  grad.addColorStop(0.3,'rgba(0,255,0,0.07)');
  grad.addColorStop(0.6,'rgba(255,0,100,0.07)');
  grad.addColorStop(1,'rgba(0,50,255,0.05)');
  ctx.fillStyle=grad; ctx.fill(); ctx.restore();

  // CIE colour fill — full-resolution ImageData pixel write (no blockiness)
  const imgData = ctx.createImageData(W, H);
  const buf     = imgData.data;
  const pad2    = W * 0.09;
  const gm = c => c <= 0.0031308 ? 12.92*c : 1.055*Math.pow(c, 1/2.4) - 0.055;
  for (let py = 0; py < H; py++) {
    for (let px = 0; px < W; px++) {
      const cx = ((px - pad2) / (W - 2*pad2)) * 0.85;
      const cy = ((H - pad2 - py) / (H - 2*pad2)) * 0.95;
      if (cx < 0 || cy < 0 || cx > 0.85 || cy > 0.95) continue;
      if (!pointInLocus(cx, cy)) continue;
      const Yv = 0.5, Xv = (cx/cy)*Yv, Zv = ((1-cx-cy)/cy)*Yv;
      let r =  3.2404542*Xv - 1.5371385*Yv - 0.4985314*Zv;
      let g = -0.9692660*Xv + 1.8760108*Yv + 0.0415560*Zv;
      let b =  0.0556434*Xv - 0.2040259*Yv + 1.0572252*Zv;
      const mx = Math.max(r, g, b);
      if (mx > 0) { r /= mx; g /= mx; b /= mx; }
      r = Math.max(0, Math.min(1, r));
      g = Math.max(0, Math.min(1, g));
      b = Math.max(0, Math.min(1, b));
      const idx = (py * W + px) * 4;
      buf[idx]   = Math.round(gm(r) * 255);
      buf[idx+1] = Math.round(gm(g) * 255);
      buf[idx+2] = Math.round(gm(b) * 255);
      buf[idx+3] = 184; // ~72% opacity
    }
  }
  ctx.putImageData(imgData, 0, 0);

  // Locus outline
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(pts[0][0],pts[0][1]);
  for(let i=1;i<pts.length;i++) ctx.lineTo(pts[i][0],pts[i][1]);
  ctx.closePath();
  ctx.strokeStyle='rgba(255,255,255,0.35)'; ctx.lineWidth=1; ctx.stroke();
  ctx.restore();

  // Gamut triangles — use PC gold + teal
  drawGamutTriangle(REC2020,'#C9A84C',1.5,0.9);
  drawGamutTriangle(P3,'#00c8d4',1.5,0.9);
  drawGamutTriangle(REC709,'rgba(255,255,255,0.65)',1.5,0.9);

  // Grid / axes
  ctx.save();
  ctx.strokeStyle='rgba(255,255,255,0.05)'; ctx.lineWidth=0.5;
  const pad=W*0.09;
  for(let v=0;v<=0.8;v+=0.1){
    const [gx]=toCanvas(v,0,W,H);
    ctx.beginPath(); ctx.moveTo(gx,pad*.3); ctx.lineTo(gx,H-pad*.3); ctx.stroke();
    ctx.fillStyle='rgba(255,255,255,0.25)';
    ctx.font=`${Math.round(W*0.02)}px DM Sans,sans-serif`;
    ctx.fillText(v.toFixed(1),gx-8,H-pad*.05);
  }
  for(let v=0;v<=0.9;v+=0.1){
    const [,gy]=toCanvas(0,v,W,H);
    ctx.beginPath(); ctx.moveTo(pad*.3,gy); ctx.lineTo(W-pad*.3,gy); ctx.stroke();
    ctx.fillStyle='rgba(255,255,255,0.25)';
    ctx.fillText(v.toFixed(1),4,gy+4);
  }
  ctx.fillStyle='rgba(255,255,255,0.4)';
  ctx.font=`bold ${Math.round(W*0.026)}px DM Sans,sans-serif`;
  ctx.fillText('x',W/2,H-2);
  ctx.save(); ctx.translate(11,H/2); ctx.rotate(-Math.PI/2);
  ctx.fillText('y',0,0); ctx.restore();
  ctx.restore();

  // D65 white point
  const [wx,wy]=toCanvas(0.3127,0.3290,W,H);
  ctx.beginPath(); ctx.arc(wx,wy,5,0,Math.PI*2);
  ctx.fillStyle='#ff6060'; ctx.fill();
  ctx.strokeStyle='#fff'; ctx.lineWidth=1; ctx.stroke();

  // Gamut labels
  ctx.save();
  ctx.font=`${Math.round(W*0.021)}px DM Sans,sans-serif`;
  ctx.fillStyle='rgba(255,255,255,0.45)';  ctx.fillText('Rec.709',...toCanvas(0.37,0.50,W,H));
  ctx.fillStyle='rgba(0,200,212,0.75)';    ctx.fillText('P3',...toCanvas(0.50,0.62,W,H));
  ctx.fillStyle='rgba(201,168,76,0.75)';   ctx.fillText('Rec.2020',...toCanvas(0.56,0.73,W,H));
  ctx.restore();

  // Plot brand colors
  colors.forEach((entry,i)=>{
    const xy=resolveToXY(entry);
    if(!xy) return;
    const [px,py]=toCanvas(xy.x,xy.y,W,H);
    const mc=entry.markerColor||'#C9A84C';

    // Glow halo
    ctx.save();
    const glow=ctx.createRadialGradient(px,py,2,px,py,16);
    glow.addColorStop(0,mc+'bb'); glow.addColorStop(1,mc+'00');
    ctx.fillStyle=glow; ctx.beginPath(); ctx.arc(px,py,16,0,Math.PI*2); ctx.fill();
    ctx.restore();

    // Dot
    ctx.beginPath(); ctx.arc(px,py,7,0,Math.PI*2);
    ctx.fillStyle=mc; ctx.strokeStyle='#fff'; ctx.lineWidth=1.5;
    ctx.fill(); ctx.stroke();

    // Index number
    ctx.fillStyle='#000';
    ctx.font=`bold ${Math.round(W*0.021)}px DM Sans,sans-serif`;
    ctx.textAlign='center'; ctx.textBaseline='middle';
    ctx.fillText(i+1,px,py);
    ctx.textAlign='left'; ctx.textBaseline='alphabetic';

    // Label
    ctx.fillStyle='#fff';
    ctx.font=`${Math.round(W*0.021)}px DM Sans,sans-serif`;
    ctx.fillText(entry.name||`Color ${i+1}`,px+10,py-6);

    entry._px=px; entry._py=py; entry._xy=xy;
  });
}

// ─────────────────────────────────────────────────────────────────
//  CARD LIST
// ─────────────────────────────────────────────────────────────────
function renderColorList() {
  const list=document.getElementById('color-list');
  list.innerHTML='';
  document.getElementById('count-badge').textContent=`${colors.length} color${colors.length!==1?'s':''}`;
  colors.forEach((entry,i)=>{
    const xy=resolveToXY(entry);
    const gs=xy?getGamutStatus(xy.x,xy.y):null;
    const dh=entry.markerColor||'#808080';
    const card=document.createElement('div');
    card.className='color-card'; card.dataset.id=entry.id;
    card.innerHTML=`
      <div class="card-top">
        <div class="swatch" style="background:${dh}">
          <input type="color" value="${dh}" onchange="updateMarkerColor(${entry.id},this.value)">
        </div>
        <input class="card-name" type="text" value="${(entry.name||'').replace(/"/g,'&quot;')}" placeholder="Color name"
               onchange="updateName(${entry.id},this.value)">
        <button class="card-delete" onclick="deleteColor(${entry.id})">×</button>
      </div>
      <div class="card-coords">
        ${xy?`x: <span>${xy.x.toFixed(4)}</span> &nbsp;y: <span>${xy.y.toFixed(4)}</span>`:'<span style="color:var(--cie-danger)">Invalid / not found</span>'}
        ${xy?`— <span>${entry.format}</span>`:''}
      </div>
      ${gs?`<div class="gamut-badge ${gs.cls}">${gs.label}</div>`:''}
      <div style="margin-top:5px;font-size:10px;color:var(--text-3)">#${i+1} · ${formatSummary(entry)}</div>
    `;
    list.appendChild(card);
  });
}

function formatSummary(e) {
  switch(e.format){
    case 'hex':     return `HEX ${e.hex||''}`;
    case 'rgb':     return `RGB(${e.r},${e.g},${e.b})`;
    case 'pantone': return `Pantone ${e.pantone||''}`;
    case 'lab':     return `L*${e.L} a*${e.a} b*${e.bstar}`;
    case 'xy':      return `xy(${e.cx},${e.cy})`;
    default:        return '';
  }
}

// ─────────────────────────────────────────────────────────────────
//  FORM ACTIONS
// ─────────────────────────────────────────────────────────────────
function toggleFormatInput() {
  const fmt=document.getElementById('new-format').value;
  ['hex','r','g','b','pantone','L','a','bstar','cx','cy'].forEach(id=>{
    const el=document.getElementById('input-'+id);
    if(el) el.style.display='none';
  });
  if(fmt==='hex')     document.getElementById('input-hex').style.display='flex';
  if(fmt==='rgb')     ['r','g','b'].forEach(x=>document.getElementById('input-'+x).style.display='flex');
  if(fmt==='pantone') document.getElementById('input-pantone').style.display='flex';
  if(fmt==='lab')     ['L','a','bstar'].forEach(x=>document.getElementById('input-'+x).style.display='flex');
  if(fmt==='xy')      ['cx','cy'].forEach(x=>document.getElementById('input-'+x).style.display='flex');
}

function addColorFromForm() {
  const fmt=document.getElementById('new-format').value;
  const name=document.getElementById('new-name').value||`Color ${colors.length+1}`;
  const mc=document.getElementById('new-markercolor').value;
  const entry={id:++idCounter, name, format:fmt, markerColor:mc};
  switch(fmt){
    case 'hex':     entry.hex=document.getElementById('new-hex').value.trim(); break;
    case 'rgb':     entry.r=document.getElementById('new-r').value;
                    entry.g=document.getElementById('new-g').value;
                    entry.b=document.getElementById('new-b').value; break;
    case 'pantone': entry.pantone=document.getElementById('new-pantone').value.trim(); break;
    case 'lab':     entry.L=document.getElementById('new-L').value;
                    entry.a=document.getElementById('new-a').value;
                    entry.bstar=document.getElementById('new-bstar').value; break;
    case 'xy':      entry.cx=document.getElementById('new-cx').value;
                    entry.cy=document.getElementById('new-cy').value; break;
  }
  if(fmt==='hex'&&entry.hex)
    entry.markerColor=entry.hex.startsWith('#')?entry.hex:'#'+entry.hex;
  colors.push(entry);
  renderColorList(); drawChart();
  document.getElementById('new-name').value='';
}

function addPreset(name,hex,markerColor) {
  colors.push({id:++idCounter,name,format:'hex',hex,markerColor});
  renderColorList(); drawChart();
}
function deleteColor(id) {
  colors=colors.filter(c=>c.id!==id);
  renderColorList(); drawChart();
}
function updateName(id,val) {
  const c=colors.find(c=>c.id===id);
  if(c){c.name=val; drawChart();}
}
function updateMarkerColor(id,val) {
  const c=colors.find(c=>c.id===id);
  if(c){c.markerColor=val; renderColorList(); drawChart();}
}
function clearAll() {
  colors=[]; renderColorList(); drawChart();
}

// ─────────────────────────────────────────────────────────────────
//  HOVER TOOLTIP
// ─────────────────────────────────────────────────────────────────
canvas.addEventListener('mousemove',e=>{
  const rect=canvas.getBoundingClientRect();
  const mx=(e.clientX-rect.left)*(canvas.width/rect.width);
  const my=(e.clientY-rect.top)*(canvas.height/rect.height);
  const tt=document.getElementById('cie-tooltip');
  let found=false;
  for(const entry of colors){
    if(!entry._px) continue;
    const dx=mx-entry._px, dy=my-entry._py;
    if(Math.sqrt(dx*dx+dy*dy)<14){
      const xy=entry._xy;
      const gs=getGamutStatus(xy.x,xy.y);
      const gc=gs.cls==='in709'?'#00c850':gs.cls==='inP3'?'#00c8d4':gs.cls==='out709'?'#C9A84C':'#e85050';
      tt.innerHTML=`<b style="color:${entry.markerColor}">${entry.name||'Color'}</b><br>
        x: ${xy.x.toFixed(4)} · y: ${xy.y.toFixed(4)}<br>
        ${formatSummary(entry)}<br>
        <span style="color:${gc}">${gs.label}</span>`;
      tt.style.display='block';
      tt.style.left=(e.clientX+14)+'px';
      tt.style.top=(e.clientY-44)+'px';
      canvas.style.cursor='crosshair';
      found=true; break;
    }
  }
  if(!found){tt.style.display='none'; canvas.style.cursor='default';}
});
canvas.addEventListener('mouseleave',()=>{
  document.getElementById('cie-tooltip').style.display='none';
});

// ─────────────────────────────────────────────────────────────────
//  ENTER KEY SHORTCUT
// ─────────────────────────────────────────────────────────────────
document.addEventListener('keydown',e=>{
  if(e.key==='Enter'&&document.activeElement!==document.body) addColorFromForm();
});

// ─────────────────────────────────────────────────────────────────
//  INIT — load default presets
// ─────────────────────────────────────────────────────────────────
window.addEventListener('load',()=>{
  resizeCanvas();
  toggleFormatInput();
  [
    {name:'T-Mobile Magenta', hex:'#E20074', mc:'#E20074'},
    {name:'Home Depot Orange',hex:'#F96302', mc:'#F96302'},
    {name:'John Deere Green', hex:'#367C2B', mc:'#5abf3a'},
    {name:'Cadbury Purple',   hex:'#3D1152', mc:'#9b59b6'},
    {name:'FedEx Purple',     hex:'#4D148C', mc:'#8e44ad'},
    {name:'Tiffany Blue',     hex:'#81D8D0', mc:'#81D8D0'},
    {name:'Barbie Pink',      hex:'#E0218A', mc:'#E0218A'},
    {name:'John Deere Yellow',hex:'#FDE100', mc:'#FDE100'},
  ].forEach(p=>colors.push({id:++idCounter,name:p.name,format:'hex',hex:p.hex,markerColor:p.mc}));
  renderColorList(); drawChart();
});
window.addEventListener('resize', resizeCanvas);
</script>

<?php include ROOT_PATH . '/required/footer.php'; ?>
