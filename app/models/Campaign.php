<?php
require_once __DIR__ . '/../../config/db.php';

class Campaign {
	public static function latest() {
		$pdo = db_connect();
		return $pdo->query("SELECT * FROM campaigns WHERE is_active=1 ORDER BY date DESC LIMIT 10")->fetchAll();
	}
}
