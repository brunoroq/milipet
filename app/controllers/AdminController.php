<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

class AdminController {
  public function __construct() {
    require_auth(); // Verificar autenticación en todas las acciones del AdminController
  }

  // Verifica token CSRF para peticiones POST
  private function checkCsrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(400);
        exit('CSRF token inválido');
      }
    }
  }

  public function dashboard() { 
    render('admin/dashboard'); 
  }

  public function products() {
    $products = Product::allAdmin();
    $categories = Category::all();
    $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
    render('admin/products', ['products'=>$products, 'categories'=>$categories, 'flash'=>$flash]);
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
          $allowed=['image/jpeg'=>'jpg','image/png'=>'png'];
          if (isset($allowed[$mime])) {
            $ext=$allowed[$mime];
            $safe=preg_replace('/[^a-zA-Z0-9_-]+/','-', strtolower(pathinfo($file['name'], PATHINFO_FILENAME)));
            $unique=$safe.'-'.bin2hex(random_bytes(4)).'.'.$ext;
            $dir=__DIR__ . '/../../public/assets/img'; if(!is_dir($dir)){ @mkdir($dir, 0775, true); }
            $target=$dir.'/'.$unique;
            if (move_uploaded_file($file['tmp_name'],$target)) {
              if (!empty($data['id']) && !empty($_POST['current_image_url'])) {
                $prev=$_POST['current_image_url'];
                if (strpos($prev,'assets/img/')===0) {
                  $prevPath=__DIR__.'/../../public/'.$prev; if(is_file($prevPath)){ @unlink($prevPath); }
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
}