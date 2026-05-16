<?php
// $page_title must be set by the including page before this is included
$username = $_SESSION['display_name'] ?? $_SESSION['username'] ?? 'there';
$initials = strtoupper(substr($username, 0, 1));
// If you store role in session, use it; otherwise hardcode for now
$role = $_SESSION['role'] ?? 'Senior producer';
?>
<aside class="sidebar" id="pc-sidebar">
  <a class="sb-new" href="/overview/new-production.php">+ New project / production</a>

  <div class="sb-sec">
    <span class="sb-lbl">Workspace</span>
    <a class="sb-link<?= $page_title==='Overview'    ? ' active' : '' ?>" href="/overview/overview.php"><span class="icon">◻</span>Dashboard</a>
    <a class="sb-link<?= $page_title==='Messages'    ? ' active' : '' ?>" href="/overview/messages.php"><span class="icon">💬</span>Messages<span class="sb-badge unread">4</span></a>
    <a class="sb-link<?= $page_title==='Drive'       ? ' active' : '' ?>" href="/overview/drive.php"><span class="icon">📁</span>My Drive</a>
    <a class="sb-link<?= $page_title==='Projects'    ? ' active' : '' ?>" href="/overview/projects.php"><span class="icon">🏗</span>Projects<span class="sb-badge">2</span></a>
    <a class="sb-link<?= $page_title==='Productions' ? ' active' : '' ?>" href="/overview/productions.php"><span class="icon">🎭</span>Productions<span class="sb-badge">3</span></a>
    <a class="sb-link<?= $page_title==='Calendar'    ? ' active' : '' ?>" href="/overview/calendar.php"><span class="icon">📅</span>Calendar</a>
    <a class="sb-link<?= $page_title==='Tasks'       ? ' active' : '' ?>" href="/overview/tasks.php"><span class="icon">✅</span>Tasks<span class="sb-badge">11</span></a>
  </div>

  <div class="sb-sec">
    <span class="sb-lbl">Active now</span>
    <div class="sb-item" onclick="location.href='/overview/production.php?id=gala'">
      <div class="sb-dot" style="background:#E24B4A"></div>
      <div class="sb-iname">Summerset Gala</div>
      <span class="sb-live-tag">LIVE</span>
    </div>
    <div class="sb-item" onclick="location.href='/overview/production.php?id=summit'">
      <div class="sb-dot" style="background:#378ADD"></div>
      <div class="sb-iname">Tech Summit 2025</div>
    </div>
    <div class="sb-item" onclick="location.href='/overview/project.php?id=sphere'">
      <div class="sb-dot" style="background:#4DB6AC"></div>
      <div class="sb-iname">Sphere Residency</div>
    </div>
  </div>

  <div class="sb-sec">
    <span class="sb-lbl">Platform</span>
    <a class="sb-link" href="#"><span class="icon">💬</span>Forum</a>
    <a class="sb-link" href="#"><span class="icon">🔧</span>Tools</a>
    <a class="sb-link" href="#"><span class="icon">🎓</span>Education</a>
  </div>

  <div class="sb-bottom">
    <div class="sb-uav"><?= htmlspecialchars($initials) ?></div>
    <div>
      <div class="sb-uname"><?= htmlspecialchars($username) ?></div>
      <div class="sb-urole"><?= htmlspecialchars($role) ?></div>
    </div>
  </div>
</aside>