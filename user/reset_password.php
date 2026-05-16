<?php
require_once '../config.php';
require_once '../functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /user/myprofile.php');
    exit;
}

$page_title = 'Reset Password';
$errors  = [];
$success = false;

$token = trim($_GET['token'] ?? '');
$user  = null;

if (empty($token)) {
    header('Location: /user/forgot_password.php');
    exit;
}

// Look up token
$stmt = $pdo->prepare('
    SELECT pr.id AS reset_id, pr.user_id, pr.expires_at, pr.used,
           u.username, u.email
    FROM   password_resets pr
    JOIN   users u ON u.id = pr.user_id
    WHERE  pr.token = ?
    LIMIT  1
');
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset || $reset['used'] || strtotime($reset['expires_at']) < time()) {
    $invalid = true;
} else {
    $invalid = false;
    $user    = $reset;
}

if (!$invalid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
            ->execute([$hash, $user['user_id']]);
        $pdo->prepare('UPDATE password_resets SET used = TRUE WHERE id = ?')
            ->execute([$reset['reset_id']]);
        $success = true;
    }
}

include '../header.php';
?>

<section class="auth-section">
  <div class="auth-card">

    <?php if ($invalid): ?>
      <div class="auth-header">
        <div class="auth-eyebrow">Link expired</div>
        <h1 class="auth-title">Invalid reset link</h1>
      </div>
      <div class="auth-error">This reset link has expired or already been used.</div>
      <div class="auth-footer">
        <p><a href="/forgot_password.php">Request a new link →</a></p>
      </div>

    <?php elseif ($success): ?>
      <div class="auth-header">
        <div class="auth-eyebrow">All done</div>
        <h1 class="auth-title">Password updated</h1>
      </div>
      <div class="auth-success">Your password has been reset. You can now sign in.</div>
      <div class="auth-footer" style="margin-top:0">
        <p><a href="/login.php">Sign in →</a></p>
      </div>

    <?php else: ?>
      <div class="auth-header">
        <div class="auth-eyebrow">Almost there</div>
        <h1 class="auth-title">New password</h1>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="auth-error">
          <?php foreach ($errors as $e): ?><div><?php echo e($e); ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="auth-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="token" value="<?php echo e($token); ?>">
        <div class="auth-field">
          <label for="password">New password</label>
          <input type="password" id="password" name="password" required
                 autocomplete="new-password" minlength="8">
          <div class="auth-field-hint">At least 8 characters.</div>
        </div>
        <div class="auth-field">
          <label for="confirm_password">Confirm new password</label>
          <input type="password" id="confirm_password" name="confirm_password" required
                 autocomplete="new-password">
        </div>
        <button type="submit" class="btn-gold-lg" style="width:100%">Set new password →</button>
      </form>

    <?php endif; ?>
  </div>
</section>

<?php include '../footer.php'; ?>
