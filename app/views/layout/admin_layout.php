<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel de Administración - MiliPet</title>
  <meta name="robots" content="noindex,nofollow">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
  <style>
    /* Admin-specific styles */
    body.admin-layout {
      background: #f5f7fa;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    .admin-navbar {
      background: linear-gradient(135deg, #2E6B30 0%, #1f5f26 100%) !important;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 0.75rem 0;
    }
    
    .admin-navbar .navbar-brand {
      font-size: 1.25rem;
      font-weight: 600;
      color: white !important;
    }
    
    .admin-nav-btn {
      color: rgba(255,255,255,0.95);
      text-decoration: none;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: all 0.2s;
      font-weight: 500;
      font-size: 0.9rem;
      border: none;
      background: transparent;
    }
    
    .admin-nav-btn:hover {
      background: rgba(255,255,255,0.15);
      color: white;
    }
    
    .admin-nav-btn.active {
      background: rgba(255,255,255,0.25);
      color: white;
      font-weight: 600;
    }
    
    .admin-nav-btn.btn-logout {
      background: rgba(220, 53, 69, 0.95);
      color: white;
    }
    
    .admin-nav-btn.btn-logout:hover {
      background: #c82333;
      transform: translateY(-1px);
    }
    
    .admin-main {
      flex: 1;
      padding: 2rem 0;
    }
    
    .admin-footer {
      background: #2E6B30;
      color: rgba(255,255,255,0.85);
      padding: 1.25rem 0;
      text-align: center;
      font-size: 0.875rem;
      margin-top: auto;
    }
    
    /* Admin Section Styles */
    .admin-section {
      padding: 2rem 0;
    }
    
    .admin-section h1 {
      font-size: 1.9rem;
      font-weight: 700;
      color: #2E6B30;
      margin-bottom: 0.5rem;
    }
    
    .admin-section .lead {
      color: #6c757d;
      margin-bottom: 2rem;
    }
    
    .admin-card {
      background: white;
      border-radius: 16px;
      border: none;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      transition: all 0.3s;
      height: 100%;
    }
    
    .admin-card:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.12);
      transform: translateY(-2px);
    }
    
    .admin-card .card-title {
      font-weight: 600;
      color: #2E6B30;
      font-size: 1.1rem;
    }
    
    .admin-card .card-text {
      color: #6c757d;
      font-size: 0.95rem;
    }
    
    .admin-card .badge-stat {
      font-size: 1.5rem;
      font-weight: 700;
      color: #2E6B30;
    }
    
    /* Alert mejoras */
    .alert {
      border-radius: 12px;
      border: none;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .alert-success {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      color: #155724;
    }
    
    .alert-danger {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      color: #721c24;
    }
    
    .alert-warning {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      color: #856404;
    }
    
    .alert-info {
      background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
      color: #0c5460;
    }
    
    /* Badge mejoras */
    .badge {
      font-weight: 600;
      padding: 0.4rem 0.65rem;
      border-radius: 6px;
    }
    
    .badge-low-stock {
      background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
      color: #c62828;
      border: 1px solid #ef9a9a;
    }
    
    .badge-success {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      color: #2E6B30;
      border: 1px solid #a5d6a7;
    }
    
    /* Tabla mejoras */
    .table {
      background: white;
      border-radius: 8px;
      overflow: hidden;
    }
    
    .table thead {
      background: linear-gradient(135deg, #2E6B30 0%, #1f5f26 100%);
      color: white;
    }
    
    .table thead th {
      border: none;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.05em;
    }
    
    .table tbody tr:hover {
      background: #f8f9fa;
    }
  </style>
</head>
<body class="admin-layout">

<!-- Admin Header -->
<nav class="navbar navbar-dark bg-success admin-navbar">
  <div class="container-fluid">
    <span class="navbar-brand d-flex align-items-center">
      <i class="fas fa-shield-alt me-2"></i>
      Panel de Administración MiliPet
    </span>
    <div class="d-flex gap-2 flex-wrap">
      <a href="<?= url(['r' => 'admin/dashboard']) ?>" class="admin-nav-btn <?= ($_GET['r'] ?? '') === 'admin/dashboard' || ($_GET['r'] ?? '') === 'admin' ? 'active' : '' ?>">
        <i class="fas fa-home me-1"></i>Dashboard
      </a>
      <a href="<?= url(['r' => 'admin/products']) ?>" class="admin-nav-btn <?= ($_GET['r'] ?? '') === 'admin/products' ? 'active' : '' ?>">
        <i class="fas fa-box me-1"></i>Productos
      </a>
      <div class="dropdown">
        <a href="#" class="admin-nav-btn dropdown-toggle <?= in_array(($_GET['r'] ?? ''), ['admin/species','admin/categories']) ? 'active' : '' ?>" id="taxonomyMenu" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-cog me-1"></i>Taxonomía
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="taxonomyMenu">
          <li><a class="dropdown-item" href="<?= url(['r' => 'admin/species']) ?>"><i class="fas fa-paw me-2"></i>Especies</a></li>
          <li><a class="dropdown-item" href="<?= url(['r' => 'admin/categories']) ?>"><i class="fas fa-tags me-2"></i>Categorías</a></li>
        </ul>
      </div>
      <a href="<?= url(['r' => 'admin/content']) ?>" class="admin-nav-btn <?= strpos(($_GET['r'] ?? ''), 'admin/content') === 0 ? 'active' : '' ?>">
        <i class="fas fa-file-alt me-1"></i>Contenido
      </a>
      <a href="<?= url(['r' => 'admin/low_stock']) ?>" class="admin-nav-btn <?= ($_GET['r'] ?? '') === 'admin/low_stock' ? 'active' : '' ?>">
        <i class="fas fa-exclamation-triangle me-1"></i>Stock Bajo
      </a>
      <a href="<?= url(['r' => 'auth/logout']) ?>" class="admin-nav-btn btn-logout">
        <i class="fas fa-sign-out-alt me-1"></i>Salir
      </a>
    </div>
  </div>
</nav>

<!-- Admin Main Content -->
<main class="admin-main">
  <div class="container">
    
    <!-- Flash Messages -->
    <?php if ($msg = flash('success')): ?>
      <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <div><?= $msg ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    <?php endif; ?>
    
    <?php if ($msg = flash('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <div><?= htmlspecialchars($msg) ?></div>
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
    
    <?php 
    // Contenido de la vista se inyecta aquí
    echo $content ?? '';
    ?>
  </div>
</main>

<!-- Admin Footer -->
<footer class="admin-footer">
  <div class="container">
    <p class="mb-0">© <?= date('Y') ?> MiliPet - Panel de Administración</p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
