<?php
if ($_SERVER['HTTP_HOST'] === 'productioncentral.org') {
    header('Location: https://www.productioncentral.org' . $_SERVER['REQUEST_URI'], true, 301);
    exit;
}

require_once 'config.php';
require_once 'functions.php';

$page_title = 'Home';

// Live forum threads for homepage
$live_threads = [];
$member_count = '8,400';
$post_count   = '2,100';

try {
    $live_threads = $pdo->query('
        SELECT ft.id, ft.title, ft.reply_count, ft.created_at,
               fc.name AS cat_name, fc.color AS cat_color,
               u.username
        FROM   forum_threads ft
        JOIN   forum_categories fc ON fc.id = ft.category_id
        JOIN   users u ON u.id = ft.user_id
        ORDER  BY COALESCE(ft.last_reply_at, ft.created_at) DESC
        LIMIT  4
    ')->fetchAll();

    $stats = $pdo->query('
        SELECT
            (SELECT COUNT(*) FROM users)         AS member_count,
            (SELECT COUNT(*) FROM forum_threads) AS thread_count,
            (SELECT COUNT(*) FROM forum_replies) AS reply_count
    ')->fetch();

    $member_count = number_format($stats['member_count'] ?: 8400);
    $post_count   = number_format(($stats['thread_count'] + $stats['reply_count']) ?: 2100);
} catch (PDOException $e) {
    // Tables not yet created — site loads with placeholder content
}

include 'header.php';
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-grid"></div>
  <div class="hero-lights"><div class="beam b1"></div><div class="beam b2"></div><div class="beam b3"></div><div class="beam b4"></div></div>
  <div class="hero-glow"></div>

  <div class="hero-content">
    <div class="hero-ey">The industry platform for live event professionals</div>
    <h1 class="hero-h1">PRODUCTION<br><span class="accent">CENTRAL</span></h1>
    <p class="hero-sub">The hub for everyone behind the show — producers, stage managers, technical crew, and the full team that makes live events happen. Project management, production planning, run of show, and crew collaboration — all in one place.</p>
    <div class="hero-ctas">
      <?php if (isset($_SESSION['user_id'])): ?>
        <a class="btn-gold-lg" href="/myprofile.php">Go to my profile →</a>
      <?php else: ?>
        <a class="btn-gold-lg" href="/register.php">Join free — it's open to all</a>
      <?php endif; ?>
      <a class="btn-ghost-lg" href="#forum">Explore the platform ↓</a>
      <a class="hero-demo-cta" href="/app/dashboard.html">
        <div class="hero-demo-cta-dot"></div>
        Try the demo — no login needed
      </a>
    </div>
    <div class="hero-demo-cta-sub">Demo gives you full access to the platform prototype — dashboard, productions, run of show, and more.</div>
  </div>

  <div class="hero-stats">
    <div class="hero-stat"><div class="hero-stat-n"><?php echo $member_count; ?>+</div><div class="hero-stat-l">Members</div></div>
    <div class="hero-stat-div"></div>
    <div class="hero-stat"><div class="hero-stat-n">340+</div><div class="hero-stat-l">Venues catalogued</div></div>
    <div class="hero-stat-div"></div>
    <div class="hero-stat"><div class="hero-stat-n"><?php echo $post_count; ?>+</div><div class="hero-stat-l">Forum posts</div></div>
  </div>
</section>

<!-- SECTION NAV CARDS -->
<div class="section-nav">
  <div class="snav-label">Explore the platform</div>
  <div class="snav-grid">
    <a class="sc sc-profile" href="/myprofile.php"><div class="sc-icon">👤</div><div class="sc-name">My Profile</div><div class="sc-desc">Productions, shows &amp; settings</div><div class="sc-arr">→</div></a>
    <a class="sc sc-edu" href="#education"><div class="sc-icon">🎓</div><div class="sc-name">Education</div><div class="sc-desc">Docs, videos &amp; courses</div><div class="sc-arr">→</div></a>
    <a class="sc sc-ref" href="#reference"><div class="sc-icon">🗂</div><div class="sc-name">Reference</div><div class="sc-desc">Venues, scans &amp; shows</div><div class="sc-arr">→</div></a>
    <a class="sc sc-tech" href="#technology"><div class="sc-icon">⚙️</div><div class="sc-name">Technology</div><div class="sc-desc">Manuals, specs &amp; gear</div><div class="sc-arr">→</div></a>
    <a class="sc sc-tools" href="/tools.php"><div class="sc-icon">🔧</div><div class="sc-name">Tools</div><div class="sc-desc">Calculators &amp; test patterns</div><div class="sc-arr">→</div></a>
    <a class="sc sc-forum" href="/forum.php"><div class="sc-icon">💬</div><div class="sc-name">Forum</div><div class="sc-desc">Discussion, jobs &amp; gear</div><div class="sc-arr">→</div></a>
    <a class="sc sc-life" href="#life"><div class="sc-icon">🌿</div><div class="sc-name">Life</div><div class="sc-desc">Health, fitness &amp; wellness</div><div class="sc-arr">→</div></a>
    <a class="sc sc-store" href="#store"><div class="sc-icon">🛒</div><div class="sc-name">Store</div><div class="sc-desc">Gaff tape, duvatine &amp; merch</div><div class="sc-arr">→</div></a>
  </div>
</div>

<!-- DEMO BAND -->
<div class="demo-band">
  <div class="demo-band-left">
    <div class="demo-band-dot"></div>
    <div>
      <div class="demo-band-title">Platform demo available — explore without signing up</div>
      <div class="demo-band-sub">Click in to the full logged-in experience — dashboard, productions, live run of show, documents, and more.</div>
    </div>
  </div>
  <a class="demo-band-btn" href="/app/dashboard.html">Open platform demo →</a>
</div>

<!-- TOOLS -->
<section class="section" id="tools">
  <div class="sec-hd">
    <div><div class="sec-ey">Utilities</div><div class="sec-title">TOOLS</div><div class="sec-sub">Interactive technical utilities — use them on site, in prep, or in the truck.</div></div>
    <a class="see-all" href="/tools.php">All tools →</a>
  </div>
  <div class="tools-grid">
    <a class="tool-card" href="/tools/test-pattern-generator.html" style="text-decoration:none;color:inherit"><div class="tool-icon" style="background:#1A0E0E">📺</div><div class="tool-name">Test pattern generator</div><div class="tool-desc">Full-screen patterns for display calibration</div><span class="tool-tag">Launch tool</span></a>
    <div class="tool-card"><div class="tool-icon" style="background:#001A1A">📡</div><div class="tool-name">RF frequency planner</div><div class="tool-desc">Intermod-free wireless coordination</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#0E1A14">📐</div><div class="tool-name">Throw distance calculator</div><div class="tool-desc">Projector lens and throw ratio</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#1A1400">⚡</div><div class="tool-name">Power &amp; distro calculator</div><div class="tool-desc">Load calculations and circuit planning</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#0E0E1A">🔊</div><div class="tool-name">SPL &amp; coverage estimator</div><div class="tool-desc">PA coverage and dB by room size</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#0E1A0E">🏟</div><div class="tool-name">Capacity estimator</div><div class="tool-desc">Seated, cocktail, theater layouts</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#1A0A0E">🍽</div><div class="tool-name">F&amp;B quantity planner</div><div class="tool-desc">Quantities by event type and duration</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#0A0A1A">🌐</div><div class="tool-name">Fiber &amp; cable run planner</div><div class="tool-desc">Signal path and cable length calculator</div><span class="tool-tag">Launch tool</span></div>
  </div>
</section>

<!-- REFERENCE -->
<section class="section alt" id="reference">
  <div class="sec-hd">
    <div><div class="sec-ey">Database</div><div class="sec-title">REFERENCE</div><div class="sec-sub">Venues, 3D scans, hotels, public show archives, and more.</div></div>
    <a class="see-all" href="#">Browse all →</a>
  </div>
</section>

<!-- FORUM — live data -->
<section class="section" id="forum">
  <div class="sec-hd">
    <div><div class="sec-ey">Community</div><div class="sec-title">FORUM</div><div class="sec-sub">Open to everyone. Read without an account — join free to post.</div></div>
    <a class="see-all" href="/forum.php">Browse all posts →</a>
  </div>
  <div class="forum-grid">
    <div class="forum-posts">
      <?php if (empty($live_threads)): ?>
        <!-- Fallback placeholder threads if DB has none yet -->
        <div class="fp"><div class="fp-left"><div class="fp-cat" style="color:#CC884A">Vendor rec</div><div class="fp-title">Best wireless IEM systems for outdoor festival use in high-RF environments?</div><div class="fp-meta"><span>Jordan K.</span><span>2h ago</span><span>🔥 Trending</span></div></div><div class="fp-replies"><div class="fp-n">22</div><div class="fp-l">replies</div></div></div>
        <div class="fp"><div class="fp-left"><div class="fp-cat" style="color:#4A9ECC">Stage management</div><div class="fp-title">Handling live run of show updates when the client changes timing day-of</div><div class="fp-meta"><span>Mara L.</span><span>5h ago</span></div></div><div class="fp-replies"><div class="fp-n">14</div><div class="fp-l">replies</div></div></div>
        <div class="fp"><div class="fp-left"><div class="fp-cat" style="color:#8888CC">Lighting</div><div class="fp-title">MA3 vs EOS for a touring LD — switching after 8 years on grandMA2</div><div class="fp-meta"><span>Dana L.</span><span>1d ago</span></div></div><div class="fp-replies"><div class="fp-n">38</div><div class="fp-l">replies</div></div></div>
        <div class="fp"><div class="fp-left"><div class="fp-cat" style="color:var(--gold)">Contracts</div><div class="fp-title">Force majeure language that has actually held up — share your clauses</div><div class="fp-meta"><span>Tom R.</span><span>2d ago</span></div></div><div class="fp-replies"><div class="fp-n">47</div><div class="fp-l">replies</div></div></div>
      <?php else: ?>
        <?php foreach ($live_threads as $t): ?>
          <a class="fp" href="/forum_thread.php?id=<?php echo $t['id']; ?>" style="text-decoration:none">
            <div class="fp-left">
              <div class="fp-cat" style="color:<?php echo e($t['cat_color']); ?>"><?php echo e($t['cat_name']); ?></div>
              <div class="fp-title"><?php echo e($t['title']); ?></div>
              <div class="fp-meta"><span><?php echo e($t['username']); ?></span><span><?php echo time_ago($t['created_at']); ?></span></div>
            </div>
            <div class="fp-replies"><div class="fp-n"><?php echo $t['reply_count']; ?></div><div class="fp-l">replies</div></div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="forum-sidebar">
      <div class="sidebar-card">
        <div class="sc-hd">Jobs board</div>
        <div class="job-row"><div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:3px"><div class="job-title">Production Manager — Vegas residency</div><div class="job-type">FT</div></div><div class="job-meta"><span>Las Vegas, NV</span><span>1d ago</span></div></div>
        <div class="job-row"><div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:3px"><div class="job-title">Touring FOH Engineer</div><div class="job-type">Tour</div></div><div class="job-meta"><span>National</span><span>2d ago</span></div></div>
        <div class="job-row"><div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:3px"><div class="job-title">Freelance LD — corporate events</div><div class="job-type">FL</div></div><div class="job-meta"><span>NYC</span><span>3d ago</span></div></div>
      </div>
      <div class="sidebar-card">
        <div class="sc-hd">Trending</div>
        <div class="trend-row"><span>Dante vs AVB for large-scale installs</span><span style="color:var(--text-3);font-size:11px">62</span></div>
        <div class="trend-row"><span>LED wall pixel pitch — what to spec</span><span style="color:var(--text-3);font-size:11px">44</span></div>
        <div class="trend-row"><span>Touring rates 2025 — what to charge</span><span style="color:var(--text-3);font-size:11px">38</span></div>
        <div class="trend-row"><span>Best hybrid event platforms</span><span style="color:var(--text-3);font-size:11px">31</span></div>
      </div>
    </div>
  </div>
</section>

<!-- LIFE -->
<section class="section alt" id="life">
  <div class="sec-hd">
    <div><div class="sec-ey">Wellbeing</div><div class="sec-title">LIFE</div><div class="sec-sub">Dedicated to the people in the industry, not just the work.</div></div>
    <a class="see-all" href="#">Explore →</a>
  </div>
  <div class="life-grid">
    <div class="life-card life-mental"><div style="font-size:26px;margin-bottom:12px">🧠</div><div style="font-size:17px;font-weight:500;margin-bottom:8px">Mental health</div><div style="font-size:12px;opacity:.75;line-height:1.65">Burnout, isolation, anxiety, and building resilience on the road.</div><div style="font-size:12px;margin-top:14px;font-weight:500;opacity:.6">Read articles →</div></div>
    <div class="life-card life-physical"><div style="font-size:26px;margin-bottom:12px">💪</div><div style="font-size:17px;font-weight:500;margin-bottom:8px">Physical wellness</div><div style="font-size:12px;opacity:.75;line-height:1.65">Staying healthy when your schedule is unpredictable.</div><div style="font-size:12px;margin-top:14px;font-weight:500;opacity:.6">Read articles →</div></div>
    <div class="life-card life-diet"><div style="font-size:26px;margin-bottom:12px">🥗</div><div style="font-size:17px;font-weight:500;margin-bottom:8px">Nutrition &amp; diet</div><div style="font-size:12px;opacity:.75;line-height:1.65">Eating well on catering, in hotels, through 14-hour days.</div><div style="font-size:12px;margin-top:14px;font-weight:500;opacity:.6">Read articles →</div></div>
  </div>
</section>

<!-- STORE -->
<section class="section" id="store">
  <div class="sec-hd">
    <div><div class="sec-ey">Marketplace</div><div class="sec-title">STORE</div><div class="sec-sub">Consumables, tools, and merch for the industry. Launching soon.</div></div>
  </div>
  <div style="background:var(--dark-2);border:.5px solid var(--gold-bd);border-radius:12px;padding:28px 32px;display:flex;align-items:center;justify-content:space-between;max-width:680px">
    <div><div style="font-size:14px;font-weight:500;color:var(--gold);margin-bottom:6px">Store launching soon</div><div style="font-size:13px;color:var(--text-2)">Gaff tape, duvatine, crew merch, and industry-curated gear. Join free to get notified.</div></div>
    <a class="btn-gold-lg" href="/register.php" style="flex-shrink:0;margin-left:24px">Get notified →</a>
  </div>
</section>

<!-- JOIN CTA -->
<div class="join-band">
  <div class="join-title">YOUR PROFILE.<br><span>YOUR PRODUCTIONS.</span></div>
  <div class="join-sub">Create a free account to manage productions, build run of shows,<br>collaborate with your crew, and unlock the full platform.</div>
  <div class="join-features">
    <div class="join-feat">Production workspace</div>
    <div class="join-feat">Project management</div>
    <div class="join-feat">Live run of show</div>
    <div class="join-feat">Team collaboration</div>
    <div class="join-feat">Documents &amp; templates</div>
    <div class="join-feat">No credit card required</div>
  </div>
  <div class="join-ctas">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a class="btn-gold-lg" href="/myprofile.php">Go to my profile →</a>
    <?php else: ?>
      <a class="btn-gold-lg" href="/register.php">Create free account →</a>
      <a class="btn-ghost-lg" href="/login.php">Sign in</a>
      <a class="hero-demo-cta" href="/app/dashboard.html" style="font-size:13px;padding:10px 20px">
        <div class="hero-demo-cta-dot"></div>
        Or try the demo — no signup needed
      </a>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
