<?php
// config/config.php

// --- Errores: loguear, no imprimir en HTML ---
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// --- Variables de entorno / defaults portables ---
define('DB_HOST', getenv('DB_HOST') ?: (getenv('MILIPET_DB_HOST') ?: 'localhost'));
define('DB_PORT', getenv('DB_PORT') ?: (getenv('MILIPET_DB_PORT') ?: '3306'));
define('DB_NAME', getenv('DB_NAME') ?: (getenv('MILIPET_DB_NAME') ?: 'milipet_db'));
define('DB_USER', getenv('DB_USER') ?: (getenv('MILIPET_DB_USER') ?: 'root'));
define('DB_PASS', getenv('DB_PASS') ?: (getenv('MILIPET_DB_PASS') ?: ''));

// --- Constantes de tienda ---
if (!defined('STORE_NAME'))            define('STORE_NAME', 'Petshop Mall PUMAY - Maipú');
if (!defined('STORE_ADDRESS_TEXT'))    define('STORE_ADDRESS_TEXT', 'Av. 5 de Abril 33, 9251759 Maipú, Región Metropolitana');
if (!defined('STORE_COORDS'))          define('STORE_COORDS', '-33.51029485659258,-70.75813133746277');
if (!defined('STORE_LOCATION_NOTE'))   define('STORE_LOCATION_NOTE', 'Local 20 (2° piso)');
if (!defined('STORE_HOURS_WEEKDAYS'))  define('STORE_HOURS_WEEKDAYS', 'Lunes a viernes de 10:00 a 19:00');
if (!defined('STORE_HOURS_SATURDAY'))  define('STORE_HOURS_SATURDAY', 'Sábados 10:00 a 16:00');
if (!defined('STORE_PICKUP_MESSAGE'))  define('STORE_PICKUP_MESSAGE', 'Retiro en tienda Gratis • Disponible hoy');

// --- BASE_URL calculada desde front controller ---
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $base   = rtrim(str_replace('\\', '/', dirname($script)), '/');
    define('BASE_URL', $scheme . '://' . $host . ($base ? $base . '/' : '/'));
}

// --- Helper URL ---
if (!function_exists('url')) {
    function url(array $params = []): string {
        $q = http_build_query($params);
        return BASE_URL . ($q ? ('?' . $q) : '');
    }
}

// --- Helper PDO (conexión Lazy) ---
if (!function_exists('pdo')) {
    function pdo(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
            $opts = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
        }
        return $pdo;
    }
}

// --- Config de la tienda (1 sola fuente) ---
return [
    'store' => [
        'name'    => 'MiliPet',
        'address' => 'Maipú, Santiago',
        'phone'   => '+56 9 XXXX XXXX',
        'email'   => 'contacto@milipet.cl',
        'social'  => [
            'whatsapp'  => 'https://wa.me/56XXXXXXXXX',
            'instagram' => 'https://www.instagram.com/mili_petshop/',
            'facebook'  => 'https://facebook.com/milipet',
        ],
        'business_hours' => [
            'monday_friday' => '9:00 - 19:00',
            'saturday'      => '10:00 - 14:00',
            'sunday'        => 'Cerrado',
        ],
    ],
];
