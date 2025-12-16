<?php ?>

<!-- 🎨 Hero Section -->
<section class="py-5">
    <div class="container">
        <!-- Header atractivo -->
        <div class="text-center mb-5">
            <p class="text-success text-uppercase fw-semibold mb-2 letter-spacing-wide">
                <i class="fas fa-paw me-2"></i>Sobre MiliPet
            </p>
            <h1 class="display-4 fw-bold text-dark mb-3">
                <?= cms_text('about.hero_title', 'Quiénes Somos') ?>
            </h1>
            <p class="lead text-muted text-narrow mx-auto">
                <?= cms_text('about.hero_subtitle', 'Tu tienda de confianza para el cuidado y bienestar de tus mascotas en Maipú.') ?>
            </p>
        </div>

        <!-- 📖 Historia Card (Horizontal con ícono) -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex gap-3 align-items-start">
                    <div class="icon-circle bg-success-subtle text-success flex-shrink-0">
                        <i class="fas fa-book-open fs-4"></i>
                    </div>
                    <div>
                        <h2 class="h3 fw-bold text-dark mb-3">
                            <i class="fas fa-history me-2 text-success"></i>
                            <?= cms_text('about.history_title', 'Nuestra Historia') ?>
                        </h2>
                        <p class="text-dark mb-0">
                            <?= cms_text('about.history_text', 'Desde 2020, MiliPet nace con la misión de ofrecer productos de calidad para el cuidado de mascotas en la comuna de Maipú.') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🎯 Misión y Visión (2 columnas) -->
        <div class="row g-4 mb-5">
            <!-- Misión -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <div class="icon-circle bg-primary-subtle text-primary mx-auto">
                                <i class="fas fa-bullseye fs-4"></i>
                            </div>
                        </div>
                        <h2 class="h4 fw-bold text-dark text-center mb-3">
                            <?= cms_text('about.mission_title', 'Nuestra Misión') ?>
                        </h2>
                        <p class="text-dark mb-0">
                            <?= cms_text('about.mission_text', 'En MiliPet nos dedicamos a mejorar la vida de las mascotas y sus familias, ofreciendo productos de alta calidad seleccionados cuidadosamente.') ?>
                        </p>
                        <ul class="mt-3 mb-0 ps-3">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Precios justos y competitivos
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Apoyo a fundaciones y causas de protección animal
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Visión -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <div class="icon-circle bg-warning-subtle text-warning mx-auto">
                                <i class="fas fa-eye fs-4"></i>
                            </div>
                        </div>
                        <h2 class="h4 fw-bold text-dark text-center mb-3">
                            <?= cms_text('about.vision_title', 'Nuestra Visión') ?>
                        </h2>
                        <p class="text-dark mb-0">
                            <?= cms_text('about.vision_text', 'Aspiramos a ser la tienda de mascotas preferida en Maipú, reconocida por nuestra calidad, servicio y compromiso con el bienestar animal.') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 💎 Valores -->
        <div class="mb-5">
            <h2 class="h3 fw-bold text-dark text-center mb-4">
                <i class="fas fa-heart me-2 text-danger"></i>Nuestros Valores
            </h2>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100 text-center hover-lift">
                        <div class="card-body p-4">
                            <div class="icon-circle bg-success-subtle text-success mx-auto mb-3">
                                <i class="fas fa-star fs-4"></i>
                            </div>
                            <h3 class="h5 fw-bold text-success mb-2">Calidad</h3>
                            <p class="text-muted small mb-0">Seleccionamos cuidadosamente cada producto para garantizar lo mejor para tu mascota.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100 text-center hover-lift">
                        <div class="card-body p-4">
                            <div class="icon-circle bg-primary-subtle text-primary mx-auto mb-3">
                                <i class="fas fa-handshake fs-4"></i>
                            </div>
                            <h3 class="h5 fw-bold text-primary mb-2">Compromiso</h3>
                            <p class="text-muted small mb-0">Nos dedicamos a entender y satisfacer las necesidades de cada mascota.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100 text-center hover-lift">
                        <div class="card-body p-4">
                            <div class="icon-circle bg-info-subtle text-info mx-auto mb-3">
                                <i class="fas fa-users fs-4"></i>
                            </div>
                            <h3 class="h5 fw-bold text-info mb-2">Comunidad</h3>
                            <p class="text-muted small mb-0">Trabajamos junto a fundaciones locales para promover la adopción responsable.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100 text-center hover-lift">
                        <div class="card-body p-4">
                            <div class="icon-circle bg-warning-subtle text-warning mx-auto mb-3">
                                <i class="fas fa-smile fs-4"></i>
                            </div>
                            <h3 class="h5 fw-bold text-warning mb-2">Servicio</h3>
                            <p class="text-muted small mb-0">Brindamos asesoría personalizada para cada cliente y sus mascotas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 📸 Galería -->
        <div class="text-center">
            <h2 class="h3 fw-bold text-dark mb-4">
                <i class="fas fa-store me-2 text-success"></i>Nuestra Tienda
            </h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden hover-zoom">
                        <div class="gallery-image-wrapper">
                       <img src="https://pumay.cl/wp-content/uploads/2024/02/Local_Milipet.jpg" 
                           alt="Fachada de MiliPet" 
                           class="card-img-top gallery-image">
                            <div class="gallery-overlay">
                                <p class="text-white fw-semibold mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>Nuestra tienda en Maipú
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden hover-zoom">
                        <div class="gallery-image-wrapper">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSZSXEevFkz3H7a8cV5rZJzLUC6StVG93Wr5A&s" 
                                 alt="Interior de la tienda" 
                                 class="card-img-top gallery-image">
                            <div class="gallery-overlay">
                                <p class="text-white fw-semibold mb-0">
                                    <i class="fas fa-shopping-bag me-2"></i>Amplia selección de productos
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden hover-zoom">
                        <div class="gallery-image-wrapper">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1eYKHnm3WOMNEjFdcBTBBNr-VCFmz_ri_cQ&s" 
                                 alt="Nuestro equipo" 
                                 class="card-img-top gallery-image">
                            <div class="gallery-overlay">
                                <p class="text-white fw-semibold mb-0">
                                    <i class="fas fa-users me-2"></i>Equipo MiliPet
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>