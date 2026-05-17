/* ============================================================
   PRODUCTION CENTRAL — Design Token Editor
   /js/design-tokens.js
   ============================================================ */

// ── TOKEN STATE ────────────────────────────────────────────────

const DT = {
  // Backgrounds
  black:   '#16181F',
  dark:    '#1E2130',
  dark2:   '#252A3A',
  dark3:   '#2E3448',
  // Accent
  gold:      '#E8B84B',
  goldDimA:  14,   // percent
  goldBdA:   32,
  // Text (opacity %)
  text1A: 100,
  text2A: 65,
  text3A: 38,
  // Borders
  borderA:   11,
  borderLtA: 9,
  // Live
  live: '#E8350A',
  // Light mode surfaces
  bg:      '#F4F3EE',
  surface: '#FFFFFF',
  lt1:     '#1A1A17',
  lt2:     '#5A5A54',
  lt3:     '#9A9A92',
  // Nav
  navH:    60,
  navPadH: 36,
  logoSize: 21,
  logoLS:   2,
  navLinkSize: 12,
  // Type
  dispSize:  118,
  secTitle:  44,
  bodySize:  14,
  dispLS:    3,
  lineHeight: 1.6,
  // Shape
  radiusCard:  12,
  radiusBtn:   5,
  radiusInput: 7,
  // Layout
  sectionPadH: 60,
  sectionPadV: 72,
  cardPad:     20,
  gridGap:     12,
};

// Keep original defaults for reset
const DT_DEFAULTS = { ...DT };

// ── COLOR UTILITIES ────────────────────────────────────────────

function hexToRgb(h) {
  h = h.replace('#', '');
  if (h.length === 3) h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
  return [parseInt(h.slice(0,2),16), parseInt(h.slice(2,4),16), parseInt(h.slice(4,6),16)];
}
function rgbToHex(r,g,b) {
  return '#' + [r,g,b].map(x => Math.max(0,Math.min(255,Math.round(x))).toString(16).padStart(2,'0')).join('');
}
function hexToHsl(h) {
  let [r,g,b] = hexToRgb(h);
  r/=255; g/=255; b/=255;
  const mx = Math.max(r,g,b), mn = Math.min(r,g,b);
  let hh, s, l = (mx+mn)/2;
  if (mx === mn) { hh = s = 0; } else {
    const d = mx - mn;
    s = l > 0.5 ? d/(2-mx-mn) : d/(mx+mn);
    switch(mx) {
      case r: hh = ((g-b)/d + (g<b?6:0))/6; break;
      case g: hh = ((b-r)/d + 2)/6; break;
      case b: hh = ((r-g)/d + 4)/6; break;
    }
  }
  return [Math.round(hh*360), Math.round(s*100), Math.round(l*100)];
}
function hslToHex(h,s,l) {
  s/=100; l/=100;
  const a = s * Math.min(l, 1-l);
  const f = n => {
    const k = (n + h/30) % 12;
    const c = l - a * Math.max(-1, Math.min(k-3, 9-k, 1));
    return Math.round(255*c).toString(16).padStart(2,'0');
  };
  return `#${f(0)}${f(8)}${f(4)}`;
}

// ── CSS BUILDER ────────────────────────────────────────────────
// Produces a full :root {} string from DT state.

