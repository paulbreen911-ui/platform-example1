<?php
require_once 'config.php';
$page_title = 'Home';
include 'header.php';
?>

 <!-- Original code -->
 <!-- -->

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
      <a class="btn-gold-lg" href="app/dashboard.html">Join free — it's open to all</a>
      <a class="btn-ghost-lg" href="#forum">Explore the platform ↓</a>
      <a class="hero-demo-cta" href="app/dashboard.html">
        <div class="hero-demo-cta-dot"></div>
        Try the demo — no login needed
      </a>
    </div>
    <div class="hero-demo-cta-sub">Demo gives you full access to the platform prototype — dashboard, productions, run of show, and more.</div>
  </div>

  <div class="hero-stats">
    <div class="hero-stat"><div class="hero-stat-n">8,400+</div><div class="hero-stat-l">Members</div></div>
    <div class="hero-stat-div"></div>
    <div class="hero-stat"><div class="hero-stat-n">340+</div><div class="hero-stat-l">Venues catalogued</div></div>
    <div class="hero-stat-div"></div>
    <div class="hero-stat"><div class="hero-stat-n">2,100+</div><div class="hero-stat-l">Forum posts</div></div>
  </div>
</section>

<!-- SECTION NAV CARDS -->
<div class="section-nav">
  <div class="snav-label">Explore the platform</div>
  <div class="snav-grid">
    <a class="sc sc-profile" href="app/dashboard.html"><div class="sc-icon">👤</div><div class="sc-name">My Profile</div><div class="sc-desc">Productions, shows &amp; settings</div><div class="sc-arr">→</div></a>
    <a class="sc sc-edu" href="#education"><div class="sc-icon">🎓</div><div class="sc-name">Education</div><div class="sc-desc">Docs, videos &amp; courses</div><div class="sc-arr">→</div></a>
    <a class="sc sc-ref" href="#reference"><div class="sc-icon">🗂</div><div class="sc-name">Reference</div><div class="sc-desc">Venues, scans &amp; shows</div><div class="sc-arr">→</div></a>
    <a class="sc sc-tech" href="#technology"><div class="sc-icon">⚙️</div><div class="sc-name">Technology</div><div class="sc-desc">Manuals, specs &amp; gear</div><div class="sc-arr">→</div></a>
    <a class="sc sc-tools" href="tools/index.html"><div class="sc-icon">🔧</div><div class="sc-name">Tools</div><div class="sc-desc">Calculators &amp; test patterns</div><div class="sc-arr">→</div></a>
    <a class="sc sc-forum" href="#forum"><div class="sc-icon">💬</div><div class="sc-name">Forum</div><div class="sc-desc">Discussion, jobs &amp; gear</div><div class="sc-arr">→</div></a>
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
  <a class="demo-band-btn" href="app/dashboard.html">Open platform demo →</a>
</div>

<!-- FORUM -->
<section class="section" id="tools">
  <div class="sec-hd">
    <div><div class="sec-ey">Utilities</div><div class="sec-title">TOOLS</div><div class="sec-sub">Interactive technical utilities — use them on site, in prep, or in the truck.</div></div>
    <a class="see-all" href="tools/index.html">All tools →</a>
  </div>
  <div class="tools-grid">
    <a class="tool-card" href="tools/test-pattern-generator.html" style="text-decoration:none;color:inherit"><div class="tool-icon" style="background:#1A0E0E">📺</div><div class="tool-name">Test pattern generator</div><div class="tool-desc">Full-screen patterns for display calibration</div><span class="tool-tag">Launch tool</span></a>
    <div class="tool-card"><div class="tool-icon" style="background:#001A1A">📡</div><div class="tool-name">RF frequency planner</div><div class="tool-desc">Intermod-free wireless coordination</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#0E1A14">📐</div><div class="tool-name">Throw distance calculator</div><div class="tool-desc">Projector lens and throw ratio</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#1A1400">⚡</div><div class="tool-name">Power &amp; distro calculator</div><div class="tool-desc">Load calculations and circuit planning</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#0E0E1A">🔊</div><div class="tool-name">SPL &amp; coverage estimator</div><div class="tool-desc">PA coverage and dB by room size</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#0E1A0E">🏟</div><div class="tool-name">Capacity estimator</div><div class="tool-desc">Seated, cocktail, theater layouts</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#1A0A0E">🍽</div><div class="tool-name">F&amp;B quantity planner</div><div class="tool-desc">Quantities by event type and duration</div><span class="tool-tag">Launch tool</span></div>
    <div class="tool-card"><div class="tool-icon" style="background:#0A0A1A">🌐</div><div class="tool-name">Fiber &amp; cable run planner</div><div class="tool-desc">Signal path and cable length calculator</div><span class="tool-tag">Launch tool</span></div>
  </div>
