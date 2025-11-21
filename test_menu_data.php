<?php
/**
 * Script de diagnóstico para verificar datos del mega-menú
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/helpers/header_menu.php';

echo "=== DIAGNÓSTICO MEGA-MENÚ ===\n\n";

$pdo = db_connect();

// 1. Verificar especies activas
echo "1. ESPECIES ACTIVAS:\n";
$species = $pdo->query("SELECT id, name, is_active FROM species ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
foreach ($species as $sp) {
    echo "  - {$sp['name']} (ID: {$sp['id']}, active: {$sp['is_active']})\n";
}
echo "Total: " . count($species) . "\n\n";

// 2. Verificar categorías activas
echo "2. CATEGORÍAS ACTIVAS:\n";
$categories = $pdo->query("SELECT id, name, is_active FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
foreach ($categories as $cat) {
    echo "  - {$cat['name']} (ID: {$cat['id']}, active: {$cat['is_active']})\n";
}
echo "Total: " . count($categories) . "\n\n";

// 3. Verificar productos
echo "3. PRODUCTOS:\n";
$products = $pdo->query("SELECT COUNT(*) as total FROM products")->fetch(PDO::FETCH_ASSOC);
echo "Total productos: {$products['total']}\n\n";

// 4. Verificar relación product_species
echo "4. RELACIÓN PRODUCT_SPECIES:\n";
$ps = $pdo->query("SELECT COUNT(*) as total FROM product_species")->fetch(PDO::FETCH_ASSOC);
echo "Total relaciones: {$ps['total']}\n";

if ($ps['total'] > 0) {
    $psDetails = $pdo->query("
        SELECT s.name AS species, COUNT(*) as count
        FROM product_species ps
        JOIN species s ON ps.species_id = s.id
        GROUP BY s.name
        ORDER BY s.name
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($psDetails as $detail) {
        echo "  - {$detail['species']}: {$detail['count']} productos\n";
    }
} else {
    echo "  ⚠️  NO HAY DATOS EN product_species\n";
}
echo "\n";

// 5. Probar consulta del mega-menú
echo "5. CONSULTA DEL MEGA-MENÚ:\n";
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
$results = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
echo "Resultados: " . count($results) . "\n";
if (count($results) > 0) {
    echo "Muestra:\n";
    foreach (array_slice($results, 0, 5) as $row) {
        echo "  - {$row['species_name']} → {$row['category_name']}\n";
    }
} else {
    echo "  ⚠️  LA CONSULTA NO DEVUELVE RESULTADOS\n";
}
echo "\n";

// 6. Probar helper
echo "6. RESULTADO DEL HELPER mp_get_categories_by_species():\n";
$grouped = mp_get_categories_by_species();
if (empty($grouped)) {
    echo "  ⚠️  EL HELPER DEVUELVE ARRAY VACÍO\n";
} else {
    echo "Especies encontradas: " . count($grouped) . "\n";
    foreach ($grouped as $slug => $data) {
        echo "  - {$data['name']} (slug: {$slug}): " . count($data['categories']) . " categorías\n";
    }
}
echo "\n";

echo "=== FIN DIAGNÓSTICO ===\n";
