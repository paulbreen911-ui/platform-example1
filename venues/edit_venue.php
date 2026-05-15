<?php
require 'db.php';

$id = $_GET['id'] ?? null;

if (!$id) die("No ID");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("
        UPDATE venues
        SET name = ?, venue_type = ?, updated_at = now()
        WHERE id = ?
    ");
    $stmt->execute([
        $_POST['name'],
        $_POST['venue_type'],
        $id
    ]);
}

$stmt = $pdo->prepare("SELECT * FROM venues WHERE id = ?");
$stmt->execute([$id]);
$venue = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2>Edit Venue</h2>

<form method="POST">
    Name: <input name="name" value="<?= htmlspecialchars($venue['name']) ?>"><br>
    Type: <input name="venue_type" value="<?= htmlspecialchars($venue['venue_type']) ?>"><br>
    <button type="submit">Save</button>
</form>