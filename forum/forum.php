<?php
require_once '../config.php';
require_once '../functions.php';

$page_title = 'Forum';

// Categories with thread counts
$cats = $pdo->query('
    SELECT fc.*, COUNT(ft.id) AS thread_count
    FROM   forum_categories fc
    LEFT   JOIN forum_threads ft ON ft.category_id = fc.id
    GROUP  BY fc.id
    ORDER  BY fc.sort_order
')->fetchAll();

// Recent threads across all categories
$recent = $pdo->query('
    SELECT ft.id, ft.title, ft.reply_count, ft.view_count, ft.created_at, ft.last_reply_at,
           fc.name AS cat_name, fc.color AS cat_color, fc.slug AS cat_slug,
           u.username
    FROM   forum_threads ft
    JOIN   forum_categories fc ON fc.id = ft.category_id
    JOIN   users u ON u.id = ft.user_id
    ORDER  BY COALESCE(ft.last_reply_at, ft.created_at) DESC
    LIMIT  20
')->fetchAll();

// Trending (most replies in last 7 days)
$trending = $pdo->query('
    SELECT ft.id, ft.title, ft.reply_count, fc.name AS cat_name, fc.color
    FROM   forum_threads ft
    JOIN   forum_categories fc ON fc.id = ft.category_id
    WHERE  ft.created_at > NOW() - INTERVAL \'7 days\'
    ORDER  BY ft.reply_count DESC
    LIMIT  5
')->fetchAll();

include '../header.php';
?>

<div class="forum-page">

  <!-- Page header -->
  <div class="forum-page-hd">
    <div>
      <div class="sec-ey">Community</div>
      <h1 class="forum-page-title">FORUM</h1>
      <div class="forum-page-sub">Open to all. Read without an account — join free to post.</div>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a class="btn-gold-lg" href="/forum/forum_post.php">+ New thread</a>
    <?php else: ?>
      <a class="btn-gold-lg" href="/forum/register.php">Join free to post →</a>
    <?php endif; ?>
  </div>

  <div class="forum-layout">
    <div class="forum-main">

      <!-- Categories -->
      <div class="forum-section-label">Categories</div>
      <div class="forum-cats">
        <?php foreach ($cats as $c): ?>
          <a class="forum-cat-card" href="/forum/forum.php?cat=<?php echo e($c['slug']); ?>">
            <div class="forum-cat-dot" style="background:<?php echo e($c['color']); ?>"></div>
            <div class="forum-cat-info">
              <div class="forum-cat-name"><?php echo e($c['name']); ?></div>
              <div class="forum-cat-desc"><?php echo e($c['description'] ?? ''); ?></div>
            </div>
            <div class="forum-cat-count"><?php echo $c['thread_count']; ?><br><span>threads</span></div>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Thread list — filtered by category or all recent -->
      <?php
      $active_cat = null;
      $thread_list = $recent;

      if (!empty($_GET['cat'])) {
          $slug = $_GET['cat'];
          $cat_stmt = $pdo->prepare('SELECT * FROM forum_categories WHERE slug = ?');
          $cat_stmt->execute([$slug]);
          $active_cat = $cat_stmt->fetch();

          if ($active_cat) {
              $t_stmt = $pdo->prepare('
                  SELECT ft.id, ft.title, ft.reply_count, ft.view_count, ft.created_at, ft.last_reply_at,
                         fc.name AS cat_name, fc.color AS cat_color, fc.slug AS cat_slug,
                         u.username
                  FROM   forum_threads ft
                  JOIN   forum_categories fc ON fc.id = ft.category_id
                  JOIN   users u ON u.id = ft.user_id
                  WHERE  ft.category_id = ?
                  ORDER  BY ft.pinned DESC, COALESCE(ft.last_reply_at, ft.created_at) DESC
                  LIMIT  50
              ');
              $t_stmt->execute([$active_cat['id']]);
              $thread_list = $t_stmt->fetchAll();
          }
      }
      ?>

      <div class="forum-section-label" style="margin-top:32px">
        <?php echo $active_cat ? e($active_cat['name']) : 'Recent threads'; ?>
        <?php if ($active_cat): ?>
          <a href="/forum/forum.php" style="font-size:11px;color:var(--text-3);margin-left:10px;font-weight:400">← All categories</a>
        <?php endif; ?>
      </div>

      <?php if (empty($thread_list)): ?>
        <div class="forum-empty">No threads yet. <a href="/forum/forum_post.php" style="color:var(--gold)">Start one →</a></div>
      <?php else: ?>
        <div class="forum-posts">
          <?php foreach ($thread_list as $t): ?>
            <a class="fp" href="/forum/forum_thread.php?id=<?php echo $t['id']; ?>" style="text-decoration:none">
              <div class="fp-left">
                <div class="fp-cat" style="color:<?php echo e($t['cat_color']); ?>"><?php echo e($t['cat_name']); ?></div>
                <div class="fp-title"><?php echo e($t['title']); ?></div>
                <div class="fp-meta">
                  <span><?php echo e($t['username']); ?></span>
                  <span><?php echo time_ago($t['created_at']); ?></span>
                  <span><?php echo number_format($t['view_count']); ?> views</span>
                </div>
              </div>
              <div class="fp-replies">
                <div class="fp-n"><?php echo $t['reply_count']; ?></div>
                <div class="fp-l">replies</div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>

    <!-- Sidebar -->
    <div class="forum-sidebar">

      <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="sidebar-card" style="padding:20px">
          <div style="font-size:14px;font-weight:500;margin-bottom:8px">Join the community</div>
          <div style="font-size:12px;color:var(--text-2);margin-bottom:16px;line-height:1.6">Free to join. Post, reply, and connect with industry professionals.</div>
          <a class="btn-gold-lg" href="/forum/register.php" style="width:100%;text-align:center;display:block;font-size:13px">Create free account →</a>
        </div>
      <?php endif; ?>

      <!-- Trending -->
      <?php if (!empty($trending)): ?>
        <div class="sidebar-card">
          <div class="sc-hd">Trending this week</div>
          <?php foreach ($trending as $tr): ?>
            <a class="trend-row" href="/forum/forum_thread.php?id=<?php echo $tr['id']; ?>" style="text-decoration:none">
              <span><?php echo e(truncate($tr['title'], 50)); ?></span>
              <span style="color:var(--text-3);font-size:11px"><?php echo $tr['reply_count']; ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Jobs (static for now) -->
      <div class="sidebar-card">
        <div class="sc-hd">Jobs board</div>
        <div class="job-row">
          <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:3px">
            <div class="job-title">Production Manager — Vegas residency</div>
            <div class="job-type">FT</div>
          </div>
          <div class="job-meta"><span>Las Vegas, NV</span><span>1d ago</span></div>
        </div>
        <div class="job-row">
          <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:3px">
            <div class="job-title">Touring FOH Engineer</div>
            <div class="job-type">Tour</div>
          </div>
          <div class="job-meta"><span>National</span><span>2d ago</span></div>
        </div>
        <div class="job-row">
          <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:3px">
            <div class="job-title">Freelance LD — corporate events</div>
            <div class="job-type">FL</div>
          </div>
          <div class="job-meta"><span>NYC</span><span>3d ago</span></div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include '../footer.php'; ?>
