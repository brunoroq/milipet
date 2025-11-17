<section class="admin-section py-4">
  <div class="container-fluid">
    <div class="mb-4">
      <h1 class="mb-2">
        <i class="fas fa-file-alt text-success me-2"></i>
        Contenido del Sitio
      </h1>
      <p class="text-muted mb-0">
        Selecciona una sección para editar los textos e imágenes mostrados en la página pública.
      </p>
    </div>

    <div class="row g-4">
      <?php foreach ($grouped as $sectionKey => $blocks): ?>
        <?php 
        $sectionName = $labels[$sectionKey] ?? ucfirst($sectionKey);
        $blockCount = count($blocks);
        
        // Iconos por sección
        $icons = [
          'home' => 'fa-home',
          'about' => 'fa-info-circle',
          'adoptions' => 'fa-heart',
          'policies' => 'fa-file-contract',
          'otros' => 'fa-folder',
        ];
        $icon = $icons[$sectionKey] ?? 'fa-folder';
        
        // Colores por sección
        $colors = [
          'home' => 'success',
          'about' => 'info',
          'adoptions' => 'danger',
          'policies' => 'warning',
          'otros' => 'secondary',
        ];
        $color = $colors[$sectionKey] ?? 'secondary';
        ?>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
          <div class="card shadow-sm section-card h-100 border-<?= $color ?>">
            <div class="card-body d-flex flex-column">
              <div class="text-center mb-3">
                <div class="section-icon-wrapper bg-<?= $color ?> bg-opacity-10">
                  <i class="fas <?= $icon ?> text-<?= $color ?> fs-1"></i>
                </div>
              </div>
              
              <h5 class="card-title text-center mb-2">
                <?= htmlspecialchars($sectionName) ?>
              </h5>
              
              <p class="text-muted text-center small mb-3">
                <i class="fas fa-cube me-1"></i>
                <?= $blockCount ?> bloque<?= $blockCount !== 1 ? 's' : '' ?> de contenido
              </p>
              
              <div class="mt-auto">
                <a href="<?= url(['r' => 'admin/content', 'section' => $sectionKey]) ?>"
                   class="btn btn-<?= $color ?> w-100">
                  <i class="fas fa-edit me-1"></i>
                  Gestionar sección
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      
      <?php if (empty($grouped)): ?>
        <div class="col-12">
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No hay bloques de contenido registrados. Ejecuta el script de inicialización de la base de datos.
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="alert alert-info mt-4 d-flex align-items-start">
      <i class="fas fa-lightbulb me-3 fs-5"></i>
      <div>
        <strong>Consejo:</strong> Los cambios que realices en cada sección se reflejarán inmediatamente en el sitio público.
        Puedes activar o desactivar bloques individuales desde la vista de cada sección.
      </div>
    </div>
  </div>
</section>

<style>
.section-card {
  transition: all 0.3s ease;
  border-width: 2px !important;
}

.section-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}

.section-icon-wrapper {
  width: 100px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  margin: 0 auto;
}

.card-title {
  font-weight: 600;
  color: #2c3e50;
}
</style>
