<?php
require_once 'config.php';
$page_title = 'Home';
include 'header.php';
?>

<section>
    <h2>Welcome to Our Website</h2>
    <p>This is the public homepage. Anyone can view this page.</p>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Hello <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Welcome back.</p>
        <p><a href="myprofile.php">Visit your profile</a></p>
    <?php else: ?>
        <p>Please <a href="login.php">login</a> to access your profile.</p>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
