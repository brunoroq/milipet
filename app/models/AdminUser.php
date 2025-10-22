<?php
require_once __DIR__ . '/../../config/db.php';

class AdminUser {
    public static function authenticate($email, $password) {
        $pdo = db_connect();
        $st = $pdo->prepare("SELECT * FROM admins WHERE email=:email LIMIT 1");
        $st->execute([':email' => $email]);
        $u = $st->fetch();
        if ($u && password_verify($password, $u['password_hash'])) return $u;
        return null;
    }
}
