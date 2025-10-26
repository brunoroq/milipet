<?php ?>
<div class="adoptions-container">
    <header class="adoptions-header">
        <h1>Jornadas de Adopción</h1>
        <p class="subtitle">Encuentra a tu compañero ideal y dale una segunda oportunidad</p>
    </header>

    <section class="adoption-section upcoming-events">
        <h2>Próximas Jornadas</h2>
        <div class="events-grid">
            <article class="event-card">
                <div class="event-date">
                    <span class="day">28</span>
                    <span class="month">Oct</span>
                </div>
                <div class="event-details">
                    <h3>Gran Jornada de Adopción</h3>
                    <p class="event-location">Plaza de Maipú</p>
                    <p class="event-time">10:00 - 17:00 hrs</p>
                    <p class="event-description">Jornada especial con más de 20 perritos y gatitos en busca de familia.</p>
                </div>
            </article>

            <article class="event-card">
                <div class="event-date">
                    <span class="day">4</span>
                    <span class="month">Nov</span>
                </div>
                <div class="event-details">
                    <h3>Adopción Responsable</h3>
                    <p class="event-location">Parque Tres Poniente</p>
                    <p class="event-time">11:00 - 16:00 hrs</p>
                    <p class="event-description">Jornada enfocada en mascotas adultas que buscan un hogar definitivo.</p>
                </div>
            </article>
        </div>
    </section>

    <section class="adoption-section foundations">
        <h2>Fundaciones Asociadas</h2>
        <div class="foundations-grid">
            <article class="foundation-card">
                <img src="assets/img/foundation1.jpg" alt="Fundación Patitas Felices">
                <div class="foundation-info">
                    <h3>Fundación Patitas Felices</h3>
                    <p>Dedicados al rescate y rehabilitación de perros y gatos en situación de calle.</p>
                    <div class="foundation-contact">
                        <a href="https://instagram.com/patitasfelices" target="_blank" class="social-link">
                            <i class="fab fa-instagram"></i> @patitasfelices
                        </a>
                        <a href="https://wa.me/56900000001" target="_blank" class="social-link">
                            <i class="fab fa-whatsapp"></i> Contactar
                        </a>
                    </div>
                </div>
            </article>

            <article class="foundation-card">
                <img src="assets/img/foundation2.jpg" alt="Fundación Amor Animal">
                <div class="foundation-info">
                    <h3>Fundación Amor Animal</h3>
                    <p>Especializada en el cuidado y rehabilitación de mascotas con necesidades especiales.</p>
                    <div class="foundation-contact">
                        <a href="https://instagram.com/amoranimal" target="_blank" class="social-link">
                            <i class="fab fa-instagram"></i> @amoranimal
                        </a>
                        <a href="https://wa.me/56900000002" target="_blank" class="social-link">
                            <i class="fab fa-whatsapp"></i> Contactar
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section class="adoption-section info">
        <h2>Adopción Responsable</h2>
        <div class="info-grid">
            <div class="info-card">
                <h3>Requisitos para Adoptar</h3>
                <ul>
                    <li>Ser mayor de edad</li>
                    <li>Presentar cédula de identidad</li>
                    <li>Comprobante de domicilio</li>
                    <li>Entrevista con la fundación</li>
                    <li>Firmar compromiso de adopción</li>
                </ul>
            </div>

            <div class="info-card">
                <h3>Beneficios incluidos</h3>
                <ul>
                    <li>Primera consulta veterinaria</li>
                    <li>Vacunas al día</li>
                    <li>Desparasitación</li>
                    <li>Esterilización</li>
                    <li>Kit de bienvenida MiliPet</li>
                </ul>
            </div>

            <div class="info-card">
                <h3>Compromiso Post-Adopción</h3>
                <ul>
                    <li>Seguimiento veterinario regular</li>
                    <li>Fotos de seguimiento</li>
                    <li>Visitas de control</li>
                    <li>Notificar cambios importantes</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="adoption-section support">
        <h2>¿Cómo Ayudar?</h2>
        <div class="support-options">
            <div class="support-card">
                <i class="fas fa-home"></i>
                <h3>Hogar Temporal</h3>
                <p>Ofrece tu casa como hogar de tránsito mientras se encuentra una familia definitiva.</p>
            </div>

            <div class="support-card">
                <i class="fas fa-heart"></i>
                <h3>Apadrinamiento</h3>
                <p>Contribuye mensualmente con los gastos de un animal rescatado.</p>
            </div>

            <div class="support-card">
                <i class="fas fa-hands-helping"></i>
                <h3>Voluntariado</h3>
                <p>Participa en jornadas de adopción y eventos de las fundaciones.</p>
            </div>

            <div class="support-card">
                <i class="fas fa-gift"></i>
                <h3>Donaciones</h3>
                <p>Dona alimentos, medicinas o implementos para los refugios.</p>
            </div>
        </div>
    </section>
</div>

<style>
.adoptions-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.adoptions-header {
    text-align: center;
    margin-bottom: 3rem;
}

.adoptions-header .subtitle {
    color: #666;
    font-size: 1.2rem;
}

.adoption-section {
    margin-bottom: 4rem;
}

.adoption-section h2 {
    color: #2e7d32;
    text-align: center;
    margin-bottom: 2rem;
}

/* Events Styling */
.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.event-card {
    display: flex;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.event-date {
    background: #2e7d32;
    color: white;
    padding: 1rem;
    text-align: center;
    min-width: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.event-date .day {
    font-size: 1.8rem;
    font-weight: bold;
}

.event-details {
    padding: 1rem;
}

.event-location, .event-time {
    color: #666;
    font-size: 0.9rem;
    margin: 0.3rem 0;
}

/* Foundations Styling */
.foundations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.foundation-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.foundation-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.foundation-info {
    padding: 1.5rem;
}

.foundation-contact {
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
}

.social-link {
    color: #2e7d32;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.social-link:hover {
    text-decoration: underline;
}

/* Info Section Styling */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.info-card {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}

.info-card h3 {
    color: #2e7d32;
    margin-bottom: 1rem;
}

.info-card ul {
    list-style-type: none;
    padding: 0;
}

.info-card li {
    margin-bottom: 0.5rem;
    padding-left: 1.5rem;
    position: relative;
}

.info-card li:before {
    content: "•";
    color: #2e7d32;
    position: absolute;
    left: 0;
}

/* Support Section Styling */
.support-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    text-align: center;
}

.support-card {
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.support-card i {
    font-size: 2.5rem;
    color: #2e7d32;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .adoptions-container {
        padding: 1rem;
    }
    
    .event-card {
        flex-direction: column;
    }
    
    .event-date {
        padding: 0.5rem;
        flex-direction: row;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>