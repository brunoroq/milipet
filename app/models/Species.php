<?php
require_once __DIR__ . '/../../config/db.php';

class Species {
    /**
     * Lista de especies activas (frontend)
     */
    public static function all() {
        $pdo = db_connect();
        return $pdo->query("SELECT * FROM species WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
    }

    /**
     * Genera slug ASCII seguro para URLs (Perros -> perros, "Gatos & Más" -> gatos-mas)
     */
    public static function slugify(string $name): string {
        $orig = $name;
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
            if ($converted !== false) { $name = $converted; }
        }
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9]+/', '-', $name);
        $name = preg_replace('/-+/', '-', $name);
        $name = trim($name, '-');
        if ($name === '') { $name = 'sp-' . substr(md5($orig), 0, 6); }
        return $name;
    }

    /**
     * Versión para menú: id, nombre y slug pre-construido.
     */
    public static function allForMenu(): array {
        $rows = self::all();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => (int)$r['id'],
                'name' => $r['name'],
                'slug' => self::slugify($r['name'] ?? ''),
            ];
        }
        return $out;
    }

    public static function allAdmin() {
        $pdo = db_connect();
        return $pdo->query("SELECT * FROM species ORDER BY name ASC")->fetchAll();
    }

    public static function find($id) {
        $pdo = db_connect();
        $st = $pdo->prepare("SELECT * FROM species WHERE id = :id");
        $st->execute([':id' => $id]);
        return $st->fetch();
    }

    public static function save($data) {
        $pdo = db_connect();
        
        if (!empty($data['id'])) {
            $sql = "UPDATE species SET 
                    name = :name,
                    description = :description,
                    is_active = :is_active
                    WHERE id = :id";
        } else {
            $sql = "INSERT INTO species (name, description, is_active) 
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
        $st = $pdo->prepare("DELETE FROM species WHERE id = :id");
        return $st->execute([':id' => $id]);
    }

    /**
     * Cuenta productos asociados a esta especie
     */
    public static function countProducts($id) {
        $pdo = db_connect();
        $st = $pdo->prepare("SELECT COUNT(*) FROM product_species WHERE species_id = :id");
        $st->execute([':id' => $id]);
        return (int)$st->fetchColumn();
    }
}
