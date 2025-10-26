<?php
require_once __DIR__ . '/../../config/db.php';

class Product {
    // Cache de columnas por tabla para evitar consultas repetidas
    private static function hasColumn(string $col): bool {
        static $cache = null;
        if ($cache === null) {
            $pdo = db_connect();
            $stmt = $pdo->query("SHOW COLUMNS FROM products");
            $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $cache = array_map('strtolower', $cols);
        }
        return in_array(strtolower($col), $cache, true);
    }

    public static function search($q = '', $category_id = null, $species = null, $include_out_of_stock = false) {
        $pdo = db_connect();
        $sql = "SELECT DISTINCT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON c.id=p.category_id
                LEFT JOIN product_species ps ON ps.product_id = p.id
                LEFT JOIN species s ON s.id = ps.species_id
                WHERE p.is_active=1" . 
                (!$include_out_of_stock ? " AND p.stock > 0" : "");
        $params = [];
        if ($q !== '') {
            $sql .= " AND (p.name LIKE :q OR p.description LIKE :q)";
            $params[':q'] = "%$q%";
        }
        if ($category_id) {
            $sql .= " AND p.category_id=:cid";
            $params[':cid'] = $category_id;
        }
        if ($species) {
            $sql .= " AND s.name=:sp";
            $params[':sp'] = $species;
        }
        $sql .= " ORDER BY p.created_at DESC";
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function find($id) {
        $pdo = db_connect();
        $st = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=:id");
        $st->execute([':id' => $id]);
        return $st->fetch();
    }

    public static function allAdmin() {
        $pdo = db_connect();
        return $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll();
    }

    public static function save($data) {
        $pdo = db_connect();
        
        if (!empty($data['id'])) {
            $sets = [
                'name' => 'name=:name',
                'description' => 'description=:description',
                'price' => 'price=:price',
                'stock' => 'stock=:stock',
                'category_id' => 'category_id=:category_id',
                'image_url' => 'image_url=:image_url',
                'is_active' => 'is_active=:is_active',
            ];
            $sql = 'UPDATE products SET ' . implode(', ', $sets) . ' WHERE id=:id';
        } else {
            $cols = ['name','description','price','stock','category_id','image_url','is_active'];
            $placeholders = array_map(fn($c) => ':' . $c, $cols);
            $sql = 'INSERT INTO products (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $placeholders) . ')';
        }

        $st = $pdo->prepare($sql);

        // Build params depending on INSERT vs UPDATE
        $isUpdate = !empty($data['id']);
        $params = [
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':stock' => $data['stock'],
            ':category_id' => $data['category_id'] ?? null,
            ':image_url' => $data['image_url'] ?? '',
            ':is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ];
        if ($isUpdate) {
            $params[':id'] = $data['id'];
        }

        // Guardar el producto primero
        $st->execute($params);
        $productId = $pdo->lastInsertId() ?: ($data['id'] ?? null);

        // Si tenemos species y un ID válido, actualizamos la relación
        if ($productId && isset($data['species']) && $data['species']) {
            // Primero eliminamos las relaciones existentes
            $st = $pdo->prepare("DELETE FROM product_species WHERE product_id = :pid");
            $st->execute([':pid' => $productId]);

            // Luego insertamos la nueva relación
            $st = $pdo->prepare("INSERT INTO product_species (product_id, species_id) 
                                SELECT :pid, s.id 
                                FROM species s 
                                WHERE s.name = :species_name");
            $st->execute([
                ':pid' => $productId,
                ':species_name' => $data['species']
            ]);
        }

        return $productId;
    }

    public static function delete($id) {
        $pdo = db_connect();
        $st = $pdo->prepare("DELETE FROM products WHERE id=:id");
        return $st->execute([':id' => $id]);
    }

    /**
     * Check if a product has stock available
     */
    public static function hasStock($id) {
        $pdo = db_connect();
        $st = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $st->execute([':id' => $id]);
        $result = $st->fetch(PDO::FETCH_COLUMN);
        return $result > 0;
    }

    /**
     * Update product stock
     */
    public static function updateStock($id, $quantity) {
        $pdo = db_connect();
        $st = $pdo->prepare("UPDATE products SET stock = stock + :quantity WHERE id = :id");
        return $st->execute([
            ':id' => $id,
            ':quantity' => $quantity
        ]);
    }

    /**
     * Get products with low stock (less than specified amount)
     */
    public static function getLowStock($threshold = 5) {
        $pdo = db_connect();
        $st = $pdo->prepare("SELECT p.*, c.name AS category_name 
                            FROM products p 
                            LEFT JOIN categories c ON c.id=p.category_id 
                            WHERE p.stock <= :threshold 
                            ORDER BY p.stock ASC");
        $st->execute([':threshold' => $threshold]);
        return $st->fetchAll();
    }
}
