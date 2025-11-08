<?php
require_once __DIR__ . '/config.php';

function db_connect(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $opts = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
    }
    return $pdo;
}

// Wrapper singleton para compatibilidad con código que usa Database::getInstance()->getConnection()
if (!class_exists('Database')) {
    class Database {
        private static ?Database $instance = null;
        private PDO $connection;
        private function __construct() { $this->connection = db_connect(); }
        public static function getInstance(): Database {
            if (self::$instance === null) { self::$instance = new Database(); }
            return self::$instance; }
        public function getConnection(): PDO { return $this->connection; }
    }
}
