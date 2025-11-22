<?php ?>
<!-- Header de la página -->
<div class="mb-4">
	<h2 class="display-5 fw-bold mb-2">
		<i class="fa-solid fa-cart-shopping me-2 text-success"></i>Carrito de Compras
	</h2>
	<p class="small text-muted mb-0">
		Productos seleccionados<?php if(!empty($hasSession)): ?> (guardado en tu cuenta)<?php endif; ?>. Puedes generar un mensaje para coordinar tu compra por WhatsApp.
	</p>
</div>

<?php if (empty($products)): ?>
	<!-- Estado vacío -->
	<div class="text-center py-5">
		<i class="fa-solid fa-cart-shopping fa-4x text-muted mb-3 opacity-25"></i>
		<h3 class="h5 mb-3">Tu carrito está vacío</h3>
		<p class="text-muted mb-4">Agrega productos desde nuestro catálogo</p>
		<a class="btn btn-success rounded-pill px-4" href="<?= url(['r'=>'catalog']) ?>">
			<i class="fa-solid fa-store me-2"></i>Ir al catálogo
		</a>
	</div>
<?php else: ?>
	<?php 
	// Calcular totales
	$total = 0; 
	$itemCount = count($products);
	foreach($products as $p){ 
		$total += (float)($p['price'] ?? 0); 
	} 
	
	// Preparar mensaje de WhatsApp
	$waBase = 'https://wa.me/+56957153992?text='; 
	$msg = 'Hola%20MiliPet%2C%20estoy%20interesado%20en%20los%20siguientes%20productos%3A%0A%0A'; 
	foreach($products as $p){ 
		$msg .= '%E2%80%A2%20' . rawurlencode($p['name']) . '%20-%20%24' . rawurlencode(number_format($p['price']??0,0,',','.')) . '%0A'; 
	} 
	$msg .= '%0A%2ATotal%20estimado%3A%20%24' . rawurlencode(number_format($total,0,',','.')) . '%2A';
	?>
	
	<!-- Layout de dos columnas -->
	<div class="row mt-4 g-4">
		<!-- Columna izquierda: Lista de productos -->
		<div class="col-lg-8">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h5 class="fw-semibold mb-0">Productos (<?= $itemCount ?>)</h5>
				<button class="btn btn-sm btn-outline-secondary" onclick="if(confirm('¿Vaciar todo el carrito?')){ localStorage.removeItem('milipet_cart'); window.location.reload(); }">
					<i class="fa-solid fa-trash-can me-1"></i>Vaciar carrito
				</button>
			</div>
			
			<!-- Items del carrito -->
			<div class="cart-items-list">
				<?php foreach($products as $p): ?>
				<div class="cart-item d-flex align-items-center justify-content-between mb-3 p-3 rounded-4 shadow-sm">
					<!-- Sección izquierda: imagen + info -->
					<div class="d-flex align-items-center gap-3 flex-grow-1">
						<img 
							src="<?= image_src($p['image_url'] ?? null) ?>" 
							alt="<?= htmlspecialchars($p['name']) ?>"
							class="cart-thumb rounded-3"
						>
						<div class="cart-item-info">
							<h6 class="mb-1 fw-semibold">
								<a href="<?= url(['r'=>'product','id'=>(int)$p['id']]) ?>" class="text-decoration-none text-dark">
									<?= htmlspecialchars($p['name']) ?>
								</a>
							</h6>
							<p class="small text-muted mb-0">
								<?php if (!empty($p['species_name'])): ?>
									<i class="fa-solid fa-paw me-1"></i><?= htmlspecialchars($p['species_name']) ?>
								<?php endif; ?>
								<?php if (!empty($p['category_name'])): ?>
									<?php if (!empty($p['species_name'])): ?><span class="mx-1">•</span><?php endif; ?>
									<i class="fa-solid fa-tag me-1"></i><?= htmlspecialchars($p['category_name']) ?>
								<?php endif; ?>
							</p>
						</div>
					</div>
					
					<!-- Sección derecha: precio + acciones -->
					<div class="d-flex align-items-center gap-3">
						<div class="text-end">
							<div class="small text-muted">Precio</div>
							<div class="fw-bold text-success fs-5">$<?= number_format($p['price'] ?? 0, 0, ',', '.') ?></div>
						</div>
						<div class="d-flex flex-column gap-2">
							<a class="btn btn-sm btn-outline-success" href="<?= url(['r'=>'product','id'=>(int)$p['id']]) ?>">
								<i class="fa-solid fa-eye me-1"></i>Ver
							</a>
							<button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(<?= (int)$p['id'] ?>); setTimeout(refreshCartPage, 50);">
								<i class="fa-solid fa-trash-can me-1"></i>Quitar
							</button>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		
		<!-- Columna derecha: Resumen -->
		<div class="col-lg-4">
			<div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
				<h5 class="mb-3 fw-bold">
					<i class="fa-solid fa-receipt me-2 text-success"></i>Resumen de tu carrito
				</h5>
				
				<div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
					<span class="text-muted">Productos</span>
					<span class="fw-semibold"><?= $itemCount ?> <?= $itemCount === 1 ? 'ítem' : 'ítems' ?></span>
				</div>
				
				<div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
					<span class="text-muted">Total estimado</span>
					<span class="fw-bold text-success fs-4">$<?= number_format($total, 0, ',', '.') ?></span>
				</div>
				
				<a class="btn btn-success w-100 mb-3 py-2" target="_blank" href="<?= $waBase . $msg ?>">
					<i class="fa-brands fa-whatsapp me-2 fs-5"></i>Enviar lista por WhatsApp
				</a>
				
				<div class="alert alert-light border-0 mb-0 small">
					<i class="fa-solid fa-circle-info me-2 text-info"></i>
					La compra se coordina directamente por WhatsApp. Precios y disponibilidad pueden variar.
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
