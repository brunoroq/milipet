<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
class CatalogController {
  public function index() {
    $q = trim($_GET['q'] ?? '');
    $cid = isset($_GET['cid']) ? (int)$_GET['cid'] : null;
    $species = $_GET['species'] ?? '';
    $include_out_of_stock = isset($_GET['show_all']) && $_GET['show_all'] === '1';
    $products = Product::search($q, $cid, $species ?: null, $include_out_of_stock);
    $categories = Category::all();
    render('catalog/list', ['products'=>$products,'categories'=>$categories,'q'=>$q,'cid'=>$cid,'species'=>$species]);
  }
  public function detail() {
    $id = (int)($_GET['id'] ?? 0);
    $product = Product::find($id);
    if (!$product) {
        http_response_code(404);
        render('static/404');
        return;
    }
    $has_stock = Product::hasStock($id);
    render('catalog/detail', [
        'product' => $product,
        'has_stock' => $has_stock
    ]);
  }
}