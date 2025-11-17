<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Campaign.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

class AdminController {
  public function __construct() {
    // Requiere rol admin/editor
    if (function_exists('requireRole')) { requireRole(['admin','editor']); }
  }
  
  // Normaliza nombre de archivo: translitera, minúsculas, reemplaza caract. no permitidos, comprime guiones
  private function sanitize_file_name(string $name): string {
    if (function_exists('iconv')) {
      $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
      if ($converted !== false) { $name = $converted; }
    }
    $name = strtolower($name);
    $name = preg_replace('/[^a-z0-9\._-]/', '-', $name);
    $name = preg_replace('/-+/', '-', $name);
    return trim($name, '-');
  }
  
  public function campaigns() {
    $campaigns = Campaign::all();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    render('admin/campaigns', ['campaigns' => $campaigns, 'flash' => $flash]);
  }

  public function saveCampaign() {
    $this->checkCsrf();
    
    $data = [
        'id' => $_POST['id'] ?? null,
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'start_date' => $_POST['start_date'] ?? null,
        'end_date' => $_POST['end_date'] ?? null,
        'banner_image' => $_POST['banner_image'] ?? '',
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    // Validación básica
    $errors = [];
    if (trim($data['title']) === '') $errors[] = 'El título es obligatorio.';
    
    // Validar que end_date no sea anterior a start_date
    if (!empty($data['start_date']) && !empty($data['end_date'])) {
        if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
            $errors[] = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
        }
    }

    if (!empty($errors)) {
        $_SESSION['flash'] = ['type'=>'error', 'messages'=>$errors];
        $campaigns = Campaign::all();
        render('admin/campaigns', [
            'campaigns' => $campaigns, 
            'flash' => $_SESSION['flash'],
            'old' => $data
        ]);
        unset($_SESSION['flash']);
        return;
    }

  // Manejo de subida de imagen
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $max = 3*1024*1024;
            if ($file['size'] <= $max) {
                if (function_exists('finfo_open')) {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->file($file['tmp_name']);
                } else {
                    $mime = mime_content_type($file['tmp_name']);
                }
        $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
                if (isset($allowed[$mime])) {
                    $ext = $allowed[$mime];
          $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
          $safe = $this->sanitize_file_name($baseName);
          $unique = $safe.'-'.bin2hex(random_bytes(4)).'.'.$ext;
          $dir = (defined('PUBLIC_PATH') ? PUBLIC_PATH : (__DIR__ . '/../../public')).'/assets/img';
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0775, true);
                    }
                    $target = $dir.'/'.$unique;
                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        if (!empty($data['id']) && !empty($_POST['current_banner_image'])) {
                            $prev = $_POST['current_banner_image'];
                            if (strpos($prev, 'assets/img/') === 0) {
                $prevPathBase = defined('PUBLIC_PATH') ? PUBLIC_PATH : (__DIR__.'/../../public');
                $prevPath = rtrim($prevPathBase, '/').'/'.$prev;
                                if (is_file($prevPath)) {
                                    @unlink($prevPath);
                                }
                            }
                        }
                        $data['banner_image'] = 'assets/img/'.$unique;
                    }
                }
            }
        }
    }

    $id = Campaign::save($data);
    $_SESSION['flash'] = ['type'=>'success', 'messages'=>['Campaña guardada con éxito.']];
    header('Location: ?r=admin/campaigns');
    exit;
  }

  public function deleteCampaign() {
    $this->checkCsrf();
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    if ($id) {
        $campaign = Campaign::find($id);
        if ($campaign) {
            // Eliminar imagen si existe
            if (!empty($campaign['banner_image']) && strpos($campaign['banner_image'], 'assets/img/') === 0) {
                $imagePath = __DIR__.'/../../public/'.$campaign['banner_image'];
                if (is_file($imagePath)) {
                    @unlink($imagePath);
                }
            }
            
            $ok = Campaign::delete($id);
            if ($ok) {
                $_SESSION['flash'] = ['type'=>'success', 'messages'=>['Campaña eliminada con éxito.']];
            } else {
                $_SESSION['flash'] = ['type'=>'error', 'messages'=>['No se pudo eliminar la campaña.']];
            }
        }
    }
    
    header('Location: ?r=admin/campaigns');
    exit;
  }

  // Verifica token CSRF para peticiones POST
  private function checkCsrf() {
    if (function_exists('csrf_check')) { csrf_check(); return; }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $t = $_POST['_csrf'] ?? ($_POST['csrf'] ?? '');
      if (empty($t) || !hash_equals($_SESSION['_csrf'] ?? ($_SESSION['csrf'] ?? ''), $t)) {
        http_response_code(400);
        exit('CSRF token inválido');
      }
    }
  }

  public function dashboard() { 
    if (defined('APP_ENV') && APP_ENV === 'dev') {
      error_log('[DASHBOARD] sid='.session_id().' uid='.($_SESSION['user_id']??'null').' role='.($_SESSION['role']??'null'));
    }
    
    // Obtener estadísticas para el dashboard
    $products = Product::allAdmin();
    $totalProducts = count($products);
    
    // Contar productos con stock bajo (usando threshold individual de cada producto)
    $lowStockProducts = array_filter($products, function($p) {
        $threshold = $p['low_stock_threshold'] ?? 5;
        return $p['stock'] <= $threshold;
    });
    $lowStockCount = count($lowStockProducts);
    
    // Flash messages
    $flash = $_SESSION['flash'] ?? null; 
    unset($_SESSION['flash']);
    
    render('admin/dashboard', [
        'totalProducts' => $totalProducts,
        'lowStockCount' => $lowStockCount,
        'campaignsCount' => 0, // TODO: implementar cuando exista modelo de campañas
        'flash' => $flash
    ]); 
  }

  public function products() {
    $products = Product::allAdmin();
    $categories = Category::all();
    $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
    
    // Get low stock products (usando threshold individual)
    $lowStockProducts = array_filter($products, function($p) {
        $threshold = $p['low_stock_threshold'] ?? 5;
        return $p['stock'] <= $threshold;
    });
    
    $lowStockCount = count($lowStockProducts);
    
    render('admin/products', [
        'products' => $products, 
        'categories' => $categories, 
        'flash' => $flash,
        'lowStockCount' => $lowStockCount
    ]);
  }
  
  public function low_stock() {
    $products = Product::allAdmin();
    $categories = Category::all();
    
    // Filter products with stock <= threshold individual
    $lowStockProducts = array_filter($products, function($p) {
        $threshold = $p['low_stock_threshold'] ?? 5;
        return $p['stock'] <= $threshold;
    });
    
    // Sort by stock ascending (most critical first)
    usort($lowStockProducts, function($a, $b) {
        return $a['stock'] <=> $b['stock'];
    });
    
    render('admin/low_stock', [
        'products' => $lowStockProducts,
        'categories' => $categories
    ]);
  }

  public function saveProduct() {
    $this->checkCsrf();
    
    // Recoger datos del formulario
    $data = [
      'id' => $_POST['id'] ?? null,
      'name' => trim($_POST['name'] ?? ''),
      'short_desc' => trim($_POST['short_desc'] ?? ''),
      'long_desc' => trim($_POST['long_desc'] ?? ''),
      'price' => $_POST['price'] ?? '',
      'stock' => $_POST['stock'] ?? '',
      'low_stock_threshold' => $_POST['stock_alert'] ?? 5,
      'species' => $_POST['species'] ?? null,
      'category_id' => $_POST['category_id'] ?? '',
      'image_url' => trim($_POST['image_url'] ?? ''),
      'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
      'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    
    // ========== VALIDACIONES ==========
    
    // 1. Nombre obligatorio
    if ($data['name'] === '') {
      flash('error', 'Debes ingresar un nombre para el producto.');
      $_SESSION['old'] = $data;
      header('Location: ?r=admin/products');
      exit;
    }
    
    // 2. Categoría obligatoria
    if ($data['category_id'] === '' || $data['category_id'] === null) {
      flash('error', 'Debes seleccionar una categoría para el producto.');
      $_SESSION['old'] = $data;
      header('Location: ?r=admin/products');
      exit;
    }
    $data['category_id'] = (int)$data['category_id'];
    
    // 3. NORMALIZACIÓN DE PRECIO (formato chileno flexible)
    // Permite formatos: 40000, 40.000, 40,000
    $rawPrice = trim($data['price']);
    
    // Eliminar todo excepto dígitos (puntos, comas, espacios, etc.)
    $normalizedPrice = preg_replace('/[^\d]/', '', $rawPrice);
    
    // Validar que queden solo dígitos
    if ($normalizedPrice === '' || !ctype_digit($normalizedPrice)) {
      flash('error', 'El precio debe ser un número entero en pesos chilenos (sin símbolos). Ejemplos válidos: 40000, 40.000, 40,000');
      $_SESSION['old'] = $data;
      header('Location: ?r=admin/products');
      exit;
    }
    
    // Convertir a entero
    $data['price'] = (int)$normalizedPrice;
    
    // Validar que el precio sea mayor a 0
    if ($data['price'] <= 0) {
      flash('error', 'El precio debe ser mayor a cero.');
      $_SESSION['old'] = $data;
      header('Location: ?r=admin/products');
      exit;
    }
    
    // 4. Stock: debe ser número entero >= 0
    if (!ctype_digit(trim($data['stock'])) || (int)$data['stock'] < 0) {
      flash('error', 'El stock debe ser un número entero mayor o igual a 0.');
      $_SESSION['old'] = $data;
      header('Location: ?r=admin/products');
      exit;
    }
    $data['stock'] = (int)$data['stock'];
    
    // 4.5 Alerta de stock bajo: debe ser número entero >= 1
    if (!ctype_digit(trim($data['low_stock_threshold'])) || (int)$data['low_stock_threshold'] < 1) {
      flash('error', 'La alerta de stock bajo debe ser un número entero mayor o igual a 1.');
      $_SESSION['old'] = $data;
      header('Location: ?r=admin/products');
      exit;
    }
    $data['low_stock_threshold'] = (int)$data['low_stock_threshold'];
    
    // 5. Validar imagen URL (solo si no se está subiendo archivo)
    if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) {
      if ($data['image_url'] !== '') {
        $errors = [];
        $validatedImage = validate_product_image($data['image_url'], $errors);
        if ($validatedImage === null) {
          flash('error', implode(' ', $errors));
          $_SESSION['old'] = $data;
          header('Location: ?r=admin/products');
          exit;
        }
        $data['image_url'] = $validatedImage;
      }
    }
    
    // ========== SUBIDA DE ARCHIVO ==========
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
      $file = $_FILES['image_file'];
      if ($file['error'] === UPLOAD_ERR_OK) {
        $max = 3*1024*1024;
        if ($file['size'] > $max) {
          flash('error', 'La imagen es demasiado grande. Máximo 3 MB.');
          $_SESSION['old'] = $data;
          header('Location: ?r=admin/products');
          exit;
        }
        
        if (function_exists('finfo_open')) { 
          $finfo = new finfo(FILEINFO_MIME_TYPE); 
          $mime = $finfo->file($file['tmp_name']); 
        } else { 
          $mime = mime_content_type($file['tmp_name']); 
        }
        
        $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
        if (!isset($allowed[$mime])) {
          flash('error', 'Formato de imagen no válido. Usa JPG, PNG o WebP.');
          $_SESSION['old'] = $data;
          header('Location: ?r=admin/products');
          exit;
        }
        
        $ext = $allowed[$mime];
        $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
        $safe = $this->sanitize_file_name($baseName);
        $unique = $safe.'-'.bin2hex(random_bytes(4)).'.'.$ext;
        $dir = (defined('PUBLIC_PATH') ? PUBLIC_PATH : (__DIR__ . '/../../public')).'/assets/img';
        
        if (!is_dir($dir)) { 
          @mkdir($dir, 0775, true); 
        }
        
        $target = $dir.'/'.$unique;
        if (move_uploaded_file($file['tmp_name'], $target)) {
          // Eliminar imagen anterior si existe
          if (!empty($data['id']) && !empty($_POST['current_image_url'])) {
            $prev = $_POST['current_image_url'];
            if (strpos($prev, 'assets/img/') === 0) {
              $prevPathBase = defined('PUBLIC_PATH') ? PUBLIC_PATH : (__DIR__.'/../../public');
              $prevPath = rtrim($prevPathBase, '/').'/'.$prev; 
              if (is_file($prevPath)) { 
                @unlink($prevPath); 
              }
            }
          }
          $data['image_url'] = 'assets/img/'.$unique;
        } else {
          flash('error', 'No se pudo subir la imagen. Intenta nuevamente.');
          $_SESSION['old'] = $data;
          header('Location: ?r=admin/products');
          exit;
        }
      }
    }
    
    // ========== GUARDAR EN BASE DE DATOS ==========
    try {
      $id = Product::save($data);
      flash('success', '<i class="fas fa-check-circle me-2"></i><strong>¡Éxito!</strong> El producto se guardó correctamente.');
      unset($_SESSION['old']);
    } catch (Exception $e) {
      flash('error', '<i class="fas fa-times-circle me-2"></i><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()));
      $_SESSION['old'] = $data;
    }
    
    header('Location: ?r=admin/products');
    exit;
  }
  public function deleteProduct() {
    // Preferimos recibir eliminación por POST con CSRF; si llega por GET simple, aún la permitimos
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->checkCsrf();
      $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    } else {
      $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    }
    if ($id) {
      $ok = Product::delete($id);
      if ($ok) {
        $_SESSION['flash'] = ['type'=>'success','messages'=>['Producto eliminado con éxito.']];
      } else {
        $_SESSION['flash'] = ['type'=>'error','messages'=>['No se pudo eliminar el producto.']];
      }
    }
    header('Location: ?r=admin/products');
    exit;
  }

  public function updateStock() {
    $this->checkCsrf();
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $adjustment = isset($_POST['adjustment']) ? (int)$_POST['adjustment'] : 0;
    
    if (!$id || $adjustment === 0) {
        $_SESSION['flash'] = ['type'=>'error','messages'=>['Datos inválidos para actualizar stock.']];
        header('Location: ?r=admin/products');
        exit;
    }
    
    $product = Product::find($id);
    if (!$product) {
        $_SESSION['flash'] = ['type'=>'error','messages'=>['Producto no encontrado.']];
        header('Location: ?r=admin/products');
        exit;
    }
    
    $newStock = max(0, $product['stock'] + $adjustment);
    $data = [
        'id' => $id,
        'stock' => $newStock,
        'name' => $product['name'],
        'short_desc' => $product['short_desc'] ?? '',
        'long_desc' => $product['long_desc'] ?? '',
        'price' => $product['price'],
        'category_id' => $product['category_id'],
        'image_url' => $product['image_url'],
        'is_featured' => $product['is_featured'] ?? 0,
        'is_active' => $product['is_active'],
    ];
    
    Product::save($data);
    
    $_SESSION['flash'] = ['type'=>'success','messages'=>[
        sprintf('Stock actualizado a %d unidades.', $newStock)
    ]];
    
    header('Location: ?r=admin/products');
    exit;
  }

  // ========== GESTIÓN DE CONTENIDO (mini-CMS) ==========

  /**
   * Lista de secciones o bloques por sección
   */
  public function contentIndex() {
    $section = $_GET['section'] ?? null;

    if ($section) {
      // Vista con solo los bloques de esa sección
      $blocks = ContentBlock::bySection($section);
      render('admin/content/list', [
        'section' => $section,
        'blocks'  => $blocks,
      ]);
      return;
    }

    // Vista con tabla/resumen de secciones
    $grouped = ContentBlock::allGroupedBySection();

    // Mapea claves técnicas a nombres bonitos
    $labels = [
      'home'      => 'Home',
      'about'     => 'Quiénes Somos',
      'adoptions' => 'Adopciones',
      'policies'  => 'Políticas y Contacto',
      'otros'     => 'Otros',
    ];

    render('admin/content/sections', [
      'grouped' => $grouped,
      'labels'  => $labels,
    ]);
  }

  /**
   * Formulario de edición de un bloque de contenido
   */
  public function contentEdit() {
    $key = $_GET['key'] ?? '';
    
    if (empty($key)) {
      flash('error', 'No se especificó qué contenido editar.');
      header('Location: ' . url(['r' => 'admin/content']));
      exit;
    }
    
    $block = ContentBlock::findByKey($key);
    
    if (!$block) {
      flash('error', 'No se encontró el bloque de contenido solicitado.');
      header('Location: ' . url(['r' => 'admin/content']));
      exit;
    }
    
    render('admin/content/edit', ['block' => $block]);
  }

  /**
   * Procesa la actualización de un bloque de contenido
   */
  public function contentUpdate() {
    $this->checkCsrf();
    
    $key = $_POST['content_key'] ?? '';
    
    if (empty($key)) {
      flash('error', 'No se especificó qué contenido actualizar.');
      header('Location: ' . url(['r' => 'admin/content']));
      exit;
    }
    
    $data = [
      'content'   => $_POST['content'] ?? '',
      'image_url' => !empty($_POST['image_url']) ? trim($_POST['image_url']) : null,
      'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    
    // Extraer la sección del key para redireccionar correctamente
    $parts = explode('.', $key);
    $sectionFromKey = $parts[0] ?? null;
    
    if (ContentBlock::updateByKey($key, $data)) {
      flash('success', '<i class="fas fa-check-circle me-2"></i><strong>¡Éxito!</strong> El contenido se actualizó correctamente.');
    } else {
      flash('error', 'No se pudo guardar el contenido. Intenta nuevamente.');
    }
    
    // Redirigir a la sección correspondiente
    if ($sectionFromKey) {
      header('Location: ' . url(['r' => 'admin/content', 'section' => $sectionFromKey]));
    } else {
      header('Location: ' . url(['r' => 'admin/content']));
    }
    exit;
  }
}
