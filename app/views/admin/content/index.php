<section class="admin-section py-4">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="mb-2">
          <i class="fas fa-file-alt text-success me-2"></i>
          Contenido del Sitio
        </h1>
        <p class="text-muted mb-0">Edita los textos e imágenes de las secciones públicas sin tocar código.</p>
      </div>
      <div>
        <a href="<?= url(['r' => 'admin/dashboard']) ?>" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-1"></i> Volver al Panel
        </a>
      </div>
    </div>

    <div class="card shadow-sm admin-card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 25%;">
                  <i class="fas fa-key me-1 text-muted"></i>
                  Clave
                </th>
                <th style="width: 20%;">
                  <i class="fas fa-tag me-1 text-muted"></i>
                  Nombre
                </th>
                <th style="width: 35%;">
                  <i class="fas fa-align-left me-1 text-muted"></i>
                  Texto
                </th>
                <th style="width: 10%;" class="text-center">
                  <i class="fas fa-image me-1 text-muted"></i>
                  Imagen
                </th>
                <th style="width: 10%;" class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($blocks)): ?>
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    No hay bloques de contenido registrados.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($blocks as $b): ?>
                  <tr class="<?= empty($b['is_active']) ? 'table-secondary' : '' ?>">
                    <td>
                      <code class="text-primary"><?= htmlspecialchars($b['content_key']) ?></code>
                      <?php if (empty($b['is_active'])): ?>
                        <span class="badge bg-secondary ms-2">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <strong><?= htmlspecialchars($b['title']) ?></strong>
                    </td>
                    <td>
                      <div class="text-truncate" style="max-width: 400px;">
                        <?php 
                        $text = $b['content'] ?? '';
                        echo htmlspecialchars(mb_strimwidth($text, 0, 100, '...'));
                        ?>
                      </div>
                    </td>
                    <td class="text-center">
                      <?php if (!empty($b['image_url'])): ?>
                        <i class="fas fa-image text-success fs-5" 
                           title="<?= htmlspecialchars($b['image_url']) ?>"
                           data-bs-toggle="tooltip"></i>
                      <?php else: ?>
                        <span class="text-muted">—</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <a href="<?= url(['r' => 'admin/content_edit', 'key' => $b['content_key']]) ?>" 
                         class="btn btn-sm btn-success">
                        <i class="fas fa-edit"></i> Editar
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="alert alert-info mt-4 d-flex align-items-start">
      <i class="fas fa-info-circle me-3 fs-5"></i>
      <div>
        <strong>Nota:</strong> Los cambios que hagas aquí se reflejarán inmediatamente en el sitio público.
        Asegúrate de revisar el contenido antes de guardarlo. Los bloques marcados como "Inactivos" no se mostrarán en el sitio.
      </div>
    </div>
  </div>
</section>

<script>
// Inicializar tooltips de Bootstrap
document.addEventListener('DOMContentLoaded', function() {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>

<style>
.admin-card {
  border: none;
  border-radius: 0.5rem;
}

.table thead th {
  font-weight: 600;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #dee2e6;
}

.table tbody tr:hover {
  background-color: rgba(25, 135, 84, 0.05);
}

code.text-primary {
  background-color: #e7f5ff;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
}
</style>
