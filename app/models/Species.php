<?php
require_once __DIR__ . '/../../config/db.php';

class Species {
    public static function all() {
        $pdo = db_connect();
        return $pdo->query("SELECT id, name FROM species ORDER BY name ASC")->fetchAll();
    }
}
