<?php ?>
<h1>Favoritos</h1>
<p class="muted">Tus productos guardados.</p>
<?php if (empty($products)): ?>
    <p>No tienes productos en favoritos.</p>
<?php else: ?>
    <div class="grid">
        <?php foreach($products as $p): ?>
            <article class="card">
                <?php
                $rel = $p['image_url'] ?? '';
                $rel = $rel ? str_replace('\\','/', strtolower($rel)) : '';
                $fs  = $rel ? (defined('PUBLIC_PATH') ? PUBLIC_PATH.'/' . ltrim($rel,'/') : null) : null;
                $placeholderCandidates = ['assets/img/placeholder.svg','assets/img/placeholder.png'];
                $chosenPlaceholder = null;
                foreach ($placeholderCandidates as $ph) {
                    $phPath = (defined('PUBLIC_PATH') ? PUBLIC_PATH.'/' : '') . $ph;
                    if (is_file($phPath)) { $chosenPlaceholder = $ph; break; }
                }
                if (!$chosenPlaceholder) { $chosenPlaceholder = 'assets/img/placeholder.svg'; }
                $src = ($fs && is_file($fs)) ? asset($rel) : asset($chosenPlaceholder);
                ?>
                <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <h3><?= htmlspecialchars($p['name']) ?></h3>
                <p class="card-category muted"><?= htmlspecialchars($p['category_name'] ?? '') ?></p>
                <strong class="card-price">$<?= number_format($p['price'] ?? 0, 0, ',', '.') ?></strong>
                <div class="row">
                    <a class="btn" href="<?= url(['r'=>'product','id'=>(int)$p['id']]) ?>">Ver</a>
                    <button class="btn-outline" onclick="removeFromFavs(<?= (int)$p['id'] ?>); setTimeout(refreshFavPage, 50);">Quitar</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
