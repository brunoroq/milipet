<?php
require_once __DIR__ . '/../../config/db.php';

class User {
    /**
     * Buscar usuario por email con su rol (para login)
     */
    public static function findByEmailWithRole($email) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("
            SELECT 
                u.id, 
                u.role_id,
                u.name, 
                u.email, 
                u.password, 
                u.is_active,
                u.remember_token,
                u.last_login,
                r.name AS role_name,
                r.description AS role_description
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.email = :email
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar usuario por ID
     */
    public static function find($id) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("
            SELECT 
                u.*,
                r.name AS role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los usuarios (admin panel)
     */
    public static function all() {
        $pdo = db_connect();
        $stmt = $pdo->query("
            SELECT 
                u.*,
                r.name AS role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY u.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear o actualizar usuario
     */
    public static function save($data) {
        $pdo = db_connect();
        
        $now = date('Y-m-d H:i:s');
        
        if (!empty($data['id'])) {
            // UPDATE
            $sql = "UPDATE users SET 
                    role_id = :role_id,
                    name = :name,
                    email = :email,
                    is_active = :is_active,
                    updated_at = :updated_at";
            
            // Solo actualizar password si se proporciona uno nuevo
            if (!empty($data['password'])) {
                $sql .= ", password = :password";
            }
            
            if (isset($data['remember_token'])) {
                $sql .= ", remember_token = :remember_token";
            }
            
            if (isset($data['last_login'])) {
                $sql .= ", last_login = :last_login";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $params = [
                ':id' => $data['id'],
                ':role_id' => $data['role_id'],
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
                ':updated_at' => $now
            ];
            
            if (!empty($data['password'])) {
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($data['remember_token'])) {
                $params[':remember_token'] = $data['remember_token'];
            }
            
            if (isset($data['last_login'])) {
                $params[':last_login'] = $data['last_login'];
            }
            
            $stmt->execute($params);
            return $data['id'];
            
        } else {
            // INSERT
            $sql = "INSERT INTO users 
                    (role_id, name, email, password, is_active, created_at, updated_at) 
                    VALUES 
                    (:role_id, :name, :email, :password, :is_active, :created_at, :updated_at)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':role_id' => $data['role_id'],
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
                ':created_at' => $now,
                ':updated_at' => $now
            ]);
            
            return $pdo->lastInsertId();
        }
    }

    /**
     * Actualizar último login
     */
    public static function updateLastLogin($userId) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        return $stmt->execute([':id' => $userId]);
    }

    /**
     * Actualizar remember token
     */
    public static function updateRememberToken($userId, $token) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
        return $stmt->execute([
            ':id' => $userId,
            ':token' => $token
        ]);
    }

    /**
     * Buscar usuario por remember token
     */
    public static function findByRememberToken($token) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("
            SELECT 
                u.*,
                r.name AS role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.remember_token = :token
            AND u.is_active = 1
            LIMIT 1
        ");
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Eliminar usuario
     */
    public static function delete($id) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Verificar si un email ya existe
     */
    public static function emailExists($email, $excludeId = null) {
        $pdo = db_connect();
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        $stmt = $pdo->prepare($sql);
        $params = [':email' => $email];
        if ($excludeId) {
            $params[':id'] = $excludeId;
        }
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
