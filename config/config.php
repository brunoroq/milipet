<?php
// config/config.php
// Definiciones de conexión a base de datos (usar variables de entorno si están presentes)
define('DB_HOST', getenv('MILIPET_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('MILIPET_DB_NAME') ?: 'milipet_db');
define('DB_USER', getenv('MILIPET_DB_USER') ?: 'root');
define('DB_PASS', getenv('MILIPET_DB_PASS') ?: '');

// Configuración de URL base
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = rtrim(str_replace('index.php', '', $scriptName), '/');
define('BASE_URL', "$scheme://$host$basePath");
