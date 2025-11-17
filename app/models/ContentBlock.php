<?php
/**
 * Modelo para bloques de contenido editable (mini-CMS)
 * Permite a los administradores editar textos e imágenes del sitio sin tocar código
 */
class ContentBlock
{
  /**
   * Obtener un bloque de contenido por su clave
   * @param string $key Clave única del bloque (ej: 'home.hero_title')
   * @return array|null Array asociativo con los datos del bloque o null si no existe
   */
  public static function findByKey(string $key): ?array
  {
    try {
      $db = db_connect();
      $stmt = $db->prepare('
        SELECT id, content_key, title, content, image_url, is_active, updated_at
        FROM content_blocks
        WHERE content_key = :key
        LIMIT 1
      ');
      $stmt->execute(['key' => $key]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      
      return $result ?: null;
    } catch (PDOException $e) {
      error_log("ContentBlock::findByKey error: " . $e->getMessage());
      return null;
    }
  }

  /**
   * Obtener todos los bloques de contenido
   * @param bool $activeOnly Si es true, solo devuelve bloques activos
   * @return array Lista de bloques ordenados por content_key
   */
  public static function all(bool $activeOnly = false): array
  {
    try {
      $db = db_connect();
      
      $sql = 'SELECT id, content_key, title, content, image_url, is_active, updated_at
              FROM content_blocks';
      
      if ($activeOnly) {
        $sql .= ' WHERE is_active = 1';
      }
      
      $sql .= ' ORDER BY content_key ASC';
      
      $stmt = $db->query($sql);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("ContentBlock::all error: " . $e->getMessage());
      return [];
    }
  }

  /**
   * Actualizar un bloque de contenido por su clave
   * @param string $key Clave única del bloque
   * @param array $data Array asociativo con los campos a actualizar ['content' => ..., 'image_url' => ..., 'is_active' => ...]
   * @return bool True si se actualizó correctamente, false en caso de error
   */
  public static function updateByKey(string $key, array $data): bool
  {
    try {
      $db = db_connect();
      
      // Construir la query dinámicamente según los campos proporcionados
      $allowedFields = ['content', 'image_url', 'is_active', 'title'];
      $setParts = [];
      $params = ['key' => $key];
      
      foreach ($allowedFields as $field) {
        if (array_key_exists($field, $data)) {
          $setParts[] = "$field = :$field";
          $params[$field] = $data[$field];
        }
      }
      
      if (empty($setParts)) {
        return false; // No hay nada que actualizar
      }
      
      $sql = 'UPDATE content_blocks SET ' . implode(', ', $setParts) . ' WHERE content_key = :key';
      
      $stmt = $db->prepare($sql);
      return $stmt->execute($params);
      
    } catch (PDOException $e) {
      error_log("ContentBlock::updateByKey error: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Crear un nuevo bloque de contenido
   * @param array $data Array con los datos del bloque ['content_key' => ..., 'title' => ..., 'content' => ..., 'image_url' => ...]
   * @return int|false ID del registro creado o false en caso de error
   */
  public static function create(array $data)
  {
    try {
      $db = db_connect();
      
      $stmt = $db->prepare('
        INSERT INTO content_blocks (content_key, title, content, image_url, is_active)
        VALUES (:content_key, :title, :content, :image_url, :is_active)
      ');
      
      $stmt->execute([
        'content_key' => $data['content_key'] ?? '',
        'title' => $data['title'] ?? '',
        'content' => $data['content'] ?? null,
        'image_url' => $data['image_url'] ?? null,
        'is_active' => $data['is_active'] ?? 1,
      ]);
      
      return $db->lastInsertId();
      
    } catch (PDOException $e) {
      error_log("ContentBlock::create error: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Eliminar un bloque de contenido por su clave
   * @param string $key Clave única del bloque
   * @return bool True si se eliminó correctamente, false en caso de error
   */
  public static function deleteByKey(string $key): bool
  {
    try {
      $db = db_connect();
      $stmt = $db->prepare('DELETE FROM content_blocks WHERE content_key = :key');
      return $stmt->execute(['key' => $key]);
    } catch (PDOException $e) {
      error_log("ContentBlock::deleteByKey error: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Obtener todos los bloques agrupados por sección
   * @return array Array asociativo ['section' => [bloques...]]
   */
  public static function allGroupedBySection(): array
  {
    try {
      $db = db_connect();
      $stmt = $db->query('SELECT * FROM content_blocks ORDER BY content_key');
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $grouped = [];
      foreach ($rows as $row) {
        $parts = explode('.', $row['content_key']);
        $section = $parts[0] ?? 'otros';
        if (!isset($grouped[$section])) {
          $grouped[$section] = [];
        }
        $grouped[$section][] = $row;
      }
      return $grouped;
    } catch (PDOException $e) {
      error_log("ContentBlock::allGroupedBySection error: " . $e->getMessage());
      return [];
    }
  }

  /**
   * Obtener bloques de una sección específica
   * @param string $section Nombre de la sección (ej: 'home', 'about')
   * @return array Lista de bloques de esa sección
   */
  public static function bySection(string $section): array
  {
    try {
      $db = db_connect();
      $stmt = $db->prepare('SELECT * FROM content_blocks WHERE content_key LIKE :prefix ORDER BY content_key');
      $stmt->execute([':prefix' => $section . '.%']);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("ContentBlock::bySection error: " . $e->getMessage());
      return [];
    }
  }
}
