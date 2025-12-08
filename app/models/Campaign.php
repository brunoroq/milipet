<?php
require_once __DIR__ . '/../../config/db.php';

class Campaign {
    // Instance attributes for a model-like API (compatible with Yii2 style usage)
    public $id;
    public $title;
    public $description;
    public $banner_image;
    public $start_date;
    public $end_date;
    public $is_active = 1;
    public $errors = [];

    /**
     * Declaración de reglas al estilo Yii2 (sólo como especificación).
     * Se provee para mantener el estilo pedido y para que validate() las use.
     * Formato simple: campo => [rules]
     */
    public static function rules() {
        return [
            'title' => ['required' => true, 'max' => 150],
            'description' => ['required' => true, 'max' => 1000],
            'start_date' => ['required' => true, 'date' => true],
            'end_date' => ['required' => true, 'date' => true],
            'is_active' => ['boolean' => true]
        ];
    }

    /**
     * Carga datos (simula $model->load())
     */
    public function load(array $data) {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->title = isset($data['title']) ? trim($data['title']) : '';
        $this->description = isset($data['description']) ? trim($data['description']) : '';
        $this->banner_image = isset($data['banner_image']) ? $data['banner_image'] : null;
        $this->start_date = isset($data['start_date']) && $data['start_date'] !== '' ? $data['start_date'] : null;
        $this->end_date = isset($data['end_date']) && $data['end_date'] !== '' ? $data['end_date'] : null;
        $this->is_active = isset($data['is_active']) && $data['is_active'] ? 1 : 0;
    }

    /**
     * Valida usando las reglas declaradas.
     * Devuelve true si válido, false si hay errores (los errores quedan en $this->errors)
     */
    public function validate(): bool {
        $this->errors = [];
        $rules = self::rules();

        // title
        if (!empty($rules['title']['required']) && $this->title === '') {
            $this->errors['title'][] = 'El nombre de la campaña es obligatorio.';
        }
        if (!empty($this->title) && isset($rules['title']['max']) && mb_strlen($this->title) > $rules['title']['max']) {
            $this->errors['title'][] = sprintf('El nombre no puede superar %d caracteres.', $rules['title']['max']);
        }

        // description
        if (!empty($rules['description']['required']) && $this->description === '') {
            $this->errors['description'][] = 'La descripción es obligatoria.';
        }
        if (!empty($this->description) && isset($rules['description']['max']) && mb_strlen($this->description) > $rules['description']['max']) {
            $this->errors['description'][] = sprintf('La descripción no puede superar %d caracteres.', $rules['description']['max']);
        }

        // fechas: formato y coherencia
        if (!empty($rules['start_date']['required']) && empty($this->start_date)) {
            $this->errors['start_date'][] = 'La fecha de inicio es obligatoria.';
        } elseif (!empty($this->start_date)) {
            $sd = strtotime($this->start_date);
            if ($sd === false) $this->errors['start_date'][] = 'Fecha de inicio inválida.';
        }

        if (!empty($rules['end_date']['required']) && empty($this->end_date)) {
            $this->errors['end_date'][] = 'La fecha de fin es obligatoria.';
        } elseif (!empty($this->end_date)) {
            $ed = strtotime($this->end_date);
            if ($ed === false) $this->errors['end_date'][] = 'Fecha de fin inválida.';
        }

        // coherencia entre fechas
        if (!empty($this->start_date) && !empty($this->end_date)) {
            if (strtotime($this->end_date) < strtotime($this->start_date)) {
                $this->errors['end_date'][] = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
            }
        }

        return empty($this->errors);
    }

    /**
     * Preparaciones antes de guardar (por ejemplo desactivar automáticamente campañas expiradas)
     */
    public function prepareForSave() {
        if (!empty($this->end_date) && strtotime($this->end_date) < strtotime('today')) {
            // marcar inactiva si ya expiró
            $this->is_active = 0;
        }
    }

    /**
     * Guarda la instancia (create/update) usando la implementación estática existente
     */
    public function saveModel() {
        $this->prepareForSave();
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'banner_image' => $this->banner_image,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active
        ];
        return self::save($data);
    }
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
     * Find currently vigentes (active by date and flag)
     */
    public static function findVigentes() {
        $pdo = db_connect();
        return $pdo->query(
            "SELECT * FROM campaigns 
             WHERE is_active = 1
             AND (start_date IS NULL OR start_date <= CURRENT_DATE)
             AND (end_date IS NULL OR end_date >= CURRENT_DATE)
             ORDER BY start_date DESC"
        )->fetchAll();
    }

    /**
     * Find campaigns that have expired (end_date before today)
     */
    public static function findExpiradas() {
        $pdo = db_connect();
        return $pdo->query(
            "SELECT * FROM campaigns 
             WHERE is_active = 1
             AND end_date IS NOT NULL AND end_date < CURRENT_DATE
             ORDER BY end_date DESC"
        )->fetchAll();
    }

    /**
     * Counts
     */
    public static function countAll() {
        $pdo = db_connect();
        $stmt = $pdo->query("SELECT COUNT(*) as c FROM campaigns");
        $r = $stmt->fetch(); return (int)($r['c'] ?? 0);
    }

    public static function countVigentes() {
        $pdo = db_connect();
        $stmt = $pdo->query(
            "SELECT COUNT(*) as c FROM campaigns
             WHERE is_active = 1
             AND (start_date IS NULL OR start_date <= CURRENT_DATE)
             AND (end_date IS NULL OR end_date >= CURRENT_DATE)"
        );
        $r = $stmt->fetch(); return (int)($r['c'] ?? 0);
    }

    public static function countExpiradas() {
        $pdo = db_connect();
        $stmt = $pdo->query(
            "SELECT COUNT(*) as c FROM campaigns
             WHERE is_active = 1
             AND end_date IS NOT NULL AND end_date < CURRENT_DATE"
        );
        $r = $stmt->fetch(); return (int)($r['c'] ?? 0);
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
            // Respetar el valor pasado en is_active (0 o 1). Evitar usar isset() que devuelve true para 0.
            ':is_active' => (int)($data['is_active'] ?? 0)
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
