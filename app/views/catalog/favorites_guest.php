<?php ?>
<section class="guest-favorites">
  <h1>Favoritos</h1>
  <div class="card" style="max-width:720px;margin:20px auto;padding:20px;border-radius:12px;">
    <p style="margin:0 0 10px 0;">Debes tener cuenta para tener favoritos.</p>
    <p class="muted">Pronto habilitaremos el inicio de sesión de clientes. Por ahora, puedes:
      <ul>
        <li>Usar el carrito para listar productos y enviarlos por WhatsApp.</li>
        <li>Guardar el enlace del producto en tu navegador.</li>
      </ul>
    </p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a class="btn" href="<?= url(['r'=>'catalog']) ?>">Ir al catálogo</a>
      <a class="btn-outline" href="<?= url(['r'=>'cart']) ?>">Ver carrito</a>
    </div>
  </div>
</section>
