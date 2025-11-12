<!doctype html><html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Acceso administración</title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head><body>
<section class="auth-wrapper">
  <div class="card auth-card">
    <h1>Panel de administración</h1>
    <?php if ($msg = flash('error')): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= url(['r'=>'auth/admin_login_post']) ?>">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <label>Email</label>
      <input type="email" name="email" required autocomplete="username">
      <label>Contraseña</label>
      <input type="password" name="password" required autocomplete="current-password">
      <label class="remember-line"><input type="checkbox" name="remember" value="1"> Recordarme (30 días)</label>
      <button class="btn" type="submit">Entrar</button>
    </form>
    <a class="link" href="<?= url(['r'=>'home']) ?>">← Volver al inicio</a>
  </div>
</section>
<style>
.auth-wrapper{ display:flex; align-items:center; justify-content:center; min-height:70vh; padding:40px;}
.auth-card{ max-width:420px; width:100%; padding:24px; border-radius:12px; box-shadow:0 10px 24px rgba(0,0,0,.08); }
.remember-line{ display:flex; align-items:center; gap:8px; margin:10px 0 6px; font-size:.9rem; }
.alert-error{ background:#ffe6e6; color:#a40000; padding:10px 12px; border-radius:8px; margin-bottom:12px;}
</style>
</body></html>
