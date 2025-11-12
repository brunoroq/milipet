<?php ?>
<section class="hero-banner">
	<div class="hero-content">
		<h1>Todo para tu mejor amigo</h1>
		<p>Alimentos, accesorios y amor por las mascotas. Envíos en Maipú y retiro en tienda.</p>
	<a href="<?= url(['r' => 'catalog']) ?>" class="btn-large">Explorar catálogo</a>
	</div>
    
</section>

<section>
	<h2>Destacados</h2>
	<div class="grid">
		<?php foreach($featured as $p): ?>
			<article class="card">
				<img src="<?= image_src($p['image_url'] ?? null) ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
				<h3 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h3>
				<p class="card-category muted"><?php echo htmlspecialchars($p['category_name'] ?? ''); ?></p>
				<strong class="card-price">$<?php echo number_format($p['price'] ?? 0, 0, ',', '.'); ?></strong>
				<div class="row">
					<a class="btn" href="?r=product&id=<?php echo $p['id']; ?>">Ver</a>
					<button class="btn-outline" onclick="addToCart(<?php echo (int)$p['id']; ?>)">Añadir</button>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>