<?php
// Session is started in config.php, which must be included before header.php.
if (!isset($pdo)) { require_once __DIR__ . '/config.php'; }
if (!isset($_SESSION['csrf_token'])) { require_once __DIR__ . '/functions.php'; csrf_token(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' — Production Central' : 'Production Central'; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<nav class="nav">
  <a class="nav-logo" href="/">PRODUCTION<span>.</span>CENTRAL</a>
  <div class="nav-links">
    <a class="nav-link" href="/overview/overview.php">Overview</a>
    <a class="nav-link" href="/tools/tools.php">Tools</a>
    <a class="nav-link" href="/reference/reference.php">Reference</a>
    <a class="nav-link" href="/education/education.php">Education</a>
    <a class="nav-link" href="/technology/technology.php">Technology</a>
    <a class="nav-link" href="/forum/forum.php">Forum</a>
    <a class="nav-link" href="/life/life.php">Life</a>
    <a class="nav-link store" href="/store/#store">Store</a>
  </div>
  <div class="nav-right">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a class="nav-link" href="/myprofile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
      <a class="btn-signin" href="/logout.php">Log out</a>
    <?php else: ?>
      <a class="btn-demo" href="/overview/overview.php">
        <div class="btn-demo-dot"></div>
        Try demo
      </a>
      <a class="btn-signin" href="/login.php">Sign in</a>
      <a class="btn-join" href="/register.php">Join free →</a>
    <?php endif; ?>
  </div>
  <!-- Mobile hamburger -->
  <button class="nav-hamburger" aria-label="Open menu" onclick="toggleMobileNav()">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- Mobile nav drawer -->
<div class="mobile-nav" id="mobile-nav">
  <div class="mobile-nav-inner">
    <a class="mobile-nav-link" href="/overview/overview.php">Overview</a>
    <a class="mobile-nav-link" href="/tools/tools.php">Tools</a>
    <a class="mobile-nav-link" href="/reference/reference.php">Reference</a>
    <a class="mobile-nav-link" href="/education/education.php">Education</a>
    <a class="mobile-nav-link" href="/technology/technology.php">Technology</a>
    <a class="mobile-nav-link" href="/forum/forum.php">Forum</a>
    <a class="mobile-nav-link" href="/life/life.php">Life</a>
    <a class="mobile-nav-link" href="/store/#store" style="color:var(--gold)">Store</a>
    <div class="mobile-nav-divider"></div>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a class="mobile-nav-link" href="/myprofile.php">My Profile</a>
      <a class="mobile-nav-link" href="/logout.php">Log out</a>
    <?php else: ?>
      <a class="mobile-nav-link" href="/login.php">Sign in</a>
      <a class="btn-gold-lg" href="/register.php" style="margin-top:8px;text-align:center">Join free →</a>
    <?php endif; ?>
  </div>
</div>
<div class="mobile-nav-overlay" id="mobile-nav-overlay" onclick="toggleMobileNav()"></div>

<script>
function toggleMobileNav() {
  document.getElementById('mobile-nav').classList.toggle('open');
  document.getElementById('mobile-nav-overlay').classList.toggle('open');
  document.body.classList.toggle('nav-open');
}
</script>

<main>
