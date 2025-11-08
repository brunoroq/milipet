<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_authenticated() {
    // Compatibilidad: aceptar uid/role nuevos o admin_id legacy
    $uid = $_SESSION['uid'] ?? ($_SESSION['admin_id'] ?? null);
    if (!$uid) { return false; }
    $last = $_SESSION['last_activity'] ?? null;
    $expire = 30 * 60;
    if (!$last || (time() - $last) > $expire) {
        logout();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

function login($user) {
    // $user puede venir de tabla users o admins (legacy)
    $_SESSION['uid'] = $user['id'];
    if (!empty($user['name'])) { $_SESSION['admin_name'] = $user['name']; }
    if (!empty($user['role'])) { $_SESSION['role'] = $user['role']; }
    else if (!isset($_SESSION['role'])) { $_SESSION['role'] = 'admin'; }
    $_SESSION['last_activity'] = time();
}

function logout() {
    $_SESSION = [];
    if (isset($_COOKIE[session_name()])) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function require_auth() {
    if (!is_authenticated()) {
        $_SESSION['flash_error'] = 'Debe iniciar sesión para acceder al panel de administración';
        header('Location: ' . (function_exists('url') ? url(['r'=>'auth/admin_login']) : '?r=auth/admin_login'));
        exit;
    }
}

// Helper para generar URLs consistentes
function get_route($path) {
    return '?r=' . $path;
}