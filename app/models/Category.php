<?php
require_once __DIR__ . '/../../config/db.php';

class Category {
    public static function all() {
        $pdo = db_connect();
        return $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
    }

    public static function allAdmin() {
        $pdo = db_connect();
        return $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }

    public static function find($id) {
        $pdo = db_connect();
        $st = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $st->execute([':id' => $id]);
        return $st->fetch();
    }

    public static function save($data) {
        $pdo = db_connect();
        
        if (!empty($data['id'])) {
            $sql = "UPDATE categories SET 
                    name = :name,
                    description = :description,
                    is_active = :is_active
                    WHERE id = :id";
        } else {
            $sql = "INSERT INTO categories (name, description, is_active) 
                    VALUES (:name, :description, :is_active)";
        }

        $st = $pdo->prepare($sql);
        
        $params = [
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ];

        if (!empty($data['id'])) {
            $params[':id'] = $data['id'];
        }

        $st->execute($params);
        return $pdo->lastInsertId() ?: ($data['id'] ?? null);
    }

    public static function delete($id) {
        $pdo = db_connect();
        $st = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        return $st->execute([':id' => $id]);
    }
}
