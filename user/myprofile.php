<?php

$page_title = 'My Profile';

require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/functions.php';
include ROOT_PATH . '/required/header.php';

require_login();

$user = get_user_by_id($pdo, $_SESSION['user_id']);
if (!$user) {
    session_destroy();
    header('Location: /user/login.php');
    exit;
}

$errors  = [];
$success = [];
$tab     = $_GET['tab'] ?? 'overview';

// ── Handle POST actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    // ── Update identity (username + display name) ─────────────
    if ($action === 'update_identity') {
        $tab          = 'settings';
        $new_username = trim($_POST['username']     ?? '');
        $display_name = trim($_POST['display_name'] ?? '');

        if (empty($new_username)) {
            $errors[] = 'Username is required.';
        } elseif (strlen($new_username) < 3 || strlen($new_username) > 30) {
            $errors[] = 'Username must be between 3 and 30 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores.';
        } elseif ($new_username !== $user['username']) {
            $ck = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
            $ck->execute([$new_username, $user['id']]);
            if ($ck->fetch()) $errors[] = 'That username is already taken.';
        }

        if (empty($errors)) {
            $pdo->prepare('UPDATE users SET username = ?, display_name = ? WHERE id = ?')
                ->execute([$new_username, $display_name ?: null, $user['id']]);
            $_SESSION['username'] = $new_username;
            $success[] = 'Identity updated.';
            $user = get_user_by_id($pdo, $user['id']);
        }
    }

    // ── Update contact info ───────────────────────────────────
    if ($action === 'update_contact') {
        $tab       = 'settings';
        $new_email = trim($_POST['email']   ?? '');
        $phone     = trim($_POST['phone']   ?? '');
        $linkedin  = trim($_POST['linkedin_url'] ?? '');
        $bio       = trim($_POST['bio']     ?? '');

        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($linkedin && !filter_var($linkedin, FILTER_VALIDATE_URL)) {
            $errors[] = 'LinkedIn URL is not valid.';
        }
        if (mb_strlen($bio) > 500) {
            $errors[] = 'Bio must be 500 characters or fewer.';
        }

        if (empty($errors)) {
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
        }
        if (empty($errors)) {
            $pdo->prepare('UPDATE users SET phone = ?, linkedin_url = ?, bio = ? WHERE id = ?')
                ->execute([$phone ?: null, $linkedin ?: null, $bio ?: null, $user['id']]);
            $success[] = 'Contact info updated.';
            $user = get_user_by_id($pdo, $user['id']);
        }
    }

    // ── Update address ────────────────────────────────────────
    if ($action === 'update_address') {
        $tab = 'settings';
        $pdo->prepare('
            UPDATE users SET
                address_line1 = ?, address_line2 = ?,
                city = ?, state = ?, zip = ?, country = ?
            WHERE id = ?
        ')->execute([
            trim($_POST['address_line1'] ?? '') ?: null,
            trim($_POST['address_line2'] ?? '') ?: null,
            trim($_POST['city']          ?? '') ?: null,
            trim($_POST['state']         ?? '') ?: null,
            trim($_POST['zip']           ?? '') ?: null,
            trim($_POST['country']       ?? '') ?: null,
            $user['id'],
        ]);
        $success[] = 'Address updated.';
        $user = get_user_by_id($pdo, $user['id']);
    }

    // ── Update emergency contact ──────────────────────────────
    if ($action === 'update_emergency') {
        $tab      = 'settings';
        $ec_email = trim($_POST['ec_email'] ?? '');
        if ($ec_email && !filter_var($ec_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Emergency contact email is not valid.';
        }
        if (empty($errors)) {
            $pdo->prepare('
                UPDATE users SET ec_name = ?, ec_relation = ?, ec_phone = ?, ec_email = ?
                WHERE id = ?
            ')->execute([
                trim($_POST['ec_name']     ?? '') ?: null,
                trim($_POST['ec_relation'] ?? '') ?: null,
                trim($_POST['ec_phone']    ?? '') ?: null,
                $ec_email ?: null,
                $user['id'],
            ]);
            $success[] = 'Emergency contact updated.';
            $user = get_user_by_id($pdo, $user['id']);
        }
    }

    // ── Update avatar URL ─────────────────────────────────────
    if ($action === 'update_avatar') {
        $tab        = 'settings';
        $avatar_url = trim($_POST['avatar_url'] ?? '');
        if ($avatar_url && !filter_var($avatar_url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Avatar URL is not valid.';
        } elseif ($avatar_url && !preg_match('/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i', $avatar_url)) {
            $errors[] = 'Avatar URL must point to an image file (jpg, png, gif, webp).';
        } else {
            $pdo->prepare('UPDATE users SET avatar_url = ? WHERE id = ?')
                ->execute([$avatar_url ?: null, $user['id']]);
            $success[] = 'Profile photo updated.';
            $user = get_user_by_id($pdo, $user['id']);
        }
    }

    // ── Change password ───────────────────────────────────────
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

    // ── Resend verification ───────────────────────────────────
    if ($action === 'resend_verify') {
        $tab = 'settings';
        if (!($user['email_verified'] ?? false)) {
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

// Avatar: use uploaded URL or fall back to initial
$has_avatar = !empty($user['avatar_url']);

include '../header.php';
?>

<section class="profile-section">

  <!-- Header -->
  <div class="profile-header">
    <div class="profile-avatar-wrap">
      <?php if ($has_avatar): ?>
        <img src="<?php echo e($user['avatar_url']); ?>" alt="<?php echo e($user['username']); ?>" class="profile-avatar-img">
      <?php else: ?>
        <div class="profile-avatar"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
      <?php endif; ?>
    </div>
    <div class="profile-header-info">
      <div class="profile-eyebrow">Member</div>
      <h1 class="profile-name"><?php echo e($user['display_name'] ?: $user['username']); ?></h1>
      <div class="profile-since">
        @<?php echo e($user['username']); ?>
        <?php if ($user['display_name']): ?>
          <span style="color:var(--text-3)">·</span> <?php echo e($user['display_name']); ?>
        <?php endif; ?>
        <span style="color:var(--text-3)">·</span> Member since <?php echo $member_since; ?>
      </div>
      <?php if (!empty($user['linkedin_url'])): ?>
        <a href="<?php echo e($user['linkedin_url']); ?>" target="_blank" rel="noopener noreferrer" class="profile-linkedin">LinkedIn →</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Alerts -->
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

  <!-- Email verify banner -->
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

  <!-- Tabs -->
  <div class="profile-tabs">
    <a class="profile-tab <?php echo $tab === 'overview'    ? 'active' : ''; ?>" href="?tab=overview">Overview</a>
    <a class="profile-tab <?php echo $tab === 'productions' ? 'active' : ''; ?>" href="?tab=productions">Productions</a>
    <a class="profile-tab <?php echo $tab === 'forum'       ? 'active' : ''; ?>" href="?tab=forum">Forum posts</a>
    <a class="profile-tab <?php echo $tab === 'settings'    ? 'active' : ''; ?>" href="?tab=settings">Settings</a>
    <a class="profile-tab" href="/games//tictactoe/dashboard.php">🎮 Games</a>
  </div>

  <!-- ── OVERVIEW ─────────────────────────────────────────── -->
  <?php if ($tab === 'overview'): ?>

    <div class="profile-grid">
      <div class="profile-card">
        <div class="profile-card-label">Username</div>
        <div class="profile-card-value"><?php echo e($user['username']); ?></div>
      </div>
      <div class="profile-card">
        <div class="profile-card-label">Display name</div>
        <div class="profile-card-value"><?php echo e($user['display_name'] ?? '—'); ?></div>
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
      <?php if (!empty($user['phone'])): ?>
        <div class="profile-card">
          <div class="profile-card-label">Phone</div>
          <div class="profile-card-value"><?php echo e($user['phone']); ?></div>
        </div>
      <?php endif; ?>
      <?php if (!empty($user['city']) || !empty($user['state'])): ?>
        <div class="profile-card">
          <div class="profile-card-label">Location</div>
          <div class="profile-card-value"><?php echo e(implode(', ', array_filter([$user['city'], $user['state']]))); ?></div>
        </div>
      <?php endif; ?>
      <div class="profile-card">
        <div class="profile-card-label">Member since</div>
        <div class="profile-card-value"><?php echo $member_since; ?></div>
      </div>
    </div>

    <?php if (!empty($user['bio'])): ?>
      <p class="profile-bio"><?php echo e($user['bio']); ?></p>
    <?php endif; ?>

    <?php if (!empty($user['ec_name'])): ?>
      <div class="profile-ec-card">
        <div class="profile-card-label" style="margin-bottom:10px">Emergency contact</div>
        <div style="font-size:14px;font-weight:500"><?php echo e($user['ec_name']); ?></div>
        <?php if (!empty($user['ec_relation'])): ?>
          <div style="font-size:12px;color:var(--text-3)"><?php echo e($user['ec_relation']); ?></div>
        <?php endif; ?>
        <div style="font-size:13px;color:var(--text-2);margin-top:6px;display:flex;gap:16px;flex-wrap:wrap">
          <?php if (!empty($user['ec_phone'])): ?><span><?php echo e($user['ec_phone']); ?></span><?php endif; ?>
          <?php if (!empty($user['ec_email'])): ?><span><?php echo e($user['ec_email']); ?></span><?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="profile-actions">
      <a class="btn-ghost-lg" href="../">← Home</a>
      <a class="btn-gold-lg" href="/overview/overview.php">Overview →</a>
      <a class="btn-ghost-lg" href="/games/tictactoe/dashboard.php">🎮 Tic Tac Toe</a>
      <a class="btn-ghost-lg" href="?tab=settings">Edit profile</a>
      <a class="btn-signin" href="/user/logout.php">Log out</a>
    </div>

  <!-- ── PRODUCTIONS ───────────────────────────────────────── -->
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

  <!-- ── FORUM POSTS ───────────────────────────────────────── -->
  <?php elseif ($tab === 'forum'): ?>
    <?php if (empty($threads)): ?>
      <div class="profile-empty">
        <div class="profile-empty-icon">💬</div>
        <div class="profile-empty-title">No posts yet</div>
        <div class="profile-empty-sub"><a href="/forum/forum.php" style="color:var(--gold)">Start a thread →</a></div>
      </div>
    <?php else: ?>
      <div class="forum-posts">
        <?php foreach ($threads as $t): ?>
          <a class="fp" href="/forum/forum_thread.php?id=<?php echo $t['id']; ?>" style="text-decoration:none">
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

  <!-- ── SETTINGS ──────────────────────────────────────────── -->
  <?php elseif ($tab === 'settings'): ?>
    <div class="settings-stack">

      <!-- Profile photo -->
      <div class="settings-card">
        <div class="settings-card-title">Profile photo</div>
        <div class="settings-avatar-row">
          <div id="avatar-preview-wrap">
            <?php if ($has_avatar): ?>
              <img src="<?php echo e($user['avatar_url']); ?>" alt="avatar" class="settings-avatar-preview" id="avatar-preview">
            <?php else: ?>
              <div class="profile-avatar" style="width:60px;height:60px;font-size:24px" id="avatar-initial"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
            <?php endif; ?>
          </div>
          <div style="flex:1">
            <div class="auth-field" style="margin-bottom:10px">
              <label>Upload photo</label>
              <input type="file" id="avatar-file-input" accept="image/jpeg,image/png,image/gif,image/webp"
                     style="display:none" onchange="uploadAvatar(this)">
              <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                <button type="button" class="btn-gold-lg" style="font-size:13px;padding:9px 20px"
                        onclick="document.getElementById('avatar-file-input').click()">
                  Choose photo
                </button>
                <?php if ($has_avatar): ?>
                  <button type="button" class="btn-ghost-lg" style="font-size:13px;padding:9px 16px"
                          onclick="removeAvatar()">Remove</button>
                <?php endif; ?>
              </div>
              <div class="auth-field-hint">JPG, PNG, GIF or WebP. Max 5MB.</div>
            </div>
            <div id="avatar-status" style="font-size:13px;display:none"></div>
          </div>
        </div>
      </div>

      <script>
      const CSRF = document.querySelector('meta[name="csrf-token"]').content;

      async function uploadAvatar(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const status = document.getElementById('avatar-status');
        status.style.display = 'block';
        status.style.color = 'var(--text-2)';
        status.textContent = 'Uploading…';

        const form = new FormData();
        form.append('avatar', file);
        form.append('csrf_token', CSRF);

        try {
          const res  = await fetch('/upload_avatar.php', { method: 'POST', body: form });
          const data = await res.json();
          if (data.error) {
            status.style.color = '#F4805A';
            status.textContent = data.error;
          } else {
            status.style.color = '#7DD4A0';
            status.textContent = 'Photo updated.';
            // Update preview
            const wrap = document.getElementById('avatar-preview-wrap');
            wrap.innerHTML = '<img src="' + data.url + '?t=' + Date.now() + '" class="settings-avatar-preview" id="avatar-preview" alt="avatar">';
            // Update header avatar too if present
            const headerAv = document.querySelector('.profile-avatar-img');
            if (headerAv) headerAv.src = data.url + '?t=' + Date.now();
          }
        } catch (e) {
          status.style.color = '#F4805A';
          status.textContent = 'Upload failed. Please try again.';
        }
      }

      async function removeAvatar() {
        if (!confirm('Remove your profile photo?')) return;
        const status = document.getElementById('avatar-status');
        status.style.display = 'block';
        status.style.color = 'var(--text-2)';
        status.textContent = 'Removing…';

        const form = new FormData();
        form.append('csrf_token', CSRF);
        form.append('action', 'update_avatar');
        form.append('avatar_url', '');

        const res = await fetch('/myprofile.php?tab=settings', { method: 'POST', body: form });
        if (res.ok) {
          window.location.reload();
        }
      }
      </script>

      <!-- Identity -->
      <div class="settings-card">
        <div class="settings-card-title">Username &amp; display name</div>
        <form method="POST" class="auth-form" style="gap:14px">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="update_identity">
          <div class="settings-two-col">
            <div class="auth-field">
              <label>Username</label>
              <input type="text" name="username" maxlength="30" required
                     value="<?php echo e($user['username']); ?>">
              <div class="auth-field-hint">Letters, numbers, underscores. Must be unique.</div>
            </div>
            <div class="auth-field">
              <label>Display name</label>
              <input type="text" name="display_name" maxlength="60"
                     value="<?php echo e($user['display_name'] ?? ''); ?>"
                     placeholder="Your full name or stage name">
              <div class="auth-field-hint">Shown on your profile instead of username.</div>
            </div>
          </div>
          <button type="submit" class="btn-gold-lg" style="font-size:13px;padding:9px 20px">Save →</button>
        </form>
      </div>

      <!-- Contact info -->
      <div class="settings-card">
        <div class="settings-card-title">Contact information</div>
        <form method="POST" class="auth-form" style="gap:14px">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="update_contact">
          <div class="settings-two-col">
            <div class="auth-field">
              <label>Email</label>
              <input type="email" name="email" required value="<?php echo e($user['email']); ?>">
              <?php if (!($user['email_verified'] ?? false)): ?>
                <div class="auth-field-hint" style="color:#F4805A">Not verified</div>
              <?php endif; ?>
            </div>
            <div class="auth-field">
              <label>Phone</label>
              <input type="tel" name="phone" maxlength="30"
                     value="<?php echo e($user['phone'] ?? ''); ?>"
                     placeholder="+1 555 000 0000">
            </div>
          </div>
          <div class="auth-field">
            <label>LinkedIn URL</label>
            <input type="url" name="linkedin_url" maxlength="255"
                   value="<?php echo e($user['linkedin_url'] ?? ''); ?>"
                   placeholder="https://linkedin.com/in/yourprofile">
          </div>
          <div class="auth-field">
            <label>Bio</label>
            <textarea name="bio" maxlength="500" rows="3" class="settings-textarea"><?php echo e($user['bio'] ?? ''); ?></textarea>
            <div class="auth-field-hint">500 characters max.</div>
          </div>
          <button type="submit" class="btn-gold-lg" style="font-size:13px;padding:9px 20px">Save →</button>
        </form>
      </div>

      <!-- Address -->
      <div class="settings-card">
        <div class="settings-card-title">Address</div>
        <form method="POST" class="auth-form" style="gap:14px">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="update_address">
          <div class="auth-field">
            <label>Address line 1</label>
            <input type="text" name="address_line1" maxlength="255"
                   value="<?php echo e($user['address_line1'] ?? ''); ?>"
                   placeholder="Street address">
          </div>
          <div class="auth-field">
            <label>Address line 2</label>
            <input type="text" name="address_line2" maxlength="255"
                   value="<?php echo e($user['address_line2'] ?? ''); ?>"
                   placeholder="Apt, suite, unit, etc.">
          </div>
          <div class="settings-three-col">
            <div class="auth-field">
              <label>City</label>
              <input type="text" name="city" maxlength="100"
                     value="<?php echo e($user['city'] ?? ''); ?>">
            </div>
            <div class="auth-field">
              <label>State / Province</label>
              <input type="text" name="state" maxlength="100"
                     value="<?php echo e($user['state'] ?? ''); ?>">
            </div>
            <div class="auth-field">
              <label>ZIP / Postal code</label>
              <input type="text" name="zip" maxlength="20"
                     value="<?php echo e($user['zip'] ?? ''); ?>">
            </div>
          </div>
          <div class="auth-field">
            <label>Country</label>
            <input type="text" name="country" maxlength="100"
                   value="<?php echo e($user['country'] ?? ''); ?>"
                   placeholder="United States">
          </div>
          <button type="submit" class="btn-gold-lg" style="font-size:13px;padding:9px 20px">Save →</button>
        </form>
      </div>

      <!-- Emergency contact -->
      <div class="settings-card">
        <div class="settings-card-title">Emergency contact</div>
        <div class="settings-card-sub">This information is private and only visible to you.</div>
        <form method="POST" class="auth-form" style="gap:14px">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="update_emergency">
          <div class="settings-two-col">
            <div class="auth-field">
              <label>Full name</label>
              <input type="text" name="ec_name" maxlength="100"
                     value="<?php echo e($user['ec_name'] ?? ''); ?>"
                     placeholder="Jane Smith">
            </div>
            <div class="auth-field">
              <label>Relationship</label>
              <input type="text" name="ec_relation" maxlength="60"
                     value="<?php echo e($user['ec_relation'] ?? ''); ?>"
                     placeholder="Spouse, parent, sibling…">
            </div>
          </div>
          <div class="settings-two-col">
            <div class="auth-field">
              <label>Phone</label>
              <input type="tel" name="ec_phone" maxlength="30"
                     value="<?php echo e($user['ec_phone'] ?? ''); ?>"
                     placeholder="+1 555 000 0000">
            </div>
            <div class="auth-field">
              <label>Email</label>
              <input type="email" name="ec_email" maxlength="255"
                     value="<?php echo e($user['ec_email'] ?? ''); ?>">
            </div>
          </div>
          <button type="submit" class="btn-gold-lg" style="font-size:13px;padding:9px 20px">Save →</button>
        </form>
      </div>

      <!-- Change password -->
      <div class="settings-card">
        <div class="settings-card-title">Change password</div>
        <form method="POST" class="auth-form" style="gap:14px">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="change_password">
          <div class="auth-field">
            <label>Current password</label>
            <input type="password" name="current_password" required autocomplete="current-password">
          </div>
          <div class="settings-two-col">
            <div class="auth-field">
              <label>New password</label>
              <input type="password" name="new_password" required autocomplete="new-password" minlength="8">
              <div class="auth-field-hint">At least 8 characters.</div>
            </div>
            <div class="auth-field">
              <label>Confirm new password</label>
              <input type="password" name="confirm_password" required autocomplete="new-password">
            </div>
          </div>
          <button type="submit" class="btn-gold-lg" style="font-size:13px;padding:9px 20px">Change password →</button>
        </form>
      </div>

    </div>
  <?php endif; ?>

</section>

<?php include ROOT_PATH . '/required/footer.php'; ?>
