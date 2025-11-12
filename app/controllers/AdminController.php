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
        'date' => $_POST['date'] ?? '',
        'location' => $_POST['location'] ?? '',
        'foundation' => $_POST['foundation'] ?? null,
        'contact_info' => $_POST['contact_info'] ?? '',
        'image_url' => $_POST['image_url'] ?? '',
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    // Validación básica
    $errors = [];
    if (trim($data['title']) === '') $errors[] = 'El título es obligatorio.';
    if (trim($data['date']) === '') $errors[] = 'La fecha es obligatoria.';
    if (trim($data['location']) === '') $errors[] = 'La ubicación es obligatoria.';

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
            if (!empty($campaign['image_url']) && strpos($campaign['image_url'], 'assets/img/') === 0) {
                $imagePath = __DIR__.'/../../public/'.$campaign['image_url'];
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
    render('admin/dashboard'); 
  }

  public function products() {
    $products = Product::allAdmin();
    $categories = Category::all();
    $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
    
    // Get low stock products (5 or less units)
    $lowStockProducts = array_filter($products, function($p) {
        return $p['stock'] <= 5;
    });
    
    if (count($lowStockProducts) > 0) {
        if (!$flash) $flash = ['type' => 'error', 'messages' => []];
        $flash['messages'][] = sprintf(
            'Hay %d producto(s) con stock bajo (5 o menos unidades).', 
            count($lowStockProducts)
        );
    }
    
    render('admin/products', [
        'products' => $products, 
        'categories' => $categories, 
        'flash' => $flash,
        'lowStockProducts' => $lowStockProducts
    ]);
  }

  public function saveProduct() {
    $this->checkCsrf();
    $data = [
      'id' => $_POST['id'] ?? null,
      'name' => $_POST['name'] ?? '',
      'description' => $_POST['description'] ?? '',
      'price' => (float)($_POST['price'] ?? 0),
      'stock' => (int)($_POST['stock'] ?? 0),
      'species' => $_POST['species'] ?? null,
      'category_id' => isset($_POST['category_id']) && $_POST['category_id']!=='' ? (int)$_POST['category_id'] : null,
      'image_url' => $_POST['image_url'] ?? '',
      'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    // Validación básica
    $errors = [];
    if (trim($data['name']) === '') $errors[] = 'El nombre es obligatorio.';
    if ($data['price'] === '' || !is_numeric($data['price']) || $data['price'] < 0) $errors[] = 'Precio inválido.';
    if (!is_int($data['stock']) || $data['stock'] < 0) $errors[] = 'Stock inválido.';
    
    // Validate image URL (only if provided and not uploading file)
    if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) {
      $imgInput = $data['image_url'];
      $validatedImage = validate_product_image($imgInput, $errors);
      if ($validatedImage === null) {
        // Validation failed
        $_SESSION['flash'] = ['type'=>'error','messages'=>$errors];
        $_SESSION['old'] = $data;
        $products = Product::allAdmin();
        $categories = Category::all();
        render('admin/products', ['products'=>$products, 'categories'=>$categories, 'old'=>$data, 'flash'=>$_SESSION['flash']]);
        unset($_SESSION['flash'], $_SESSION['old']);
        return;
      }
      $data['image_url'] = $validatedImage;
    }
    
    if (!empty($errors)) {
      // devolver a la vista con errores y los datos previos
      $_SESSION['flash'] = ['type'=>'error','messages'=>$errors];
      $products = Product::allAdmin();
      $categories = Category::all();
      render('admin/products', ['products'=>$products, 'categories'=>$categories, 'old'=>$data, 'flash'=>$_SESSION['flash']]);
      unset($_SESSION['flash']);
      return;
    }
    // Upload block
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
      $file = $_FILES['image_file'];
      if ($file['error'] === UPLOAD_ERR_OK) {
        $max = 3*1024*1024;
        if ($file['size'] <= $max) {
          if (function_exists('finfo_open')) { $finfo=new finfo(FILEINFO_MIME_TYPE); $mime=$finfo->file($file['tmp_name']); }
          else { $mime = mime_content_type($file['tmp_name']); }
          $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
          if (isset($allowed[$mime])) {
            $ext=$allowed[$mime];
            $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
            $safe = $this->sanitize_file_name($baseName);
            $unique = $safe.'-'.bin2hex(random_bytes(4)).'.'.$ext;
            $dir = (defined('PUBLIC_PATH') ? PUBLIC_PATH : (__DIR__ . '/../../public')).'/assets/img';
            if(!is_dir($dir)){ @mkdir($dir, 0775, true); }
            $target = $dir.'/'.$unique;
            if (move_uploaded_file($file['tmp_name'],$target)) {
              if (!empty($data['id']) && !empty($_POST['current_image_url'])) {
                $prev=$_POST['current_image_url'];
                if (strpos($prev,'assets/img/')===0) {
                  $prevPathBase = defined('PUBLIC_PATH') ? PUBLIC_PATH : (__DIR__.'/../../public');
                  $prevPath = rtrim($prevPathBase,'/').'/'.$prev; if(is_file($prevPath)){ @unlink($prevPath); }
                }
              }
              $data['image_url']='assets/img/'.$unique;
            }
          }
        }
      }
    }
    $id = Product::save($data);
    $_SESSION['flash'] = ['type'=>'success','messages'=>['Producto guardado con éxito.']];
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
        'description' => $product['description'],
        'price' => $product['price'],
        'category_id' => $product['category_id'],
        'image_url' => $product['image_url'],
        'is_active' => $product['is_active'],
    ];
    
    Product::save($data);
    
    $_SESSION['flash'] = ['type'=>'success','messages'=>[
        sprintf('Stock actualizado a %d unidades.', $newStock)
    ]];
    
    header('Location: ?r=admin/products');
    exit;
  }
}