<?php
require 'db.php';

$rows = $pdo->query("SELECT * FROM venue_raw_imports ORDER BY created_at DESC LIMIT 50")
            ->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Raw Imports</h2>

<?php foreach ($rows as $r): ?>
    <div>
        <b><?= htmlspecialchars($r['raw_name']) ?></b><br>
        Status: <?= $r['status'] ?><br>
        Source: <?= $r['source_name'] ?><br>
        Match: <?= $r['matched_venue_id'] ?><br>
        Confidence: <?= $r['confidence_score'] ?>
    </div>
    <hr>
<?php endforeach; ?>