<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';

class AuthController {
  // Rate limit simple por email + IP
  private function throttleKey($email){ return 'login_attempts_'.md5(strtolower(trim($email)).'|'.($_SERVER['REMOTE_ADDR'] ?? '')); }
  private function tooManyAttempts($key){ $d = $_SESSION[$key] ?? ['n'=>0,'t'=>0]; return $d['n'] >= 5 && (time()-$d['t'] < 15*60); }
  private function hitAttempt($key){ $d = $_SESSION[$key] ?? ['n'=>0,'t'=>0]; $d['n']++; $d['t']=time(); $_SESSION[$key]=$d; }
  private function clearAttempts($key){ unset($_SESSION[$key]); }

  // Formulario de login administración
  public function adminLoginForm() {
    render('auth/admin_login', [
      'csrf' => csrf_token(),
    ]);
  }

  // Procesa login administración
  public function adminLogin() {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $pass  = (string)($_POST['password'] ?? '');
    $remember = !empty($_POST['remember']);
    $key   = $this->throttleKey($email);
    if ($this->tooManyAttempts($key)) {
      flash('error','Credenciales inválidas.');
      header('Location: /?r=auth/admin_login');
      exit;
    }

    // Validaciones de entrada (genérico)
    if ($email === '' || $pass === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      if (defined('APP_ENV') && APP_ENV === 'dev') { error_log('[LOGIN] invalid input email/pass'); }
      flash('error','Credenciales inválidas.');
      header('Location: /?r=auth/admin_login');
      exit;
    }

    // Buscar usuario con rol
    try {
      $u = User::findByEmailWithRole($email);
    } catch (Throwable $e) {
      if (defined('APP_ENV') && APP_ENV === 'dev') { error_log('[LOGIN] DB error: '.$e->getMessage()); }
      flash('error','No fue posible autenticar. Inténtalo nuevamente.');
      header('Location: /?r=auth/admin_login');
      exit;
    }

    $pass_ok = isset($u['password']) && password_verify($pass, $u['password']);
    if (defined('APP_ENV') && APP_ENV === 'dev') {
      error_log('[LOGIN] found='.(int)(bool)$u.' pass_ok='.(int)$pass_ok.' role='.($u['role_name'] ?? 'null'));
    }

    // Validar: usuario existe, password correcto, cuenta activa
    if (!$u || !$pass_ok || (int)$u['is_active'] !== 1) {
      $this->hitAttempt($key);
      flash('error','Credenciales inválidas.');
      header('Location: /?r=auth/admin_login');
      exit;
    }

    // Validar que sea admin
    if (!isset($u['role_name']) || $u['role_name'] !== 'admin') {
      flash('error','Credenciales inválidas.');
      header('Location: /?r=auth/admin_login');
      exit;
    }

    // Login exitoso
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$u['id'];
    $_SESSION['uid']  = (int)$u['id']; // compatibilidad
    $_SESSION['email'] = $u['email'];
    $_SESSION['name'] = $u['name'];
    $_SESSION['role'] = $u['role_name'];
    $_SESSION['last_activity'] = time();
    
    // Actualizar último login
    User::updateLastLogin($u['id']);
    
    $this->clearAttempts($key);
    
    // Remember me (usando el campo remember_token de la tabla users)
    if ($remember) {
      $token = bin2hex(random_bytes(32));
      User::updateRememberToken($u['id'], $token);
      setcookie('mp_remember', $token, [
        'expires' => time() + (30 * 86400),
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
      ]);
    }
    if (defined('APP_ENV') && APP_ENV === 'dev') {
      error_log('[LOGIN OK] sid='.session_id().' uid='.$_SESSION['user_id'].' role='.$_SESSION['role']);
    }
    header('Location: /?r=admin/dashboard');
    exit;
  }

  public function logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    if (function_exists('mp_clear_remember_cookie')) { mp_clear_remember_cookie(); }
    header('Location: '.url(['r'=>'home']));
  }
}