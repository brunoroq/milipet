<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_authenticated() {
    // Verificar si hay una sesión activa y no ha expirado
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['last_activity'])) {
        return false;
    }

    // Verificar si la sesión ha expirado (30 minutos)
    $expire_time = 30 * 60; // 30 minutos en segundos
    if (time() - $_SESSION['last_activity'] > $expire_time) {
        logout();
        return false;
    }

    // Actualizar timestamp de última actividad
    $_SESSION['last_activity'] = time();
    return true;
}

function login($admin) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['last_activity'] = time();
}

function logout() {
    // Destruir todas las variables de sesión
    $_SESSION = array();

    // Destruir la cookie de sesión si existe
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destruir la sesión
    session_destroy();
}

function require_auth() {
    if (!is_authenticated()) {
        $_SESSION['flash_error'] = 'Debe iniciar sesión para acceder al panel de administración';
        header('Location: ?r=auth/login');
        exit;
    }
}

// Helper para generar URLs consistentes
function get_route($path) {
    return '?r=' . $path;
}