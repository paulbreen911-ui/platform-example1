<?php
require_once 'config.php';
require_once 'functions.php';

$page_title = 'Verify Email';
$token = trim($_GET['token'] ?? '');

if (empty($token)) {
    header('Location: /');
    exit;
}

$stmt = $pdo->prepare('
    SELECT ev.id, ev.user_id, ev.used, ev.expires_at, u.username
    FROM   email_verifications ev
    JOIN   users u ON u.id = ev.user_id
    WHERE  ev.token = ?
    LIMIT  1
');
$stmt->execute([$token]);
$ev = $stmt->fetch();

if (!$ev || $ev['used'] || strtotime($ev['expires_at']) < time()) {
    $status = 'invalid';
} else {
    $pdo->prepare('UPDATE users SET email_verified = TRUE WHERE id = ?')->execute([$ev['user_id']]);
    $pdo->prepare('UPDATE email_verifications SET used = TRUE WHERE id = ?')->execute([$ev['id']]);
    $status = 'success';
}

include 'header.php';
?>

<section class="auth-section">
  <div class="auth-card" style="text-align:center">

    <?php if ($status === 'success'): ?>
      <div style="font-size:40px;margin-bottom:16px">✓</div>
      <div class="auth-eyebrow">Verified</div>
      <h1 class="auth-title" style="margin-bottom:16px">Email confirmed</h1>
      <p style="color:var(--text-2);font-size:14px;margin-bottom:24px">
        Your email is verified. Welcome to Production Central.
      </p>
      <a class="btn-gold-lg" href="/">Go to homepage →</a>

    <?php else: ?>
      <div style="font-size:40px;margin-bottom:16px">✗</div>
      <div class="auth-eyebrow">Error</div>
      <h1 class="auth-title" style="margin-bottom:16px">Link invalid</h1>
      <p style="color:var(--text-2);font-size:14px;margin-bottom:24px">
        This verification link has expired or already been used.
      </p>
      <a class="btn-ghost-lg" href="/">Go to homepage</a>
    <?php endif; ?>

  </div>
</section>

<?php include 'footer.php'; ?>
