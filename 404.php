<?php
if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';
}

http_response_code(404);
$page_title = '404 — Not Found';
$header = file_exists(__DIR__ . '/../header.php') ? __DIR__ . '/../header.php' : null;
if ($header) include $header;
?>

<section class="error-section">
  <div class="error-code">404</div>
  <div class="error-title">Page not found</div>
  <div class="error-sub">That page doesn't exist, or it may have moved.</div>
  <div class="error-actions">
    <a class="btn-gold-lg" href="/">Go to homepage →</a>
    <a class="btn-ghost-lg" href="/forum.php">Browse the forum</a>
  </div>
</section>

<?php
$footer = file_exists(__DIR__ . '/../footer.php') ? __DIR__ . '/../footer.php' : null;
if ($footer) include $footer;
?>
