<?php
require 'db.php';

$result = null;

if (!empty($_GET['q'])) {
    $stmt = $pdo->prepare("
        SELECT * FROM find_best_venue_match(?)
    ");
    
    $q = $_GET['q'];
    $stmt->execute([$q]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Search Venue</h2>

<form method="GET">
    <input name="q" placeholder="Search venue name">
    <button type="submit">Search</button>
</form>

<?php if ($result): ?>
    <pre>
Venue ID: <?= $result['venue_id'] ?>

Score: <?= $result['match_score'] ?>
    </pre>
<?php endif; ?>