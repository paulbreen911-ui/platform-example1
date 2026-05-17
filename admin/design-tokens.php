<?php
// /admin/design-tokens.php
// Temporary internal tool — not linked publicly.
require_once __DIR__ . '/../config.php';
$page_title = 'Design Tokens';
require_once __DIR__ . '/../required/header.php';
?>

<link rel="stylesheet" href="/css/design-tokens.css">

<div class="dt-shell">

  <!-- ── SIDEBAR CONTROLS ── -->
  <aside class="dt-sidebar">

    <div class="dt-sidebar-hd">
      <div class="dt-sidebar-title">Design Tokens</div>
      <div class="dt-sidebar-sub">Live editor — changes reflect in preview</div>
    </div>

    <!-- Tab nav -->
    <div class="dt-tabs">
      <button class="dt-tab active" onclick="dtTab('colors',this)">Colors</button>
      <button class="dt-tab" onclick="dtTab('type',this)">Type</button>
      <button class="dt-tab" onclick="dtTab('space',this)">Space</button>
      <button class="dt-tab" onclick="dtTab('light',this)">Light</button>
      <button class="dt-tab" onclick="dtTab('all',this)">All</button>
    </div>

    <!-- COLORS -->
    <div class="dt-panel" id="dt-panel-colors">

      <div class="dt-section-lbl">Backgrounds</div>

      <div class="dt-ctrl" data-token="black">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--black</span><span class="dt-ctrl-val" id="val-black">#16181F</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-black" value="#16181F" oninput="dtColorDirect('black',this.value)">
          <input type="range" class="dt-range" min="5" max="35" value="11" step="1" oninput="dtColorL('black',this.value)">
        </div>
      </div>

      <div class="dt-ctrl" data-token="dark">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--dark</span><span class="dt-ctrl-val" id="val-dark">#1E2130</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-dark" value="#1E2130" oninput="dtColorDirect('dark',this.value)">
          <input type="range" class="dt-range" min="5" max="40" value="15" step="1" oninput="dtColorL('dark',this.value)">
        </div>
      </div>

      <div class="dt-ctrl" data-token="dark2">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--dark-2</span><span class="dt-ctrl-val" id="val-dark2">#252A3A</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-dark2" value="#252A3A" oninput="dtColorDirect('dark2',this.value)">
          <input type="range" class="dt-range" min="5" max="45" value="18" step="1" oninput="dtColorL('dark2',this.value)">
        </div>
      </div>

      <div class="dt-ctrl" data-token="dark3">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--dark-3</span><span class="dt-ctrl-val" id="val-dark3">#2E3448</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-dark3" value="#2E3448" oninput="dtColorDirect('dark3',this.value)">
          <input type="range" class="dt-range" min="5" max="50" value="21" step="1" oninput="dtColorL('dark3',this.value)">
        </div>
      </div>

      <div class="dt-section-lbl" style="margin-top:14px">Gold accent</div>

      <div class="dt-ctrl" data-token="gold">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--gold (hue + sat)</span><span class="dt-ctrl-val" id="val-gold">#E8B84B</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-gold" value="#E8B84B" oninput="dtGoldDirect(this.value)">
          <input type="range" class="dt-range" min="25" max="55" value="43" step="1" id="gold-l-range" oninput="dtGoldL(this.value)">
        </div>
        <div class="dt-hint">Slider = lightness. --gold-dim & --gold-bd auto-follow.</div>
      </div>

      <div class="dt-ctrl" data-token="goldDimA">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--gold-dim opacity</span><span class="dt-ctrl-val" id="val-goldDimA">14%</span></div>
        <input type="range" class="dt-range dt-range-full" min="4" max="40" value="14" step="1" oninput="dtAlpha('goldDimA',this.value)">
      </div>

      <div class="dt-ctrl" data-token="goldBdA">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--gold-bd opacity</span><span class="dt-ctrl-val" id="val-goldBdA">32%</span></div>
        <input type="range" class="dt-range dt-range-full" min="8" max="60" value="32" step="1" oninput="dtAlpha('goldBdA',this.value)">
      </div>

      <div class="dt-section-lbl" style="margin-top:14px">Text</div>

      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--text-1 opacity</span><span class="dt-ctrl-val" id="val-text1A">100%</span></div>
        <input type="range" class="dt-range dt-range-full" min="70" max="100" value="100" step="1" oninput="dtAlpha('text1A',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--text-2 opacity</span><span class="dt-ctrl-val" id="val-text2A">65%</span></div>
        <input type="range" class="dt-range dt-range-full" min="20" max="95" value="65" step="1" oninput="dtAlpha('text2A',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--text-3 opacity</span><span class="dt-ctrl-val" id="val-text3A">38%</span></div>
        <input type="range" class="dt-range dt-range-full" min="5" max="70" value="38" step="1" oninput="dtAlpha('text3A',this.value)">
      </div>

      <div class="dt-section-lbl" style="margin-top:14px">Borders &amp; Live</div>

      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--border opacity</span><span class="dt-ctrl-val" id="val-borderA">11%</span></div>
        <input type="range" class="dt-range dt-range-full" min="4" max="40" value="11" step="1" oninput="dtAlpha('borderA',this.value)">
      </div>

      <div class="dt-ctrl" data-token="live">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--live</span><span class="dt-ctrl-val" id="val-live">#E8350A</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-live" value="#E8350A" oninput="dtColorDirect('live',this.value)">
          <input type="range" class="dt-range" min="0" max="359" value="10" step="1" oninput="dtHue('live',this.value)">
        </div>
        <div class="dt-hint">Slider = hue shift</div>
      </div>

    </div><!-- /colors -->

    <!-- TYPE -->
    <div class="dt-panel" id="dt-panel-type" style="display:none">

      <div class="dt-section-lbl">Scale</div>

      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--nav-h</span><span class="dt-ctrl-val" id="val-navH">60px</span></div>
        <input type="range" class="dt-range dt-range-full" min="48" max="80" value="60" step="2" oninput="dtPx('navH',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Hero display size</span><span class="dt-ctrl-val" id="val-dispSize">118px</span></div>
        <input type="range" class="dt-range dt-range-full" min="60" max="160" value="118" step="2" oninput="dtPx('dispSize',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Section title (.sec-title)</span><span class="dt-ctrl-val" id="val-secTitle">44px</span></div>
        <input type="range" class="dt-range dt-range-full" min="24" max="80" value="44" step="2" oninput="dtPx('secTitle',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Body font-size</span><span class="dt-ctrl-val" id="val-bodySize">14px</span></div>
        <input type="range" class="dt-range dt-range-full" min="12" max="18" value="14" step="1" oninput="dtPx('bodySize',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Nav link size</span><span class="dt-ctrl-val" id="val-navLinkSize">12px</span></div>
        <input type="range" class="dt-range dt-range-full" min="10" max="16" value="12" step="1" oninput="dtPx('navLinkSize',this.value)">
      </div>

      <div class="dt-section-lbl" style="margin-top:14px">Spacing &amp; rhythm</div>

      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Display letter-spacing</span><span class="dt-ctrl-val" id="val-dispLS">3px</span></div>
        <input type="range" class="dt-range dt-range-full" min="0" max="10" value="3" step="0.5" oninput="dtDecPx('dispLS',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Body line-height</span><span class="dt-ctrl-val" id="val-lineHeight">1.6</span></div>
        <input type="range" class="dt-range dt-range-full" min="1.2" max="2.2" value="1.6" step="0.05" oninput="dtFloat('lineHeight',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Nav logo size</span><span class="dt-ctrl-val" id="val-logoSize">21px</span></div>
        <input type="range" class="dt-range dt-range-full" min="14" max="34" value="21" step="1" oninput="dtPx('logoSize',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Nav logo letter-spacing</span><span class="dt-ctrl-val" id="val-logoLS">2px</span></div>
        <input type="range" class="dt-range dt-range-full" min="0" max="8" value="2" step="0.5" oninput="dtDecPx('logoLS',this.value)">
      </div>

    </div><!-- /type -->

    <!-- SPACE -->
    <div class="dt-panel" id="dt-panel-space" style="display:none">

      <div class="dt-section-lbl">Shape</div>

      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Card border-radius</span><span class="dt-ctrl-val" id="val-radiusCard">12px</span></div>
        <input type="range" class="dt-range dt-range-full" min="0" max="28" value="12" step="1" oninput="dtPx('radiusCard',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Button border-radius</span><span class="dt-ctrl-val" id="val-radiusBtn">5px</span></div>
        <input type="range" class="dt-range dt-range-full" min="0" max="24" value="5" step="1" oninput="dtPx('radiusBtn',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Input border-radius</span><span class="dt-ctrl-val" id="val-radiusInput">7px</span></div>
        <input type="range" class="dt-range dt-range-full" min="0" max="20" value="7" step="1" oninput="dtPx('radiusInput',this.value)">
      </div>

      <div class="dt-section-lbl" style="margin-top:14px">Layout</div>

      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Section padding H</span><span class="dt-ctrl-val" id="val-sectionPadH">60px</span></div>
        <input type="range" class="dt-range dt-range-full" min="16" max="120" value="60" step="4" oninput="dtPx('sectionPadH',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Section padding V</span><span class="dt-ctrl-val" id="val-sectionPadV">72px</span></div>
        <input type="range" class="dt-range dt-range-full" min="24" max="120" value="72" step="4" oninput="dtPx('sectionPadV',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Card padding</span><span class="dt-ctrl-val" id="val-cardPad">20px</span></div>
        <input type="range" class="dt-range dt-range-full" min="8" max="48" value="20" step="2" oninput="dtPx('cardPad',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Grid gap</span><span class="dt-ctrl-val" id="val-gridGap">12px</span></div>
        <input type="range" class="dt-range dt-range-full" min="4" max="40" value="12" step="2" oninput="dtPx('gridGap',this.value)">
      </div>
      <div class="dt-ctrl">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">Nav padding H</span><span class="dt-ctrl-val" id="val-navPadH">36px</span></div>
        <input type="range" class="dt-range dt-range-full" min="12" max="80" value="36" step="4" oninput="dtPx('navPadH',this.value)">
      </div>

    </div><!-- /space -->

    <!-- LIGHT MODE -->
    <div class="dt-panel" id="dt-panel-light" style="display:none">

      <div class="dt-hint" style="margin-bottom:12px">These control light-mode sections (reference, education, .section.light)</div>

      <div class="dt-section-lbl">Surfaces</div>

      <div class="dt-ctrl" data-token="bg">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--bg (page bg)</span><span class="dt-ctrl-val" id="val-bg">#F4F3EE</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-bg" value="#F4F3EE" oninput="dtColorDirect('bg',this.value)">
          <input type="range" class="dt-range" min="85" max="100" value="96" step="1" oninput="dtColorL('bg',this.value)">
        </div>
      </div>

      <div class="dt-ctrl" data-token="surface">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--surface</span><span class="dt-ctrl-val" id="val-surface">#FFFFFF</span></div>
        <input type="color" class="dt-color-pick" id="pick-surface" value="#FFFFFF" oninput="dtColorDirect('surface',this.value)">
      </div>

      <div class="dt-ctrl" data-token="borderLt">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--border-lt opacity</span><span class="dt-ctrl-val" id="val-borderLtA">9%</span></div>
        <input type="range" class="dt-range dt-range-full" min="2" max="30" value="9" step="1" oninput="dtAlpha('borderLtA',this.value)">
      </div>

      <div class="dt-section-lbl" style="margin-top:14px">Light text</div>

      <div class="dt-ctrl" data-token="lt1">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--lt-1</span><span class="dt-ctrl-val" id="val-lt1">#1A1A17</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-lt1" value="#1A1A17" oninput="dtColorDirect('lt1',this.value)">
          <input type="range" class="dt-range" min="5" max="30" value="10" step="1" oninput="dtColorL('lt1',this.value)">
        </div>
      </div>

      <div class="dt-ctrl" data-token="lt2">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--lt-2</span><span class="dt-ctrl-val" id="val-lt2">#5A5A54</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-lt2" value="#5A5A54" oninput="dtColorDirect('lt2',this.value)">
          <input type="range" class="dt-range" min="20" max="55" value="35" step="1" oninput="dtColorL('lt2',this.value)">
        </div>
      </div>

      <div class="dt-ctrl" data-token="lt3">
        <div class="dt-ctrl-hd"><span class="dt-ctrl-name">--lt-3</span><span class="dt-ctrl-val" id="val-lt3">#9A9A92</span></div>
        <div class="dt-ctrl-row">
          <input type="color" class="dt-color-pick" id="pick-lt3" value="#9A9A92" oninput="dtColorDirect('lt3',this.value)">
          <input type="range" class="dt-range" min="40" max="75" value="60" step="1" oninput="dtColorL('lt3',this.value)">
        </div>
      </div>

    </div><!-- /light -->

    <!-- ALL TOKENS -->
    <div class="dt-panel" id="dt-panel-all" style="display:none">
      <div id="dt-all-list"></div>
    </div>

    <!-- Footer actions -->
    <div class="dt-sidebar-footer">
      <button class="dt-btn-export" onclick="dtExport()">Export :root CSS</button>
      <button class="dt-btn-reset" onclick="dtReset()">Reset</button>
    </div>

  </aside>

  <!-- ── MAIN AREA ── -->
  <div class="dt-main">

    <!-- Preview toolbar -->
    <div class="dt-preview-bar">
      <span class="dt-preview-label">Preview</span>
      <div class="dt-preview-pages">
        <button class="dt-page-btn active" onclick="dtLoadPage('/',this)">Home</button>
        <button class="dt-page-btn" onclick="dtLoadPage('/tools/tools.php',this)">Tools</button>
        <button class="dt-page-btn" onclick="dtLoadPage('/forum/forum.php',this)">Forum</button>
        <button class="dt-page-btn" onclick="dtLoadPage('/user/login.php',this)">Login</button>
      </div>
      <div class="dt-preview-size">
        <button class="dt-size-btn active" onclick="dtSize('100%',this)" title="Full width">⬛</button>
        <button class="dt-size-btn" onclick="dtSize('1024px',this)" title="1024px">◼</button>
        <button class="dt-size-btn" onclick="dtSize('768px',this)" title="768px tablet">▪</button>
      </div>
      <span class="dt-preview-hint">Changes inject live into the iframe</span>
    </div>

    <!-- iframe -->
    <div class="dt-frame-wrap">
      <iframe id="dt-frame" src="/" class="dt-frame"></iframe>
    </div>

  </div>

</div><!-- /dt-shell -->

<!-- Export modal -->
<div class="dt-modal-overlay" id="dt-modal" onclick="this.style.display='none'">
  <div class="dt-modal" onclick="event.stopPropagation()">
    <div class="dt-modal-hd">
      <span>Exported :root — paste into style.css</span>
      <button class="dt-modal-copy" onclick="dtCopy()">Copy to clipboard</button>
      <button class="dt-modal-close" onclick="document.getElementById('dt-modal').style.display='none'">✕</button>
    </div>
    <textarea class="dt-modal-code" id="dt-export-out" readonly></textarea>
  </div>
</div>

<script src="/js/design-tokens.js"></script>

<?php require_once __DIR__ . '/../required/footer.php'; ?>
