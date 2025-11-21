<?php ?>
<!-- Alerta de Stock Bajo -->
<?php if (!empty($lowStockCount) && $lowStockCount > 0): ?>
  <div class="alert alert-warning d-flex align-items-start mb-4" role="alert">
    <i class="fas fa-exclamation-triangle me-3 mt-1" style="font-size: 1.25rem;"></i>
    <div class="flex-grow-1">
      <strong>Atención:</strong>
      Hay <?= (int)$lowStockCount ?> producto(s) con stock bajo (5 o menos unidades).
      <a href="<?= url(['r'=>'admin/low_stock']) ?>" class="alert-link">
        <i class="fas fa-list me-1"></i>Ver productos con stock bajo
      </a>
    </div>
  </div>
<?php endif; ?>

<h1>Productos</h1>

<form method="post" action="?r=admin/product/save" class="card p" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['_csrf'] ?? ($_SESSION['csrf'] ?? '')); ?>">
  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
  <h2>Nuevo / Editar producto</h2>
  <input type="hidden" name="id" id="prod-id">
  <label>Nombre <input type="text" name="name" id="prod-name" required></label>
  <label>Descripción Corta <input type="text" name="short_desc" id="prod-short-desc" maxlength="255" placeholder="Breve descripción para listados"></label>
  <label>Descripción Completa <textarea name="long_desc" id="prod-long-desc" rows="5" placeholder="Descripción detallada del producto"></textarea></label>
  <div class="row">
    <label>
      Precio (CLP)
      <input type="text" name="price" id="prod-price" placeholder="Ej: 40.000" required>
      <small class="form-text text-muted">Puedes usar puntos o comas como separadores (ej: 40000, 40.000 o 40,000).</small>
    </label>
    <label>
      Stock 
      <div class="stock-input">
        <input type="number" name="stock" id="prod-stock" step="1" min="0" required>
        <div class="stock-actions">
          <button type="button" class="btn-sm" onclick="adjustStock(1)">+1</button>
          <button type="button" class="btn-sm" onclick="adjustStock(5)">+5</button>
          <button type="button" class="btn-sm danger" onclick="adjustStock(-1)">-1</button>
        </div>
      </div>
    </label>
  </div>
  <div class="stock-alerts">
    <label><input type="number" name="stock_alert" id="prod-stock-alert" step="1" min="1" value="" placeholder="5"> 
      Alerta de stock bajo (unidades)
      <small class="text-muted d-block">El sistema alertará cuando el stock sea igual o menor a este valor (por defecto: 5)</small>
    </label>
  </div>
  <?php if (!empty($flash)): ?>
    <div class="alert <?php echo $flash['type']==='error' ? 'alert-error' : 'alert-success'; ?>">
      <?php foreach ($flash['messages'] as $m): ?>
        <div><?php echo htmlspecialchars($m); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['old'])): ?>
    <script>
      // Repoblar campos con valores antiguos
      document.addEventListener('DOMContentLoaded', function() {
        const old = <?php echo json_encode($_SESSION['old']); ?>;
        if (old.name) document.getElementById('prod-name').value = old.name;
        if (old.short_desc) document.getElementById('prod-short-desc').value = old.short_desc;
        if (old.long_desc) document.getElementById('prod-long-desc').value = old.long_desc;
        if (old.price) document.getElementById('prod-price').value = old.price;
        if (old.stock) document.getElementById('prod-stock').value = old.stock;
        if (old.image_url) document.getElementById('prod-img').value = old.image_url;
        if (old.category_id) document.getElementById('prod-cat').value = old.category_id;
        if (old.species) {
          const radio = document.querySelector('input[name="species"][value="' + old.species + '"]');
          if (radio) radio.checked = true;
        }
        if (old.is_active) document.getElementById('prod-active').checked = true;
        if (old.is_featured) document.getElementById('prod-featured').checked = true;
      });
      <?php unset($_SESSION['old']); ?>
    </script>
  <?php endif; ?>
  <fieldset><legend>Especies</legend>
    <label><input type="radio" name="species" value="" checked> Sin filtro</label>
    <?php foreach ($species as $sp): ?>
      <label><input type="radio" name="species" value="<?= htmlspecialchars($sp['name']) ?>"> <?= htmlspecialchars($sp['name']) ?></label>
    <?php endforeach; ?>
  </fieldset>
  <label>Categoría
    <select name="category_id" id="prod-cat">
      <option value="">Sin categoría</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Imagen (URL)
    <input type="url" name="image_url" id="prod-img" placeholder="assets/img/tu-imagen.jpg o https://...">
  </label>
  
  <!-- Vista previa de la imagen -->
  <div class="mt-2" id="image-preview-container">
    <small class="text-muted d-block mb-1">Vista previa:</small>
    <div class="border rounded p-3 bg-light text-center" style="min-height: 120px;">
      <img id="product-image-preview"
           src=""
           alt="Vista previa de la imagen"
           class="img-fluid"
           style="max-width: 200px; max-height: 200px; display: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <div id="preview-placeholder" class="text-muted" style="padding: 2rem 0;">
        <i class="fas fa-image" style="font-size: 2rem; opacity: 0.3;"></i>
        <p class="mb-0 mt-2 small">Ingresa una URL para ver la vista previa</p>
      </div>
    </div>
  </div>
  
  <input type="hidden" name="current_image_url" id="prod-current-img">
  <label>Subir imagen (JPG/PNG, máx 3MB)
    <input type="file" name="image_file" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
  </label>
  <img id="preview" alt="" style="max-width:160px;border-radius:8px;margin-top:8px;display:none;">
  <label><input type="checkbox" name="is_featured" id="prod-featured"> Producto destacado</label>
  <label><input type="checkbox" name="is_active" id="prod-active" checked> Activo</label>
  <button class="btn" type="submit">Guardar</button>