</section>

<section class="section alt" id="reference">
  <div class="sec-hd">
    <div><div class="sec-ey">Database</div><div class="sec-title">REFERENCE</div><div class="sec-sub">Venues, 3D scans, hotels, public show archives, and more.</div></div>
    <a class="see-all" href="#">Browse all →</a>
  </div>
  <div class="ref-grid">
    <div class="ref-card" style="background:var(--dark-2);border-color:var(--border)"><div class="ref-icon" style="background:#EAF1F8">🏟</div><div><div class="ref-title" style="color:var(--text-1)">Venue database</div><div class="ref-desc" style="color:var(--text-3)">Floor plans, rigging, power specs, contact info</div><div class="ref-count" style="color:var(--text-3)">340 venues</div></div></div>
    <div class="ref-card" style="background:var(--dark-2);border-color:var(--border)"><div class="ref-icon" style="background:#EAF4F0">📐</div><div><div class="ref-title" style="color:var(--text-1)">3D venue scans</div><div class="ref-desc" style="color:var(--text-3)">Matterport walkthroughs for pre-production</div><div class="ref-count" style="color:var(--text-3)">82 venues scanned</div></div></div>
    <div class="ref-card" style="background:var(--dark-2);border-color:var(--border)"><div class="ref-icon" style="background:#F8F4EA">🏨</div><div><div class="ref-title" style="color:var(--text-1)">Hotel directory</div><div class="ref-desc" style="color:var(--text-3)">Crew-recommended near major venues</div><div class="ref-count" style="color:var(--text-3)">600+ hotels</div></div></div>
    <div class="ref-card" style="background:var(--dark-2);border-color:var(--border)"><div class="ref-icon" style="background:#F8EAF0">🎭</div><div><div class="ref-title" style="color:var(--text-1)">Public show archive</div><div class="ref-desc" style="color:var(--text-3)">Tech specs from notable productions</div><div class="ref-count" style="color:var(--text-3)">180 shows</div></div></div>
    <div class="ref-card" style="background:var(--dark-2);border-color:var(--border)"><div class="ref-icon" style="background:#F0EAF8">🗺</div><div><div class="ref-title" style="color:var(--text-1)">Venue maps</div><div class="ref-desc" style="color:var(--text-3)">Loading docks, power, freight elevator specs</div><div class="ref-count" style="color:var(--text-3)">210 mapped</div></div></div>
    <div class="ref-card" style="background:var(--dark-2);border-color:var(--border)"><div class="ref-icon" style="background:#EAF4EE">📋</div><div><div class="ref-title" style="color:var(--text-1)">Union &amp; regulations</div><div class="ref-desc" style="color:var(--text-3)">IATSE, Teamsters, local contacts by city</div><div class="ref-count" style="color:var(--text-3)">48 markets</div></div></div>
  </div>
</section>

