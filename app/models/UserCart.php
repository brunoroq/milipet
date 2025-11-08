<?php
require_once __DIR__ . '/../../config/db.php';

class UserCart {
    /**
     * Obtiene todos los product_id del carrito de un usuario
     */
    public static function getCartItems(int $userId): array {
        $pdo = db_connect();
        $stmt = $pdo->prepare('SELECT product_id, quantity FROM user_carts WHERE user_id = ? ORDER BY created_at ASC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene solo los IDs de productos (sin cantidad) para compatibilidad con código existente
     */
    public static function getCartProductIds(int $userId): array {
        $items = self::getCartItems($userId);
        return array_column($items, 'product_id');
    }

    /**
     * Añade un producto al carrito o incrementa cantidad si ya existe
     */
    public static function addItem(int $userId, int $productId, int $quantity = 1): bool {
        $pdo = db_connect();
        $stmt = $pdo->prepare('INSERT INTO user_carts (user_id, product_id, quantity) VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), updated_at = NOW()');
        return $stmt->execute([$userId, $productId, $quantity]);
    }

    /**
     * Elimina un producto del carrito
     */
    public static function removeItem(int $userId, int $productId): bool {
        $pdo = db_connect();
        $stmt = $pdo->prepare('DELETE FROM user_carts WHERE user_id = ? AND product_id = ?');
        return $stmt->execute([$userId, $productId]);
    }

    /**
     * Actualiza la cantidad de un producto (o lo elimina si qty=0)
     */
    public static function updateQuantity(int $userId, int $productId, int $quantity): bool {
        if ($quantity <= 0) {
            return self::removeItem($userId, $productId);
        }
        $pdo = db_connect();
        $stmt = $pdo->prepare('UPDATE user_carts SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?');
        return $stmt->execute([$quantity, $userId, $productId]);
    }

    /**
     * Vacía el carrito de un usuario
     */
    public static function clearCart(int $userId): bool {
        $pdo = db_connect();
        $stmt = $pdo->prepare('DELETE FROM user_carts WHERE user_id = ?');
        return $stmt->execute([$userId]);
    }

    /**
     * Sincroniza el carrito de localStorage con BD (merge: añade los que no estén)
     */
    public static function syncFromLocalStorage(int $userId, array $productIds): void {
        foreach ($productIds as $pid) {
            self::addItem($userId, (int)$pid, 1);
        }
    }
}
