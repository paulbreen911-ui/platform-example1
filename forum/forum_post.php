<?php
require_once '../config.php';
require_once '../functions.php';
require_login();

$page_title = 'New Thread';
$errors = [];

$categories = $pdo->query('SELECT id, name, slug, color FROM forum_categories ORDER BY sort_order')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $action = $_POST['action'] ?? 'post_thread';

    // ── Create new category ───────────────────────────────────
    if ($action === 'create_category') {
        $cat_name = trim($_POST['new_cat_name'] ?? '');
        $cat_desc = trim($_POST['new_cat_desc'] ?? '');
        $cat_color = $_POST['new_cat_color'] ?? '#CC884A';

        if (empty($cat_name) || mb_strlen($cat_name) < 2) {
            $errors[] = 'Category name must be at least 2 characters.';
        } elseif (mb_strlen($cat_name) > 80) {
            $errors[] = 'Category name is too long.';
        } else {
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($cat_name));
            $slug = trim($slug, '-');
            // Check uniqueness
            $ck = $pdo->prepare('SELECT id FROM forum_categories WHERE slug = ?');
            $ck->execute([$slug]);
            if ($ck->fetch()) {
                $errors[] = 'A category with that name already exists.';
            } else {
                $max = $pdo->query('SELECT COALESCE(MAX(sort_order),0)+1 FROM forum_categories')->fetchColumn();
                $pdo->prepare('INSERT INTO forum_categories (slug, name, description, color, sort_order) VALUES (?, ?, ?, ?, ?)')
                    ->execute([$slug, $cat_name, $cat_desc ?: null, $cat_color, $max]);
                // Reload categories
                $categories = $pdo->query('SELECT id, name, slug, color FROM forum_categories ORDER BY sort_order')->fetchAll();
                $success_msg = 'Category "' . e($cat_name) . '" created.';
            }
        }
    }

    // ── Post thread ───────────────────────────────────────────
    if ($action === 'post_thread') {
        $title  = trim($_POST['title']       ?? '');
        $body   = trim($_POST['body']        ?? '');
        $cat_id = (int)($_POST['category_id'] ?? 0);

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
}

include '../header.php';
?>

<div class="forum-page">
  <div class="forum-breadcrumb">
    <a href="/forum/forum.php">Forum</a>
    <span>›</span>
    <span>New thread</span>
  </div>

  <div class="forum-post-layout">

    <!-- New thread form -->
    <div>
      <h1 class="forum-page-title" style="font-size:36px;margin-bottom:24px">New thread</h1>

      <?php if (!empty($errors)): ?>
        <div class="auth-error" style="margin-bottom:20px">
          <?php foreach ($errors as $err): ?><div><?php echo e($err); ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($success_msg)): ?>
        <div class="auth-success" style="margin-bottom:20px"><?php echo $success_msg; ?></div>
      <?php endif; ?>

      <form method="POST" class="thread-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="post_thread">

        <div class="auth-field">
          <label>Category</label>
          <?php if (empty($categories)): ?>
            <div class="auth-error">No categories yet — create one using the panel on the right first.</div>
          <?php else: ?>
            <select name="category_id" class="thread-select" required>
              <option value="">Select a category…</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c['id']; ?>"
                  <?php echo (($_POST['category_id'] ?? '') == $c['id']) ? 'selected' : ''; ?>>
                  <?php echo e($c['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
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
          <div class="auth-field-hint">Plain text. URLs are linked automatically.</div>
        </div>

        <div style="display:flex;gap:10px;align-items:center;margin-top:8px">
          <button type="submit" class="btn-gold-lg">Post thread →</button>
          <a class="btn-ghost-lg" href="/forum/forum.php">Cancel</a>
        </div>
      </form>
    </div>

    <!-- Sidebar: existing categories + create new -->
    <div>
      <div class="sidebar-card" style="margin-bottom:16px">
        <div class="sc-hd">Categories</div>
        <?php if (empty($categories)): ?>
          <div style="padding:16px;font-size:13px;color:var(--text-3)">No categories yet.</div>
        <?php else: ?>
          <?php foreach ($categories as $c): ?>
            <div class="trend-row" style="cursor:default">
              <span style="display:flex;align-items:center;gap:8px">
                <span style="width:8px;height:8px;border-radius:50%;background:<?php echo e($c['color']); ?>;flex-shrink:0;display:inline-block"></span>
                <?php echo e($c['name']); ?>
              </span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="settings-card">
        <div class="settings-card-title">Create a new category</div>
        <form method="POST" class="auth-form" style="gap:12px">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="create_category">
          <div class="auth-field">
            <label>Name</label>
            <input type="text" name="new_cat_name" maxlength="80" required
                   placeholder="e.g. Stage Management">
          </div>
          <div class="auth-field">
            <label>Description <span style="color:var(--text-3);font-weight:400">(optional)</span></label>
            <input type="text" name="new_cat_desc" maxlength="200"
                   placeholder="Brief description">
          </div>
          <div class="auth-field">
            <label>Color</label>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
              <?php
              $palette = ['#CC884A','#4A9ECC','#8888CC','#E8A030','#3A9ECC','#AA7755','#C9A84C','#4DB090','#CC7788','#3A7A44'];
              foreach ($palette as $col):
              ?>
                <label style="cursor:pointer">
                  <input type="radio" name="new_cat_color" value="<?php echo $col; ?>"
                         style="display:none"
                         <?php echo ($col === '#CC884A') ? 'checked' : ''; ?>>
                  <span class="color-swatch" style="background:<?php echo $col; ?>"></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
          <button type="submit" class="btn-gold-lg" style="width:100%;font-size:13px;padding:10px">Create category →</button>
        </form>
      </div>
    </div>

  </div>
</div>

<?php include '../footer.php'; ?>
