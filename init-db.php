<?php
// Database Initialization - Run this once to set up your database
require_once 'config.php';

$setup_queries = [
    // Create users table
    "CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Create index for faster lookups
    "CREATE INDEX IF NOT EXISTS idx_username ON users(username)",
];

try {
    foreach ($setup_queries as $query) {
        $pdo->exec($query);
        echo "✓ Query executed successfully<br>";
    }
    
    // Insert demo user if it doesn't exist
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
    $stmt->execute(['testuser']);
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        $demo_password = password_hash('password123', PASSWORD_BCRYPT);
        $insert = $pdo->prepare(
            "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
        );
        $insert->execute(['testuser', 'testuser@example.com', $demo_password]);
        echo "✓ Demo user created (testuser / password123)<br>";
    }
    
    echo "<h2>Database initialized successfully!</h2>";
    echo "<p><a href='index.php'>Go to Home Page</a></p>";
    
} catch (PDOException $e) {
    die('<h2>Database Error</h2><p>' . htmlspecialchars($e->getMessage()) . '</p>');
}
?>