</form>

<table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Categoría</th>
      <th>Precio</th>
      <th>Stock</th>
      <th>Destacado</th>
      <th>Estado</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($products as $p): ?>
      <?php $threshold = $p['low_stock_threshold'] ?? 5; ?>
      <tr class="<?php echo ($p['stock'] <= $threshold ? 'low-stock' : ''); ?>">
        <td><?php echo $p['id']; ?></td>
        <td><?php echo htmlspecialchars($p['name']); ?></td>
        <td><?php echo htmlspecialchars($p['category_name'] ?? ''); ?></td>
        <td>$<?php echo number_format($p['price'] ?? 0, 0, ',', '.'); ?></td>
        <td>
          <span class="stock-badge <?php echo ($p['stock'] <= 0 ? 'out' : ($p['stock'] <= $threshold ? 'low' : 'ok')); ?>">
            <?php echo (int)$p['stock']; ?>
          </span>
        </td>
        <td><?php echo !empty($p['is_featured']) ? '⭐' : '-'; ?></td>
        <td><?php echo $p['is_active'] ? 'Activo' : 'Oculto'; ?></td>
        <td class="row">
          <button class="btn-outline" onclick='prefill(<?php echo json_encode($p, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>)'>Editar</button>
            <form method="post" action="?r=admin/product/delete" style="display:inline" onsubmit="return confirm('¿Eliminar?')">
              <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
              <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['_csrf'] ?? ($_SESSION['csrf'] ?? '')); ?>">
              <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
              <button class="btn-danger" type="submit">Eliminar</button>
            </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
function prefill(p) {
  document.getElementById('prod-id').value = p.id;
  document.getElementById('prod-name').value = p.name;
  document.getElementById('prod-short-desc').value = p.short_desc || '';
  document.getElementById('prod-long-desc').value = p.long_desc || '';
  
  // Formatear el precio en formato chileno (40.000)
  const price = parseInt(p.price) || 0;
  document.getElementById('prod-price').value = price.toLocaleString('es-CL');
  
  document.getElementById('prod-stock').value = p.stock || 0;
  document.getElementById('prod-stock-alert').value = p.low_stock_threshold || 5;
  document.getElementById('prod-cat').value = p.category_id || '';
  document.getElementById('prod-img').value = p.image_url || '';
  document.getElementById('prod-current-img').value = p.image_url || '';
  document.getElementById('prod-featured').checked = p.is_featured == 1;
  document.getElementById('prod-active').checked = p.is_active == 1;
  
  // Seleccionar especie correcta
  if (p.species) {
    const radio = document.querySelector('input[name="species"][value="' + p.species + '"]');
    if (radio) radio.checked = true;
  } else {
    document.querySelector('input[name="species"][value=""]').checked = true;
  }
  
  // Actualizar vista previa nueva
  const newPreview = document.getElementById('product-image-preview');
  const placeholder = document.getElementById('preview-placeholder');
  if (p.image_url) {
    newPreview.src = p.image_url;
    newPreview.style.display = 'block';
    if (placeholder) placeholder.style.display = 'none';
  } else {
    newPreview.style.display = 'none';
    if (placeholder) placeholder.style.display = 'block';
  }
  
  // Mantener compatibilidad con preview antiguo
  const preview = document.getElementById('preview');
  if (p.image_url) { preview.src = p.image_url; preview.style.display='block'; } else { preview.style.display='none'; }
  
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function adjustStock(amount) {
  const stockInput = document.getElementById('prod-stock');
  const currentStock = parseInt(stockInput.value) || 0;
  stockInput.value = Math.max(0, currentStock + amount);
}
</script>

<style>
.stock-input {
  display: flex;
  align-items: center;
  gap: 10px;
}

.stock-actions {
  display: flex;
  gap: 5px;
}

.btn-sm {
  padding: 2px 8px;
  font-size: 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: #fff;
  cursor: pointer;
}

.btn-sm:hover {
  background: #f5f5f5;
}

.btn-sm.danger {
  color: #c62828;
  border-color: #ffcdd2;
}

.btn-sm.danger:hover {
  background: #ffebee;
}

.stock-alerts {
  margin: 10px 0;
  padding: 10px;
  background: #f5f5f5;
  border-radius: 4px;
}

.stock-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 12px;
  font-weight: bold;
  font-size: 0.9em;
}

