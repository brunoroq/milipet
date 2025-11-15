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
		<a class="navbar-brand" href="<?= url(['r' => 'home']) ?>">
			<img src="<?= asset('assets/img/logo-milipet.png') ?>" alt="MiliPet" class="header-logo">
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<nav id="mainNav" class="collapse navbar-collapse">
			<ul class="navbar-nav ms-auto align-items-lg-center gap-2">
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
				<li class="nav-item"><a class="btn btn-warning rounded-pill px-3 text-dark fw-semibold d-inline-flex align-items-center gap-2" href="<?php echo htmlspecialchars($wa); ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i><span>WhatsApp</span></a></li>
				<li class="nav-item"><a class="btn btn-warning rounded-pill px-3 text-dark fw-semibold d-inline-flex align-items-center gap-2" href="<?php echo htmlspecialchars($ig); ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i><span>Instagram</span></a></li>
				<li class="nav-item d-flex align-items-center gap-2 icon-bar">
					<button class="btn btn-outline-light rounded-pill px-3 header-icon" id="search-toggle" type="button" title="Buscar productos"><i class="fa-solid fa-magnifying-glass"></i></button>
					<a class="btn btn-outline-light rounded-pill px-3 header-icon position-relative" id="fav-link" href="<?= url(['r'=>'favorites']) ?>" title="Favoritos">
						<i class="fa-regular fa-heart"></i>
						<span class="count-badge" id="fav-count" style="display:none;"></span>
					</a>
					<a class="btn btn-outline-light rounded-pill px-3 header-icon position-relative" id="cart-link" href="<?= url(['r'=>'cart']) ?>" title="Carrito">
						<i class="fa-solid fa-cart-shopping"></i>
						<span class="count-badge" id="cart-count" style="display:none;"></span>
					</a>
					<a class="btn btn-outline-light rounded-pill px-3 header-icon" href="<?= url(['r'=>'auth/admin_login']) ?>" title="Administrador"><i class="fa-regular fa-user"></i></a>
				</li>
			</ul>
		</nav>
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
<main class="container mt-3">