<?php
// Map section for other pages
$coords = defined('STORE_COORDS') ? STORE_COORDS : null;
$addr   = defined('STORE_ADDRESS_TEXT') ? STORE_ADDRESS_TEXT : 'Maipú, Región Metropolitana';
$qParam = $coords ? $coords : $addr;
?>
<section class="bottom-section bottom-map">
  <div class="container">
    <div class="row g-4 align-items-center">
      <div class="col-lg-5">
        <div class="map-info">
          <div class="icon-circle mb-3">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <h2 class="section-title mb-3">¿Dónde estamos?</h2>
          <p class="lead mb-4">Visítanos en nuestra tienda física en Maipú. Te esperamos con todo lo que tu mascota necesita.</p>
          
          <div class="info-items">
            <div class="info-item mb-3">
              <div class="info-icon">
                <i class="fas fa-location-dot text-success"></i>
              </div>
              <div class="info-content">
                <strong>Dirección</strong>
                <p class="mb-0 text-muted"><?= htmlspecialchars($addr) ?></p>
              </div>
            </div>
            
            <div class="info-item mb-3">
              <div class="info-icon">
                <i class="fas fa-clock text-success"></i>
              </div>
              <div class="info-content">
                <strong>Horarios de atención</strong>
                <?php if (defined('STORE_HOURS_WEEKDAYS') && defined('STORE_HOURS_SATURDAY')): ?>
                  <p class="mb-0 text-muted">
                    <?= htmlspecialchars(STORE_HOURS_WEEKDAYS) ?><br>
                    <?= htmlspecialchars(STORE_HOURS_SATURDAY) ?>
                  </p>
                <?php else: ?>
                  <p class="mb-0 text-muted">
                    Lunes a Viernes: 10:00 - 19:00<br>
                    Sábados: 10:00 - 19:00
                  </p>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="info-item mb-3">
              <div class="info-icon">
                <i class="fas fa-store text-success"></i>
              </div>
              <div class="info-content">
                <strong>Retiro en tienda</strong>
                <p class="mb-0 text-muted">Retiro gratis • Disponible el mismo día</p>
              </div>
            </div>
          </div>
          
          <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($qParam) ?>" 
             target="_blank" 
             rel="noopener"
             class="btn btn-success btn-lg mt-3">
            <i class="fas fa-directions me-2"></i>
            Cómo llegar
          </a>
        </div>
      </div>
      
      <div class="col-lg-7">
        <div class="map-wrapper shadow-lg rounded-3 overflow-hidden">
          <iframe
            src="https://www.google.com/maps?q=<?= urlencode($qParam) ?>&z=16&hl=es&output=embed"
            loading="lazy" 
            allowfullscreen 
            referrerpolicy="no-referrer-when-downgrade"
            class="map-iframe">
          </iframe>
        </div>
      </div>
    </div>
  </div>
</section>
