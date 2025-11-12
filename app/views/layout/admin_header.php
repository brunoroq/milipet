<?php
// Admin header styled like public header but with admin nav
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - MiliPet</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<header class="navbar"><div class="container"><a class="brand" href="<?= url(['r' => 'admin/dashboard']) ?>">
  <img src="<?= asset('assets/img/logo-milipet.png') ?>" alt="MiliPet - Productos para Mascotas" class="img-fluid" style="max-height: 60px;">
</a><nav>
  <a href="<?= url(['r' => 'admin/products']) ?>">Productos</a>
  <a class="btn-outline" href="<?= url(['r' => 'auth/logout']) ?>">Salir</a>
</nav></div></header>
<main class="container">
