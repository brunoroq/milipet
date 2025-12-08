<?php
require_once __DIR__ . '/../app/models/Campaign.php';

$pdo = db_connect();
try {
    // Save with is_active = 0
    $id1 = Campaign::save([
        'title' => 'Static Save Inactive',
        'description' => 'Guardada vía Campaign::save con is_active=0',
        'banner_image' => null,
        'start_date' => '2025-12-01',
        'end_date' => '2025-12-31',
        'is_active' => 0
    ]);

    // Save with is_active = 1
    $id2 = Campaign::save([
        'title' => 'Static Save Active',
        'description' => 'Guardada vía Campaign::save con is_active=1',
        'banner_image' => null,
        'start_date' => '2025-12-01',
        'end_date' => '2025-12-31',
        'is_active' => 1
    ]);

    $stmt = $pdo->prepare('SELECT id, title, is_active FROM campaigns WHERE id IN (:a,:b)');
    // PDO doesn't accept binding list like that for two params with IN and a single placeholder, so fetch separately
    $row1 = $pdo->query("SELECT id, title, is_active FROM campaigns WHERE id = " . (int)$id1)->fetch(PDO::FETCH_ASSOC);
    $row2 = $pdo->query("SELECT id, title, is_active FROM campaigns WHERE id = " . (int)$id2)->fetch(PDO::FETCH_ASSOC);

    echo json_encode([$row1, $row2], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage();
}

?>