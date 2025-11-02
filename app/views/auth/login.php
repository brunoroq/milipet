<?php ?>
<h1>Administración</h1>

<?php if(!empty($error) || !empty($flash)): ?>
<div class="alert">
    <?php echo htmlspecialchars($error ?? $flash); ?>
</div>
<?php endif; ?>

<form method="post" action="<?= url(['r' => 'auth/login']) ?>">
    <label>
        Email
        <input type="email" name="email" required autofocus>
    </label>
    <label>
        Contraseña 
        <input type="password" name="password" required>
    </label>
    <button class="btn" type="submit">Ingresar</button>
</form>

<script>
// Prevenir reenvío del formulario al recargar
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>