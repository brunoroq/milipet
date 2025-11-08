<?php
// public/api/cart_sync.php
// API endpoint para sincronizar carrito localStorage -> BD cuando hay sesión activa
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../app/models/UserCart.php';

header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No session']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$productIds = $input['productIds'] ?? [];

if (!is_array($productIds)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$userId = (int)$_SESSION['uid'];
UserCart::syncFromLocalStorage($userId, $productIds);

// Devolver los IDs actuales del carrito persistente
$cartIds = UserCart::getCartProductIds($userId);
echo json_encode(['success' => true, 'cartIds' => $cartIds]);