.stock-badge.out {
  background: #ffebee;
  color: #c62828;
}

.stock-badge.low {
  background: #fff3e0;
  color: #ef6c00;
}

.stock-badge.ok {
  background: #e8f5e9;
  color: #2e7d32;
}

tr.low-stock {
  background-color: #fff3e0;
}

tr.low-stock:hover {
  background-color: #ffe0b2;
}

.image-url-hint {
  font-size: 0.9em;
  margin-top: 4px;
  padding: 4px 8px;
  border-radius: 4px;
  display: none;
}

.image-url-hint.warning {
  background: #fff3e0;
  color: #ef6c00;
  display: block;
}

.image-url-hint.error {
  background: #ffebee;
  color: #c62828;
  display: block;
}

.image-url-hint.success {
  background: #e8f5e9;
  color: #2e7d32;
  display: block;
}
</style>

<script>
// Client-side image URL validation hint (non-blocking)
document.addEventListener('DOMContentLoaded', function() {
  const imgUrlInput = document.getElementById('prod-img');
  if (!imgUrlInput) return;
  
  let hintEl = document.createElement('div');
  hintEl.className = 'image-url-hint';
  imgUrlInput.parentNode.appendChild(hintEl);
  
  let timeout;
  imgUrlInput.addEventListener('blur', function() {
    const url = imgUrlInput.value.trim();
    if (!url) {
      hintEl.className = 'image-url-hint';
      hintEl.textContent = '';
      return;
    }
    
    // Check if it's a Google redirect URL
    if (/^https?:\/\/(www\.)?google\.[^/]+\/url\?/i.test(url)) {
      hintEl.className = 'image-url-hint error';
      hintEl.textContent = '⚠️ URLs de redirección de Google no son válidas. Use la URL directa de la imagen.';
      return;
    }
    
    // Check if HTTPS for external URLs
    if (/^http:\/\//i.test(url)) {
      hintEl.className = 'image-url-hint error';
      hintEl.textContent = '⚠️ Use HTTPS para evitar problemas de contenido mixto.';
      return;
    }
    
    // Try to validate external HTTPS URLs
    if (/^https:\/\//i.test(url)) {
      hintEl.className = 'image-url-hint';
      hintEl.textContent = '⏳ Verificando...';
      
      clearTimeout(timeout);
      timeout = setTimeout(function() {
        fetch(url, { method: 'HEAD', mode: 'no-cors' })
          .then(function() {
            hintEl.className = 'image-url-hint success';
            hintEl.textContent = '✓ URL externa parece válida (se verificará en el servidor).';
          })
          .catch(function() {
            hintEl.className = 'image-url-hint warning';
            hintEl.textContent = '⚠️ No se pudo verificar la URL (se validará al guardar).';
          });
      }, 500);
    } else {
      // Local path hint
      hintEl.className = 'image-url-hint';
      hintEl.textContent = 'ℹ️ Ruta local - se verificará al guardar.';
    }
  });
});

// ========== VISTA PREVIA DE IMAGEN ==========
document.addEventListener('DOMContentLoaded', function () {
  const urlInput = document.getElementById('prod-img');
  const preview = document.getElementById('product-image-preview');
  const placeholder = document.getElementById('preview-placeholder');
  
  if (!urlInput || !preview || !placeholder) return;

  function updatePreview() {
    const url = urlInput.value.trim();
    
    if (!url) {
      // Sin URL: mostrar placeholder
      preview.style.display = 'none';
      placeholder.style.display = 'block';
      preview.removeAttribute('src');
      return;
    }
    
    // Con URL: intentar cargar imagen
    placeholder.style.display = 'none';
    preview.style.display = 'block';
    preview.src = url;
  }

  // Escuchar cambios en el input
  urlInput.addEventListener('input', updatePreview);
  urlInput.addEventListener('blur', updatePreview);

  // Ocultar imagen si falla la carga
  preview.addEventListener('error', function () {
    preview.style.display = 'none';
    placeholder.innerHTML = '<i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i><p class="mb-0 mt-2 small text-warning">No se pudo cargar la imagen desde esta URL</p>';
    placeholder.style.display = 'block';
  });
  
  // Mostrar placeholder de éxito cuando la imagen carga
  preview.addEventListener('load', function () {
    if (preview.src && preview.style.display !== 'none') {
      placeholder.style.display = 'none';
    }
  });
  
  // Inicializar al cargar la página
  updatePreview();
});
</script>
