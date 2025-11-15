<?php ?>
<div class="campaigns-admin">
    <h1>Campañas de Adopción</h1>

    <form method="post" action="?r=admin/campaign/save" class="card p" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['_csrf'] ?? ($_SESSION['csrf'] ?? '')); ?>">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? ''); ?>">
        <h2>Nueva / Editar Campaña</h2>
        
        <input type="hidden" name="id" id="campaign-id">
        
        <label>Título <input type="text" name="title" id="campaign-title" required></label>
        
        <label>Descripción <textarea name="description" id="campaign-desc"></textarea></label>
        
        <div class="row">
            <label>Fecha de Inicio
                <input type="date" name="start_date" id="campaign-start-date" 
                       min="<?php echo date('Y-m-d'); ?>">
            </label>
            
            <label>Fecha de Fin
                <input type="date" name="end_date" id="campaign-end-date">
            </label>
        </div>
        
        <label>Banner (URL)
            <input type="url" name="banner_image" id="campaign-banner" 
                   placeholder="assets/img/banner-campana.jpg">
        </label>
        
        <input type="hidden" name="current_banner_image" id="campaign-current-banner">
        
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
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $c): ?>
                    <?php 
                    $isPast = !empty($c['end_date']) && strtotime($c['end_date']) < strtotime('today');
                    $isCurrent = (empty($c['start_date']) || strtotime($c['start_date']) <= time()) && 
                                 (empty($c['end_date']) || strtotime($c['end_date']) >= strtotime('today'));
                    ?>
                    <tr class="<?php echo $isPast ? 'past-event' : ($isCurrent ? 'current-event' : ''); ?>">
                        <td><?php echo !empty($c['start_date']) ? date('d/m/Y', strtotime($c['start_date'])) : '-'; ?></td>
                        <td><?php echo !empty($c['end_date']) ? date('d/m/Y', strtotime($c['end_date'])) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($c['title']); ?></td>
                        <td><?php echo htmlspecialchars(mb_substr($c['description'] ?? '', 0, 50)); ?><?php echo mb_strlen($c['description'] ?? '') > 50 ? '...' : ''; ?></td>
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
                                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['_csrf'] ?? ($_SESSION['csrf'] ?? '')); ?>">
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

.current-event {
    background-color: #e8f5e9;
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
    document.getElementById('campaign-start-date').value = c.start_date || '';
    document.getElementById('campaign-end-date').value = c.end_date || '';
    document.getElementById('campaign-banner').value = c.banner_image || '';
    document.getElementById('campaign-current-banner').value = c.banner_image || '';
    document.getElementById('campaign-active').checked = c.is_active == 1;
    
    const preview = document.getElementById('preview');
    if (c.banner_image) {
        preview.src = c.banner_image;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>