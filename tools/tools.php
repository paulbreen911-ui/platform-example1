<?php
require_once '/config.php';
require_once '/functions.php';

$page_title = 'Tools';
include 'header.php';

$tools = [
    [
        'icon'   => '📺',
        'name'   => 'Test Pattern Generator',
        'desc'   => 'Full-screen test patterns for display calibration and alignment.',
        'url'    => '/tools/test-pattern-generator.php',
        'status' => 'live',
        'color'  => '#1A0E0E',
    ],
    [
        'icon'   => '🎨',
        'name'   => 'CIE Chromaticity Plotter',
        'desc'   => 'Plot brand colors on the CIE 1931 diagram. Gamut analysis across Rec.709, DCI-P3, and Rec.2020.',
        'url'    => '/tools/cie-color-plotter.php',
        'status' => 'live',
        'color'  => '#0A1A1A',
    ],
    [
        'icon'   => '📡',
        'name'   => 'RF Frequency Planner',
        'desc'   => 'Intermodulation-free wireless frequency coordination.',
        'url'    => null,
        'status' => 'coming',
        'color'  => '#001A1A',
    ],
    [
        'icon'   => '📐',
        'name'   => 'Throw Distance Calculator',
        'desc'   => 'Projector lens selection and throw ratio calculations.',
        'url'    => null,
        'status' => 'coming',
        'color'  => '#0E1A14',
    ],
    [
        'icon'   => '⚡',
        'name'   => 'Power & Distro Calculator',
        'desc'   => 'Load calculations, circuit planning, and amperage checks.',
        'url'    => null,
        'status' => 'coming',
        'color'  => '#1A1400',
    ],
    [
        'icon'   => '🔊',
        'name'   => 'SPL & Coverage Estimator',
        'desc'   => 'PA coverage and SPL estimates by room size and speaker placement.',
        'url'    => null,
        'status' => 'coming',
        'color'  => '#0E0E1A',
    ],
    [
        'icon'   => '🏟',
        'name'   => 'Capacity Estimator',
        'desc'   => 'Seated, cocktail, theater, and festival layout capacity calculations.',
        'url'    => null,
        'status' => 'coming',
        'color'  => '#0E1A0E',
    ],
    [
        'icon'   => '🍽',
        'name'   => 'F&B Quantity Planner',
        'desc'   => 'Food and beverage quantities by event type, duration, and headcount.',
        'url'    => null,
        'status' => 'coming',
        'color'  => '#1A0A0E',
    ],
    [
        'icon'   => '🌐',
        'name'   => 'Fiber & Cable Run Planner',
        'desc'   => 'Signal path planning and cable length calculations.',
        'url'    => null,
        'status' => 'coming',
        'color'  => '#0A0A1A',
    ],
];
?>

<div class="tools-page">

  <div class="tools-page-hd">
    <div>
      <div class="sec-ey">Utilities</div>
      <h1 class="tools-page-title">TOOLS</h1>
      <p class="tools-page-sub">Interactive technical utilities for live event production. Use them on site, in prep, or in the truck. More tools launching regularly.</p>
    </div>
  </div>

  <div class="tools-page-grid">
    <?php foreach ($tools as $t): ?>
      <?php if ($t['status'] === 'live' && $t['url']): ?>
        <a class="tool-card-lg" href="<?php echo e($t['url']); ?>">
          <div class="tool-card-lg-icon" style="background:<?php echo e($t['color']); ?>"><?php echo $t['icon']; ?></div>
          <div class="tool-card-lg-body">
            <div class="tool-card-lg-name"><?php echo e($t['name']); ?></div>
            <div class="tool-card-lg-desc"><?php echo e($t['desc']); ?></div>
          </div>
          <div class="tool-card-lg-action">
            <span class="tool-tag-live">Launch tool →</span>
          </div>
        </a>
      <?php else: ?>
        <div class="tool-card-lg tool-card-lg-coming">
          <div class="tool-card-lg-icon" style="background:<?php echo e($t['color']); ?>;opacity:.5"><?php echo $t['icon']; ?></div>
          <div class="tool-card-lg-body">
            <div class="tool-card-lg-name" style="opacity:.5"><?php echo e($t['name']); ?></div>
            <div class="tool-card-lg-desc" style="opacity:.4"><?php echo e($t['desc']); ?></div>
          </div>
          <div class="tool-card-lg-action">
            <span class="tool-tag-coming">Coming soon</span>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>

  <div class="tools-suggest">
    <div class="tools-suggest-title">Got a tool idea?</div>
    <div class="tools-suggest-sub">We build tools requested by the community. Post in the forum with your idea.</div>
    <a class="btn-gold-lg" href="/forum.php?cat=general" style="font-size:13px;padding:10px 22px">Post a suggestion →</a>
  </div>

</div>

<?php include 'footer.php'; ?>
