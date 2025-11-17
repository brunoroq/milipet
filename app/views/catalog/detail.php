<?php ?>
<section class="main-section home-section-bg py-5">
    <div class="container">
        <div class="row g-5 align-items-start justify-content-center">
            <!-- Left Column: Product Image -->
            <div class="col-md-5 col-lg-4">
                <div class="product-detail-image card border-0 shadow-sm rounded-4 p-3">
                    <img src="<?= image_src($product['image_url'] ?? null) ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         class="img-fluid rounded-4">
                </div>
            </div>

            <!-- Right Column: Product Info -->
            <div class="col-md-7 col-lg-6">
                <!-- Breadcrumb -->
                <p class="text-muted small mb-2">
                    <a href="<?= url(['r' => 'catalog']) ?>" class="text-decoration-none text-success">Catálogo</a>
                    / <?= htmlspecialchars($product['name']) ?>
                </p>

                <!-- Product Title -->
                <h1 class="display-5 fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>

                <!-- Category -->
                <p class="text-muted mb-3"><?= htmlspecialchars($product['category_name'] ?? '') ?></p>

                <!-- Price -->
                <p class="product-price h3 fw-bold text-dark mb-2">$<?= number_format($product['price'] ?? 0, 0, ',', '.') ?></p>

                <?php
                $STORE_ADDRESS = defined('STORE_ADDRESS') ? STORE_ADDRESS : 'Maipú, Chile';
                $PICKUP_MSG    = defined('STORE_PICKUP_MESSAGE') ? STORE_PICKUP_MESSAGE : 'Retiro en tienda Gratis • Disponible hoy';
                $inStock = (int)($product['stock'] ?? 0) > 0;
                ?>

                <!-- Stock & Pickup Badges -->
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <?php if ($inStock): ?>
                        <span class="badge badge-stock">
                            <i class="fas fa-check-circle me-1"></i><?= (int)$product['stock'] ?> unid.
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

                <!-- Action Buttons: Cart + Favorites -->
                <div class="d-flex flex-wrap gap-3 align-items-center mb-4">
                    <button class="btn btn-success rounded-pill px-4 <?= $inStock ? '' : 'disabled' ?>"
                            onclick="addToCart(<?= (int)$product['id'] ?>)"
                            <?= $inStock ? '' : 'disabled' ?>>
                        <i class="fas fa-cart-plus me-2"></i><?= $inStock ? 'Añadir al carrito' : 'Sin stock' ?>
                    </button>
                    <button class="btn btn-favorite"
                            data-fav-id="<?= (int)$product['id'] ?>"
                            onclick="toggleFav(<?= (int)$product['id'] ?>)"
                            aria-pressed="false">
                        <i class="fa-regular fa-heart"></i>
                        <span>Agregar a favoritos</span>
                    </button>
                </div>

                <!-- Back to Catalog -->
                <a href="<?= url(['r' => 'catalog']) ?>" class="btn btn-outline-secondary rounded-pill mb-4">
                    ← Volver al catálogo
                </a>

                <!-- Description -->
                <?php if (!empty($product['long_desc'])): ?>
                <div class="mt-4">
                    <h2 class="h5 fw-semibold mb-2">Descripción</h2>
                    <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($product['long_desc'])) ?></p>
                </div>
                <?php endif; ?>

                <!-- Pickup Info & Delivery Options (only when in stock) -->
                <?php if ($inStock): ?>
                <div class="mt-4 p-4 bg-light rounded-4">
                    <p class="text-muted mb-3">
                        <i class="fas fa-store text-success me-2"></i><?= htmlspecialchars($PICKUP_MSG) ?> — <?= htmlspecialchars($STORE_ADDRESS) ?>
                    </p>
                    <?php if (defined('STORE_HOURS') && is_array(STORE_HOURS)): ?>
                    <p class="text-muted small mb-1"><i class="fas fa-clock text-success me-2"></i>Horarios:</p>
                    <ul class="list-unstyled text-muted small ps-4">
                        <?php foreach (STORE_HOURS as $day => $hours): ?>
                            <li><?= htmlspecialchars($day) ?>: <?= htmlspecialchars($hours) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="alert alert-warning mt-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>Este producto está temporalmente agotado.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* Product Detail Image */
.product-detail-image img {
    object-fit: contain;
    width: 100%;
    background: #fff;
}

@media (max-width: 767px) {
    .product-detail-image {
        margin-bottom: 1.5rem;
    }
}
</style>