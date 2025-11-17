<?php
// Carga de configuración y datos para el menú
require_once __DIR__ . '/../../../config/config.php';
$storeConfig = (isset($config) && is_array($config)) ? $config : [];
$wa = $storeConfig['store']['social']['whatsapp'] ?? 'https://wa.me/5695458036';
$ig = $storeConfig['store']['social']['instagram'] ?? 'https://www.instagram.com/mili_petshop/';
// Categorías para el dropdown de Catálogo
require_once __DIR__ . '/../../models/Category.php';
$navCategories = [];
try { $navCategories = Category::all(); } catch (Throwable $e) { $navCategories = []; }
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
<body>
<header class="navbar navbar-expand-lg navbar-dark bg-primary paw-pattern">
	<div class="container">
		<!-- Logo -->
		<a class="navbar-brand" href="<?= url(['r' => 'home']) ?>">
			<img src="<?= asset('assets/img/logo-milipet.png') ?>" alt="MiliPet" class="header-logo">
		</a>

		<!-- Menu principal (desktop only - left side) -->
		<ul class="navbar-nav flex-row gap-3 ms-4 d-none d-lg-flex">
			<li class="nav-item dropdown">
				<div class="btn-group">
					<a class="btn btn-outline-light rounded-pill px-3" href="<?= url(['r' => 'catalog']) ?>">Catálogo</a>
					<button class="btn btn-outline-light rounded-pill px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Mostrar categorías del catálogo"><span class="visually-hidden">Ver categorías</span></button>
					<?php if (!empty($navCategories)): ?>
					<ul class="dropdown-menu shadow">
						<?php foreach ($navCategories as $cat): ?>
							<li><a class="dropdown-item" href="<?= url(['r' => 'catalog', 'category' => (int)$cat['id']]) ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
						<?php endforeach; ?>
					</ul>
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
				<li class="nav-item dropdown">
					<div class="btn-group w-100">
						<a class="btn btn-outline-light rounded-pill px-3" href="<?= url(['r' => 'catalog']) ?>">Catálogo</a>
						<button class="btn btn-outline-light rounded-pill px-3 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Mostrar categorías del catálogo"><span class="visually-hidden">Ver categorías</span></button>
						<?php if (!empty($navCategories)): ?>
						<ul class="dropdown-menu shadow">
							<?php foreach ($navCategories as $cat): ?>
								<li><a class="dropdown-item" href="<?= url(['r' => 'catalog', 'category' => (int)$cat['id']]) ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
					</div>
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
<main class="container mt-3">
	
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