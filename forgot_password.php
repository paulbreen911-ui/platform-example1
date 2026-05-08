<?php
require_once 'config.php';
require_once 'functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /myprofile.php');
    exit;
}

$page_title = 'Forgot Password';
$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = trim($_POST['email'] ?? '');
    $ip    = client_ip();

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } elseif (!rate_limit_check($pdo, "forgot:{$ip}", 'forgot_password', 3, 600)) {
        $errors[] = 'Too many attempts. Please wait 10 minutes and try again.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Always show success — don't reveal whether email exists
        if ($user) {
            send_password_reset($pdo, $user);
        }

        $success = true;
    }
}

include 'header.php';
?>

<section class="auth-section">
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-eyebrow">Account recovery</div>
      <h1 class="auth-title">Forgot password</h1>
    </div>

    <?php if ($success): ?>
      <div class="auth-success">
        If that email has an account, we've sent a reset link. Check your inbox — it expires in 1 hour.
      </div>
      <div class="auth-footer" style="margin-top:0">
        <p><a href="/login.php">← Back to sign in</a></p>
      </div>
    <?php else: ?>

      <?php if (!empty($errors)): ?>
        <div class="auth-error">
          <?php foreach ($errors as $e): ?><div><?php echo e($e); ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="auth-form">
        <?php echo csrf_field(); ?>
        <div class="auth-field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" required autocomplete="email"
                 value="<?php echo e($_POST['email'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn-gold-lg" style="width:100%">Send reset link →</button>
      </form>

      <div class="auth-footer">
        <p><a href="/login.php">← Back to sign in</a></p>
      </div>

    <?php endif; ?>
  </div>
</section>

<?php include 'footer.php'; ?>
