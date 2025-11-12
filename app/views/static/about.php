<?php ?>
<article class="about-section">
    <header class="about-header">
        <h1 class="text-dark">Quiénes somos</h1>
        <p class="tagline text-secondary">Tu tienda de confianza para el cuidado y bienestar de tus mascotas en Maipú</p>
    </header>

    <section class="about-content">
        <div class="about-story">
            <h2 class="text-dark">Nuestra Historia</h2>
            <p class="text-dark">Desde 2020, MiliPet nace con la misión de ofrecer productos de calidad para el cuidado de mascotas en la comuna de Maipú. Lo que comenzó como un pequeño emprendimiento familiar, se ha convertido en un referente local para dueños de mascotas que buscan lo mejor para sus compañeros peludos.</p>
            
            <p class="text-dark">Nuestra experiencia en el cuidado animal y el compromiso con el bienestar de las mascotas nos ha permitido crear un espacio donde encontrarás todo lo necesario para el cuidado de tus animales, desde alimentos premium hasta accesorios especializados.</p>
        </div>

        <div class="about-mission">
            <h2 class="text-dark">Nuestra Misión</h2>
            <p class="text-dark">En MiliPet nos dedicamos a mejorar la vida de las mascotas y sus familias, ofreciendo:</p>
            <ul class="text-dark">
                <li>Productos de alta calidad seleccionados cuidadosamente</li>
                <li>Asesoría personalizada para el cuidado de tu mascota</li>
                <li>Precios justos y competitivos</li>
                <li>Apoyo a fundaciones y causas de protección animal</li>
            </ul>
        </div>

        <div class="about-vision">
            <h2 class="text-dark">Nuestra Visión</h2>
            <p class="text-dark">Aspiramos a ser la tienda de mascotas preferida en Maipú, reconocida por nuestra calidad, servicio y compromiso con el bienestar animal.</p>
        </div>

        <div class="about-values">
            <h2 class="text-dark">Nuestros Valores</h2>
            <div class="values-grid">
                <div class="value-item">
                    <h3>Calidad</h3>
                    <p class="text-dark">Seleccionamos cuidadosamente cada producto para garantizar lo mejor para tu mascota.</p>
                </div>
                <div class="value-item">
                    <h3>Compromiso</h3>
                    <p class="text-dark">Nos dedicamos a entender y satisfacer las necesidades de cada mascota.</p>
                </div>
                <div class="value-item">
                    <h3>Comunidad</h3>
                    <p class="text-dark">Trabajamos junto a fundaciones locales para promover la adopción responsable.</p>
                </div>
                <div class="value-item">
                    <h3>Servicio</h3>
                    <p class="text-dark">Brindamos asesoría personalizada para cada cliente y sus mascotas.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-gallery">
        <h2 class="text-dark">Nuestra Tienda</h2>
        <div class="gallery-grid">
            <!-- Aquí se pueden agregar las fotos de la tienda -->
            <div class="gallery-item">
                <img src="<?= asset('assets/img/store-front.jpg') ?>" alt="Fachada de MiliPet">
                <p>Nuestra tienda en Maipú</p>
            </div>
            <div class="gallery-item">
                <img src="<?= asset('assets/img/store-interior.jpg') ?>" alt="Interior de la tienda">
                <p>Amplia selección de productos</p>
            </div>
            <div class="gallery-item">
                <img src="<?= asset('assets/img/team.jpg') ?>" alt="Nuestro equipo">
                <p>Equipo MiliPet</p>
            </div>
        </div>
    </section>
</article>

<style>
.about-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.about-header {
    text-align: center;
    margin-bottom: 3rem;
}

.about-header .tagline {
    font-size: 1.2rem;
    color: #666;
    margin-top: 1rem;
}

.about-content {
    display: grid;
    gap: 3rem;
}

.about-story, .about-mission, .about-vision {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.value-item {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
}

.value-item h3 {
    color: #2e7d32;
    margin-bottom: 1rem;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.gallery-item img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.gallery-item p {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.7);
    color: white;
    margin: 0;
    padding: 1rem;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .about-section {
        padding: 1rem;
    }
    
    .values-grid {
        grid-template-columns: 1fr;
    }
    
    .gallery-grid {
        grid-template-columns: 1fr;
    }
}
</style>