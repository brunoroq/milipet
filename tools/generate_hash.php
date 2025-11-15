#!/usr/bin/env php
<?php
/**
 * Script para generar hashes de contraseñas
 * 
 * Uso:
 *   php tools/generate_hash.php micontraseña
 *   
 * O interactivo:
 *   php tools/generate_hash.php
 */

// Verificar si se ejecuta desde CLI
if (php_sapi_name() !== 'cli') {
    die("Este script debe ejecutarse desde la línea de comandos.\n");
}

// Función para generar hash
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Obtener contraseña
$password = null;

// Opción 1: Desde argumentos de línea de comandos
if (isset($argv[1])) {
    $password = $argv[1];
} else {
    // Opción 2: Pedir interactivamente
    echo "===========================================\n";
    echo "  Generador de Hashes de Contraseñas\n";
    echo "===========================================\n\n";
    
    // Intentar ocultar la entrada (solo funciona en Unix/Linux/Mac)
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        echo "Ingresa la contraseña: ";
        system('stty -echo');
        $password = trim(fgets(STDIN));
        system('stty echo');
        echo "\n";
    } else {
        // En Windows, simplemente pedir sin ocultar
        echo "Ingresa la contraseña: ";
        $password = trim(fgets(STDIN));
    }
}

// Validar
if (empty($password)) {
    echo "❌ Error: No se proporcionó ninguna contraseña.\n";
    echo "\nUso:\n";
    echo "  php tools/generate_hash.php micontraseña\n";
    echo "  php tools/generate_hash.php  (modo interactivo)\n";
    exit(1);
}

// Generar hash
echo "\n";
echo "Generando hash...\n\n";

$hash = generatePasswordHash($password);

echo "===========================================\n";
echo "  HASH GENERADO\n";
echo "===========================================\n\n";
echo $hash . "\n\n";

echo "Puedes usar este hash para:\n\n";
echo "1. Crear un usuario admin:\n";
echo "   INSERT INTO users (role_id, name, email, password, is_active)\n";
echo "   SELECT r.id, 'Admin', 'admin@milipet.cl', '{$hash}', 1\n";
echo "   FROM roles r WHERE r.name = 'admin';\n\n";

echo "2. Actualizar contraseña de un usuario existente:\n";
echo "   UPDATE users SET password = '{$hash}'\n";
echo "   WHERE email = 'admin@milipet.cl';\n\n";

echo "===========================================\n";
echo "✅ Hash generado exitosamente.\n";
echo "===========================================\n";
