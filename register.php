<?php
require_once 'config.php';
require_once 'functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /myprofile.php');
    exit;
}

$page_title = 'Join Free';
$errors = [];
$values = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $ip       = client_ip();

    $values = ['username' => $username, 'email' => $email];

    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3 || strlen($username) > 30) {
        $errors[] = 'Username must be between 3 and 30 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!rate_limit_check($pdo, "register:{$ip}", 'register', 5, 3600)) {
        $errors[] = 'Too many registration attempts from this IP. Try again in an hour.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            $existing = $stmt->fetch();

            if ($existing) {
                $ck = $pdo->prepare('SELECT id FROM users WHERE username = ?');
                $ck->execute([$username]);
                if ($ck->fetch()) {
                    $errors[] = 'That username is already taken.';
                } else {
                    $errors[] = 'An account with that email already exists.';
                }
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error. Please try again.';
        }
    }

    if (empty($errors)) {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?) RETURNING id');
            $stmt->execute([$username, $email, $hashed]);
            $new_id = $stmt->fetchColumn();

            // Send verification email (non-blocking — failure won't stop registration)
            $new_user = ['id' => $new_id, 'username' => $username, 'email' => $email];
            @send_verification_email($pdo, $new_user);

            header('Location: /login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Could not create account. Please try again.';
        }
    }
}

include 'header.php';
?>

<section class="auth-section">
  <div class="auth-card">
    <div class="auth-header">
      <div class="auth-eyebrow">It's free</div>
      <h1 class="auth-title">Create your account</h1>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="auth-error">
        <?php foreach ($errors as $error): ?><div><?php echo e($error); ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
      <?php echo csrf_field(); ?>
      <div class="auth-field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="username"
               maxlength="30" value="<?php echo e($values['username']); ?>">
        <div class="auth-field-hint">Letters, numbers, and underscores only.</div>
      </div>
      <div class="auth-field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autocomplete="email"
               value="<?php echo e($values['email']); ?>">
      </div>
      <div class="auth-field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required
               autocomplete="new-password" minlength="8">
        <div class="auth-field-hint">At least 8 characters.</div>
      </div>
      <div class="auth-field">
        <label for="confirm_password">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password" required
               autocomplete="new-password">
      </div>
      <button type="submit" class="btn-gold-lg" style="width:100%">Create account →</button>
    </form>

    <div class="auth-footer">
      <p>Already have an account? <a href="/login.php">Sign in</a></p>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
