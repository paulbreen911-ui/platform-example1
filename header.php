<?php
// Session is started in config.php — included before header.php on every page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' — Production Central' : 'Production Central'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/style.css" />
</head>
<body>

<nav class="nav">
  <a class="nav-logo" href="/">PRODUCTION<span>.</span>CENTRAL</a>
  <div class="nav-links">
    <a class="nav-link" href="/#forum">Forum</a>
    <a class="nav-link" href="/#education">Education</a>
    <a class="nav-link" href="/#reference">Reference</a>
    <a class="nav-link" href="/#technology">Technology</a>
    <a class="nav-link" href="/tools/tools-index.html">Tools</a>
    <a class="nav-link" href="/#life">Life</a>
    <a class="nav-link store" href="/#store">Store</a>
  </div>
  <div class="nav-right">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a class="nav-link" href="/myprofile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
      <a class="btn-signin" href="/logout.php">Log out</a>
    <?php else: ?>
      <a class="btn-demo" href="/app/dashboard.html">
        <div class="btn-demo-dot"></div>
        Try demo
      </a>
      <a class="btn-signin" href="/login.php">Sign in</a>
      <a class="btn-join" href="/register.php">Join free →</a>
    <?php endif; ?>
  </div>
</nav>

<main>
