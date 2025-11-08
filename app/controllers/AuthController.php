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
      'flash' => flash('auth')
    ]);
  }

  // Procesa login administración
  public function adminLogin() {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $pass  = (string)($_POST['password'] ?? '');
    $remember = !empty($_POST['remember']);
    $key   = $this->throttleKey($email);
    if ($this->tooManyAttempts($key)) { flash('auth','Demasiados intentos. Intenta en 15 minutos.'); return header('Location: '.url(['r'=>'auth/admin_login'])); }

    $db = Database::getInstance()->getConnection();
    try {
      $stmt=$db->prepare("SELECT id,email,password_hash,role,is_active FROM users WHERE email=? LIMIT 1");
      $stmt->execute([$email]);
      $u = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
      // Compatibilidad: fallback a tabla 'admins' antigua (sin roles)
      $stmt=$db->prepare("SELECT id,email,password_hash FROM admins WHERE email=? LIMIT 1");
      $stmt->execute([$email]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row && password_verify($pass, $row['password_hash'])) {
        $u = ['id'=>$row['id'], 'email'=>$row['email'], 'password_hash'=>$row['password_hash'], 'role'=>'admin', 'is_active'=>1];
      } else {
        $u = false;
      }
    }

    if (!$u || !$u['is_active'] || !in_array($u['role'], ['admin','editor'], true) || !password_verify($pass, $u['password_hash'])) {
      $this->hitAttempt($key);
      flash('auth','Credenciales inválidas o sin permisos.');
      return header('Location: '.url(['r'=>'auth/admin_login']));
    }

    session_regenerate_id(true);
    $_SESSION['uid']  = (int)$u['id'];
    $_SESSION['role'] = $u['role'];
    $this->clearAttempts($key);
    if ($remember && function_exists('mp_set_remember_token')) { mp_set_remember_token((int)$u['id']); }
    header('Location: '.url(['r'=>'admin/dashboard']));
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