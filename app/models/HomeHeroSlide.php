<?php
/**
 * Modelo HomeHeroSlide
 * Gestiona las slides del carousel del hero en la página de inicio
 */

require_once __DIR__ . '/../../config/db.php';

class HomeHeroSlide {
    
    /**
     * Obtiene todas las slides activas ordenadas por sort_order y id
     * @return array Lista de slides activas
     */
    public static function getActiveSlides(): array {
        try {
            $pdo = db_connect();
            $sql = "SELECT * FROM home_hero_slides 
                    WHERE is_active = 1 
                    ORDER BY sort_order ASC, id ASC";
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener slides activas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene todas las slides (para administración)
     * @return array Lista de todas las slides
     */
    public static function getAllSlides(): array {
        try {
            $pdo = db_connect();
            $sql = "SELECT * FROM home_hero_slides 
                    ORDER BY sort_order ASC, id ASC";
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener todas las slides: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene una slide por ID
     * @param int $id ID de la slide
     * @return array|null Datos de la slide o null si no existe
     */
    public static function getById(int $id): ?array {
        try {
            $pdo = db_connect();
            $sql = "SELECT * FROM home_hero_slides WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Error al obtener slide por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Crea una nueva slide
     * @param array $data Datos de la slide (title, subtitle, image_url, sort_order, is_active)
     * @return int|false ID de la slide creada o false si falla
     */
    public static function create(array $data) {
        try {
            $pdo = db_connect();
            $sql = "INSERT INTO home_hero_slides 
                    (title, subtitle, image_url, sort_order, is_active) 
                    VALUES (:title, :subtitle, :image_url, :sort_order, :is_active)";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':title' => $data['title'] ?? null,
                ':subtitle' => $data['subtitle'] ?? null,
                ':image_url' => $data['image_url'],
                ':sort_order' => $data['sort_order'] ?? 0,
                ':is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1
            ]);
            
            return $result ? $pdo->lastInsertId() : false;
        } catch (Exception $e) {
            error_log("Error al crear slide: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza una slide existente
     * @param int $id ID de la slide
     * @param array $data Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public static function update(int $id, array $data): bool {
        try {
            $pdo = db_connect();
            $sql = "UPDATE home_hero_slides 
                    SET title = :title,
                        subtitle = :subtitle,
                        image_url = :image_url,
                        sort_order = :sort_order,
                        is_active = :is_active
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':title' => $data['title'] ?? null,
                ':subtitle' => $data['subtitle'] ?? null,
                ':image_url' => $data['image_url'],
                ':sort_order' => $data['sort_order'] ?? 0,
                ':is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1
            ]);
        } catch (Exception $e) {
            error_log("Error al actualizar slide: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina una slide
     * @param int $id ID de la slide a eliminar
     * @return bool True si se eliminó correctamente
     */
    public static function delete(int $id): bool {
        try {
            $pdo = db_connect();
            $sql = "DELETE FROM home_hero_slides WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log("Error al eliminar slide: " . $e->getMessage());
            return false;
        }
    }
}
