<?php ?>
<h1>Carrito</h1>
<p class="muted">Productos seleccionados<?php if(!empty($hasSession)): ?> (guardado en tu cuenta)<?php endif; ?>. Puedes generar un mensaje para WhatsApp.</p>
<?php if (empty($products)): ?>
    <p>Tu carrito está vacío.</p>
    <a class="btn" href="<?= url(['r'=>'catalog']) ?>">Ir al catálogo</a>
<?php else: ?>
    <?php $total = 0; foreach($products as $p){ $total += (float)($p['price'] ?? 0); } ?>
    <div class="grid">
        <?php foreach($products as $p): ?>
            <article class="card">
                <img src="<?= image_src($p['image_url'] ?? null) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <h3><?= htmlspecialchars($p['name']) ?></h3>
                <p class="card-category muted"><?= htmlspecialchars($p['category_name'] ?? '') ?></p>
                <strong class="card-price">$<?= number_format($p['price'] ?? 0, 0, ',', '.') ?></strong>
                <div class="row">
                    <a class="btn" href="<?= url(['r'=>'product','id'=>(int)$p['id']]) ?>">Ver</a>
                    <button class="btn-outline" onclick="removeFromCart(<?= (int)$p['id'] ?>); setTimeout(refreshCartPage, 50);">Quitar</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php $waBase = 'https://wa.me/+56957153992?text='; $msg = 'Hola MiliPet, estoy interesado en los siguientes productos:%0A'; foreach($products as $p){ $msg .= rawurlencode($p['name']).'%20$'.rawurlencode(number_format($p['price']??0,0,',','.')).'%0A'; } $msg .= '%0ATotal:%20$'.rawurlencode(number_format($total,0,',','.')); ?>
    <p><strong>Total estimado: $<?= number_format($total,0,',','.') ?></strong></p>
    <a class="btn btn-success mt-3" target="_blank" href="<?= $waBase . $msg ?>">Enviar lista por WhatsApp</a>
<?php endif; ?>
