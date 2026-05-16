<?php
require_once __DIR__ . '/config.php';
http_response_code(500);
$page_title = '500 — Server Error';
include ROOT_PATH . '/required/header.php';
?>

<section class="error-section">
  <div class="error-code">500</div>
  <div class="error-title">Something went wrong. I guess the ones and zeros aren't doing their thing or something or other.</div>
  <div class="error-sub">We're looking into it. Try again in a moment.</div>
  <div class="error-actions">
    <a class="btn-gold-lg" href="/">Check out our homepage →</a>
  </div>
</section>

<?php
include ROOT_PATH . '/required/footer.php';
?>
