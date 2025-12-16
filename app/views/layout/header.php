<?php
// =============================
// Header bootstrap: config + datos para menú Catálogo
// Si $headerSpecies / $headerCategories ya vienen desde render() los usamos.
// Fallback: intentar cargar directamente (evita romper si alguien incluye header.php aislado)
// =============================
require_once __DIR__ . '/../../../config/config.php';
$storeConfig = (isset($config) && is_array($config)) ? $config : [];
$wa = $storeConfig['store']['social']['whatsapp'] ?? 'https://wa.me/5695458036';
$ig = $storeConfig['store']['social']['instagram'] ?? 'https://www.instagram.com/mili_petshop/';

// Normalizar variables provenientes del helper centralizado
$speciesList = isset($headerSpecies) ? $headerSpecies : [];
$categoryList = isset($headerCategories) ? $headerCategories : [];

if (empty($speciesList) || empty($categoryList)) {
	// Fallback mínimo (solo si no llegaron por render)
	require_once __DIR__ . '/../../models/Species.php';
	require_once __DIR__ . '/../../models/Category.php';
	try {
		if (empty($speciesList)) { $speciesList = Species::allForMenu(); }
		if (empty($categoryList)) { $categoryList = Category::allForMenu(); }
	} catch (Throwable $e) {
		$speciesList = [];
		$categoryList = [];
	}
}

// Obtener categorías agrupadas por especie (para mega-menú estilo SuperZoo)
require_once __DIR__ . '/../../helpers/header_menu.php';
$categoriesBySpecies = mp_get_categories_by_species();
// Route-based body class (sanitize GET param 'r')
// FILTER_SANITIZE_STRING is deprecated; read raw and sanitize manually.
$rawRoute = filter_input(INPUT_GET, 'r', FILTER_UNSAFE_RAW);
$rawRoute = is_scalar($rawRoute) ? trim((string)$rawRoute) : '';
if ($rawRoute === '') {
	$rawRoute = 'home';
}
// keep only safe characters for class names
$route = preg_replace('/[^a-z0-9_-]/i', '-', $rawRoute);
$route = strtolower(trim($route, '-'));
$route_class = 'route-' . ($route ?: 'home');
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>MiliPet</title>
	<?php if (!empty($is_admin_layout)): ?>
	<meta name="robots" content="noindex,nofollow">
	<?php endif; ?>
	<!-- Google Fonts: Poppins -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome Icons -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
	<!-- Estilos del sitio -->
	<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body class="<?= htmlspecialchars($route_class, ENT_QUOTES, 'UTF-8') ?>">