function buildRootCSS() {
  const [rG,gG,bG] = hexToRgb(DT.gold);
  const a = x => (x/100).toFixed(2);
  return `:root {
  --black:   ${DT.black};
  --dark:    ${DT.dark};
  --dark-2:  ${DT.dark2};
  --dark-3:  ${DT.dark3};
  --surface: ${DT.surface};
  --bg:      ${DT.bg};
  --border:    rgba(255, 255, 255, ${a(DT.borderA)});
  --border-lt: rgba(0, 0, 0, ${a(DT.borderLtA)});
  --text-1: rgba(240, 238, 230, ${a(DT.text1A)});
  --text-2: rgba(240, 238, 230, ${a(DT.text2A)});
  --text-3: rgba(240, 238, 230, ${a(DT.text3A)});
  --lt-1: ${DT.lt1};
  --lt-2: ${DT.lt2};
  --lt-3: ${DT.lt3};
  --gold:     ${DT.gold};
  --gold-dim: rgba(${rG}, ${gG}, ${bG}, ${a(DT.goldDimA)});
  --gold-bd:  rgba(${rG}, ${gG}, ${bG}, ${a(DT.goldBdA)});
  --live:     ${DT.live};
  --sans:  'DM Sans', system-ui, sans-serif;
  --serif: 'DM Serif Display', Georgia, serif;
  --disp:  'Bebas Neue', Impact, sans-serif;
  --nav-h: ${DT.navH}px;
}

/* ── Token editor overrides ── */
.nav { padding: 0 ${DT.navPadH}px; }
.nav-logo { font-size: ${DT.logoSize}px; letter-spacing: ${DT.logoLS}px; }
.nav-link { font-size: ${DT.navLinkSize}px; }
.hero-h1 { font-size: ${DT.dispSize}px; letter-spacing: ${DT.dispLS}px; }
.sec-title { font-size: ${DT.secTitle}px; }
body { font-size: ${DT.bodySize}px; line-height: ${DT.lineHeight}; }
.section { padding: ${DT.sectionPadV}px ${DT.sectionPadH}px; }
.tool-card, .edu-card, .ref-card, .fp, .sidebar-card, .profile-card,
.settings-card, .profile-ec-card { border-radius: ${DT.radiusCard}px; padding: ${DT.cardPad}px; }
.tool-card-lg { border-radius: ${DT.radiusCard}px; }
.btn-gold-lg, .btn-ghost-lg { border-radius: ${DT.radiusBtn}px; }
.btn-join, .btn-signin, .btn-demo { border-radius: ${DT.radiusBtn}px; }
.form-inp, .form-sel, .settings-textarea { border-radius: ${DT.radiusInput}px; }
.tools-grid, .forum-grid, .edu-grid, .ref-grid { gap: ${DT.gridGap}px; }`;
}

// ── IFRAME INJECTION ───────────────────────────────────────────
// Sends the CSS to the iframe via postMessage.

function dtInject() {
  const frame = document.getElementById('dt-frame');
  if (!frame || !frame.contentWindow) return;
  frame.contentWindow.postMessage({
    type: 'DT_TOKENS',
    css: buildRootCSS()
  }, window.location.origin);
}

// Retry injection on frame load (first load + page switches)
document.getElementById('dt-frame').addEventListener('load', () => {
  setTimeout(dtInject, 80);
});

// ── CONTROL HANDLERS ───────────────────────────────────────────

function dtColorDirect(key, val) {
  DT[key] = val;
  setVal(key, val);
  dtInject();
}

function dtColorL(key, lVal) {
  const [h, s] = hexToHsl(DT[key]);
  const newHex = hslToHex(h, s, parseInt(lVal));
  DT[key] = newHex;
  setVal(key, newHex);
  const pick = document.getElementById('pick-' + key);
  if (pick) pick.value = newHex;
  dtInject();
}

function dtHue(key, hVal) {
  const [, s, l] = hexToHsl(DT[key]);
  const newHex = hslToHex(parseInt(hVal), s, l);
  DT[key] = newHex;
  setVal(key, newHex);
  const pick = document.getElementById('pick-' + key);
  if (pick) pick.value = newHex;
  dtInject();
}

function dtGoldDirect(val) {
  DT.gold = val;
  setVal('gold', val);
  dtInject();
}

function dtGoldL(lVal) {
  const [h, s] = hexToHsl(DT.gold);
  const newHex = hslToHex(h, s, parseInt(lVal));
  DT.gold = newHex;
  setVal('gold', newHex);
  document.getElementById('pick-gold').value = newHex;
  dtInject();
}

function dtAlpha(key, val) {
  DT[key] = parseInt(val);
  setVal(key, val + '%');
  dtInject();
}

function dtPx(key, val) {
  DT[key] = parseInt(val);
  setVal(key, val + 'px');
  dtInject();
}

function dtDecPx(key, val) {
  DT[key] = parseFloat(val);
  setVal(key, parseFloat(val).toFixed(1) + 'px');
  dtInject();
}

function dtFloat(key, val) {
  DT[key] = parseFloat(val);
  setVal(key, parseFloat(val).toFixed(2));
  dtInject();
}

// Update the displayed value label
function setVal(key, display) {
  const el = document.getElementById('val-' + key);
  if (el) el.textContent = display;
}

// ── TABS ───────────────────────────────────────────────────────

