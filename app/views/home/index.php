<?php ?>
<?php ?>
<section class="hero-banner">
	<div class="hero-content">
		<h1>Todo para tu mejor amigo</h1>
		<p>Alimentos, accesorios y amor por las mascotas. Envíos en Maipú y retiro en tienda.</p>
		<a href="<?= url(['r' => 'catalog']) ?>" class="btn-large">Explorar catálogo</a>
	</div>
</section>

<section class="py-5">
	<div class="container">
		<!-- Section Header -->
		<div class="text-center mb-5">
			<p class="text-uppercase text-success fw-semibold mb-2 letter-spacing-1">
				<i class="fas fa-star me-2"></i>Ofertas especiales
			</p>
			<h2 class="display-6 fw-bold mb-3">Productos Destacados</h2>
			<p class="text-muted mx-auto" style="max-width: 600px;">
				Los productos favoritos de nuestros clientes, seleccionados especialmente para tu mascota.
			</p>
		</div>

		<?php if (!empty($featured)): ?>
		<!-- Products Grid - Centered -->
		<div class="row g-4 justify-content-center">
			<?php foreach($featured as $p): ?>
			<div class="col-12 col-sm-6 col-lg-4 col-xl-3">
				<article class="card product-card h-100 shadow-sm border-0">
					<!-- Product Image -->
					<div class="product-image-wrapper position-relative">
						<img src="<?= image_src($p['image_url'] ?? null) ?>" 
						     alt="<?= htmlspecialchars($p['name']) ?>" 
						     class="card-img-top product-image">
						<?php if (!empty($p['is_featured'])): ?>
						<span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">
							<i class="fas fa-star me-1"></i>Destacado
						</span>
						<?php endif; ?>
					</div>

					<div class="card-body d-flex flex-column">
						<!-- Category Badge -->
						<?php if (!empty($p['category_name'])): ?>
						<span class="badge bg-light text-muted mb-2 align-self-start">
							<?= htmlspecialchars($p['category_name']) ?>
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
								<i class="fas fa-check-circle me-1"></i><?= (int)$p['stock'] ?> disponibles
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

						<!-- Actions -->
						<div class="d-grid gap-2">
							<a class="btn btn-success" href="<?= url(['r' => 'product', 'id' => (int)$p['id']]) ?>">
								<i class="fas fa-eye me-2"></i>Ver detalles
							</a>
							<?php if (($p['stock'] ?? 0) > 0): ?>
							<button class="btn btn-outline-success" onclick="addToCart(<?= (int)$p['id'] ?>)">
								<i class="fas fa-cart-plus me-2"></i>Añadir al carrito
							</button>
							<?php endif; ?>
						</div>
					</div>
				</article>
			</div>
			<?php endforeach; ?>
		</div>
		<?php else: ?>
		<!-- Empty State -->
		<div class="text-center py-5">
			<div class="empty-state mx-auto" style="max-width: 400px;">
				<i class="fas fa-box-open display-1 text-muted mb-3"></i>
				<h4 class="fw-bold mb-3">No hay productos destacados</h4>
				<p class="text-muted mb-4">
					Estamos preparando ofertas especiales para ti. Visita nuestro catálogo completo.
				</p>
				<a href="<?= url(['r' => 'catalog']) ?>" class="btn btn-success">
					<i class="fas fa-shopping-bag me-2"></i>Ver catálogo completo
				</a>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>