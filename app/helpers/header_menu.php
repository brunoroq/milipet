<?php
/**
 * Helper centralizado para cargar datos del mega-menú del header.
 * Devuelve especies y categorías activas con slug seguro para URLs.
 */
require_once __DIR__ . '/../models/Species.php';
require_once __DIR__ . '/../models/Category.php';

if (!function_exists('mp_slugify')) {
    /**
     * Genera un slug ASCII seguro (minúsculas, guiones) para nombres.
     */
    function mp_slugify(string $name): string {
        $orig = $name;
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
            if ($converted !== false) { $name = $converted; }
        }
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9]+/', '-', $name);
        $name = preg_replace('/-+/', '-', $name);
        $name = trim($name, '-');
        if ($name === '') { // fallback si transliteración deja vacío
            $name = 'item-' . substr(md5($orig), 0, 6);
        }
        return $name;
    }
}

if (!function_exists('mp_get_header_menu')) {
    /**
     * Obtiene arrays para el header.
     * @return array{species: array<int,array{id:int,name:string,slug:string}>, categories: array<int,array{id:int,name:string,slug:string}>}
     */
    function mp_get_header_menu(): array {
        $species   = [];
        $categories = [];
        try {
            $rawSpecies = Species::all();
            foreach ($rawSpecies as $sp) {
                if ((int)($sp['is_active'] ?? 1) !== 1) { continue; }
                $species[] = [
                    'id' => (int)$sp['id'],
                    'name' => $sp['name'],
                    'slug' => mp_slugify($sp['name'] ?? ''),
                ];
            }
        } catch (Throwable $e) { $species = []; }
        try {
            $rawCats = Category::all();
            foreach ($rawCats as $cat) {
                if ((int)($cat['is_active'] ?? 1) !== 1) { continue; }
                $categories[] = [
                    'id' => (int)$cat['id'],
                    'name' => $cat['name'],
                    'slug' => mp_slugify($cat['name'] ?? ''),
                ];
            }
        } catch (Throwable $e) { $categories = []; }
        return ['species' => $species, 'categories' => $categories];
    }
}

if (!function_exists('mp_get_categories_by_species')) {
    /**
     * Agrupa categorías por especie basándose en la relación products -> product_species.
     * Devuelve estructura simple: ['perros' => ['name' => 'Perros', 'categories' => [['slug'=>'...', 'name'=>'...']]], ...]
     */
    function mp_get_categories_by_species(): array {
        require_once __DIR__ . '/../../config/db.php';
        $pdo = db_connect();
        
        // Consulta que relaciona especies con categorías a través de productos
        $sql = "
            SELECT DISTINCT
                s.name AS species_name,
                c.name AS category_name
            FROM species s
            JOIN product_species ps ON s.id = ps.species_id
            JOIN products p ON ps.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            WHERE s.is_active = 1 AND c.is_active = 1
            ORDER BY s.name ASC, c.name ASC
        ";
        
        $grouped = [];
        try {
            $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: registrar cuántas filas se obtuvieron
            if (defined('APP_ENV') && APP_ENV === 'dev') {
                error_log('[HEADER_MENU] Query returned ' . count($rows) . ' rows');
            }
            
            foreach ($rows as $row) {
                $speciesSlug = mp_slugify($row['species_name']);
                
                if (!isset($grouped[$speciesSlug])) {
                    $grouped[$speciesSlug] = [
                        'name' => $row['species_name'],
                        'categories' => [],
                    ];
                }
                
                $grouped[$speciesSlug]['categories'][] = [
                    'name' => $row['category_name'],
                    'slug' => mp_slugify($row['category_name']),
                ];
            }
            
            // Fallback: Si no hay datos en product_species, crear estructura básica con todas las categorías para cada especie
            if (empty($grouped)) {
                if (defined('APP_ENV') && APP_ENV === 'dev') {
                    error_log('[HEADER_MENU] No data found, using fallback');
                }
                
                // Obtener especies y categorías por separado
                $speciesRows = $pdo->query("SELECT name FROM species WHERE is_active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
                $categoryRows = $pdo->query("SELECT name FROM categories WHERE is_active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($speciesRows as $sp) {
                    $speciesSlug = mp_slugify($sp['name']);
                    $grouped[$speciesSlug] = [
                        'name' => $sp['name'],
                        'categories' => [],
                    ];
                    
                    // Asignar todas las categorías a cada especie (temporal)
                    foreach ($categoryRows as $cat) {
                        $grouped[$speciesSlug]['categories'][] = [
                            'name' => $cat['name'],
                            'slug' => mp_slugify($cat['name']),
                        ];
                    }
                }
            }
        } catch (Throwable $e) {
            // Log del error si está en desarrollo
            if (defined('APP_ENV') && APP_ENV === 'dev') {
                error_log('[HEADER_MENU] Error: ' . $e->getMessage());
            }
            $grouped = [];
        }
        
        return $grouped;
    }
}
