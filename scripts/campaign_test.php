<?php
require_once __DIR__ . '/../config/db.php';

$pdo = db_connect();

// Insert sample campaigns (vigente, expirada, inactiva)
$today = '2025-12-08';
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO campaigns (title, description, banner_image, start_date, end_date, is_active) VALUES (:title, :description, :banner_image, :start_date, :end_date, :is_active)");

    // Vigente
    $stmt->execute([
        ':title' => 'Prueba Vigente',
        ':description' => 'Campaña actualmente vigente',
        ':banner_image' => null,
        ':start_date' => '2025-12-01',
        ':end_date' => '2025-12-31',
        ':is_active' => 1
    ]);

    // Expirada
    $stmt->execute([
        ':title' => 'Prueba Expirada',
        ':description' => 'Campaña ya expirada',
        ':banner_image' => null,
        ':start_date' => '2025-11-01',
        ':end_date' => '2025-11-30',
        ':is_active' => 1
    ]);

    // Inactiva
    $stmt->execute([
        ':title' => 'Prueba Inactiva',
        ':description' => 'Campaña marcada como inactiva',
        ':banner_image' => null,
        ':start_date' => '2025-12-01',
        ':end_date' => '2025-12-31',
        ':is_active' => 0
    ]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    echo "ERROR: ", $e->getMessage();
    exit(1);
}

// Fetch last 10 campaigns
$stmt = $pdo->query("SELECT id, title, start_date, end_date, is_active, created_at FROM campaigns ORDER BY id DESC LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>