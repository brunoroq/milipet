<div class="admin-card">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-exclamation-triangle text-warning me-2"></i>Productos con Stock Bajo</h2>
    <a href="<?= url(['r' => 'admin/products']) ?>" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-2"></i>Volver a Productos
    </a>
  </div>

  <?php if (empty($products)): ?>
    <div class="alert alert-success">
      <i class="fas fa-check-circle me-2"></i><strong>¡Excelente!</strong> No hay productos con stock bajo en este momento.
    </div>
  <?php else: ?>
    <div class="alert alert-warning mb-4">
      <i class="fas fa-info-circle me-2"></i>
      Se muestran <strong><?= count($products) ?> producto(s)</strong> con 5 o menos unidades disponibles.
      Considera reabastecer para evitar quedarte sin inventario.
    </div>

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th class="text-center">Stock</th>
            <th class="text-center">Precio</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($products as $p): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <?php if (!empty($p['image_url'])): ?>
                  <img src="<?= image_src($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" 
                       style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                <?php endif; ?>
                <div>
                  <strong><?= htmlspecialchars($p['name']) ?></strong>
                  <?php if (!empty($p['description'])): ?>
                    <br><small class="text-muted"><?= htmlspecialchars(substr($p['description'], 0, 50)) ?>...</small>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td>
              <?php if (!empty($p['category_name'])): ?>
                <span class="badge bg-light text-dark"><?= htmlspecialchars($p['category_name']) ?></span>
              <?php else: ?>
                <span class="badge bg-secondary">Sin categoría</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php 
              $stockClass = 'badge-low-stock';
              if ($p['stock'] === 0) {
                $stockClass = 'badge bg-danger';
                $stockText = 'Agotado';
              } elseif ($p['stock'] <= 2) {
                $stockClass = 'badge bg-danger';
                $stockText = $p['stock'] . ' unidad' . ($p['stock'] > 1 ? 'es' : '');
              } else {
                $stockClass = 'badge-low-stock';
                $stockText = $p['stock'] . ' unidades';
              }
              ?>
              <span class="<?= $stockClass ?>"><?= $stockText ?></span>
            </td>
            <td class="text-center">
              <strong>$<?= number_format($p['price'], 0, ',', '.') ?></strong>
            </td>
            <td class="text-center">
              <?php if ($p['is_active']): ?>
                <span class="badge-success"><i class="fas fa-check-circle me-1"></i>Activo</span>
              <?php else: ?>
                <span class="badge bg-secondary"><i class="fas fa-pause-circle me-1"></i>Inactivo</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <a href="<?= url(['r' => 'admin/products']) ?>#product-<?= $p['id'] ?>" 
                 class="btn btn-sm btn-primary" 
                 title="Editar producto">
                <i class="fas fa-edit"></i> Editar
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
