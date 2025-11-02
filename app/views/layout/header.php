<?php
// Carga de configuración y datos para el menú
require_once __DIR__ . '/../../../config/config.php';
$storeConfig = (isset($config) && is_array($config)) ? $config : [];
$wa = $storeConfig['store']['social']['whatsapp'] ?? 'https://wa.me/56900000000';
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
	<!-- Estilos del sitio -->
	<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<header class="navbar navbar-expand-lg navbar-dark bg-primary paw-pattern">
	<div class="container">
		<a class="navbar-brand fw-bold" href="<?= url(['r' => 'home']) ?>">MiliPet</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<nav id="mainNav" class="collapse navbar-collapse">
			<ul class="navbar-nav ms-auto align-items-lg-center gap-2">
				<li class="nav-item dropdown">
					<a class="btn btn-outline-light rounded-pill px-3 dropdown-toggle" href="<?= url(['r' => 'catalog']) ?>" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Catálogo</a>
					<?php if (!empty($navCategories)): ?>
					<ul class="dropdown-menu shadow">
						<?php foreach ($navCategories as $cat): ?>
							<li><a class="dropdown-item" href="<?= url(['r' => 'catalog', 'category' => (int)$cat['id']]) ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				</li>
				<li class="nav-item"><a class="btn btn-outline-light rounded-pill px-3" href="<?= url(['r' => 'adoptions']) ?>">Adopciones</a></li>
				<li class="nav-item"><a class="btn btn-outline-light rounded-pill px-3" href="<?= url(['r' => 'about']) ?>">Quiénes somos</a></li>
				<li class="nav-item"><a class="btn btn-outline-light rounded-pill px-3" href="<?= url(['r' => 'policies']) ?>">Políticas y contacto</a></li>
				<li class="nav-item"><a class="btn btn-warning rounded-pill px-3 text-dark fw-semibold d-inline-flex align-items-center gap-2" href="<?php echo htmlspecialchars($wa); ?>" target="_blank" rel="noopener"><img src="<?= BASE_URL ?>assets/img/whatsapp.png" alt="WhatsApp" class="icon-img icon-img-dark"> <span>WhatsApp</span></a></li>
				<li class="nav-item"><a class="btn btn-warning rounded-pill px-3 text-dark fw-semibold d-inline-flex align-items-center gap-2" href="<?php echo htmlspecialchars($ig); ?>" target="_blank" rel="noopener"><img src="<?= BASE_URL ?>assets/img/instagram.png" alt="Instagram" class="icon-img icon-img-dark"> <span>Instagram</span></a></li>
			</ul>
		</nav>
	</div>
</header>
<main class="container mt-3">