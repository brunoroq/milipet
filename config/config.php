<?php
require_once __DIR__ . '/app.php';
// config/config.php

// --- Errores: controlados por APP_ENV, siempre registrar ---
error_reporting(E_ALL);
ini_set('log_errors', '1');
if (defined('APP_ENV') && APP_ENV === 'dev') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    ini_set('display_errors', '0');
}

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

// Ruta absoluta al directorio public (para verificar existencia de archivos)
if (!defined('PUBLIC_PATH')) {
    $publicPath = realpath(__DIR__ . '/../public');
    define('PUBLIC_PATH', $publicPath ?: (__DIR__ . '/../public'));
}

// Helper asset() para construir URLs de recursos evitando dobles /public
if (!function_exists('asset')) {
    function asset(string $path): string {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
}

// Helper image_src() para normalizar rutas de imagen con fallback a placeholder
if (!function_exists('image_src')) {
    function image_src(?string $path): string {
        if (empty($path)) {
            return asset('assets/img/placeholder.png');
        }
        // External URLs pass through unchanged
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        // Normalize local path to /assets/...
        $normalized = ltrim($path, '/');
        if (strpos($normalized, 'assets/') !== 0) {
            $normalized = 'assets/' . ltrim($normalized, '/');
        }
        // Check if file exists
        $filePath = PUBLIC_PATH . '/' . $normalized;
        if (is_file($filePath)) {
            return asset($normalized);
        }
        // Fallback to placeholder
        return asset('assets/img/placeholder.png');
    }
}

// --- Helper URL ---
if (!function_exists('url')) {
    function url(array $params = []): string {
        $q = http_build_query($params);
        return BASE_URL . ($q ? ('?' . $q) : '');
    }
}

// --- Flash messages ---
if (!function_exists('flash')) {
    function flash(string $key, ?string $val=null) {
        if ($val === null) { $v = $_SESSION['_flash'][$key] ?? null; unset($_SESSION['_flash'][$key]); return $v; }
        $_SESSION['_flash'][$key] = $val;
        return null;
    }
}

// --- CSRF token y verificación (acepta compatibilidad con 'csrf') ---
if (empty($_SESSION['_csrf'])) { $_SESSION['_csrf'] = bin2hex(random_bytes(16)); }
if (!function_exists('csrf_token')) {
    function csrf_token(): string { return $_SESSION['_csrf']; }
}
if (!function_exists('csrf_check')) {
    function csrf_check() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $t = $_POST['_csrf'] ?? ($_POST['csrf'] ?? '');
            if (!hash_equals($_SESSION['_csrf'], $t)) {
                http_response_code(400);
                exit('CSRF token inválido');
            }
        }
    }
}

// --- Guard para roles ---
if (!function_exists('requireRole')) {
    function requireRole(array $allowed) {
        $role = $_SESSION['role'] ?? null;
        $uid  = $_SESSION['uid'] ?? ($_SESSION['user_id'] ?? null);
        if (!$uid || !$role || !in_array($role, $allowed, true)) {
            flash('error','Debes iniciar sesión.');
            header('Location: ' . url(['r'=>'auth/admin_login']));
            exit;
        }
    }
}

// --- Remember me (selector + validator) ---
if (!defined('REMEMBER_COOKIE_NAME')) {
    define('REMEMBER_COOKIE_NAME', 'mp_remember');
}

if (!function_exists('mp_set_remember_token')) {
    function mp_set_remember_token(int $userId, int $days = 30): bool {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32)); // guardaremos hash en DB
        $hash = hash('sha256', $validator);
        $expires = (new DateTime('+'.$days.' days'))->format('Y-m-d H:i:s');

        // Guardar en DB
        $db = class_exists('Database') ? Database::getInstance()->getConnection() : (function_exists('db_connect') ? db_connect() : null);
        if ($db instanceof PDO) {
            try {
                $stmt = $db->prepare('INSERT INTO remember_tokens (user_id, selector, hashed_validator, expires_at) VALUES (?,?,?,?)');
                $stmt->execute([$userId, $selector, $hash, $expires]);
            } catch (Throwable $e) {
                error_log('[REMEMBER] insert failed: '.$e->getMessage());
                // No establecer cookie si no podemos persistir el token
                return false;
            }
        } else {
            return false;
        }

        // Cookie segura
        $cookieVal = $selector.':'.$validator;
        $params = [
            'expires'  => time() + ($days * 86400),
            'path'     => '/',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ];
        setcookie(REMEMBER_COOKIE_NAME, $cookieVal, $params);
        return true;
    }
}

