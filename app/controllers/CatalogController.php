<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Species.php';
class CatalogController {
  public function index() {
    // Sanitizar y normalizar filtros
    $query    = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
    // compat con enlaces antiguos que usan "cid"
    $category = $_GET['category'] ?? ($_GET['cid'] ?? null);
    $species  = $_GET['species'] ?? null;
    $in_stock = isset($_GET['stock']);
    // Resolver slugs: si no es dígito, intentar mapear a ID usando slugify de modelos
    require_once __DIR__ . '/../models/Category.php';
    require_once __DIR__ . '/../models/Species.php';

    $resolvedCategoryId = null;
    $resolvedSpeciesId = null;

    if (isset($category) && $category !== '') {
      if (ctype_digit((string)$category) && (int)$category > 0) {
        $resolvedCategoryId = (int)$category;
      } else {
        // Buscar por slug
        try {
          $allCats = Category::allForMenu();
          foreach ($allCats as $c) {
            if ($c['slug'] === strtolower($category)) { $resolvedCategoryId = (int)$c['id']; break; }
          }
        } catch (Throwable $e) { /* ignorar */ }
      }
    }

    if (isset($species) && $species !== '') {
      if (ctype_digit((string)$species) && (int)$species > 0) {
        $resolvedSpeciesId = (int)$species;
      } else {
        try {
          $allSpecies = Species::allForMenu();
          foreach ($allSpecies as $sp) {
            if ($sp['slug'] === strtolower($species)) { $resolvedSpeciesId = (int)$sp['id']; break; }
          }
        } catch (Throwable $e) { /* ignorar */ }
      }
    }

    $category = $resolvedCategoryId;
    $species  = $resolvedSpeciesId;

    // Búsqueda con filtros sanitizados
    $products   = Product::search($query, $category, $species, $in_stock);
  $categories = Category::all();
  $speciesList = [];
  try { $speciesList = Species::all(); } catch (Throwable $e) { $speciesList = []; }

  // Si no hay resultados y hubo búsqueda (query !== ''), cargar sugeridos
  $suggested = (empty($products) && $query !== '') ? Product::getFeatured(4) : [];

    render('catalog/list', [
      'products'   => $products,
      'categories' => $categories,
      'speciesList'=> $speciesList,
      'suggested'  => $suggested,
      'query'      => $query,
      'category'   => $category,
      'species'    => $species,
      'in_stock'   => $in_stock,
    ]);
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