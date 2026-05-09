<?php
require_once 'config.php';
require_once 'functions.php';
require_login();

$page_title = 'My Profile';

$user = get_user_by_id($pdo, $_SESSION['user_id']);
if (!$user) {
    session_destroy();
    header('Location: /login.php');
    exit;
}

$errors   = [];
$success  = [];
$tab      = $_GET['tab'] ?? 'overview';

// ── Handle POST actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $display_name = trim($_POST['display_name'] ?? '');
        $bio          = trim($_POST['bio']          ?? '');
        $new_email    = trim($_POST['email']        ?? '');
        $tab          = 'settings';

        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif (mb_strlen($bio) > 500) {
            $errors[] = 'Bio must be 500 characters or fewer.';
        } else {
            if ($new_email !== $user['email']) {
                $ck = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
                $ck->execute([$new_email, $user['id']]);
                if ($ck->fetch()) {
                    $errors[] = 'That email is already in use.';
                } else {
                    $pdo->prepare('UPDATE users SET email = ?, email_verified = FALSE WHERE id = ?')
                        ->execute([$new_email, $user['id']]);
                    $pdo->prepare('UPDATE email_verifications SET used = TRUE WHERE user_id = ?')
                        ->execute([$user['id']]);
                    send_verification_email($pdo, array_merge($user, ['email' => $new_email]));
                    $success[] = 'Email updated. A verification link has been sent to your new address.';
                }
            }
            if (empty($errors)) {
                $pdo->prepare('UPDATE users SET display_name = ?, bio = ? WHERE id = ?')
                    ->execute([$display_name ?: null, $bio ?: null, $user['id']]);
                $success[] = 'Profile updated.';
                $user = get_user_by_id($pdo, $user['id']);
            }
        }
    }

    if ($action === 'change_password') {
        $tab     = 'settings';
        $current = $_POST['current_password'] ?? '';
        $newpass = $_POST['new_password']      ?? '';
        $confirm = $_POST['confirm_password']  ?? '';

        if (!password_verify($current, $user['password'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (strlen($newpass) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        } elseif ($newpass !== $confirm) {
            $errors[] = 'Passwords do not match.';
        } else {
            $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([password_hash($newpass, PASSWORD_DEFAULT), $user['id']]);
            $success[] = 'Password changed successfully.';
        }
    }

    if ($action === 'resend_verify') {
        $tab = 'settings';
        if (!$user['email_verified']) {
            $pdo->prepare('UPDATE email_verifications SET used = TRUE WHERE user_id = ?')->execute([$user['id']]);
            send_verification_email($pdo, $user);
            $success[] = 'Verification email sent.';
        }
    }
}

// ── Load data ─────────────────────────────────────────────────
$prods_stmt = $pdo->prepare('SELECT * FROM productions WHERE user_id = ? ORDER BY created_at DESC LIMIT 20');
$prods_stmt->execute([$user['id']]);
$productions = $prods_stmt->fetchAll();

$threads_stmt = $pdo->prepare('
    SELECT ft.id, ft.title, ft.created_at, ft.reply_count, fc.name AS category_name, fc.color
    FROM   forum_threads ft
    JOIN   forum_categories fc ON fc.id = ft.category_id
    WHERE  ft.user_id = ?
    ORDER  BY ft.created_at DESC LIMIT 10
');
$threads_stmt->execute([$user['id']]);
$threads = $threads_stmt->fetchAll();

$member_since = date('F j, Y', strtotime($user['created_at']));
include 'header.php';
?>

<section class="profile-section">

  <div class="profile-header">
    <div class="profile-avatar"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
    <div class="profile-header-info">
      <div class="profile-eyebrow">Member</div>
      <h1 class="profile-name"><?php echo e($user['display_name'] ?: $user['username']); ?></h1>
      <div class="profile-since">@<?php echo e($user['username']); ?> · Member since <?php echo $member_since; ?></div>
    </div>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="auth-error" style="margin-bottom:24px">
      <?php foreach ($errors as $err): ?><div><?php echo e($err); ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
    <div class="auth-success" style="margin-bottom:24px">
      <?php foreach ($success as $msg): ?><div><?php echo e($msg); ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!($user['email_verified'] ?? false)): ?>
    <div class="verify-banner">
      <span>⚠ Your email address is not verified.</span>
      <form method="POST" style="display:inline">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="resend_verify">
        <button type="submit" class="verify-banner-btn">Resend verification email</button>
      </form>
    </div>
  <?php endif; ?>

  <div class="profile-tabs">
    <a class="profile-tab <?php echo $tab === 'overview'    ? 'active' : ''; ?>" href="?tab=overview">Overview</a>
    <a class="profile-tab <?php echo $tab === 'productions' ? 'active' : ''; ?>" href="?tab=productions">Productions</a>
    <a class="profile-tab <?php echo $tab === 'forum'       ? 'active' : ''; ?>" href="?tab=forum">Forum posts</a>
    <a class="profile-tab <?php echo $tab === 'settings'    ? 'active' : ''; ?>" href="?tab=settings">Settings</a>
  </div>

  <?php if ($tab === 'overview'): ?>
    <div class="profile-grid">
      <div class="profile-card">
        <div class="profile-card-label">Username</div>
        <div class="profile-card-value"><?php echo e($user['username']); ?></div>
      </div>
      <div class="profile-card">
        <div class="profile-card-label">Email</div>
        <div class="profile-card-value">
          <?php echo e($user['email']); ?>
          <?php if ($user['email_verified'] ?? false): ?>
            <span class="verified-badge">✓</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="profile-card">
        <div class="profile-card-label">Member since</div>
        <div class="profile-card-value"><?php echo $member_since; ?></div>
      </div>
    </div>
    <?php if ($user['bio']): ?>
      <p class="profile-bio"><?php echo e($user['bio']); ?></p>
    <?php endif; ?>
    <div class="profile-actions">
      <a class="btn-ghost-lg" href="/">← Home</a>
      <a class="btn-ghost-lg" href="?tab=settings">Edit profile</a>
      <a class="btn-signin" href="/logout.php">Log out</a>
    </div>

  <?php elseif ($tab === 'productions'): ?>
    <?php if (empty($productions)): ?>
      <div class="profile-empty">
        <div class="profile-empty-icon">🎭</div>
        <div class="profile-empty-title">No productions yet</div>
        <div class="profile-empty-sub">Productions you create will appear here.</div>
      </div>
    <?php else: ?>
      <div class="prod-list">
        <?php foreach ($productions as $p): ?>
          <div class="prod-row">
            <div class="prod-dot prod-dot-<?php echo e($p['status']); ?>"></div>
            <div class="prod-info">
              <div class="prod-name"><?php echo e($p['name']); ?></div>
              <div class="prod-meta">
                <?php if ($p['venue']): ?><span><?php echo e($p['venue']); ?></span><?php endif; ?>
                <?php if ($p['event_date']): ?><span><?php echo date('M j, Y', strtotime($p['event_date'])); ?></span><?php endif; ?>
              </div>
            </div>
            <div class="prod-status prod-status-<?php echo e($p['status']); ?>"><?php echo e($p['status']); ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php elseif ($tab === 'forum'): ?>
    <?php if (empty($threads)): ?>
      <div class="profile-empty">
        <div class="profile-empty-icon">💬</div>
        <div class="profile-empty-title">No posts yet</div>
        <div class="profile-empty-sub"><a href="/forum.php" style="color:var(--gold)">Start a thread →</a></div>
      </div>
    <?php else: ?>
      <div class="forum-posts">
        <?php foreach ($threads as $t): ?>
          <a class="fp" href="/forum_thread.php?id=<?php echo $t['id']; ?>" style="text-decoration:none">
            <div class="fp-left">
              <div class="fp-cat" style="color:<?php echo e($t['color']); ?>"><?php echo e($t['category_name']); ?></div>
              <div class="fp-title"><?php echo e($t['title']); ?></div>
              <div class="fp-meta"><span><?php echo time_ago($t['created_at']); ?></span></div>
            </div>
            <div class="fp-replies">
              <div class="fp-n"><?php echo $t['reply_count']; ?></div>
              <div class="fp-l">replies</div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php elseif ($tab === 'settings'): ?>
    <div style="display:grid;gap:24px;max-width:560px">

      <div class="settings-card">
        <div class="settings-card-title">Profile information</div>
        <form method="POST" class="auth-form" style="gap:14px">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="update_profile">
          <div class="auth-field">
            <label>Display name</label>
            <input type="text" name="display_name" maxlength="60"
                   value="<?php echo e($user['display_name'] ?? ''); ?>"
                   placeholder="Shown instead of username">
          </div>
          <div class="auth-field">
            <label>Email</label>
            <input type="email" name="email" required value="<?php echo e($user['email']); ?>">
            <?php if (!($user['email_verified'] ?? false)): ?>
              <div class="auth-field-hint" style="color:#F4805A">Not verified</div>
            <?php endif; ?>
          </div>
          <div class="auth-field">
            <label>Bio</label>
            <textarea name="bio" maxlength="500" rows="3" class="settings-textarea"><?php echo e($user['bio'] ?? ''); ?></textarea>
            <div class="auth-field-hint">500 characters max.</div>
          </div>
          <button type="submit" class="btn-gold-lg" style="width:100%;margin-top:4px">Save changes →</button>
        </form>
      </div>

      <div class="settings-card">
        <div class="settings-card-title">Change password</div>
        <form method="POST" class="auth-form" style="gap:14px">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="change_password">
          <div class="auth-field">
            <label>Current password</label>
            <input type="password" name="current_password" required autocomplete="current-password">
          </div>
          <div class="auth-field">
            <label>New password</label>
            <input type="password" name="new_password" required autocomplete="new-password" minlength="8">
            <div class="auth-field-hint">At least 8 characters.</div>
          </div>
          <div class="auth-field">
            <label>Confirm new password</label>
            <input type="password" name="confirm_password" required autocomplete="new-password">
          </div>
          <button type="submit" class="btn-gold-lg" style="width:100%;margin-top:4px">Change password →</button>
        </form>
      </div>

    </div>
  <?php endif; ?>

</section>

<?php include 'footer.php'; ?>
