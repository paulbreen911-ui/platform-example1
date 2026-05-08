<?php
require_once 'config.php';
require_once 'functions.php';
require_login();

$page_title = 'New Thread';

$categories = $pdo->query('SELECT id, name, slug, color FROM forum_categories ORDER BY sort_order')->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title   = trim($_POST['title']       ?? '');
    $body    = trim($_POST['body']        ?? '');
    $cat_id  = (int)($_POST['category_id'] ?? 0);
    $ip      = client_ip();

    if (empty($title) || mb_strlen($title) < 5) {
        $errors[] = 'Title must be at least 5 characters.';
    } elseif (mb_strlen($title) > 255) {
        $errors[] = 'Title is too long (max 255 characters).';
    }
    if (empty($body) || mb_strlen($body) < 10) {
        $errors[] = 'Post body must be at least 10 characters.';
    } elseif (mb_strlen($body) > 20000) {
        $errors[] = 'Post is too long (max 20,000 characters).';
    }
    if (!$cat_id) {
        $errors[] = 'Please select a category.';
    } else {
        $ck = $pdo->prepare('SELECT id FROM forum_categories WHERE id = ?');
        $ck->execute([$cat_id]);
        if (!$ck->fetch()) $errors[] = 'Invalid category.';
    }
    if (!rate_limit_check($pdo, "post:{$_SESSION['user_id']}", 'forum_post', 5, 300)) {
        $errors[] = 'You\'re posting too fast. Please wait a few minutes.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('
            INSERT INTO forum_threads (category_id, user_id, title, body)
            VALUES (?, ?, ?, ?)
            RETURNING id
        ');
        $stmt->execute([$cat_id, $_SESSION['user_id'], $title, $body]);
        $new_id = $stmt->fetchColumn();
        header("Location: /forum_thread.php?id={$new_id}");
        exit;
    }
}

include 'header.php';
?>

<div class="forum-page">
  <div class="forum-breadcrumb">
    <a href="/forum.php">Forum</a>
    <span>›</span>
    <span>New thread</span>
  </div>

  <div style="max-width:760px">
    <div class="forum-page-hd" style="margin-bottom:28px">
      <div>
        <h1 class="forum-page-title" style="font-size:36px">New thread</h1>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="auth-error" style="margin-bottom:20px">
        <?php foreach ($errors as $err): ?><div><?php echo e($err); ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="thread-form">
      <?php echo csrf_field(); ?>

      <div class="auth-field">
        <label>Category</label>
        <select name="category_id" class="thread-select" required>
          <option value="">Select a category…</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c['id']; ?>"
              <?php echo (($_POST['category_id'] ?? '') == $c['id']) ? 'selected' : ''; ?>>
              <?php echo e($c['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="auth-field">
        <label>Title</label>
        <input type="text" name="title" maxlength="255" required
               value="<?php echo e($_POST['title'] ?? ''); ?>"
               placeholder="Ask a question, share knowledge, start a discussion…">
        <div class="auth-field-hint">Be specific and clear.</div>
      </div>

      <div class="auth-field">
        <label>Post</label>
        <textarea name="body" class="reply-textarea" rows="10" required
                  placeholder="Write your post…"><?php echo e($_POST['body'] ?? ''); ?></textarea>
        <div class="auth-field-hint">Supports **bold**, *italic*, `code`, and ```code blocks```</div>
      </div>

      <div style="display:flex;gap:10px;align-items:center;margin-top:8px">
        <button type="submit" class="btn-gold-lg">Post thread →</button>
        <a class="btn-ghost-lg" href="/forum.php">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>
