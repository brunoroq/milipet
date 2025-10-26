<?php ?>
<h1>Catálogo</h1>

<form method="get" class="filters">
    <input type="hidden" name="r" value="catalog">
    <input type="text" name="q" placeholder="Buscar..." value="<?php echo htmlspecialchars($q); ?>">
    
    <select name="cid">
        <option value="">Todas las categorías</option>
        <?php foreach($categories as $c): ?>
        <option value="<?php echo $c['id']; ?>" <?php echo ($cid==$c['id']?'selected':''); ?>>
            <?php echo htmlspecialchars($c['name']); ?>
        </option>
        <?php endforeach; ?>
    </select>
    
    <select name="species">
        <option value="">Todas las especies</option>
        <option value="Perros" <?php echo ($species==='Perros'?'selected':''); ?>>Perros</option>
        <option value="Gatos" <?php echo ($species==='Gatos'?'selected':''); ?>>Gatos</option>
        <option value="Aves" <?php echo ($species==='Aves'?'selected':''); ?>>Aves</option>
        <option value="Otros" <?php echo ($species==='Otros'?'selected':''); ?>>Otros</option>
    </select>
    
    <label>
        <input type="checkbox" name="show_all" value="1" <?php echo (isset($_GET['show_all']) && $_GET['show_all'] === '1' ? 'checked' : ''); ?>>
        Mostrar productos sin stock
    </label>
    
    <button class="btn" type="submit">Filtrar</button>
</form>

<div class="grid">
    <?php foreach($products as $p): ?>
    <article class="card <?php echo ($p['stock'] <= 0 ? 'out-of-stock' : ''); ?>">
        <img src="<?php echo htmlspecialchars($p['image_url'] ?: 'assets/img/placeholder.png'); ?>">
        <h3><?php echo htmlspecialchars($p['name']); ?></h3>
        <p class="muted"><?php echo htmlspecialchars($p['category_name'] ?? ''); ?></p>
        <strong>$<?php echo number_format($p['price'] ?? 0, 0, ',', '.'); ?></strong>
        
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
            <a class="btn" href="?r=catalog&action=detail&id=<?php echo $p['id']; ?>">Ver</a>
            <?php if ($p['stock'] > 0): ?>
            <a class="btn-outline" target="_blank" 
               href="https://wa.me/56900000000?text=Hola%20MiliPet,%20me%20interesa:%20<?php echo urlencode($p['name']); ?>">
                Comprar / Consultar
            </a>
            <?php endif; ?>
        </div>
    </article>
    <?php endforeach; ?>
</div>

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
    color: #2e7d32;
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
    color: #2e7d32;
    font-size: 0.9rem;
    padding: 5px 10px;
    background-color: #e8f5e9;
    border-radius: 4px;
}

.pickup-info i {
    font-size: 1rem;
}
</style>