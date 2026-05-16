<?php
require_once __DIR__ . '/config.php';
http_response_code(404);
$page_title = '404 — Not Found';
include ROOT_PATH . '/required/header.php';
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
include ROOT_PATH . '/required/footer.php';
?>
