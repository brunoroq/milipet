<?php ?>
<div class="container py-4">
	<!-- Page Header -->
	<div class="text-center mb-4">
		<h1 class="display-5 fw-bold mb-2">Catálogo de Productos</h1>
		<p class="text-muted">Encuentra todo lo que tu mascota necesita</p>
	</div>

	<!-- Filters Card -->
	<div class="card shadow-sm border-0 mb-4">
		<div class="card-body p-4">
			<form method="get" action="<?= url(['r' => 'catalog']) ?>">
				<input type="hidden" name="r" value="catalog">
				
				<div class="row g-3 align-items-end">
					<!-- Search Input -->
					<div class="col-md-3">
						<label for="search-input" class="form-label fw-semibold">
							<i class="fas fa-search me-1"></i>Buscar
						</label>
						<input type="text" 
						       id="search-input"
						       name="q"
						       class="form-control"
						       value="<?= htmlspecialchars($query ?? '', ENT_QUOTES, 'UTF-8') ?>"
						       placeholder="Nombre del producto...">
					</div>

					<!-- Category Select -->
					<div class="col-md-3">
						<label for="category-select" class="form-label fw-semibold">
							<i class="fas fa-tags me-1"></i>Categoría
						</label>
						<select name="category" id="category-select" class="form-select">
							<option value="0" <?= (($category ?? null) === null ? 'selected' : '') ?>>Todas</option>
							<?php foreach($categories as $c): ?>
							<option value="<?= (int)$c['id'] ?>" <?= (((int)($category ?? 0) === (int)$c['id']) ? 'selected' : '') ?>>
								<?= htmlspecialchars($c['name']) ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- Species Select -->
					<div class="col-md-2">
						<label for="species-select" class="form-label fw-semibold">
							<i class="fas fa-paw me-1"></i>Especie
						</label>
						<select name="species" id="species-select" class="form-select">
							<option value="0" <?= (($species ?? null) === null ? 'selected' : '') ?>>Todas</option>
							<?php if (!empty($speciesList ?? [])): ?>
								<?php foreach ($speciesList as $s): ?>
								<option value="<?= (int)$s['id'] ?>" <?= (((int)($species ?? 0) === (int)$s['id']) ? 'selected' : '') ?>>
									<?= htmlspecialchars($s['name'] ?? '') ?>
								</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>

					<!-- Stock Filter -->
					<div class="col-md-2">
						<div class="form-check mt-4">
							<input class="form-check-input" 
							       type="checkbox" 
							       name="stock" 
							       value="1" 
							       id="stock-check"
							       <?= !empty($in_stock ?? null) ? 'checked' : '' ?>>
							<label class="form-check-label fw-semibold" for="stock-check">
								Solo con stock
							</label>
						</div>
					</div>

					<!-- Action Buttons -->
					<div class="col-md-2">
						<div class="d-grid gap-2">
							<button class="btn btn-success" type="submit">
								<i class="fas fa-filter me-1"></i>Filtrar
							</button>
							<?php if ((($query ?? '') !== '') || !empty($category ?? null) || !empty($species ?? null) || !empty($in_stock ?? null)): ?>
							<a href="<?= url(['r' => 'catalog']) ?>" class="btn btn-outline-secondary btn-sm">
								<i class="fas fa-times me-1"></i>Limpiar
							</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<?php if ((($query ?? '') !== '') && empty($products)): ?>
	<!-- No Results -->
	<div class="text-center py-5">
		<i class="fas fa-search display-1 text-muted mb-3"></i>
		<h3 class="fw-bold mb-2">No se encontraron resultados</h3>
		<p class="text-muted mb-4">
			No encontramos productos para "<strong><?= htmlspecialchars($query ?? '', ENT_QUOTES, 'UTF-8') ?></strong>"
		</p>

		<?php if (!empty($suggested ?? [])): ?>
		<hr class="my-4" style="max-width: 200px; margin: 0 auto;">
		<h4 class="fw-semibold mb-4">Tal vez te guste</h4>
		
		<div class="row g-4 justify-content-center">
			<?php foreach ($suggested as $s): ?>
			<div class="col-12 col-sm-6 col-lg-3">
				<div class="card product-card h-100 shadow-sm border-0">
					<img src="<?= image_src($s['image_url'] ?? null) ?>"
					     alt="<?= htmlspecialchars($s['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
					     class="card-img-top product-image">
					<div class="card-body">
						<h5 class="card-title fw-bold"><?= htmlspecialchars($s['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h5>
						<p class="text-muted small"><?= htmlspecialchars($s['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
						<p class="product-price">$<?= number_format((float)($s['price'] ?? 0), 0, ',', '.') ?></p>
						<a href="<?= url(['r'=>'product','id'=>(int)$s['id']]) ?>" class="btn btn-success btn-sm w-100">
							<i class="fas fa-eye me-1"></i>Ver
						</a>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php else: ?>
	<!-- Products Grid - Centered -->
	<div class="row g-4 justify-content-center">
		<?php foreach($products as $p): ?>
		<div class="col-12 col-sm-6 col-lg-4 col-xl-3">
			<article class="card product-card h-100 shadow-sm border-0 <?= ($p['stock'] <= 0 ? 'out-of-stock' : '') ?>">
				<!-- Product Image -->
				<div class="product-image-wrapper position-relative">
					<img src="<?= image_src($p['image_url'] ?? null) ?>" 
					     alt="<?= htmlspecialchars($p['name']) ?>" 
					     class="card-img-top product-image">
					
					<!-- Favorite & Cart Icons -->
					<div class="product-actions position-absolute top-0 start-0 w-100 p-2 d-flex justify-content-between">
						<button type="button" 
						        class="btn btn-sm btn-light rounded-circle shadow-sm fav-btn" 
						        data-fav-id="<?= (int)$p['id'] ?>" 
						        onclick="toggleFav(<?= (int)$p['id'] ?>)" 
						        aria-pressed="false"
						        title="Favorito">
							<i class="fa-regular fa-heart"></i>
						</button>
						<?php if (($p['stock'] ?? 0) > 0): ?>
						<button type="button" 
						        class="btn btn-sm btn-light rounded-circle shadow-sm" 
						        onclick="addToCart(<?= (int)$p['id'] ?>)"
						        title="Añadir al carrito">
							<i class="fas fa-cart-plus"></i>
						</button>
						<?php endif; ?>
					</div>
				</div>

				<div class="card-body d-flex flex-column">
					<!-- Category Badge -->
					<?php if (!empty($p['category_name'])): ?>
					<span class="badge bg-light text-muted mb-2 align-self-start small">
						<?= htmlspecialchars($p['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
					</span>
					<?php endif; ?>

					<!-- Product Title -->
					<h5 class="card-title fw-bold mb-2">
						<?= htmlspecialchars($p['name']) ?>
					</h5>

					<!-- Stock & Pickup Badges -->
					<div class="d-flex flex-wrap gap-2 mb-3">
						<?php if (($p['stock'] ?? 0) > 0): ?>
						<span class="badge badge-stock">
							<i class="fas fa-check-circle me-1"></i><?= (int)$p['stock'] ?> unid.
						</span>
						<span class="badge badge-pickup">
							<i class="fas fa-store me-1"></i>Retiro hoy
						</span>
						<?php else: ?>
						<span class="badge badge-out-stock">
							<i class="fas fa-times-circle me-1"></i>Sin stock
						</span>
						<?php endif; ?>
					</div>

					<!-- Price -->
					<div class="price-wrapper mt-auto mb-3">
						<span class="product-price">
							$<?= number_format($p['price'] ?? 0, 0, ',', '.') ?>
						</span>
					</div>

					<!-- Action Button -->
					<a class="btn btn-success w-100" href="<?= url(['r' => 'product', 'id' => (int)$p['id']]) ?>">
						<i class="fas fa-eye me-2"></i>Ver detalles
					</a>
				</div>
			</article>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>
