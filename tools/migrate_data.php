#!/usr/bin/env php
<?php
/**
 * Script de migración de datos del esquema antiguo al nuevo
 * 
 * Este script ayuda a migrar datos si ya existían en el formato antiguo:
 * - Productos: description -> short_desc / long_desc
 * - Campañas: date, location, foundation, contact_info, image_url -> nuevos campos
 * 
 * Uso:
 *   php tools/migrate_data.php
 */

// Verificar si se ejecuta desde CLI
if (php_sapi_name() !== 'cli') {
    die("Este script debe ejecutarse desde la línea de comandos.\n");
}

require_once __DIR__ . '/../config/db.php';

echo "===========================================\n";
echo "  Script de Migración de Datos\n";
echo "===========================================\n\n";

try {
    $pdo = db_connect();
    
    // Verificar si hay productos con description pero sin short_desc/long_desc
    echo "1. Verificando productos...\n";
    
    // Como ya no existe la columna description, este script es solo informativo
    // Si necesitas migrar datos reales, deberías hacerlo manualmente con SQL
    
    echo "   ℹ️  Si tenías productos con el campo 'description', deberías:\n";
    echo "      - Dividir la descripción en corta y larga\n";
    echo "      - Actualizar manualmente con SQL:\n";
    echo "        UPDATE products SET \n";
    echo "          short_desc = SUBSTRING(description, 1, 255),\n";
    echo "          long_desc = description\n";
    echo "        WHERE short_desc IS NULL;\n\n";
    
    // Verificar campañas
    echo "2. Verificando campañas...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM campaigns");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ Total de campañas: " . $result['total'] . "\n";
    
    if ($result['total'] > 0) {
        $stmt = $pdo->query("SELECT COUNT(*) as with_start FROM campaigns WHERE start_date IS NOT NULL");
        $withStart = $stmt->fetch(PDO::FETCH_ASSOC)['with_start'];
        echo "   ✓ Campañas con fecha de inicio: {$withStart}\n";
        
        if ($withStart < $result['total']) {
            echo "   ⚠️  Hay campañas sin fecha de inicio asignada.\n";
        }
    }
    
    echo "\n3. Verificando usuarios y roles...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ Total de usuarios: " . $result['total'] . "\n";
    
    if ($result['total'] === 0) {
        echo "\n   ⚠️  ADVERTENCIA: No hay usuarios en la base de datos.\n";
        echo "      Debes crear al menos un usuario admin para poder acceder.\n";
        echo "      Usa: php tools/generate_hash.php <contraseña>\n";
        echo "      Luego ejecuta en MySQL:\n";
        echo "      INSERT INTO users (role_id, name, email, password, is_active)\n";
        echo "      SELECT r.id, 'Admin', 'admin@milipet.cl', '<hash>', 1\n";
        echo "      FROM roles r WHERE r.name = 'admin';\n";
    } else {
        $stmt = $pdo->query("SELECT u.name, u.email, r.name as role_name 
                            FROM users u 
                            JOIN roles r ON u.role_id = r.id 
                            WHERE u.is_active = 1");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\n   Usuarios activos:\n";
        foreach ($users as $user) {
            echo "   - {$user['name']} ({$user['email']}) - Rol: {$user['role_name']}\n";
        }
    }
    
    echo "\n===========================================\n";
    echo "✅ Verificación completada.\n";
    echo "===========================================\n";
    
} catch (PDOException $e) {
    echo "\n❌ Error de base de datos: " . $e->getMessage() . "\n";
    exit(1);
}