if (!function_exists('mp_clear_remember_cookie')) {
    function mp_clear_remember_cookie(): void {
        if (!empty($_COOKIE[REMEMBER_COOKIE_NAME])) {
            $params = session_get_cookie_params();
            setcookie(REMEMBER_COOKIE_NAME, '', time() - 42000, '/');
            unset($_COOKIE[REMEMBER_COOKIE_NAME]);
        }
    }
}

if (!function_exists('mp_forget_token_by_selector')) {
    function mp_forget_token_by_selector(string $selector): void {
        $db = class_exists('Database') ? Database::getInstance()->getConnection() : (function_exists('db_connect') ? db_connect() : null);
        if ($db instanceof PDO) {
            try {
                $stmt = $db->prepare('DELETE FROM remember_tokens WHERE selector=?');
                $stmt->execute([$selector]);
            } catch (Throwable $e) {
                error_log('[REMEMBER] delete failed: '.$e->getMessage());
            }
        }
    }
}

if (!function_exists('mp_remember_autologin')) {
    function mp_remember_autologin(): void {
        if (!empty($_SESSION['uid'])) { return; }
        $val = $_COOKIE[REMEMBER_COOKIE_NAME] ?? '';
        if (!$val || strpos($val, ':') === false) { return; }
        [$selector, $validator] = explode(':', $val, 2);
        if (!$selector || !$validator) { return; }

        $db = class_exists('Database') ? Database::getInstance()->getConnection() : (function_exists('db_connect') ? db_connect() : null);
        if (!($db instanceof PDO)) { return; }

        try {
            $stmt = $db->prepare('SELECT rt.user_id, rt.hashed_validator, rt.expires_at, u.role, u.is_active FROM remember_tokens rt JOIN users u ON u.id=rt.user_id WHERE selector=? LIMIT 1');
            $stmt->execute([$selector]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('[REMEMBER] select failed: '.$e->getMessage());
            mp_clear_remember_cookie();
            return;
        }
        if (!$row) { mp_clear_remember_cookie(); return; }
        if (strtotime($row['expires_at']) < time() || empty($row['is_active'])) { mp_forget_token_by_selector($selector); mp_clear_remember_cookie(); return; }

        $calc = hash('sha256', $validator);
        if (!hash_equals($row['hashed_validator'], $calc)) {
            // posible abuso: borrar token y cookie
            mp_forget_token_by_selector($selector);
            mp_clear_remember_cookie();
            return;
        }

        // Rotar token al usarlo
        mp_forget_token_by_selector($selector);
        mp_set_remember_token((int)$row['user_id']);

        // Iniciar sesión
        session_regenerate_id(true);
        $_SESSION['uid'] = (int)$row['user_id'];
        $_SESSION['role'] = $row['role'] ?? 'admin';
        $_SESSION['last_activity'] = time();
    }
}

// Limpieza de tokens expirados (se puede llamar desde cron)
if (!function_exists('mp_cleanup_expired_tokens')) {
    function mp_cleanup_expired_tokens(): int {
        $db = class_exists('Database') ? Database::getInstance()->getConnection() : (function_exists('db_connect') ? db_connect() : null);
        if (!($db instanceof PDO)) { return 0; }
        try {
            $stmt = $db->prepare('DELETE FROM remember_tokens WHERE expires_at < NOW()');
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Throwable $e) {
            error_log('[REMEMBER] cleanup failed: '.$e->getMessage());
            return 0;
        }
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
        'phone'   => '+56 9 5458 036',
        'email'   => 'contacto@milipet.cl',
        'social'  => [
            'whatsapp'  => 'https://wa.me/5695458036',
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