<header class="navbar navbar-expand-lg navbar-dark bg-primary paw-pattern">
	<div class="container">
		<!-- Logo -->
		<a class="navbar-brand" href="<?= url(['r' => 'home']) ?>">
			<img src="<?= asset('assets/img/logo-milipet.png') ?>" alt="MiliPet" class="header-logo">
		</a>

		<!-- Menu principal (desktop only - left side) -->
		<ul class="navbar-nav flex-row gap-3 ms-4 d-none d-lg-flex">
			<!-- Catálogo: mega-menú estilo moderno (columnas especies + categorías) -->
			<li class="nav-item dropdown catalog-nav-item" id="catalogNav">
				<a href="<?= url(['r' => 'catalog']) ?>"
				   class="nav-link text-white d-flex align-items-center catalog-toggle"
				   id="catalogMenuToggle"
				   role="button"
				   aria-haspopup="true"
				   aria-expanded="false"
				   aria-controls="catalogMegaMenu">
					Catálogo <i class="fa-solid fa-chevron-down ms-1 small" aria-hidden="true"></i>
				</a>
				<div class="dropdown-menu catalog-megamenu shadow-lg border-0 p-0" id="catalogMegaMenu" role="menu" aria-label="Menú de Catálogo">
					<?php if (empty($categoriesBySpecies)): ?>
						<div class="text-center py-4 px-4 text-muted">
							<i class="fa-solid fa-box-open fa-2x mb-2 opacity-25"></i>
							<p class="mb-0 small">No hay categorías disponibles</p>
						</div>
					<?php else: ?>
						<?php
						// Preparar estructuras para formato dos columnas
						$speciesForMenu = [];
						$catalogMapping = [];
						foreach ($categoriesBySpecies as $slug => $data) {
							$speciesForMenu[] = [
								'slug' => $slug,
								'name' => $data['name']
							];
							$catalogMapping[$slug] = [];
							foreach ($data['categories'] as $cat) {
								$catalogMapping[$slug][] = [
									'name' => $cat['name'],
									'slug' => $cat['slug']
								];
							}
						}
						$firstSpecies = $speciesForMenu[0]['slug'] ?? null;
						$firstSpeciesName = $speciesForMenu[0]['name'] ?? '';
						?>
						<div class="catalog-mega-inner">
							<!-- Columna izquierda: Especies (pills verticales) -->
							<div class="catalog-species-column">
								<div class="catalog-species-header">
									<h6 class="text-uppercase fw-semibold mb-0 small text-muted">Especies</h6>
								</div>
								<div class="catalog-species-list">
									<?php foreach ($speciesForMenu as $sp): ?>
									<button type="button"
										class="catalog-species-pill <?= $sp['slug'] === $firstSpecies ? 'active' : '' ?>"
										data-species="<?= htmlspecialchars($sp['slug']) ?>"
										data-species-name="<?= htmlspecialchars($sp['name']) ?>"
										aria-controls="catalogCategories"
										aria-selected="<?= $sp['slug'] === $firstSpecies ? 'true' : 'false' ?>">
										<i class="fa-solid fa-paw me-2" aria-hidden="true"></i><?= htmlspecialchars($sp['name']) ?>
									</button>
									<?php endforeach; ?>
								</div>
							</div>
							
							<!-- Columna derecha: Categorías (chips) -->
							<div class="catalog-category-column">
								<div class="catalog-category-header">
									<h6 class="small text-muted mb-0">Categorías para: <span class="text-dark fw-semibold" id="catalogCurrentSpecies"><?= htmlspecialchars($firstSpeciesName) ?></span></h6>
								</div>
								<div class="catalog-category-chips" id="catalogCategories" aria-label="Categorías">
									<?php if ($firstSpecies && isset($catalogMapping[$firstSpecies])): ?>
										<?php foreach ($catalogMapping[$firstSpecies] as $cat): ?>
										<a class="catalog-category-chip" 
										   href="<?= url(['r'=>'catalog','species'=>$firstSpecies,'category'=>$cat['slug']]) ?>">
											<?= htmlspecialchars($cat['name']) ?>
										</a>
										<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<script id="catalogData" type="application/json"><?= json_encode($catalogMapping) ?></script>
					<?php endif; ?>
				</div>
			</li>
			<li class="nav-item"><a class="btn btn-outline-light rounded-pill px-3" href="<?= url(['r' => 'adoptions']) ?>">Adopciones</a></li>
			<li class="nav-item"><a class="btn btn-outline-light rounded-pill px-3" href="<?= url(['r' => 'about']) ?>">Quiénes somos</a></li>
			<li class="nav-item"><a class="btn btn-outline-light rounded-pill px-3" href="<?= url(['r' => 'policies']) ?>">Políticas y contacto</a></li>
		</ul>

		<!-- Toggler for mobile -->
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<!-- Right side icons (desktop) + Mobile menu -->
		<div class="collapse navbar-collapse" id="mainNav">
			<!-- Main menu links for mobile only -->
			<ul class="navbar-nav d-lg-none mb-3">
				<li class="nav-item">
					<a class="btn btn-outline-light rounded-pill px-3 w-100" href="<?= url(['r' => 'catalog']) ?>">Catálogo</a>
				</li>
				<li class="nav-item"><a class="btn btn-outline-light rounded-pill px-3 w-100 mt-2" href="<?= url(['r' => 'adoptions']) ?>">Adopciones</a></li>
				<li class="nav-item"><a class="btn btn-outline-light rounded-pill px-3 w-100 mt-2" href="<?= url(['r' => 'about']) ?>">Quiénes somos</a></li>
				<li class="nav-item"><a class="btn btn-outline-light rounded-pill px-3 w-100 mt-2" href="<?= url(['r' => 'policies']) ?>">Políticas y contacto</a></li>
			</ul>

			<!-- Icon bar (visible on all screens, right aligned on desktop) -->
			<ul class="navbar-nav ms-lg-auto align-items-lg-center gap-2">
				<li class="nav-item d-flex align-items-center gap-2 icon-bar">
					<button class="btn btn-outline-light rounded-pill px-3 header-icon" id="search-toggle" type="button" title="Buscar productos"><i class="fa-solid fa-magnifying-glass"></i></button>
					<a class="btn btn-outline-light rounded-pill px-3 header-icon position-relative" id="fav-link" href="<?= url(['r'=>'favorites']) ?>" title="Favoritos">
						<i class="fa-regular fa-heart" id="header-fav-icon"></i>
						<span class="count-badge" id="fav-count" style="display:none;"></span>
					</a>
					<a class="btn btn-outline-light rounded-pill px-3 header-icon position-relative" id="cart-link" href="<?= url(['r'=>'cart']) ?>" title="Carrito">
						<i class="fa-solid fa-cart-shopping"></i>
						<span class="count-badge" id="cart-count" style="display:none;"></span>
					</a>
					<a class="btn btn-outline-light rounded-pill px-3 header-icon" href="<?= url(['r'=>'auth/login']) ?>" title="Iniciar sesión"><i class="fa-regular fa-user"></i></a>
				</li>
			</ul>
		</div>
	</div>
