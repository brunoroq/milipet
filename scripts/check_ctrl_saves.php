<?php
require_once __DIR__ . '/../config/db.php';

$pdo = db_connect();
$stmt = $pdo->query("SELECT id, title, is_active FROM campaigns WHERE title LIKE '%Controller Test%' ORDER BY id DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>