<section class="section" id="education">
  <div class="sec-hd">
    <div><div class="sec-ey">Learn</div><div class="sec-title">EDUCATION</div><div class="sec-sub">Documents, videos, and courses for every level.</div></div>
    <a class="see-all" href="#">All content →</a>
  </div>
  <div class="edu-grid">
    <div class="edu-card"><div class="edu-thumb" style="background:#0E1A24">📹</div><div class="edu-body"><div class="edu-type">Video · 42 min</div><div class="edu-title">RF coordination fundamentals for live events</div><div class="edu-meta"><span>🎓 Intermediate</span><span>👁 3,200</span></div></div></div>
    <div class="edu-card"><div class="edu-thumb" style="background:#0E2010">📄</div><div class="edu-body"><div class="edu-type">Document · PDF</div><div class="edu-title">Stage manager's field guide — show day protocol</div><div class="edu-meta"><span>🎓 All levels</span><span>⬇ 1,840</span></div></div></div>
    <div class="edu-card"><div class="edu-thumb" style="background:#1A1020">🎓</div><div class="edu-body"><div class="edu-type">Course · 6 modules</div><div class="edu-title">Producing corporate events from contract to curtain</div><div class="edu-meta"><span>🎓 Producer track</span><span>👥 820</span></div></div></div>
    <div class="edu-card"><div class="edu-thumb" style="background:#1A1000">📊</div><div class="edu-body"><div class="edu-type">Document · Reference</div><div class="edu-title">Audio system gain structure — the definitive guide</div><div class="edu-meta"><span>🎓 Technical</span><span>👁 5,100</span></div></div></div>
    <div class="edu-card"><div class="edu-thumb" style="background:#0A1A18">📹</div><div class="edu-body"><div class="edu-type">Video · 28 min</div><div class="edu-title">Reading and building a lighting plot from scratch</div><div class="edu-meta"><span>🎓 Intermediate</span><span>👁 2,600</span></div></div></div>
    <div class="edu-card"><div class="edu-thumb" style="background:#0E0E1A">📄</div><div class="edu-body"><div class="edu-type">Template</div><div class="edu-title">Production budget template — corporate &amp; live events</div><div class="edu-meta"><span>🎓 All levels</span><span>⬇ 3,400</span></div></div></div>
  </div>
</section>

<section class="section alt" id="technology">
  <div class="sec-hd">
    <div><div class="sec-ey">Gear & specs</div><div class="sec-title">TECHNOLOGY</div><div class="sec-sub">Manuals, spec sheets, and guides for the gear that powers live events.</div></div>
    <a class="see-all" href="#">All gear →</a>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <div style="background:var(--dark-2);border:.5px solid var(--border);border-radius:10px;overflow:hidden;cursor:pointer;transition:all .15s;display:flex" onmouseover="this.style.borderColor='rgba(255,255,255,.13)'" onmouseout="this.style.borderColor='rgba(255,255,255,.07)'"><div style="width:4px;background:#4A9ECC;flex-shrink:0;border-radius:2px 0 0 2px"></div><div style="padding:16px 18px"><div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-3);margin-bottom:6px">Audio</div><div style="font-size:14px;font-weight:500;margin-bottom:4px">Shure Axient Digital AD4Q — full manual</div><div style="font-size:12px;color:var(--text-3)">📄 PDF · 148 pages · Updated Jan 2025</div></div></div>
    <div style="background:var(--dark-2);border:.5px solid var(--border);border-radius:10px;overflow:hidden;cursor:pointer;transition:all .15s;display:flex" onmouseover="this.style.borderColor='rgba(255,255,255,.13)'" onmouseout="this.style.borderColor='rgba(255,255,255,.07)'"><div style="width:4px;background:#FFD600;flex-shrink:0;border-radius:2px 0 0 2px"></div><div style="padding:16px 18px"><div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-3);margin-bottom:6px">Lighting</div><div style="font-size:14px;font-weight:500;margin-bottom:4px">grandMA3 full-size — operation guide</div><div style="font-size:12px;color:var(--text-3)">📄 PDF · 620 pages · Updated Mar 2025</div></div></div>
    <div style="background:var(--dark-2);border:.5px solid var(--border);border-radius:10px;overflow:hidden;cursor:pointer;transition:all .15s;display:flex" onmouseover="this.style.borderColor='rgba(255,255,255,.13)'" onmouseout="this.style.borderColor='rgba(255,255,255,.07)'"><div style="width:4px;background:#CE93D8;flex-shrink:0;border-radius:2px 0 0 2px"></div><div style="padding:16px 18px"><div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-3);margin-bottom:6px">Video</div><div style="font-size:14px;font-weight:500;margin-bottom:4px">ROE Visual CB5 LED panel — spec sheet</div><div style="font-size:12px;color:var(--text-3)">📄 PDF · 24 pages · Spec v2.1</div></div></div>
    <div style="background:var(--dark-2);border:.5px solid var(--border);border-radius:10px;overflow:hidden;cursor:pointer;transition:all .15s;display:flex" onmouseover="this.style.borderColor='rgba(255,255,255,.13)'" onmouseout="this.style.borderColor='rgba(255,255,255,.07)'"><div style="width:4px;background:#81C784;flex-shrink:0;border-radius:2px 0 0 2px"></div><div style="padding:16px 18px"><div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-3);margin-bottom:6px">Networking</div><div style="font-size:14px;font-weight:500;margin-bottom:4px">Dante network design for live events</div><div style="font-size:12px;color:var(--text-3)">📄 PDF · 56 pages · Community contributed</div></div></div>
  </div>
