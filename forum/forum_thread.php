<?php
require_once './config.php';
require_once './functions.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: /forum/forum.php'); exit; }

// Fetch thread
$stmt = $pdo->prepare('
    SELECT ft.*, fc.name AS cat_name, fc.color AS cat_color, fc.slug AS cat_slug,
           u.username, u.display_name, u.created_at AS user_joined
    FROM   forum_threads ft
    JOIN   forum_categories fc ON fc.id = ft.category_id
    JOIN   users u ON u.id = ft.user_id
    WHERE  ft.id = ?
');
$stmt->execute([$id]);
$thread = $stmt->fetch();

if (!$thread) {
    http_response_code(404);
    include 'errors/404.php';
    exit;
}

// Increment view count
$pdo->prepare('UPDATE forum_threads SET view_count = view_count + 1 WHERE id = ?')->execute([$id]);

// Fetch replies
$rep_stmt = $pdo->prepare('
    SELECT fr.*, u.username, u.display_name, u.created_at AS user_joined
    FROM   forum_replies fr
    JOIN   users u ON u.id = fr.user_id
    WHERE  fr.thread_id = ?
    ORDER  BY fr.created_at ASC
');
$rep_stmt->execute([$id]);
$replies = $rep_stmt->fetchAll();

// Handle reply POST
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    csrf_verify();

    if ($thread['locked']) {
        $errors[] = 'This thread is locked.';
    } else {
        $body = trim($_POST['body'] ?? '');
        $ip   = client_ip();

        if (empty($body)) {
            $errors[] = 'Reply cannot be empty.';
        } elseif (mb_strlen($body) > 10000) {
            $errors[] = 'Reply is too long (max 10,000 characters).';
        } elseif (!rate_limit_check($pdo, "reply:{$_SESSION['user_id']}", 'forum_reply', 10, 60)) {
            $errors[] = 'You\'re posting too fast. Wait a moment and try again.';
        } else {
            $pdo->prepare('INSERT INTO forum_replies (thread_id, user_id, body) VALUES (?, ?, ?)')
                ->execute([$id, $_SESSION['user_id'], $body]);
            $pdo->prepare('
                UPDATE forum_threads
                SET    reply_count = reply_count + 1,
                       last_reply_at = NOW(),
                       last_reply_user_id = ?
                WHERE  id = ?
            ')->execute([$_SESSION['user_id'], $id]);

            header("Location: /forum_thread.php?id={$id}#reply-end");
            exit;
        }
    }
}

$page_title = $thread['title'];
include './header.php';
?>

<div class="forum-page">

  <!-- Breadcrumb -->
  <div class="forum-breadcrumb">
    <a href="/forum/forum.php">Forum</a>
    <span>›</span>
    <a href="/forum/forum.php?cat=<?php echo e($thread['cat_slug']); ?>"><?php echo e($thread['cat_name']); ?></a>
    <span>›</span>
    <span><?php echo e(truncate($thread['title'], 60)); ?></span>
  </div>

  <div class="forum-layout">
    <div class="forum-main">

      <!-- Original post -->
      <div class="thread-card">
        <div class="thread-hd">
          <div class="fp-cat" style="color:<?php echo e($thread['cat_color']); ?>;font-size:11px"><?php echo e($thread['cat_name']); ?></div>
          <h1 class="thread-title"><?php echo e($thread['title']); ?></h1>
          <div class="thread-meta">
            <div class="post-av"><?php echo strtoupper(substr($thread['username'], 0, 1)); ?></div>
            <span class="thread-author"><?php echo e($thread['display_name'] ?: $thread['username']); ?></span>
            <span class="thread-ts"><?php echo time_ago($thread['created_at']); ?></span>
            <span class="thread-views"><?php echo number_format($thread['view_count'] + 1); ?> views</span>
            <?php if ($thread['locked']): ?><span class="thread-locked-badge">🔒 Locked</span><?php endif; ?>
          </div>
        </div>
        <div class="post-body"><?php echo format_post($thread['body']); ?></div>
      </div>

      <!-- Replies -->
      <?php foreach ($replies as $i => $r): ?>
        <div class="reply-card" id="post-<?php echo $r['id']; ?>">
          <div class="reply-av"><?php echo strtoupper(substr($r['username'], 0, 1)); ?></div>
          <div class="reply-content">
            <div class="reply-meta">
              <span class="reply-author"><?php echo e($r['display_name'] ?: $r['username']); ?></span>
              <span class="reply-ts"><?php echo time_ago($r['created_at']); ?></span>
              <a href="#post-<?php echo $r['id']; ?>" class="reply-anchor">#<?php echo $i + 1; ?></a>
            </div>
            <div class="post-body"><?php echo format_post($r['body']); ?></div>
          </div>
        </div>
      <?php endforeach; ?>

      <!-- Reply form -->
      <div id="reply-end"></div>
      <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($thread['locked']): ?>
          <div class="forum-empty">This thread is locked — no new replies.</div>
        <?php else: ?>
          <div class="reply-form-card">
            <div class="reply-form-title">Post a reply</div>
            <?php if (!empty($errors)): ?>
              <div class="auth-error" style="margin-bottom:16px">
                <?php foreach ($errors as $err): ?><div><?php echo e($err); ?></div><?php endforeach; ?>
              </div>
            <?php endif; ?>
            <form method="POST">
              <?php echo csrf_field(); ?>
              <textarea name="body" class="reply-textarea" placeholder="Write your reply…" rows="5" required><?php echo e($_POST['body'] ?? ''); ?></textarea>
              <div class="reply-form-footer">
                <div class="reply-form-hint">Plain text. URLs are linked automatically.</div>
                <button type="submit" class="btn-gold-lg" style="font-size:13px;padding:10px 22px">Post reply →</button>
              </div>
            </form>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="reply-form-card" style="text-align:center;padding:32px">
          <div style="font-size:14px;font-weight:500;margin-bottom:8px">Join to reply</div>
          <div style="font-size:13px;color:var(--text-2);margin-bottom:20px">Create a free account to post in the forum.</div>
          <a class="btn-gold-lg" href="/forum/register.php">Join free →</a>
          <span style="margin:0 10px;color:var(--text-3)">or</span>
          <a class="btn-ghost-lg" href="/forum/login.php">Sign in</a>
        </div>
      <?php endif; ?>

    </div>

    <!-- Sidebar -->
    <div class="forum-sidebar">
      <div class="sidebar-card" style="padding:16px">
        <div style="font-size:11px;color:var(--text-3);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px">Thread info</div>
        <div style="display:grid;gap:10px">
          <div><div style="font-size:10px;color:var(--text-3)">Replies</div><div style="font-size:18px;font-weight:500"><?php echo count($replies); ?></div></div>
          <div><div style="font-size:10px;color:var(--text-3)">Views</div><div style="font-size:18px;font-weight:500"><?php echo number_format($thread['view_count']); ?></div></div>
          <div><div style="font-size:10px;color:var(--text-3)">Posted</div><div style="font-size:13px"><?php echo date('M j, Y', strtotime($thread['created_at'])); ?></div></div>
        </div>
      </div>
      <div style="padding:4px 0">
        <a class="sidebar-back" href="/forum/forum.php?cat=<?php echo e($thread['cat_slug']); ?>">← <?php echo e($thread['cat_name']); ?></a>
        <a class="sidebar-back" href="/forum/forum.php" style="margin-top:6px">← All categories</a>
      </div>
    </div>
  </div>

</div>

<?php include './footer.php'; ?>