function dtTab(name, btn) {
  ['colors','type','space','light','all'].forEach(t => {
    const p = document.getElementById('dt-panel-' + t);
    if (p) p.style.display = t === name ? 'block' : 'none';
  });
  document.querySelectorAll('.dt-tab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  if (name === 'all') dtBuildAllList();
}

function dtBuildAllList() {
  const [rG,gG,bG] = hexToRgb(DT.gold);
  const a = x => (x/100).toFixed(2);
  const rows = [
    ['--black', DT.black], ['--dark', DT.dark], ['--dark-2', DT.dark2], ['--dark-3', DT.dark3],
    ['--bg', DT.bg], ['--surface', DT.surface],
    ['--gold', DT.gold],
    ['--gold-dim', `rgba(${rG},${gG},${bG},${a(DT.goldDimA)})`],
    ['--gold-bd',  `rgba(${rG},${gG},${bG},${a(DT.goldBdA)})`],
    ['--text-1', `rgba(240,238,230,${a(DT.text1A)})`],
    ['--text-2', `rgba(240,238,230,${a(DT.text2A)})`],
    ['--text-3', `rgba(240,238,230,${a(DT.text3A)})`],
    ['--border',    `rgba(255,255,255,${a(DT.borderA)})`],
    ['--border-lt', `rgba(0,0,0,${a(DT.borderLtA)})`],
    ['--lt-1', DT.lt1], ['--lt-2', DT.lt2], ['--lt-3', DT.lt3],
    ['--live', DT.live],
    ['--nav-h', DT.navH + 'px'],
  ];
  document.getElementById('dt-all-list').innerHTML = rows.map(([k,v]) =>
    `<div class="dt-token-row"><span class="dt-token-key">${k}</span><span class="dt-token-sep">: </span><span class="dt-token-val">${v}</span></div>`
  ).join('');
}

// ── PREVIEW PAGE / SIZE ────────────────────────────────────────

function dtLoadPage(path, btn) {
  document.querySelectorAll('.dt-page-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('dt-frame').src = path;
  // dtInject fires again on the 'load' event above
}

function dtSize(width, btn) {
  document.querySelectorAll('.dt-size-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const frame = document.getElementById('dt-frame');
  frame.style.width = width;
  frame.style.maxWidth = width;
}

// ── EXPORT ─────────────────────────────────────────────────────

function dtExport() {
  document.getElementById('dt-export-out').value = buildRootCSS();
  document.getElementById('dt-modal').style.display = 'flex';
}

function dtCopy() {
  const ta = document.getElementById('dt-export-out');
  ta.select();
  navigator.clipboard.writeText(ta.value).then(() => {
    const btn = document.querySelector('.dt-modal-copy');
    btn.textContent = 'Copied ✓';
    setTimeout(() => btn.textContent = 'Copy to clipboard', 2000);
  }).catch(() => document.execCommand('copy'));
}

// ── RESET ──────────────────────────────────────────────────────

function dtReset() {
  Object.assign(DT, DT_DEFAULTS);
  // Re-sync all color pickers
  const picks = { black:'#16181F', dark:'#1E2130', dark2:'#252A3A', dark3:'#2E3448',
                  gold:'#E8B84B', live:'#E8350A', bg:'#F4F3EE', surface:'#FFFFFF',
                  lt1:'#1A1A17', lt2:'#5A5A54', lt3:'#9A9A92' };
  Object.entries(picks).forEach(([k,v]) => {
    const el = document.getElementById('pick-' + k);
    if (el) el.value = v;
  });
  // Re-sync value labels
  setVal('black','#16181F'); setVal('dark','#1E2130'); setVal('dark2','#252A3A'); setVal('dark3','#2E3448');
  setVal('gold','#E8B84B'); setVal('goldDimA','14%'); setVal('goldBdA','32%');
  setVal('text1A','100%'); setVal('text2A','65%'); setVal('text3A','38%');
  setVal('borderA','11%'); setVal('borderLtA','9%'); setVal('live','#E8350A');
  setVal('bg','#F4F3EE'); setVal('surface','#FFFFFF');
  setVal('lt1','#1A1A17'); setVal('lt2','#5A5A54'); setVal('lt3','#9A9A92');
  setVal('navH','60px'); setVal('dispSize','118px'); setVal('secTitle','44px');
  setVal('bodySize','14px'); setVal('navLinkSize','12px');
  setVal('dispLS','3.0px'); setVal('lineHeight','1.60'); setVal('logoSize','21px'); setVal('logoLS','2.0px');
  setVal('radiusCard','12px'); setVal('radiusBtn','5px'); setVal('radiusInput','7px');
  setVal('sectionPadH','60px'); setVal('sectionPadV','72px');
  setVal('cardPad','20px'); setVal('gridGap','12px'); setVal('navPadH','36px');
  dtInject();
}

// ── INIT ───────────────────────────────────────────────────────
// Initial inject fires via the iframe 'load' listener above.
