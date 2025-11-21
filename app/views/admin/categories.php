<?php require_once __DIR__ . '/../layout/admin_header.php'; ?>

<div class="container-fluid py-4">
  <div class="row mb-4">
    <div class="col">
      <h2><i class="fas fa-tags me-2"></i>Gestión de Categorías</h2>
      <p class="text-muted">Administra las categorías de productos del catálogo</p>
    </div>
  </div>

  <?php if (isset($flash)): ?>
    <div class="alert alert-<?php echo strpos($flash, 'Éxito') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
      <?php echo $flash; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- Formulario -->
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nueva / Editar Categoría</h5>
        </div>
        <div class="card-body">
          <form method="POST" action="<?php echo url(['r' => 'admin/categories/save']); ?>" id="category-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="id" id="category-id" value="">
            
            <div class="mb-3">
              <label for="category-name" class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="category-name" name="name" required 
                     value="<?php echo isset($old['name']) ? htmlspecialchars($old['name']) : ''; ?>">
              <small class="text-muted">Ej: Alimento, Juguetes, Accesorios, etc.</small>
            </div>

            <div class="mb-3">
              <label for="category-description" class="form-label">Descripción</label>
              <textarea class="form-control" id="category-description" name="description" rows="3"><?php echo isset($old['description']) ? htmlspecialchars($old['description']) : ''; ?></textarea>
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="category-active" name="is_active" checked>
              <label class="form-check-label" for="category-active">
                Activa (visible en el sitio)
              </label>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i>Guardar Categoría
              </button>
              <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                <i class="fas fa-times me-2"></i>Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Nota:</strong> El slug se genera automáticamente a partir del nombre de la categoría.
      </div>
    </div>

    <!-- Listado -->
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h5 class="mb-0"><i class="fas fa-list me-2"></i>Categorías Registradas (<?php echo count($categories); ?>)</h5>
        </div>
        <div class="card-body p-0">
          <?php if (empty($categories)): ?>
            <div class="p-4 text-center text-muted">
              <i class="fas fa-tags fa-3x mb-3 opacity-25"></i>
              <p>No hay categorías registradas. Crea la primera usando el formulario.</p>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th width="60">ID</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th width="100" class="text-center">Estado</th>
                    <th width="120" class="text-center">Productos</th>
                    <th width="140" class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($categories as $cat): ?>
                    <tr>
                      <td><?php echo $cat['id']; ?></td>
                      <td>
                        <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                        <?php if (!empty($cat['description'])): ?>
                          <br><small class="text-muted"><?php echo htmlspecialchars(substr($cat['description'], 0, 60)); ?><?php echo strlen($cat['description']) > 60 ? '...' : ''; ?></small>
                        <?php endif; ?>
                      </td>
                      <td>
                        <code><?php 
                          require_once __DIR__ . '/../../models/Category.php';
                          echo Category::slugify($cat['name']); 
                        ?></code>
                      </td>
                      <td class="text-center">
                        <?php if ($cat['is_active']): ?>
                          <span class="badge bg-success">Activa</span>
                        <?php else: ?>
                          <span class="badge bg-secondary">Inactiva</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-center">
                        <?php
                          $count = Category::countProducts($cat['id']);
                          if ($count > 0) {
                            echo '<span class="badge bg-info">' . $count . '</span>';
                          } else {
                            echo '<span class="text-muted">0</span>';
                          }
                        ?>
                      </td>
                      <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" onclick='editCategory(<?php echo json_encode($cat); ?>)' title="Editar">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>')" title="Eliminar">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Formulario oculto para eliminación -->
<form id="delete-form" method="POST" action="<?php echo url(['r' => 'admin/categories/delete']); ?>" style="display:none;">
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
  <input type="hidden" name="id" id="delete-id">
</form>

<script>
function editCategory(cat) {
  document.getElementById('category-id').value = cat.id;
  document.getElementById('category-name').value = cat.name;
  document.getElementById('category-description').value = cat.description || '';
  document.getElementById('category-active').checked = cat.is_active == 1;
  
  // Scroll al formulario
  document.getElementById('category-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
  document.getElementById('category-name').focus();
}

function resetForm() {
  document.getElementById('category-form').reset();
  document.getElementById('category-id').value = '';
  document.getElementById('category-active').checked = true;
}

function deleteCategory(id, name) {
  if (confirm('¿Eliminar la categoría "' + name + '"?\n\nEsta acción no se puede deshacer.')) {
    document.getElementById('delete-id').value = id;
    document.getElementById('delete-form').submit();
  }
}

// Auto-dismissal de alertas
setTimeout(function() {
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    const bsAlert = new bootstrap.Alert(alert);
    bsAlert.close();
  });
}, 5000);
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
