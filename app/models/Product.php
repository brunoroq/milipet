<?php
require_once __DIR__ . '/../../config/db.php';

class Product {

    public static function search($query = '', $category_id = null, $species_id = null, $in_stock = false) {
        try {
            $pdo = db_connect();
            
            // Base query con WHERE 1=1 para facilitar concatenación
            $sql = "SELECT DISTINCT p.*, c.name AS category_name 
                    FROM products p 
                    LEFT JOIN categories c ON c.id = p.category_id
                    LEFT JOIN product_species ps ON ps.product_id = p.id
                    LEFT JOIN species s ON s.id = ps.species_id
                    WHERE 1=1";
            
            $params = [];
            
            // Filtro: solo productos activos
            $sql .= " AND p.is_active = 1";

            // Filtro: stock disponible (true => solo stock)
            if ($in_stock) {
                $sql .= " AND p.stock > 0";
            }

            // Filtro: búsqueda por texto (insensible a mayúsculas/minúsculas)
            $query = trim((string)$query);
            if ($query !== '') {
                $sql .= " AND (LOWER(p.name) LIKE LOWER(?) OR LOWER(p.short_desc) LIKE LOWER(?) OR LOWER(p.long_desc) LIKE LOWER(?))";
                $like = "%{$query}%";
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }
            
            // Filtro: categoría
            if ($category_id !== null) {
                $sql .= " AND p.category_id = ?";
                $params[] = (int)$category_id;
            }
            
            // Filtro: especie (por ID en tabla puente)
            if ($species_id !== null) {
                $sql .= " AND ps.species_id = ?";
                $params[] = (int)$species_id;
            }
            
            $sql .= " ORDER BY p.name ASC";
            
            // DEBUG opcional en local (comentar en producción)
            if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
                error_log('[SEARCH SQL] ' . $sql);
                error_log('[SEARCH PARAMS] ' . json_encode($params));
            }
            
            $st = $pdo->prepare($sql);
            $st->execute($params);
            return $st->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en Product::search() - " . $e->getMessage());
            return [];
        }
    }

    public static function find($id) {
        $pdo = db_connect();
        $st = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=:id");
        $st->execute([':id' => $id]);
        $product = $st->fetch();
        
        // Obtener especies asociadas
        if ($product) {
            $st = $pdo->prepare("SELECT s.name FROM product_species ps JOIN species s ON s.id = ps.species_id WHERE ps.product_id = :id");
            $st->execute([':id' => $id]);
            $species = $st->fetchAll(PDO::FETCH_COLUMN);
            $product['species'] = !empty($species) ? $species[0] : null; // Primera especie (actualmente solo soportamos una)
        }
        
        return $product;
    }

    public static function allAdmin() {
        $pdo = db_connect();
        $products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll();
        
        // Agregar especies a cada producto
        foreach ($products as &$product) {
            $st = $pdo->prepare("SELECT s.name FROM product_species ps JOIN species s ON s.id = ps.species_id WHERE ps.product_id = :id");
            $st->execute([':id' => $product['id']]);
            $species = $st->fetchAll(PDO::FETCH_COLUMN);
            $product['species'] = !empty($species) ? $species[0] : null;
        }
        
        return $products;
    }

    public static function save($data) {
        $pdo = db_connect();
        
        if (!empty($data['id'])) {
            $sets = [
                'name' => 'name=:name',
                'short_desc' => 'short_desc=:short_desc',
                'long_desc' => 'long_desc=:long_desc',
                'price' => 'price=:price',
                'stock' => 'stock=:stock',
                'low_stock_threshold' => 'low_stock_threshold=:low_stock_threshold',
                'category_id' => 'category_id=:category_id',
                'image_url' => 'image_url=:image_url',
                'is_featured' => 'is_featured=:is_featured',
                'is_active' => 'is_active=:is_active',
            ];
            $sql = 'UPDATE products SET ' . implode(', ', $sets) . ' WHERE id=:id';
        } else {
            $cols = ['name','short_desc','long_desc','price','stock','low_stock_threshold','category_id','image_url','is_featured','is_active'];
            $placeholders = array_map(fn($c) => ':' . $c, $cols);
            $sql = 'INSERT INTO products (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $placeholders) . ')';
        }

        $st = $pdo->prepare($sql);

        // Build params depending on INSERT vs UPDATE
        $isUpdate = !empty($data['id']);
        $params = [
            ':name' => $data['name'],
            ':short_desc' => $data['short_desc'] ?? '',
            ':long_desc' => $data['long_desc'] ?? '',
            ':price' => $data['price'],
            ':stock' => $data['stock'],
            ':low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            ':category_id' => $data['category_id'] ?? null,
            ':image_url' => $data['image_url'] ?? '',
            ':is_featured' => isset($data['is_featured']) ? (int)$data['is_featured'] : 0,
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

    /**
     * Get featured products (is_featured = 1) or random if no featured
     */
    public static function getFeatured($limit = 4) {
        try {
            $pdo = db_connect();
            
            // Primero intentar obtener productos destacados
            $st = $pdo->prepare("SELECT p.*, c.name AS category_name 
                                FROM products p 
                                LEFT JOIN categories c ON c.id = p.category_id 
                                WHERE p.is_active = 1 AND p.stock > 0 AND p.is_featured = 1
                                ORDER BY p.created_at DESC 
                                LIMIT :limit");
            $st->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $st->execute();
            $featured = $st->fetchAll(PDO::FETCH_ASSOC);
            
            // Si no hay suficientes destacados, completar con aleatorios
            if (count($featured) < $limit) {
                $remaining = $limit - count($featured);
                $st = $pdo->prepare("SELECT p.*, c.name AS category_name 
                                    FROM products p 
                                    LEFT JOIN categories c ON c.id = p.category_id 
                                    WHERE p.is_active = 1 AND p.stock > 0 AND p.is_featured = 0
                                    ORDER BY RAND() 
                                    LIMIT :limit");
                $st->bindValue(':limit', (int)$remaining, PDO::PARAM_INT);
                $st->execute();
                $random = $st->fetchAll(PDO::FETCH_ASSOC);
                $featured = array_merge($featured, $random);
            }
            
            return $featured;
        } catch (PDOException $e) {
            error_log("Error en Product::getFeatured() - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Recuperar productos por lista de IDs (solo activos por defecto)
     */
    public static function findByIds(array $ids, bool $onlyActive = true): array {
        $ids = array_filter(array_map('intval', $ids), fn($v) => $v > 0);
        if (empty($ids)) return [];
        $pdo = db_connect();
        $in  = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id IN ($in)";
        if ($onlyActive) { $sql .= " AND p.is_active = 1"; }
        $sql .= " ORDER BY FIELD(p.id, $in)"; // Mantener orden original
        // FIELD necesita repetir los parámetros para mantener orden
        $params = array_merge($ids, $ids);
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
