<?php ?>
<article class="detail">
    <img src="<?= image_src($product['image_url'] ?? null) ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    <div>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="muted"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></p>
        
        <?php if (!empty($product['short_desc'])): ?>
            <p class="product-short-desc"><?php echo htmlspecialchars($product['short_desc']); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($product['long_desc'])): ?>
            <div class="product-long-desc">
                <?php echo nl2br(htmlspecialchars($product['long_desc'])); ?>
            </div>
        <?php endif; ?>
        
        <strong>$<?php echo number_format($product['price'] ?? 0, 0, ',', '.'); ?></strong>
        
        <?php if ($product['stock'] <= 0): ?>
            <p class="stock-status out">Sin stock</p>
            <div class="notify-form">
                <p>Este producto está temporalmente agotado.</p>
                <!-- Aquí se podría agregar un formulario de notificación cuando haya stock -->
            </div>
        <?php else: ?>
            <?php
            $STORE_ADDRESS = defined('STORE_ADDRESS') ? STORE_ADDRESS : 'Maipú, Chile';
            $PICKUP_MSG    = defined('STORE_PICKUP_MESSAGE') ? STORE_PICKUP_MESSAGE : 'Retiro en tienda Gratis • Disponible hoy';
            ?>
            <span class="badge-stock">Stock disponible: <?php echo (int)$product['stock']; ?> unidades</span>
            <p class="muted"><?php echo htmlspecialchars($PICKUP_MSG); ?> — <?php echo htmlspecialchars($STORE_ADDRESS); ?></p>
            <div class="product-actions">
                <div class="delivery-options">
                    <h3>Opciones de entrega</h3>
                    <div class="option-wrapper">
                        <label class="delivery-option">
                            <input type="radio" name="delivery" value="store" checked>
                            <div class="option-content">
                                <i class="fas fa-store"></i>
                                <div class="option-text">
                                    <strong>Retiro en tienda</strong>
                                    <span><?php echo htmlspecialchars($PICKUP_MSG); ?></span>
                                </div>
                            </div>
                        </label>
                        <div class="store-info">
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($STORE_ADDRESS); ?></p>
                            <p><i class="fas fa-clock"></i> Horario:</p>
                            <?php if (defined('STORE_HOURS') && is_array(STORE_HOURS)): ?>
                                <?php foreach (STORE_HOURS as $day => $hours): ?>
                                    <p class="store-hours"><?php echo htmlspecialchars($day); ?>: <?php echo htmlspecialchars($hours); ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="purchase-buttons">
                    <button class="btn purchase-btn" onclick="addToCart(<?php echo (int)$product['id']; ?>)">
                        <i class="fas fa-shopping-cart"></i>
                        Añadir al carrito
                    </button>
                    <button class="btn-outline purchase-btn" data-fav-id="<?= (int)$product['id'] ?>" onclick="toggleFav(<?= (int)$product['id'] ?>)" aria-pressed="false">
                        <i class="fa-regular fa-heart"></i>
                        Favorito
                    </button>
                    
                    <a class="btn-outline whatsapp-btn" target="_blank" 
                       href="https://wa.me/<?php echo trim(SOCIAL_MEDIA['whatsapp']['display'], '+'); ?>?text=Hola%20MiliPet,%20me%20interesa:%20<?php echo urlencode($product['name']); ?>%20para%20retiro%20en%20tienda">
                        <i class="fab fa-whatsapp"></i>
                        Comprar por WhatsApp
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</article>

<style>
.product-short-desc {
    font-size: 1.1rem;
    color: #555;
    margin: 1rem 0;
    font-weight: 500;
}

.product-long-desc {
    color: #666;
    line-height: 1.6;
    margin: 1rem 0 1.5rem 0;
}

.stock-status {
    font-weight: bold;
    padding: 10px 15px;
    border-radius: 4px;
    text-align: center;
    margin: 15px 0;
}
.stock-status.out {
    background-color: #ffebee;
    color: #c62828;
}
.stock-status.in {
    background-color: #e8f5e9;
    color: #2e7d32;
}
.notify-form {
    background-color: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    margin: 15px 0;
}

.product-actions {
    margin-top: 2rem;
}

.delivery-options {
    margin-bottom: 2rem;
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.delivery-options h3 {
    margin-bottom: 1rem;
    color: #2e7d32;
}

.option-wrapper {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.delivery-option {
    display: block;
    cursor: pointer;
    padding: 1rem;
    transition: background-color 0.2s;
}

.delivery-option:hover {
    background-color: #f5f5f5;
}

.delivery-option input[type="radio"] {
    display: none;
}

.delivery-option input[type="radio"]:checked + .option-content {
    color: #2e7d32;
}

.option-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.option-content i {
    font-size: 1.5rem;
    color: #2e7d32;
}

.option-text {
    display: flex;
    flex-direction: column;
}

.option-text strong {
    font-size: 1.1rem;
}

.option-text span {
    font-size: 0.9rem;
    color: #666;
}

.store-info {
    padding: 1rem;
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
}

.store-info p {
    margin: 0.5rem 0;
    color: #333;
}

.store-info i {
    color: #2e7d32;
    width: 20px;
    text-align: center;
    margin-right: 0.5rem;
}

.store-hours {
    padding-left: 25px;
    font-size: 0.9rem;
    color: #666;
}

.purchase-buttons {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

.purchase-btn,
.whatsapp-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    width: 100%;
    font-size: 1.1rem;
}

.whatsapp-btn {
    background-color: #25D366;
    color: white;
    border: none;
}

.whatsapp-btn:hover {
    background-color: #128C7E;
}

@media (min-width: 768px) {
    .purchase-buttons {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 767px) {
    .delivery-options {
        padding: 1rem;
    }
    
    .option-content {
        flex-direction: column;
        text-align: center;
    }
    
    .option-text {
        align-items: center;
    }
}
</style>