</header>
<!-- Search overlay header -->
<div id="search-overlay" class="search-overlay" aria-hidden="true">
	<div class="search-overlay-inner container">
		<form class="search-form" role="search" action="<?= url(['r'=>'catalog']) ?>" method="get" onsubmit="return headerSearchSubmit(event)">
			<input type="hidden" name="r" value="catalog">
			<div class="input-wrapper">
				<i class="fa-solid fa-magnifying-glass"></i>
				<input class="search-input" name="q" id="header-search-input" type="search" placeholder="Buscar productos..." aria-label="Buscar">
				<button class="btn-close btn-close-white" type="button" aria-label="Cerrar" onclick="closeSearchOverlay()"></button>
			</div>
		</form>
	</div>
</div>
<!-- Backdrop to dim the page and close on click -->
<div id="search-dim" class="search-dim" onclick="closeSearchOverlay()" aria-hidden="true"></div>

<!-- Main content -->
<main class="page-shell <?= htmlspecialchars($route_class, ENT_QUOTES, 'UTF-8') ?>">
	<?php if (empty($is_admin_layout)): ?>
		<div class="page-surface container">
	<?php else: ?>
		<div class="container mt-3">
	<?php endif; ?>

	
	<!-- Flash Messages -->
	<?php if ($msg = flash('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
			<i class="fas fa-exclamation-circle me-2"></i>
			<div><?= htmlspecialchars($msg) ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
		</div>
	<?php endif; ?>
	
	<?php if ($msg = flash('success')): ?>
		<div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
			<i class="fas fa-check-circle me-2"></i>
			<div><?= $msg ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
		</div>
	<?php endif; ?>
	
	<?php if ($msg = flash('warning')): ?>
		<div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
			<i class="fas fa-exclamation-triangle me-2"></i>
			<div><?= $msg ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
		</div>
	<?php endif; ?>
	
	<?php if ($msg = flash('info')): ?>
		<div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
			<i class="fas fa-info-circle me-2"></i>
			<div><?= htmlspecialchars($msg) ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
		</div>
	<?php endif; ?>