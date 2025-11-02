<?php
// config/config.php

// Manejo de errores: loguear pero no imprimir en HTML
error_reporting(E_ALL);
ini_set('display_errors', '0');   // no enviar a la salida HTML
ini_set('log_errors', '1');       // sí a error_log

// Store configuration array
$config = [
    'store' => [
        'name' => 'MiliPet',
        'address' => 'Maipú, Santiago',
        'phone' => '+56 9 XXXX XXXX', // Replace with actual phone
        'email' => 'contacto@milipet.cl',
        'social' => [
            'whatsapp' => 'https://wa.me/56XXXXXXXXX', // Replace with actual WhatsApp number
            'instagram' => 'https://www.instagram.com/mili_petshop/',
            'facebook' => 'https://facebook.com/milipet'
        ],
        'business_hours' => [
            'monday_friday' => '9:00 - 19:00',
            'saturday' => '10:00 - 14:00',
            'sunday' => 'Cerrado'
        ]
    ]
];

// Database configuration
define('DB_HOST', getenv('MILIPET_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('MILIPET_DB_NAME') ?: 'milipet_db');
define('DB_USER', getenv('MILIPET_DB_USER') ?: 'root');
define('DB_PASS', getenv('MILIPET_DB_PASS') ?: '');

// Store constants for detail views
// === Ubicación y datos de la tienda ===
if (!defined('STORE_NAME')) define('STORE_NAME', 'Petshop Mall PUMAY - Maipú');
if (!defined('STORE_ADDRESS_TEXT')) define('STORE_ADDRESS_TEXT', 'Av. 5 de Abril 33, 9251759 Maipú, Región Metropolitana');
if (!defined('STORE_COORDS')) define('STORE_COORDS', '-33.51029485659258,-70.75813133746277');

// Notas y horarios
if (!defined('STORE_LOCATION_NOTE')) define('STORE_LOCATION_NOTE', 'Local 20 (2° piso)');
if (!defined('STORE_HOURS_WEEKDAYS')) define('STORE_HOURS_WEEKDAYS', 'Lunes a viernes de 10:00 a 19:00');
if (!defined('STORE_HOURS_SATURDAY')) define('STORE_HOURS_SATURDAY', 'Sábados 10:00 a 16:00');
if (!defined('STORE_PICKUP_MESSAGE')) {
    define('STORE_PICKUP_MESSAGE', 'Retiro en tienda Gratis • Disponible hoy');
}

// URL base calculada a partir del front controller (public/index.php)
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';            // ej: /milipet_site/public/index.php
    $base   = rtrim(str_replace('\\','/', dirname($script)), '/'); // ej: /milipet_site/public
    // Asegura una sola barra final
    $base  .= '/';
    define('BASE_URL', $scheme . '://' . $host . $base); // ej: http://localhost/milipet_site/public/
}

// Helper para componer URLs con querystring
if (!function_exists('url')) {
    function url(array $params = []): string {
        $q = http_build_query($params);
        return BASE_URL . ($q ? ('?' . $q) : '');
    }
}

// Store configuration
return [
    'store' => [
        'name' => 'MiliPet',
        'address' => 'Maipú, Santiago',
        'phone' => '+56 9 XXXX XXXX', // Replace with actual phone
        'email' => 'contacto@milipet.cl',
        'social' => [
            'whatsapp' => 'https://wa.me/56XXXXXXXXX', // Replace with actual WhatsApp number
            'instagram' => 'https://www.instagram.com/mili_petshop/',
            'facebook' => 'https://facebook.com/milipet'
        ],
        'business_hours' => [
            'monday_friday' => '9:00 - 19:00',
            'saturday' => '10:00 - 14:00',
            'sunday' => 'Cerrado'
        ]
    ]
];
