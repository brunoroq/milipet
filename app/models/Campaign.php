<?php
require_once __DIR__ . '/../../config/db.php';

class Campaign {
    /**
     * Get latest active campaigns
     */
    public static function latest($limit = 10) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("SELECT * FROM campaigns WHERE is_active=1 AND (start_date IS NULL OR start_date <= CURRENT_DATE) AND (end_date IS NULL OR end_date >= CURRENT_DATE) ORDER BY start_date ASC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all campaigns for admin panel
     */
    public static function all() {
        $pdo = db_connect();
        return $pdo->query("SELECT * FROM campaigns ORDER BY created_at DESC")->fetchAll();
    }

    /**
     * Find a specific campaign
     */
    public static function find($id) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("SELECT * FROM campaigns WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Save (create or update) a campaign
     */
    public static function save($data) {
        $pdo = db_connect();
        
        if (!empty($data['id'])) {
            $sql = "UPDATE campaigns SET 
                    title = :title,
                    description = :description,
                    banner_image = :banner_image,
                    start_date = :start_date,
                    end_date = :end_date,
                    is_active = :is_active
                    WHERE id = :id";
        } else {
            $sql = "INSERT INTO campaigns 
                    (title, description, banner_image, start_date, end_date, is_active) 
                    VALUES 
                    (:title, :description, :banner_image, :start_date, :end_date, :is_active)";
        }

        $stmt = $pdo->prepare($sql);
        
        $params = [
            ':title' => $data['title'],
            ':description' => $data['description'] ?? '',
            ':banner_image' => $data['banner_image'] ?? null,
            ':start_date' => $data['start_date'] ?? null,
            ':end_date' => $data['end_date'] ?? null,
            ':is_active' => isset($data['is_active']) ? 1 : 0
        ];

        if (!empty($data['id'])) {
            $params[':id'] = $data['id'];
        }

        $stmt->execute($params);
        return $pdo->lastInsertId() ?: ($data['id'] ?? null);
    }

    /**
     * Delete a campaign
     */
    public static function delete($id) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("DELETE FROM campaigns WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get upcoming campaigns (starting within next X days)
     */
    public static function upcoming($days = 30) {
        $pdo = db_connect();
        $stmt = $pdo->prepare(
            "SELECT * FROM campaigns 
            WHERE is_active = 1 
            AND start_date BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL :days DAY)
            ORDER BY start_date ASC"
        );
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get currently active campaigns (between start_date and end_date)
     */
    public static function current() {
        $pdo = db_connect();
        return $pdo->query(
            "SELECT * FROM campaigns 
            WHERE is_active = 1 
            AND (start_date IS NULL OR start_date <= CURRENT_DATE)
            AND (end_date IS NULL OR end_date >= CURRENT_DATE)
            ORDER BY start_date DESC"
        )->fetchAll();
    }
}
