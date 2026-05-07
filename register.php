<?php
require_once 'config.php';

// Already logged in — send to profile
if (isset($_SESSION['user_id'])) {
    header('Location: /myprofile.php');
    exit;
}

$page_title = 'Join Free';
$errors = [];
$values = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $values = ['username' => $username, 'email' => $email];

    // Validate
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3 || strlen($username) > 30) {
        $errors[] = 'Username must be between 3 and 30 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Check for existing username/email
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Find out which one is taken
                $stmt2 = $pdo->prepare('SELECT id FROM users WHERE username = ?');
                $stmt2->execute([$username]);
                if ($stmt2->fetch()) {
                    $errors[] = 'That username is already taken.';
                } else {
                    $errors[] = 'An account with that email already exists.';
                }
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error. Please try again.';
        }
    }

    // All good — create the account
    if (empty($errors)) {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$username, $email, $hashed]);

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
        <?php foreach ($errors as $error): ?>
          <div><?php echo htmlspecialchars($error); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="/register.php" class="auth-form">
      <div class="auth-field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required
               autocomplete="username"
               maxlength="30"
               value="<?php echo htmlspecialchars($values['username']); ?>">
        <div class="auth-field-hint">Letters, numbers, and underscores only.</div>
      </div>
      <div class="auth-field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
               autocomplete="email"
               value="<?php echo htmlspecialchars($values['email']); ?>">
      </div>
      <div class="auth-field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required
               autocomplete="new-password"
               minlength="8">
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
