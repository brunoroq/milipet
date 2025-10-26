<?php
// config/config.php

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

// Configuración de URL base
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = rtrim(str_replace('index.php', '', $scriptName), '/');
define('BASE_URL', "$scheme://$host$basePath");

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
