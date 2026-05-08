<?php
http_response_code(500);
$page_title = '500 — Server Error';
$header = file_exists(__DIR__ . '/../header.php') ? __DIR__ . '/../header.php' : null;
if ($header) include $header;
?>

<section class="error-section">
  <div class="error-code">500</div>
  <div class="error-title">Something went wrong</div>
  <div class="error-sub">We're looking into it. Try again in a moment.</div>
  <div class="error-actions">
    <a class="btn-gold-lg" href="/">Go to homepage →</a>
  </div>
</section>

<?php
$footer = file_exists(__DIR__ . '/../footer.php') ? __DIR__ . '/../footer.php' : null;
if ($footer) include $footer;
?>
