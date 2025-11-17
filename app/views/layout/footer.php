<?php
// Cargar la configuración desde la ruta raíz del proyecto
// app/views/layout -> views -> app -> (root) -> config/config.php
require_once __DIR__ . '/../../../config/config.php';
// Evitar notices si $config no está definido o no es array
$storeConfig = (isset($config) && is_array($config)) ? $config : [];
// Helper para renderizar íconos: usa Font Awesome si está disponible, si no, usa un emoji legible
if (!function_exists('mp_render_icon')) {
    function mp_render_icon(string $platform): string {
        $p = strtolower($platform);
        if (defined('FONTAWESOME_KIT') && FONTAWESOME_KIT) {
            return '<i class="fab fa-' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . '"></i>';
        }
        $emoji = [
            'instagram' => '📷',
            'facebook'  => '📘',
            'whatsapp'  => '💬',
        ];
        $char = $emoji[$p] ?? '🔗';
        return '<span class="icon-fallback" aria-hidden="true">' . $char . '</span>';
    }
}
?>
</main>

<?php 
// Include bottom section (newsletter or map depending on page)
require_once __DIR__ . '/section-bottom.php'; 
?>

<footer class="footer-modern">
  <div class="container">
    <div class="row align-items-center g-3 py-4">
      <!-- Copyright - Left -->
      <div class="col-lg-4 col-md-12 text-center text-lg-start">
        <p class="footer-copyright mb-0">
          <strong>© <?= date('Y') ?> MiliPet</strong>
          <span class="footer-separator d-none d-md-inline">·</span>
          <span class="d-block d-md-inline">Petshop en Maipú</span>
        </p>
      </div>
      
      <!-- Quick Links - Center -->
      <div class="col-lg-4 col-md-12 text-center">
        <nav class="footer-nav">
          <a href="<?= url(['r' => 'catalog']) ?>" class="footer-link">Catálogo</a>
          <a href="<?= url(['r' => 'adoptions']) ?>" class="footer-link">Adopciones</a>
          <a href="<?= url(['r' => 'about']) ?>" class="footer-link">Quiénes somos</a>
          <a href="<?= url(['r' => 'policies']) ?>" class="footer-link">Políticas</a>
        </nav>
      </div>
      
      <!-- Social Media - Right -->
      <div class="col-lg-4 col-md-12 text-center text-lg-end">
        <p class="text-white small fw-semibold mb-2">Síguenos</p>
        <div class="footer-social mt-3 d-flex gap-3 justify-content-center justify-content-lg-end">
          <?php
          $whatsappUrl = $storeConfig['store']['social']['whatsapp'] ?? 'https://wa.me/5695458036';
          $instagramUrl = $storeConfig['store']['social']['instagram'] ?? 'https://www.instagram.com/mili_petshop/';
          ?>
          <a href="<?= htmlspecialchars($whatsappUrl) ?>" 
             class="btn btn-success rounded-pill px-3" 
             target="_blank" 
             rel="noopener"
             aria-label="WhatsApp">
            <i class="fab fa-whatsapp me-2"></i> WhatsApp
          </a>
          <a href="<?= htmlspecialchars($instagramUrl) ?>" 
             class="btn btn-outline-light rounded-pill px-3" 
             target="_blank" 
             rel="noopener"
             aria-label="Instagram">
            <i class="fab fa-instagram me-2"></i> Instagram
          </a>
        </div>
      </div>
    </div>
  </div>
</footer>

<?php if (defined('FONTAWESOME_KIT') && FONTAWESOME_KIT): ?>
<script src="https://kit.fontawesome.com/<?php echo htmlspecialchars(FONTAWESOME_KIT, ENT_QUOTES, 'UTF-8'); ?>.js" crossorigin="anonymous"></script>
<?php endif; ?>
<!-- Bootstrap JS (bundle incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>