<?php
require_once 'config.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$page_title = 'My Profile';
include 'header.php';

try {
    $stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header('Location: /login.php');
        exit;
    }
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

$member_since = date('F j, Y', strtotime($user['created_at']));
?>

<section class="profile-section">
  <div class="profile-header">
    <div class="profile-avatar">
      <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
    </div>
    <div class="profile-header-info">
      <div class="profile-eyebrow">Member</div>
      <h1 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h1>
      <div class="profile-since">Member since <?php echo $member_since; ?></div>
    </div>
  </div>

  <div class="profile-grid">
    <div class="profile-card">
      <div class="profile-card-label">Username</div>
      <div class="profile-card-value"><?php echo htmlspecialchars($user['username']); ?></div>
    </div>
    <div class="profile-card">
      <div class="profile-card-label">Email</div>
      <div class="profile-card-value"><?php echo htmlspecialchars($user['email']); ?></div>
    </div>
    <div class="profile-card">
      <div class="profile-card-label">Member since</div>
      <div class="profile-card-value"><?php echo $member_since; ?></div>
    </div>
  </div>

  <div class="profile-actions">
    <a class="btn-ghost-lg" href="/">← Back to home</a>
    <a class="btn-signin" href="/logout.php">Log out</a>
  </div>
</section>

<?php include 'footer.php'; ?>
