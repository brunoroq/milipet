<?php
/**
 * Vista de administración de Hero Slides
 * Gestiona las imágenes del carousel del home
 */

require_once __DIR__ . '/../../helpers/auth_helper.php';
require_admin();

$pageTitle = 'Gestión de Slides del Hero';
$slides = $slides ?? [];
$editSlide = $editSlide ?? null;
?>

<?php ob_start(); ?>

<div class="container-fluid py-4">
	<div class="row mb-4">
		<div class="col">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h1 class="h3 mb-1">
						<i class="fas fa-images text-primary me-2"></i>
						Gestión de Slides del Hero
					</h1>
					<p class="text-muted mb-0">
						<i class="fas fa-info-circle me-1"></i>
						Administra las imágenes del carousel principal de la página de inicio
					</p>
				</div>
				<a href="<?= url(['r' => 'admin/dashboard']) ?>" class="btn btn-outline-secondary">
					<i class="fas fa-arrow-left me-2"></i>Volver al dashboard
				</a>
			</div>
		</div>
	</div>

	<!-- Mensajes flash -->
	<?php if (isset($_SESSION['message'])): ?>
	<div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
		<i class="fas fa-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
		<?= htmlspecialchars($_SESSION['message']) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
	</div>
	<?php 
		unset($_SESSION['message'], $_SESSION['message_type']); 
	endif; 
	?>

	<div class="row">
		<div class="col-lg-8">
			<!-- Lista de Slides -->
			<div class="card shadow-sm mb-4">
				<div class="card-header bg-white py-3">
					<h5 class="mb-0">
						<i class="fas fa-list me-2"></i>Slides Registrados
					</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($slides)): ?>
					<div class="text-center py-5">
						<i class="fas fa-image text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
						<p class="text-muted mt-3 mb-0">No hay slides registrados aún</p>
						<small class="text-muted">Usa el formulario de la derecha para crear el primer slide</small>
					</div>
					<?php else: ?>
					<div class="table-responsive">
						<table class="table table-hover align-middle mb-0">
							<thead class="table-light">
								<tr>
									<th style="width: 80px;">Vista previa</th>
									<th>Imagen URL</th>
									<th style="width: 100px;" class="text-center">Orden</th>
									<th style="width: 90px;" class="text-center">Estado</th>
									<th style="width: 120px;" class="text-center">Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($slides as $slide): ?>
								<tr>
									<td>
										<img src="<?= htmlspecialchars($slide['image_url']) ?>" 
										     alt="Slide <?= $slide['id'] ?>" 
										     class="img-thumbnail"
										     style="width: 60px; height: 40px; object-fit: cover;">
									</td>
									<td>
										<div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
											<?= htmlspecialchars($slide['image_url']) ?>
										</div>
										<?php if (!empty($slide['title'])): ?>
											<small class="text-muted d-block">
												<i class="fas fa-heading me-1"></i>
												<?= htmlspecialchars($slide['title']) ?>
											</small>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<span class="badge bg-secondary"><?= (int)$slide['sort_order'] ?></span>
									</td>
									<td class="text-center">
										<?php if ($slide['is_active']): ?>
											<span class="badge bg-success">
												<i class="fas fa-check-circle me-1"></i>Activo
											</span>
										<?php else: ?>
											<span class="badge bg-secondary">
												<i class="fas fa-times-circle me-1"></i>Inactivo
											</span>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<div class="btn-group btn-group-sm" role="group">
											<a href="<?= url(['r' => 'admin/hero-slides', 'edit' => $slide['id']]) ?>" 
											   class="btn btn-outline-primary"
											   title="Editar">
												<i class="fas fa-edit"></i>
											</a>
											<button type="button" 
											        class="btn btn-outline-danger" 
											        onclick="confirmDelete(<?= $slide['id'] ?>, '<?= htmlspecialchars($slide['image_url'], ENT_QUOTES) ?>')"
											        title="Eliminar">
												<i class="fas fa-trash"></i>
											</button>
										</div>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Info Card -->
			<div class="card shadow-sm border-primary">
				<div class="card-body">
					<h6 class="card-title text-primary">
						<i class="fas fa-lightbulb me-2"></i>Consejos para mejores resultados
					</h6>
					<ul class="mb-0 small">
						<li><strong>Resolución recomendada:</strong> Mínimo 1200x800px (ratio 3:2)</li>
						<li><strong>Peso máximo:</strong> 500KB para tiempos de carga óptimos</li>
						<li><strong>Formato:</strong> JPG o WebP para mejor compresión</li>
						<li><strong>Orden:</strong> Los slides se muestran según el número de orden (menor primero)</li>
						<li><strong>Estado:</strong> Solo los slides activos se muestran en el sitio</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<!-- Formulario Create/Edit -->
			<div class="card shadow-sm sticky-top" style="top: 90px;">
				<div class="card-header bg-primary text-white py-3">
					<h5 class="mb-0">
						<i class="fas fa-<?= $editSlide ? 'edit' : 'plus-circle' ?> me-2"></i>
						<?= $editSlide ? 'Editar Slide' : 'Nuevo Slide' ?>
					</h5>
				</div>
				<div class="card-body">
					<form action="<?= url(['r' => 'admin/hero-slides/save']) ?>" method="POST">
						<?php if ($editSlide): ?>
						<input type="hidden" name="id" value="<?= $editSlide['id'] ?>">
						<?php endif; ?>

						<!-- URL de la imagen (requerido) -->
						<div class="mb-3">
							<label for="image_url" class="form-label fw-bold">
								URL de la imagen <span class="text-danger">*</span>
							</label>
							<input type="url" 
							       class="form-control" 
							       id="image_url" 
							       name="image_url" 
							       value="<?= htmlspecialchars($editSlide['image_url'] ?? '') ?>"
							       placeholder="https://ejemplo.com/imagen.jpg"
							       required>
							<div class="form-text">
								<i class="fas fa-info-circle me-1"></i>
								URL completa de la imagen (debe empezar con http:// o https://)
							</div>
						</div>

						<!-- Título (opcional) -->
						<div class="mb-3">
							<label for="title" class="form-label fw-bold">Título (opcional)</label>
							<input type="text" 
							       class="form-control" 
							       id="title" 
							       name="title" 
							       value="<?= htmlspecialchars($editSlide['title'] ?? '') ?>"
							       placeholder="Título descriptivo">
							<div class="form-text">Solo para referencia interna</div>
						</div>

						<!-- Subtítulo (opcional) -->
						<div class="mb-3">
							<label for="subtitle" class="form-label fw-bold">Subtítulo (opcional)</label>
							<input type="text" 
							       class="form-control" 
							       id="subtitle" 
							       name="subtitle" 
							       value="<?= htmlspecialchars($editSlide['subtitle'] ?? '') ?>"
							       placeholder="Subtítulo descriptivo">
							<div class="form-text">Solo para referencia interna</div>
						</div>

						<!-- Orden -->
						<div class="mb-3">
							<label for="sort_order" class="form-label fw-bold">Orden de visualización</label>
							<input type="number" 
							       class="form-control" 
							       id="sort_order" 
							       name="sort_order" 
							       value="<?= isset($editSlide['sort_order']) ? (int)$editSlide['sort_order'] : 0 ?>"
							       min="0"
							       step="1">
							<div class="form-text">
								<i class="fas fa-sort-numeric-down me-1"></i>
								Los slides se ordenan de menor a mayor (0, 1, 2...)
							</div>
						</div>

						<!-- Estado activo -->
						<div class="mb-4">
							<div class="form-check form-switch">
								<input class="form-check-input" 
								       type="checkbox" 
								       id="is_active" 
								       name="is_active" 
								       value="1"
								       <?= isset($editSlide) ? ($editSlide['is_active'] ? 'checked' : '') : 'checked' ?>>
								<label class="form-check-label fw-bold" for="is_active">
									<i class="fas fa-eye me-1"></i>Slide activo
								</label>
							</div>
							<small class="text-muted d-block mt-1">
								Solo los slides activos se muestran en el sitio público
							</small>
						</div>

						<!-- Botones de acción -->
						<div class="d-grid gap-2">
							<button type="submit" class="btn btn-primary">
								<i class="fas fa-save me-2"></i>
								<?= $editSlide ? 'Actualizar slide' : 'Crear slide' ?>
							</button>
							<?php if ($editSlide): ?>
							<a href="<?= url(['r' => 'admin/hero-slides']) ?>" class="btn btn-secondary">
								<i class="fas fa-times me-2"></i>Cancelar edición
							</a>
							<?php endif; ?>
						</div>
					</form>

					<!-- Vista previa de la imagen -->
					<?php if ($editSlide && !empty($editSlide['image_url'])): ?>
					<div class="mt-4 pt-3 border-top">
						<h6 class="text-muted mb-2">Vista previa actual:</h6>
						<img src="<?= htmlspecialchars($editSlide['image_url']) ?>" 
						     alt="Vista previa" 
						     class="img-fluid rounded shadow-sm"
						     style="max-height: 200px; width: 100%; object-fit: cover;">
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-danger text-white">
				<h5 class="modal-title" id="deleteModalLabel">
					<i class="fas fa-exclamation-triangle me-2"></i>Confirmar eliminación
				</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>
			<div class="modal-body">
				<p class="mb-2">¿Estás seguro de que deseas eliminar este slide?</p>
				<p class="text-muted mb-0 small">
					<strong>Imagen:</strong> <span id="deleteImageUrl"></span>
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
					<i class="fas fa-times me-2"></i>Cancelar
				</button>
				<form id="deleteForm" method="POST" style="display: inline;">
					<button type="submit" class="btn btn-danger">
						<i class="fas fa-trash me-2"></i>Eliminar definitivamente
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
// Confirmación de eliminación
function confirmDelete(slideId, imageUrl) {
	const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
	const form = document.getElementById('deleteForm');
	const urlDisplay = document.getElementById('deleteImageUrl');
	
	// Actualizar form action
	form.action = '<?= url(['r' => 'admin/hero-slides/delete']) ?>?id=' + slideId;
	
	// Mostrar URL truncada
	const maxLength = 50;
	urlDisplay.textContent = imageUrl.length > maxLength 
		? imageUrl.substring(0, maxLength) + '...' 
		: imageUrl;
	
	modal.show();
}

// Auto-hide alerts después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
	const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
	alerts.forEach(function(alert) {
		setTimeout(function() {
			const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
			bsAlert.close();
		}, 5000);
	});
});
</script>

<?php 
$content = ob_get_clean(); 
require __DIR__ . '/../layout/admin_layout.php';
?>
