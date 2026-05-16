<?php
$page_title = 'Sign In';

require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/functions.php';
include ROOT_PATH . '/required/header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /user/myprofile.php');
    exit;
}

?>

<section class="auth-section">
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-eyebrow">Welcome back</div>
      <h1 class="auth-title">Sign in</h1>
    </div>

    <?php if (isset($_GET['error'])): ?>
      <div class="auth-error">
        <?php
          if ($_GET['error'] === 'invalid')   echo 'Incorrect username or password.';
          if ($_GET['error'] === 'required')  echo 'Please fill in all fields.';
          if ($_GET['error'] === 'ratelimit') echo 'Too many failed attempts. Please wait 5 minutes.';
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
      <div class="auth-success">Account created — you can now sign in.</div>
    <?php endif; ?>

    <?php if (isset($_GET['reset'])): ?>
      <div class="auth-success">Password reset. You can sign in with your new password.</div>
    <?php endif; ?>

    <form method="POST" action="/user/login_process.php" class="auth-form">
      <?php echo csrf_field(); ?>
      <div class="auth-field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="username">
      </div>
      <div class="auth-field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn-gold-lg" style="width:100%">Sign in →</button>
    </form>

    <div class="auth-footer">
      <p>Don't have an account? <a href="/user/register.php">Join free</a></p>
      <p><a href="/user/forgot_password.php">Forgot your password?</a></p>
      <p class="auth-demo">Demo: <strong>testuser</strong> / <strong>password123</strong></p>
    </div>
  </div>
</section>

<?php include ROOT_PATH . '/required/footer.php'; ?>
