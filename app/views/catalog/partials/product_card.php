<?php
/* Product card partial expects $product array */
?>
<article class="card product-card h-100 shadow-sm border-0 <?= (($product['stock'] ?? 0) <= 0 ? 'out-of-stock' : '') ?>">
	<div class="product-image-wrapper position-relative">
		<img src="<?= image_src($product['image_url'] ?? null) ?>" alt="<?= htmlspecialchars($product['name'] ?? '') ?>" class="card-img-top product-image">
		<div class="product-actions position-absolute top-0 start-0 w-100 p-2 d-flex justify-content-between">
			<button type="button" class="btn btn-favorite" data-fav-id="<?= (int)($product['id'] ?? 0) ?>" onclick="toggleFav(<?= (int)($product['id'] ?? 0) ?>)" aria-pressed="false" title="Agregar a favoritos">
				<i class="fa-regular fa-heart"></i><span>Agregar a favoritos</span>
			</button>
			<?php if (($product['stock'] ?? 0) > 0): ?>
				<button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="addToCart(<?= (int)$product['id'] ?>)" title="Añadir al carrito">
					<i class="fas fa-cart-plus"></i>
				</button>
			<?php endif; ?>
		</div>
	</div>
	<div class="card-body d-flex flex-column">
		<?php if (!empty($product['category_name'])): ?>
			<span class="badge bg-light text-muted mb-2 align-self-start small"><?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
		<?php endif; ?>
		<h5 class="card-title fw-bold mb-2"><?= htmlspecialchars($product['name'] ?? '') ?></h5>
		<div class="d-flex flex-wrap gap-2 mb-3">
			<?php if (($product['stock'] ?? 0) > 0): ?>
				<span class="badge badge-stock"><i class="fas fa-check-circle me-1"></i><?= (int)$product['stock'] ?> unid.</span>
				<span class="badge badge-pickup"><i class="fas fa-store me-1"></i>Retiro hoy</span>
			<?php else: ?>
				<span class="badge badge-out-stock"><i class="fas fa-times-circle me-1"></i>Sin stock</span>
			<?php endif; ?>
		</div>
		<div class="price-wrapper mt-auto mb-3"><span class="product-price">$<?= number_format($product['price'] ?? 0, 0, ',', '.') ?></span></div>
		<a class="btn btn-success w-100" href="<?= url(['r' => 'product', 'id' => (int)($product['id'] ?? 0)]) ?>"><i class="fas fa-eye me-2"></i>Ver detalles</a>
	</div>
</article>