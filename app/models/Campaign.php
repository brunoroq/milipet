<?php
require_once __DIR__ . '/../../config/db.php';

class Campaign {
    /**
     * Get latest active campaigns
     */
    public static function latest($limit = 10) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("SELECT * FROM campaigns WHERE is_active=1 AND date >= CURRENT_DATE ORDER BY date ASC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all campaigns for admin panel
     */
    public static function all() {
        $pdo = db_connect();
        return $pdo->query("SELECT * FROM campaigns ORDER BY date DESC")->fetchAll();
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
                    date = :date,
                    location = :location,
                    foundation = :foundation,
                    image_url = :image_url,
                    contact_info = :contact_info,
                    is_active = :is_active
                    WHERE id = :id";
        } else {
            $sql = "INSERT INTO campaigns 
                    (title, description, date, location, foundation, image_url, contact_info, is_active) 
                    VALUES 
                    (:title, :description, :date, :location, :foundation, :image_url, :contact_info, :is_active)";
        }

        $stmt = $pdo->prepare($sql);
        
        $params = [
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':date' => $data['date'],
            ':location' => $data['location'],
            ':foundation' => $data['foundation'],
            ':image_url' => $data['image_url'] ?? '',
            ':contact_info' => $data['contact_info'] ?? '',
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
     * Get upcoming campaigns (within next 30 days)
     */
    public static function upcoming($days = 30) {
        $pdo = db_connect();
        $stmt = $pdo->prepare(
            "SELECT * FROM campaigns 
            WHERE is_active = 1 
            AND date BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL :days DAY)
            ORDER BY date ASC"
        );
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get campaigns by foundation
     */
    public static function byFoundation($foundation) {
        $pdo = db_connect();
        $stmt = $pdo->prepare(
            "SELECT * FROM campaigns 
            WHERE foundation = :foundation 
            AND is_active = 1 
            ORDER BY date DESC"
        );
        $stmt->execute([':foundation' => $foundation]);
        return $stmt->fetchAll();
    }

    /**
     * Get list of active foundations
     */
    public static function getFoundations() {
        $pdo = db_connect();
        return $pdo->query(
            "SELECT DISTINCT foundation 
            FROM campaigns 
            WHERE foundation IS NOT NULL 
            AND foundation != '' 
            ORDER BY foundation"
        )->fetchAll(PDO::FETCH_COLUMN);
    }
}
