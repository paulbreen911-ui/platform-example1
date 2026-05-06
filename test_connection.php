<?php
// Test database connection only - no table creation
$db_host = getenv('PGHOST') ?: 'localhost';
$db_port = getenv('PGPORT') ?: '5432';
$db_name = getenv('PGDATABASE') ?: 'my_website_db';
$db_user = getenv('PGUSER') ?: 'postgres';
$db_password = getenv('PGPASSWORD') ?: 'your_password';

echo "<h2>PostgreSQL Connection Test</h2>";
echo "<p><strong>Host:</strong> " . htmlspecialchars($db_host) . "</p>";
echo "<p><strong>Port:</strong> " . htmlspecialchars($db_port) . "</p>";
echo "<p><strong>Database:</strong> " . htmlspecialchars($db_name) . "</p>";
echo "<p><strong>User:</strong> " . htmlspecialchars($db_user) . "</p>";

try {
    $pdo = new PDO(
        "pgsql:host=$db_host;port=$db_port;dbname=$db_name",
        $db_user,
        $db_password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    
    echo "<p style='color: green;'><strong>✓ Database connection SUCCESSFUL!</strong></p>";
    
    // List all tables in the database
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Tables in database:</strong></p>";
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table['table_name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>No tables found. You need to run init-db.php</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>✗ Connection FAILED:</strong></p>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<hr>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Check that postgres-volume is deployed on Railway</li>";
    echo "<li>Verify PGHOST, PGUSER, PGPASSWORD in Railway Variables tab</li>";
    echo "<li>Make sure the database name matches PGDATABASE</li>";
    echo "</ul>";
}
?>
