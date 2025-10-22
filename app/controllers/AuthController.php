<?php
require_once __DIR__ . '/../models/AdminUser.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

class AuthController {
  public function login() {
    // Si ya está autenticado, redirigir al dashboard
    if (is_authenticated()) {
      header('Location: ?r=admin/dashboard');
      exit;
    }

    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = $_POST['email'] ?? '';
      $password = $_POST['password'] ?? '';
      
      if ($user = AdminUser::authenticate($email, $password)) {
        login($user);
        header('Location: ?r=admin/dashboard');
        exit;
      } else {
        $error = "Credenciales inválidas";
        $_SESSION['flash_error'] = $error;
      }
    }
    
    $flash = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_error']);
    render('auth/login', ['error' => $error, 'flash' => $flash]);
  }

  public function logout() {
    logout();
    header('Location: ?r=auth/login');
    exit;
  }
}