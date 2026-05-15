<?php
require 'db.php';

// INSERT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO venues (name, venue_type) VALUES (?, ?)");
    $stmt->execute([$_POST['name'], $_POST['venue_type']]);
}

// FETCH
$venues = $pdo->query("SELECT * FROM venues ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Venues</h2>

<form method="POST">
    Name: <input name="name">
    Type: <input name="venue_type">
    <button type="submit">Add</button>
</form>

<hr>

<?php foreach ($venues as $v): ?>
    <div>
        <b><?= htmlspecialchars($v['name']) ?></b> |
        <?= htmlspecialchars($v['venue_type']) ?>
        <a href="edit_venue.php?id=<?= $v['id'] ?>">Edit</a>
    </div>
<?php endforeach; ?>