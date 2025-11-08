#!/usr/bin/env php
<?php
// scripts/cleanup_tokens.php
// Limpia tokens remember expirados. Uso:
//   php scripts/cleanup_tokens.php
// Añadir a cron, ejemplo: cada noche a las 03:15
// 15 3 * * * /usr/bin/php /ruta/a/milipet/scripts/cleanup_tokens.php >> /ruta/a/milipet/var/log/cleanup_tokens.log 2>&1

$root = dirname(__DIR__);
require_once $root . '/config/config.php';
require_once $root . '/config/db.php';

if (!function_exists('mp_cleanup_expired_tokens')) {
    fwrite(STDERR, "Función mp_cleanup_expired_tokens no disponible.\n");
    exit(1);
}

$count = mp_cleanup_expired_tokens();
$ts = date('c');
echo "[$ts] Tokens expirados eliminados: $count\n";
