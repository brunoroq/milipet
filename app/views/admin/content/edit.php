<section class="admin-section py-4">
  <div class="container">
    <div class="mb-4">
      <h1 class="mb-2">
        <i class="fas fa-edit text-success me-2"></i>
        Editar Contenido
      </h1>
      <p class="text-muted mb-0">Modifica el texto o imagen de este bloque de contenido.</p>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card shadow-sm admin-card">
          <div class="card-body">
            <form method="post" action="<?= url(['r' => 'admin/content_update']) ?>">
              <!-- CSRF Token -->
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf'] ?? ($_SESSION['csrf'] ?? '')) ?>">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
              <input type="hidden" name="content_key" value="<?= htmlspecialchars($block['content_key']) ?>">

              <!-- Clave del bloque (solo lectura) -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-key me-1"></i> Clave del Bloque
                </label>
                <input type="text" 
                       class="form-control bg-light" 
                       value="<?= htmlspecialchars($block['content_key']) ?>" 
                       readonly>
                <small class="form-text text-muted">
                  Identificador único que se usa en el código. No se puede modificar.
                </small>
              </div>

              <!-- Título interno (informativo) -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-tag me-1"></i> Título / Descripción
                </label>
                <input type="text" 
                       class="form-control bg-light" 
                       value="<?= htmlspecialchars($block['title']) ?>" 
                       readonly>
                <small class="form-text text-muted">
                  Nombre descriptivo para identificar fácilmente este bloque.
                </small>
              </div>

              <!-- Contenido de texto -->
              <div class="mb-4">
                <label for="content" class="form-label fw-bold">
                  <i class="fas fa-align-left me-1"></i> Texto
                </label>
                <textarea name="content" 
                          id="content" 
                          class="form-control" 
                          rows="6" 
                          placeholder="Ingresa el texto que se mostrará en el sitio..."><?= htmlspecialchars($block['content'] ?? '') ?></textarea>
                <small class="form-text text-muted">
                  El texto principal que se mostrará en esta sección del sitio.
                </small>
              </div>

              <!-- URL de imagen -->
              <div class="mb-4">
                <label for="cms_image_url" class="form-label fw-bold">
                  <i class="fas fa-image me-1"></i> Imagen (URL opcional)
                </label>
                <input type="url" 
                       name="image_url" 
                       id="cms_image_url" 
                       class="form-control"
                       value="<?= htmlspecialchars($block['image_url'] ?? '') ?>"
                       placeholder="https://ejemplo.com/imagen.jpg">
                <small class="form-text text-muted">
                  URL de la imagen asociada a este bloque (opcional).
                </small>

                <!-- Vista previa de imagen -->
                <div class="mt-3">
                  <small class="text-muted d-block mb-2 fw-bold">
                    <i class="fas fa-eye me-1"></i> Vista previa:
                  </small>
                  <div class="border rounded p-3 bg-light text-center" style="min-height: 200px;">
                    <img id="cms-image-preview"
                         src="<?= !empty($block['image_url']) ? htmlspecialchars($block['image_url']) : '' ?>"
                         alt="Vista previa"
                         class="img-fluid rounded"
                         style="max-width: 100%; max-height: 300px; display: <?= !empty($block['image_url']) ? 'block' : 'none' ?>; margin: 0 auto;">
                    <div id="cms-preview-placeholder" style="display: <?= !empty($block['image_url']) ? 'none' : 'block' ?>;">
                      <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                      <p class="text-muted mt-2 mb-0">Ingresa una URL para ver la vista previa</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Estado activo/inactivo -->
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" 
                         type="checkbox" 
                         name="is_active" 
                         id="is_active"
                         <?= !empty($block['is_active']) ? 'checked' : '' ?>>
                  <label class="form-check-label fw-bold" for="is_active">
                    <i class="fas fa-toggle-on me-1"></i> Bloque Activo
                  </label>
                  <div class="form-text">
                    Si está desactivado, este contenido no se mostrará en el sitio público.
                  </div>
                </div>
              </div>

              <!-- Botones de acción -->
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save me-1"></i> Guardar Cambios
                </button>
                <a href="<?= url(['r' => 'admin/content']) ?>" class="btn btn-outline-secondary">
                  <i class="fas fa-times me-1"></i> Cancelar
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Sidebar con información adicional -->
      <div class="col-lg-4">
        <div class="card shadow-sm border-info">
          <div class="card-header bg-info text-white">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Información</strong>
          </div>
          <div class="card-body">
            <h6 class="fw-bold">
              <i class="fas fa-clock me-1 text-muted"></i> Última actualización
            </h6>
            <p class="mb-3">
              <?php 
              if (!empty($block['updated_at'])) {
                $date = new DateTime($block['updated_at']);
                echo $date->format('d/m/Y H:i');
              } else {
                echo 'No disponible';
              }
              ?>
            </p>

            <h6 class="fw-bold">
              <i class="fas fa-lightbulb me-1 text-warning"></i> Consejos
            </h6>
            <ul class="small mb-0">
              <li class="mb-2">Mantén los textos concisos y claros.</li>
              <li class="mb-2">Usa imágenes optimizadas para web (menos de 500KB).</li>
              <li class="mb-2">Verifica que las URLs de imágenes sean accesibles públicamente.</li>
              <li>Los cambios se aplican inmediatamente al guardar.</li>
            </ul>
          </div>
        </div>

        <div class="alert alert-warning mt-3">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>Importante:</strong> Los cambios afectarán directamente el contenido visible en el sitio público.
        </div>
      </div>
    </div>
  </div>
</section>

<script>
// Vista previa de imagen en tiempo real
document.addEventListener('DOMContentLoaded', function() {
  const urlInput = document.getElementById('cms_image_url');
  const preview = document.getElementById('cms-image-preview');
  const placeholder = document.getElementById('cms-preview-placeholder');

  function updatePreview() {
    const url = urlInput.value.trim();
    
    if (!url) {
      preview.style.display = 'none';
      placeholder.style.display = 'block';
      placeholder.innerHTML = '<i class="fas fa-image text-muted" style="font-size: 3rem;"></i><p class="text-muted mt-2 mb-0">Ingresa una URL para ver la vista previa</p>';
      return;
    }

    placeholder.style.display = 'none';
    preview.style.display = 'block';
    preview.src = url;
  }

  // Actualizar al escribir
  urlInput.addEventListener('input', updatePreview);
  urlInput.addEventListener('blur', updatePreview);

  // Manejar errores de carga de imagen
  preview.addEventListener('error', function() {
    preview.style.display = 'none';
    placeholder.style.display = 'block';
    placeholder.innerHTML = '<i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i><p class="text-danger mt-2 mb-0">No se pudo cargar la imagen</p>';
  });

  // Mostrar loading mientras carga la imagen
  preview.addEventListener('load', function() {
    if (preview.src && preview.src !== window.location.href) {
      placeholder.style.display = 'none';
      preview.style.display = 'block';
    }
  });
});
</script>

<style>
.admin-card {
  border: none;
  border-radius: 0.5rem;
}

.form-label.fw-bold {
  color: #495057;
  font-size: 0.95rem;
}

.form-control:focus {
  border-color: #198754;
  box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

#cms-preview-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 200px;
}
</style>
