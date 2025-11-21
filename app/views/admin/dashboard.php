<section class="admin-section">
  <div class="container">
    <!-- Header del Dashboard -->
    <div class="mb-4">
      <h1>Panel de Administración</h1>
      <p class="lead">Desde aquí podrás gestionar el catálogo, stock y campañas de adopción.</p>
    </div>
    
    <!-- Flash Messages -->
    <?php if (!empty($flash)): ?>
      <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?php foreach ($flash['messages'] as $m): ?>
          <div><?= $m ?></div>
        <?php endforeach; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    <?php endif; ?>
    
    <!-- Cards Grid -->
    <div class="row g-4">
      <!-- Card 1: Productos -->
      <div class="col-md-4">
        <div class="card admin-card">
          <div class="card-body text-center">
            <div class="mb-3">
              <i class="fas fa-box text-success" style="font-size: 3rem;"></i>
            </div>
            <h5 class="card-title">Productos</h5>
            <p class="card-text">Administra el catálogo visible en la página principal.</p>
            <?php if (isset($totalProducts)): ?>
              <div class="badge-stat mb-3"><?= (int)$totalProducts ?></div>
              <div class="text-muted small">productos en total</div>
            <?php endif; ?>
            <div class="mt-3">
              <a href="<?= url(['r'=>'admin/products']) ?>" class="btn btn-success btn-sm">
                <i class="fas fa-arrow-right me-1"></i>Ver productos
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Card 2: Stock Bajo -->
      <div class="col-md-4">
        <div class="card admin-card">
          <div class="card-body text-center">
            <div class="mb-3">
              <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
            </div>
            <h5 class="card-title">Stock Bajo</h5>
            <p class="card-text">Productos con 5 o menos unidades disponibles.</p>
            <?php if (isset($lowStockCount)): ?>
              <div class="badge-stat <?= $lowStockCount > 0 ? 'text-danger' : 'text-success' ?> mb-3">
                <?= (int)$lowStockCount ?>
              </div>
              <div class="text-muted small">productos requieren atención</div>
            <?php endif; ?>
            <div class="mt-3">
              <a href="<?= url(['r'=>'admin/low_stock']) ?>" class="btn btn-warning btn-sm text-dark">
                <i class="fas fa-list me-1"></i>Ver lista
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Card 3: Campañas de Adopción -->
      <div class="col-md-4">
        <div class="card admin-card">
          <div class="card-body text-center">
            <div class="mb-3">
              <i class="fas fa-heart text-danger" style="font-size: 3rem;"></i>
            </div>
            <h5 class="card-title">Adopciones</h5>
            <p class="card-text">Gestiona las campañas de adopción de mascotas.</p>
            <?php if (isset($campaignsCount)): ?>
              <div class="badge-stat mb-3"><?= (int)$campaignsCount ?></div>
              <div class="text-muted small">campañas activas</div>
            <?php endif; ?>
            <div class="mt-3">
              <a href="<?= url(['r'=>'admin/campaigns']) ?>" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-paw me-1"></i>Gestionar campañas
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Información adicional -->
    <div class="row g-4 mt-2">
      <div class="col-12">
        <div class="card admin-card">
          <div class="card-body">
            <h5 class="card-title">
              <i class="fas fa-info-circle me-2"></i>Accesos Rápidos
            </h5>
            <div class="row mt-3">
              <div class="col-md-6">
                <ul class="list-unstyled">
                  <li class="mb-2">
                    <a href="<?= url(['r'=>'admin/products']) ?>" class="text-decoration-none">
                      <i class="fas fa-plus-circle text-success me-2"></i>Añadir nuevo producto
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="<?= url(['r'=>'admin/low_stock']) ?>" class="text-decoration-none">
                      <i class="fas fa-boxes text-warning me-2"></i>Revisar inventario
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="<?= url(['r'=>'admin/species']) ?>" class="text-decoration-none">
                      <i class="fas fa-paw text-success me-2"></i>Gestionar especies
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="<?= url(['r'=>'admin/categories']) ?>" class="text-decoration-none">
                      <i class="fas fa-tags text-success me-2"></i>Gestionar categorías
                    </a>
                  </li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul class="list-unstyled">
                  <li class="mb-2">
                    <a href="<?= url(['r'=>'home']) ?>" target="_blank" class="text-decoration-none">
                      <i class="fas fa-external-link-alt text-primary me-2"></i>Ver sitio público
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="<?= url(['r'=>'catalog']) ?>" target="_blank" class="text-decoration-none">
                      <i class="fas fa-store text-info me-2"></i>Ver catálogo
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="<?= url(['r'=>'admin/species']) ?>" class="text-decoration-none">
                      <i class="fas fa-sitemap text-primary me-2"></i>Taxonomía: Especies
                    </a>
                  </li>
                  <li class="mb-2">
                    <a href="<?= url(['r'=>'admin/categories']) ?>" class="text-decoration-none">
                      <i class="fas fa-layer-group text-primary me-2"></i>Taxonomía: Categorías
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>