</section>

<section class="section" id="forum">
  <div class="sec-hd">
    <div><div class="sec-ey">Community</div><div class="sec-title">FORUM</div><div class="sec-sub">Open to everyone. Read without an account — join free to post.</div></div>
    <a class="see-all" href="#">Browse all posts →</a>
  </div>
  <div class="forum-grid">
    <div class="forum-posts">
      <div class="fp"><div class="fp-left"><div class="fp-cat" style="color:#CC884A">Vendor rec</div><div class="fp-title">Best wireless IEM systems for outdoor festival use in high-RF environments?</div><div class="fp-meta"><span>Jordan K.</span><span>2h ago</span><span>🔥 Trending</span></div></div><div class="fp-replies"><div class="fp-n">22</div><div class="fp-l">replies</div></div></div>
      <div class="fp"><div class="fp-left"><div class="fp-cat" style="color:#4A9ECC">Stage management</div><div class="fp-title">Handling live run of show updates when the client changes timing day-of</div><div class="fp-meta"><span>Mara L.</span><span>5h ago</span></div></div><div class="fp-replies"><div class="fp-n">14</div><div class="fp-l">replies</div></div></div>
      <div class="fp"><div class="fp-left"><div class="fp-cat" style="color:#8888CC">Lighting</div><div class="fp-title">MA3 vs EOS for a touring LD — switching after 8 years on grandMA2</div><div class="fp-meta"><span>Dana L.</span><span>1d ago</span></div></div><div class="fp-replies"><div class="fp-n">38</div><div class="fp-l">replies</div></div></div>
      <div class="fp"><div class="fp-left"><div class="fp-cat" style="color:var(--gold)">Contracts</div><div class="fp-title">Force majeure language that has actually held up — share your clauses</div><div class="fp-meta"><span>Tom R.</span><span>2d ago</span></div></div><div class="fp-replies"><div class="fp-n">47</div><div class="fp-l">replies</div></div></div>
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

<section class="section" id="store">
  <div class="sec-hd">
    <div><div class="sec-ey">Marketplace</div><div class="sec-title">STORE</div><div class="sec-sub">Consumables, tools, and merch for the industry. Launching soon.</div></div>
  </div>
  <div style="background:var(--dark-2);border:.5px solid var(--gold-bd);border-radius:12px;padding:28px 32px;display:flex;align-items:center;justify-content:space-between;max-width:680px">
    <div><div style="font-size:14px;font-weight:500;color:var(--gold);margin-bottom:6px">Store launching soon</div><div style="font-size:13px;color:var(--text-2)">Gaff tape, duvatine, crew merch, and industry-curated gear. Join free to get notified.</div></div>
    <a class="btn-gold-lg" href="app/dashboard.html" style="flex-shrink:0;margin-left:24px">Get notified →</a>
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
    <div class="join-feat">Documents & templates</div>
    <div class="join-feat">No credit card required</div>
  </div>
  <div class="join-ctas">
    <a class="btn-gold-lg" href="app/dashboard.html">Create free account →</a>
    <a class="btn-ghost-lg" href="app/dashboard.html">Sign in</a>
    <a class="hero-demo-cta" href="app/dashboard.html" style="font-size:13px;padding:10px 20px">
      <div class="hero-demo-cta-dot"></div>
      Or try the demo — no signup needed
    </a>
  </div>
</div>



<?php include 'footer.php'; ?>
