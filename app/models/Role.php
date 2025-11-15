<?php
require_once __DIR__ . '/../../config/db.php';

class Role {
    /**
     * Obtener todos los roles activos
     */
    public static function all() {
        $pdo = db_connect();
        $stmt = $pdo->query("SELECT * FROM roles WHERE is_active = 1 ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar rol por ID
     */
    public static function find($id) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar rol por nombre
     */
    public static function findByName($name) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE name = :name LIMIT 1");
        $stmt->execute([':name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener ID del rol admin
     */
    public static function getAdminRoleId() {
        $role = self::findByName('admin');
        return $role ? $role['id'] : null;
    }
}
