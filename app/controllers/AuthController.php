<?php
require_once __DIR__ . '/../../config/db.php';

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

    // Conexión DB
    try {
      $db = Database::getInstance()->getConnection();
    } catch (Throwable $e) {
      if (defined('APP_ENV') && APP_ENV === 'dev') { error_log('[LOGIN] DB connect error: '.$e->getMessage()); }
      flash('error','No fue posible autenticar. Inténtalo nuevamente.');
      header('Location: /?r=auth/admin_login');
      exit;
    }

    // Buscar usuario en tabla principal; fallback a admins si corresponde
    $u = null;
    try {
      $stmt=$db->prepare("SELECT id,email,password_hash,role,is_active,fullname FROM users WHERE email=? LIMIT 1");
      $stmt->execute([$email]);
      $u = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Throwable $e) {
      if (defined('APP_ENV') && APP_ENV === 'dev') { error_log('[LOGIN] users fallback: '.$e->getMessage()); }
      try {
        $stmt=$db->prepare("SELECT id,email,password_hash FROM admins WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if ($row) {
          $u = ['id'=>$row['id'], 'email'=>$row['email'], 'password_hash'=>$row['password_hash'], 'role'=>'admin', 'is_active'=>1];
        }
      } catch (Throwable $e2) {
        if (defined('APP_ENV') && APP_ENV === 'dev') { error_log('[LOGIN] admins query failed: '.$e2->getMessage()); }
      }
    }

    $pass_ok = isset($u['password_hash']) && password_verify($pass, $u['password_hash']);
    if (defined('APP_ENV') && APP_ENV === 'dev') {
      error_log('[LOGIN] found='.(int)(bool)$u.' pass_ok='.(int)$pass_ok);
    }

    if (!$u || !$pass_ok || (isset($u['is_active']) && (int)$u['is_active'] === 0)) {
      $this->hitAttempt($key);
      flash('error','Credenciales inválidas.');
      header('Location: /?r=auth/admin_login');
      exit;
    }

    if (isset($u['role']) && !in_array($u['role'], ['admin','editor'], true)) {
      // Mantener genérico para no filtrar permisos
      flash('error','Credenciales inválidas.');
      header('Location: /?r=auth/admin_login');
      exit;
    }

    session_regenerate_id(true);
    $userId = isset($u['id_user']) ? (int)$u['id_user'] : (int)($u['id'] ?? 0);
    $_SESSION['user_id'] = $userId;
    $_SESSION['uid']  = $userId; // compatibilidad con código existente
    $_SESSION['email'] = $u['email'] ?? '';
    $_SESSION['role'] = $u['role'] ?? 'admin';
    $_SESSION['last_activity'] = time();
    $this->clearAttempts($key);
    if ($remember && function_exists('mp_set_remember_token')) {
      $ok = mp_set_remember_token((int)$u['id']);
      if (!$ok) {
        // Degradar silenciosamente: iniciar sesión sin 'recordarme'
        // Opcional: podríamos guardar un flash pero los layouts admin no lo muestran actualmente
        error_log('[LOGIN] remember-me unavailable, continuing without persistent token');
      }
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