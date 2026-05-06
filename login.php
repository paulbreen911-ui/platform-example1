<?php
require_once 'config.php';
$page_title = 'Login';
include 'header.php';
?>

<section>
    <h2>Login</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;">
            <?php
            if ($_GET['error'] == 'invalid') echo 'Invalid username or password.';
            if ($_GET['error'] == 'required') echo 'Please fill in all fields.';
            ?>
        </p>
    <?php endif; ?>
    
    <form method="POST" action="login_process.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
    
    <p>Demo account: username: <strong>testuser</strong> | password: <strong>password123</strong></p>
</section>

<?php include 'footer.php'; ?>
