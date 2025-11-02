<?php 
$flash = $_SESSION['flash'] ?? null; 
unset($_SESSION['flash']);
?>
<h1>Panel de administración</h1>
<?php if (!empty($flash)): ?>
	<div class="alert <?php echo $flash['type']==='error' ? 'alert-error' : 'alert-success'; ?>">
		<?php foreach ($flash['messages'] as $m): ?>
			<div><?php echo htmlspecialchars($m); ?></div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
<nav class="admin-nav">
	<a class="btn" href="?r=admin/products">Productos</a>
	<a class="btn-outline" href="?r=logout">Salir</a>
	<a class="btn-outline" href="?r=admin/campaigns">Campañas</a>
	</nav>
<p>Desde aquí podrás gestionar el catálogo y contenido.</p>