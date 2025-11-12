<?php ?>
<h1>Catálogo</h1>

<form method="get" class="filters" action="<?= url(['r' => 'catalog']) ?>">
    <input type="hidden" name="r" value="catalog">
    <input type="text" name="q"
           value="<?= htmlspecialchars($query ?? '', ENT_QUOTES, 'UTF-8') ?>"
           placeholder="Buscar...">
    
    <select name="category">
        <option value="0" <?= (($category ?? null) === null ? 'selected' : '') ?>>Todas las categorías</option>
        <?php foreach($categories as $c): ?>
        <option value="<?php echo (int)$c['id']; ?>" <?php echo (((int)($category ?? 0) === (int)$c['id']) ? 'selected' : ''); ?>>
            <?php echo htmlspecialchars($c['name']); ?>
        </option>
        <?php endforeach; ?>
    </select>
    
    <select name="species">
        <option value="0" <?= (($species ?? null) === null ? 'selected' : '') ?>>Todas las especies</option>
        <?php if (!empty($speciesList ?? [])): ?>
            <?php foreach ($speciesList as $s): ?>
                <option value="<?= (int)$s['id'] ?>" <?= (((int)($species ?? 0) === (int)$s['id']) ? 'selected' : '') ?>>
                    <?= htmlspecialchars($s['name'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    
    <label>
        <input type="checkbox" name="stock" value="1" <?php echo (!empty($in_stock ?? null) ? 'checked' : ''); ?>>
        Mostrar solo productos con stock
    </label>
    
    <button class="btn btn-accent" type="submit">Filtrar</button>
    <?php if ((($query ?? '') !== '') || !empty($category ?? $cid ?? null) || !empty($species ?? null) || !empty($in_stock ?? null)): ?>
    <a href="<?= url(['r' => 'catalog']) ?>" class="btn-outline">Limpiar filtros</a>
    <?php endif; ?>
</form>

<?php if ((($query ?? '') !== '') && empty($products)): ?>
<div class="no-results" style="text-align:center; margin-top:40px;">
    <h2>No se encontraron resultados para “<?= htmlspecialchars($query ?? '', ENT_QUOTES, 'UTF-8') ?>”.</h2>
    <p>Revisa la ortografía o prueba con otra palabra.</p>
    <hr style="width:200px; margin:20px auto; border:0; border-top:1px solid #ccc;">
    <h3>Tal vez te guste</h3>

    <?php if (!empty($suggested ?? [])): ?>
        <div class="suggested-grid" style="
                    display:grid;
                    grid-template-columns:repeat(auto-fit, minmax(220px,1fr));
                    gap:24px;
                    max-width:1000px;
                    margin:40px auto;
                ">
            <?php foreach ($suggested as $s): ?>
                <div class="product-card">
                        <img src="<?= image_src($s['image_url'] ?? null) ?>"
                         alt="<?= htmlspecialchars($s['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                         style="width:100%;height:200px;object-fit:cover;border-radius:8px;">
                    <h4 style="margin-top:10px;"><?= htmlspecialchars($s['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h4>
                    <p><?= htmlspecialchars($s['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>$<?= number_format((float)($s['price'] ?? 0), 0, ',', '.') ?></strong></p>
                    <a href="<?= url(['r'=>'product','id'=>(int)$s['id']]) ?>"
                       class="btn"
                       style="display:inline-block;background:#3D873F;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;">
                       Ver
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="grid">
    <?php foreach($products as $p): ?>
    <article class="card <?php echo ($p['stock'] <= 0 ? 'out-of-stock' : ''); ?>">
        <img src="<?= image_src($p['image_url'] ?? null) ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
        <h3 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h3>
        <p class="card-category muted"><?php echo htmlspecialchars($p['category_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        <strong class="card-price">$<?php echo number_format($p['price'] ?? 0, 0, ',', '.'); ?></strong>
        <div class="actions-inline" style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn btn-outline btn-sm fav-btn" data-fav-id="<?= (int)$p['id'] ?>" onclick="toggleFav(<?= (int)$p['id'] ?>)" aria-pressed="false" title="Agregar a favoritos">
                <i class="fa-regular fa-heart"></i>
            </button>
            <button type="button" class="btn btn-outline btn-sm" onclick="addToCart(<?= (int)$p['id'] ?>)" title="Agregar al carrito">
                <i class="fa-solid fa-cart-plus"></i>
            </button>
        </div>
        
        <?php if ($p['stock'] <= 0): ?>
            <p class="stock-status out">Sin stock</p>
        <?php else: ?>
            <div class="product-availability">
                <p class="stock-status in">Stock: <?php echo (int)$p['stock']; ?> unidades</p>
                <p class="pickup-info">
                    <i class="fas fa-store"></i>
                    Retiro en tienda disponible hoy
                </p>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <a class="btn" href="<?= url(['r' => 'product', 'id' => (int)$p['id']]) ?>">Ver</a>
            <?php if ($p['stock'] > 0): ?>
            <a class="btn-outline" target="_blank" 
               href="https://wa.me/56900000000?text=Hola%20MiliPet,%20me%20interesa:%20<?php echo rawurlencode($p['name']); ?>">
                Comprar / Consultar
            </a>
            <?php endif; ?>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<style>
.out-of-stock {
    opacity: 0.7;
}
.stock-status {
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 4px;
    text-align: center;
    margin: 10px 0;
}
.stock-status.out {
    background-color: #ffebee;
    color: #c62828;
}
.stock-status.in {
    background-color: #e8f5e9;
    color: var(--success);
}
form.filters label {
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 10px 0;
}

.product-availability {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.pickup-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--green);
    font-size: 0.9rem;
    padding: 5px 10px;
    background-color: #e8f5e9;
    border-radius: 4px;
}

.pickup-info i {
    font-size: 1rem;
}
</style>