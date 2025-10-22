<?php ?><h1>Productos</h1>
<form method="post" action="?r=admin/product/save" class="card p" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
  <h2>Nuevo / Editar producto</h2>
  <input type="hidden" name="id" id="prod-id">
  <label>Nombre <input type="text" name="name" id="prod-name" required></label>
  <label>Descripción <textarea name="description" id="prod-desc"></textarea></label>
  <div class="row">
    <label>Precio <input type="number" name="price" id="prod-price" step="1" min="0" required></label>
    <label>Stock <input type="number" name="stock" id="prod-stock" step="1" min="0" required></label>
  </div>
  <?php if (!empty($flash)): ?>
    <div class="alert <?php echo $flash['type']==='error' ? 'alert-error' : 'alert-success'; ?>">
      <?php foreach ($flash['messages'] as $m): ?>
        <div><?php echo htmlspecialchars($m); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <fieldset><legend>Especies</legend>
    <label><input type="radio" name="species" value="" checked> Sin filtro</label>
    <label><input type="radio" name="species" value="dogs"> Perros</label>
    <label><input type="radio" name="species" value="cats"> Gatos</label>
    <label><input type="radio" name="species" value="birds"> Aves</label>
    <label><input type="radio" name="species" value="other"> Otros</label>
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
    <input type="url" name="image_url" id="prod-img" placeholder="assets/img/tu-imagen.jpg">
  </label>
  <input type="hidden" name="current_image_url" id="prod-current-img">
  <label>Subir imagen (JPG/PNG, máx 3MB)
    <input type="file" name="image_file" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
  </label>
  <img id="preview" alt="" style="max-width:160px;border-radius:8px;margin-top:8px;display:none;">
  <label><input type="checkbox" name="is_active" id="prod-active" checked> Activo</label>
  <button class="btn" type="submit">Guardar</button>
</form>

<table class="table">
  <thead><tr><th>ID</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th></th></tr></thead>
  <tbody>
    <?php foreach ($products as $p): ?>
      <tr>
        <td><?php echo $p['id']; ?></td>
        <td><?php echo htmlspecialchars($p['name']); ?></td>
        <td><?php echo htmlspecialchars($p['category_name'] ?? ''); ?></td>
        <td>$<?php echo number_format($p['price'] ?? 0, 0, ',', '.'); ?></td>
        <td><?php echo (int)$p['stock']; ?></td>
        <td><?php echo $p['is_active'] ? 'Activo' : 'Oculto'; ?></td>
        <td class="row">
          <button class="btn-outline" onclick='prefill(<?php echo json_encode($p, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>)'>Editar</button>
            <form method="post" action="?r=admin/product/delete" style="display:inline" onsubmit="return confirm('¿Eliminar?')">
              <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
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
  document.getElementById('prod-desc').value = p.description || '';
  document.getElementById('prod-price').value = p.price || 0;
  document.getElementById('prod-stock').value = p.stock || 0;
  document.getElementById('prod-cat').value = p.category_id || '';
  document.getElementById('prod-img').value = p.image_url || '';
  document.getElementById('prod-current-img').value = p.image_url || '';
  document.getElementById('prod-active').checked = p.is_active == 1;
  const preview = document.getElementById('preview');
  if (p.image_url) { preview.src = p.image_url; preview.style.display='block'; } else { preview.style.display='none'; }
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
