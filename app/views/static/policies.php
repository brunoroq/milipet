<?php ?>
<div class="policies-container">
    <header class="policies-header">
        <h1><?= cms_text('policies.hero_title', 'Políticas y Términos') ?></h1>
        <p class="subtitle"><?= cms_text('policies.hero_subtitle', 'Información importante para nuestros clientes') ?></p>
    </header>

    <section class="policy-section">
        <h2><?= cms_text('policies.returns_title', 'Política de Devoluciones y Cambios') ?></h2>
        <div class="policy-content">
            <p><?= cms_text('policies.returns_text', 'Plazo de 10 días hábiles desde la compra para realizar cambios o devoluciones. Presentar boleta o factura original.') ?></p>
        </div>
    </section>

    <section class="policy-section">
        <h2><?= cms_text('policies.store_pickup_title', 'Retiro en Tienda') ?></h2>
        <div class="policy-content">
            <p><?= cms_text('policies.store_pickup_text', 'Local principal: Pumay, Maipú. Horarios: Lunes a Sábado 10:00 - 19:00.') ?></p>
            </ol>
        </div>
    </section>

    <section class="policy-section">
        <h2>Garantías</h2>
        <div class="policy-content">
            <ul>
                <li>Productos con garantía del fabricante mantienen sus condiciones originales</li>
                <li>Para accesorios y productos de nuestra marca, ofrecemos 30 días de garantía</li>
                <li>Fallas de fábrica serán evaluadas caso a caso</li>
            </ul>
        </div>
    </section>

    <section class="policy-section contact-info">
        <h2>Información de Contacto</h2>
        <div class="contact-grid">
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <h3>WhatsApp</h3>
                <p>+56 9 9545 8036</p>
            </div>
            <div class="contact-item">
                <i class="fas fa-clock"></i>
                <h3>Horario de Atención</h3>
                <p>Lunes a Sábado<br>10:00 - 19:00</p>
            </div>
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Dirección</h3>
                <p>Pumay, Maipú<br>Región Metropolitana</p>
            </div>
            <div class="contact-item">
                <i class="fab fa-instagram"></i>
                <h3>Instagram</h3>
                <p>@mili_petshop</p>
            </div>
        </div>
    </section>
</div>

<style>
.policies-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

.policies-header {
    text-align: center;
    margin-bottom: 3rem;
}

.policies-header .subtitle {
    color: #666;
    font-size: 1.2rem;
}

.policy-section {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

.policy-section h2 {
    color: #2e7d32;
    border-bottom: 2px solid #e8f5e9;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.policy-content {
    color: #333;
}

.policy-content h3 {
    color: #1b5e20;
    margin: 1.5rem 0 1rem;
}

.policy-content ul, 
.policy-content ol {
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}

.policy-content li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.hours-table {
    width: 100%;
    max-width: 400px;
    margin: 1rem 0;
}

.hours-table td {
    padding: 0.5rem;
    border-bottom: 1px solid #eee;
}

.hours-table td:first-child {
    font-weight: bold;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    text-align: center;
}

.contact-item {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.contact-item i {
    font-size: 2rem;
    color: #2e7d32;
    margin-bottom: 1rem;
}

.contact-item h3 {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .policies-container {
        padding: 1rem;
    }
    
    .policy-section {
        padding: 1.5rem;
    }
    
    .contact-grid {
        grid-template-columns: 1fr;
    }
}
</style>