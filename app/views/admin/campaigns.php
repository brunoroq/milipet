<?php ?>
<div class="campaigns-admin">
    <h1>Campañas de Adopción</h1>

    <form method="post" action="?r=admin/campaign/save" class="card p" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
        <h2>Nueva / Editar Campaña</h2>
        
        <input type="hidden" name="id" id="campaign-id">
        
        <label>Título <input type="text" name="title" id="campaign-title" required></label>
        
        <label>Descripción <textarea name="description" id="campaign-desc"></textarea></label>
        
        <div class="row">
            <label>Fecha
                <input type="date" name="date" id="campaign-date" required 
                       min="<?php echo date('Y-m-d'); ?>">
            </label>
            
            <label>Ubicación
                <input type="text" name="location" id="campaign-location" required 
                       placeholder="Ej: Plaza de Maipú">
            </label>
        </div>
        
        <div class="row">
            <label>Fundación
                <input type="text" name="foundation" id="campaign-foundation" 
                       placeholder="Nombre de la fundación">
            </label>
            
            <label>Información de Contacto
                <input type="text" name="contact_info" id="campaign-contact" 
                       placeholder="WhatsApp, Instagram, etc.">
            </label>
        </div>
        
        <label>Imagen (URL)
            <input type="url" name="image_url" id="campaign-img" 
                   placeholder="assets/img/tu-imagen.jpg">
        </label>
        
        <input type="hidden" name="current_image_url" id="campaign-current-img">
        
        <label>Subir imagen (JPG/PNG, máx 3MB)
            <input type="file" name="image_file" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        </label>
        
        <img id="preview" alt="" style="max-width:160px;border-radius:8px;margin-top:8px;display:none;">
        
        <label>
            <input type="checkbox" name="is_active" id="campaign-active" checked> 
            Activa
        </label>
        
        <?php if (!empty($flash)): ?>
            <div class="alert <?php echo $flash['type']==='error' ? 'alert-error' : 'alert-success'; ?>">
                <?php foreach ($flash['messages'] as $m): ?>
                    <div><?php echo htmlspecialchars($m); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <button class="btn" type="submit">Guardar Campaña</button>
    </form>

    <div class="campaigns-list">
        <h2>Campañas Programadas</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Título</th>
                    <th>Ubicación</th>
                    <th>Fundación</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $c): ?>
                    <tr class="<?php echo strtotime($c['date']) < strtotime('today') ? 'past-event' : ''; ?>">
                        <td><?php echo date('d/m/Y', strtotime($c['date'])); ?></td>
                        <td><?php echo htmlspecialchars($c['title']); ?></td>
                        <td><?php echo htmlspecialchars($c['location']); ?></td>
                        <td><?php echo htmlspecialchars($c['foundation'] ?? '-'); ?></td>
                        <td>
                            <span class="status-badge <?php echo $c['is_active'] ? 'active' : 'inactive'; ?>">
                                <?php echo $c['is_active'] ? 'Activa' : 'Inactiva'; ?>
                            </span>
                        </td>
                        <td class="row">
                            <button class="btn-outline" onclick='prefillCampaign(<?php 
                                echo json_encode($c, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); 
                            ?>)'>Editar</button>
                            
                            <form method="post" action="?r=admin/campaign/delete" 
                                  style="display:inline" 
                                  onsubmit="return confirm('¿Eliminar esta campaña?')">
                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
                                <button class="btn-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.campaigns-admin {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
}

.past-event {
    opacity: 0.7;
    background-color: #f5f5f5;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
}

.status-badge.active {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.status-badge.inactive {
    background-color: #ffebee;
    color: #c62828;
}

.campaigns-list {
    margin-top: 2rem;
}

.campaigns-list h2 {
    margin-bottom: 1rem;
}
</style>

<script>
function prefillCampaign(c) {
    document.getElementById('campaign-id').value = c.id;
    document.getElementById('campaign-title').value = c.title;
    document.getElementById('campaign-desc').value = c.description || '';
    document.getElementById('campaign-date').value = c.date;
    document.getElementById('campaign-location').value = c.location || '';
    document.getElementById('campaign-foundation').value = c.foundation || '';
    document.getElementById('campaign-contact').value = c.contact_info || '';
    document.getElementById('campaign-img').value = c.image_url || '';
    document.getElementById('campaign-current-img').value = c.image_url || '';
    document.getElementById('campaign-active').checked = c.is_active == 1;
    
    const preview = document.getElementById('preview');
    if (c.image_url) {
        preview.src = c.image_url;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>