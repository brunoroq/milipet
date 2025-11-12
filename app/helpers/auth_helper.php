<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_authenticated() {
    // Compatibilidad: aceptar uid/role nuevos o admin_id legacy
    $uid = $_SESSION['uid'] ?? ($_SESSION['admin_id'] ?? ($_SESSION['user_id'] ?? null));
    if (!$uid) { return false; }
    $last = $_SESSION['last_activity'] ?? null;
    $expire = 30 * 60;
    if (!$last || (time() - $last) > $expire) {
        // Session expired: clear session WITHOUT destroying it (to preserve flash messages)
        $_SESSION['uid'] = null;
        $_SESSION['user_id'] = null;
        $_SESSION['role'] = null;
        $_SESSION['last_activity'] = null;
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
        if (function_exists('flash')) { flash('error','Debes iniciar sesión.'); }
        header('Location: ' . (function_exists('url') ? url(['r'=>'auth/admin_login']) : '?r=auth/admin_login'));
        exit;
    }
}

function require_admin() {
    $uid = $_SESSION['user_id'] ?? ($_SESSION['uid'] ?? null);
    $role = $_SESSION['role'] ?? null;
    if (!$uid || $role !== 'admin') {
        if (function_exists('flash')) { flash('error','Debes iniciar sesión.'); }
        header('Location: ' . (function_exists('url') ? url(['r'=>'auth/admin_login']) : '?r=auth/admin_login'));
        exit;
    }
}

// Helper para generar URLs consistentes
function get_route($path) {
    return '?r=' . $path;
}