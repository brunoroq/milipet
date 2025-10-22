<?php
require_once __DIR__ . '/../../config/db.php';

class Category {
    public static function all() {
        $pdo = db_connect();
        return $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }
}
