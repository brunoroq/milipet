<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Mega-Menú</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .status { padding: 15px; margin: 10px 0; border-radius: 8px; }
        .ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .test-item { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>🔍 Test del Mega-Menú de Catálogo</h1>
    
    <?php
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../app/helpers/header_menu.php';
    
    echo '<div class="test-item">';
    echo '<h2>1. Conexión a Base de Datos</h2>';
    try {
        $pdo = db_connect();
        echo '<div class="status ok">✓ Conexión exitosa</div>';
    } catch (Exception $e) {
        echo '<div class="status error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        exit;
    }
    echo '</div>';
    
    echo '<div class="test-item">';
    echo '<h2>2. Datos de Especies</h2>';
    $species = $pdo->query("SELECT id, name, is_active FROM species ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    if (count($species) > 0) {
        echo '<div class="status ok">✓ ' . count($species) . ' especies encontradas</div>';
        echo '<pre>' . print_r($species, true) . '</pre>';
    } else {
        echo '<div class="status error">✗ No hay especies</div>';
    }
    echo '</div>';
    
    echo '<div class="test-item">';
    echo '<h2>3. Datos de Categorías</h2>';
    $categories = $pdo->query("SELECT id, name, is_active FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    if (count($categories) > 0) {
        echo '<div class="status ok">✓ ' . count($categories) . ' categorías encontradas</div>';
        echo '<pre>' . print_r($categories, true) . '</pre>';
    } else {
        echo '<div class="status error">✗ No hay categorías</div>';
    }
    echo '</div>';
    
    echo '<div class="test-item">';
    echo '<h2>4. Relación Product-Species</h2>';
    $ps = $pdo->query("SELECT COUNT(*) as total FROM product_species")->fetch(PDO::FETCH_ASSOC);
    if ($ps['total'] > 0) {
        echo '<div class="status ok">✓ ' . $ps['total'] . ' relaciones encontradas</div>';
    } else {
        echo '<div class="status info">⚠ No hay datos en product_species (se usará fallback)</div>';
    }
    echo '</div>';
    
    echo '<div class="test-item">';
    echo '<h2>5. Helper mp_get_categories_by_species()</h2>';
    $grouped = mp_get_categories_by_species();
    if (!empty($grouped)) {
        echo '<div class="status ok">✓ Helper funcionando - ' . count($grouped) . ' especies con categorías</div>';
        echo '<h3>Estructura generada:</h3>';
        echo '<pre>' . print_r($grouped, true) . '</pre>';
    } else {
        echo '<div class="status error">✗ Helper devuelve array vacío</div>';
    }
    echo '</div>';
    
    echo '<div class="test-item">';
    echo '<h2>6. Simulación del HTML del Mega-Menú</h2>';
    if (!empty($grouped)) {
        echo '<div style="border: 2px solid #28a745; padding: 20px; background: #fff; border-radius: 8px;">';
        echo '<h4 style="margin-top:0;">Vista previa del menú:</h4>';
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">';
        foreach ($grouped as $speciesSlug => $data) {
            echo '<div style="border: 1px solid #ddd; padding: 10px; border-radius: 4px;">';
            echo '<h5 style="margin:0 0 10px 0; color: #333;">' . htmlspecialchars($data['name']) . '</h5>';
            echo '<ul style="list-style: none; padding: 0; margin: 0;">';
            foreach ($data['categories'] as $cat) {
                echo '<li style="margin: 5px 0;">';
                echo '<span style="display: inline-block; padding: 4px 12px; background: #e9ecef; border-radius: 50px; font-size: 14px;">';
                echo htmlspecialchars($cat['name']);
                echo '</span>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        echo '<div class="status ok" style="margin-top: 15px;">✓ El HTML se generará correctamente</div>';
    }
    echo '</div>';
    
    echo '<div class="test-item">';
    echo '<h3>✅ Conclusión</h3>';
    if (!empty($grouped)) {
        echo '<div class="status ok">';
        echo '<strong>Todo está funcionando correctamente.</strong><br>';
        echo 'El mega-menú debería mostrarse al hacer hover sobre "Catálogo".<br><br>';
        echo '<strong>Si no se ve en el navegador:</strong><br>';
        echo '1. Refresca con Ctrl+Shift+R (hard refresh)<br>';
        echo '2. Abre la consola del navegador (F12) y busca mensajes [CATALOG MENU]<br>';
        echo '3. Verifica que el CSS se esté cargando (style.css)<br>';
        echo '4. Verifica que el JS se esté cargando (app.js)';
        echo '</div>';
    } else {
        echo '<div class="status error">Hay un problema con los datos</div>';
    }
    echo '</div>';
    ?>
    
    <hr>
    <p style="text-align: center; color: #666; font-size: 14px;">
        Generado: <?= date('Y-m-d H:i:s') ?>
    </p>
</body>
</html>
