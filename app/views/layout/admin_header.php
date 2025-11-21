<?php
// Admin header styled like public header but with admin nav
require_once __DIR__ . '/../../../config/config.php';
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - MiliPet</title>
  <meta name="robots" content="noindex,nofollow">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <?php if (defined('FONTAWESOME_KIT') && FONTAWESOME_KIT): ?>
  <script src="https://kit.fontawesome.com/<?php echo htmlspecialchars(FONTAWESOME_KIT, ENT_QUOTES, 'UTF-8'); ?>.js" crossorigin="anonymous"></script>
  <?php endif; ?>
  <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="<?= url(['r' => 'admin/dashboard']) ?>">
      <img src="<?= asset('assets/img/logo-milipet.png') ?>" alt="MiliPet - Productos para Mascotas" style="max-height: 60px;">
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?= url(['r' => 'admin/dashboard']) ?>">
            <i class="fas fa-home me-1"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= url(['r' => 'admin/products']) ?>">
            <i class="fas fa-box me-1"></i> Productos
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="taxonomyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-cog me-1"></i> Taxonomía
          </a>
          <ul class="dropdown-menu" aria-labelledby="taxonomyDropdown">
            <li><a class="dropdown-item" href="<?= url(['r' => 'admin/species']) ?>"><i class="fas fa-paw me-2"></i>Especies</a></li>
            <li><a class="dropdown-item" href="<?= url(['r' => 'admin/categories']) ?>"><i class="fas fa-tags me-2"></i>Categorías</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="<?= url(['r' => 'auth/logout']) ?>">
            <i class="fas fa-sign-out-alt me-1"></i> Salir
          </a>
        </li>
      </ul>
    </div>
  </div>
</header>
<main class="container">
