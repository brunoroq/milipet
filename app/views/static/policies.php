<?php ?>
<div class="policies-container">
    <header class="policies-header">
        <h1>Políticas y Términos</h1>
        <p class="subtitle">Información importante para nuestros clientes</p>
    </header>

    <section class="policy-section">
        <h2>Política de Devoluciones y Cambios</h2>
        <div class="policy-content">
            <h3>Condiciones Generales</h3>
            <ul>
                <li>Plazo de 10 días hábiles desde la compra para realizar cambios o devoluciones</li>
                <li>Presentar boleta o factura original</li>
                <li>El producto debe estar sin uso y en su empaque original</li>
                <li>Accesorios y productos de higiene deben estar sellados</li>
            </ul>

            <h3>Excepciones</h3>
            <ul>
                <li>Alimentos abiertos o con el sello roto</li>
                <li>Productos de uso higiénico una vez abiertos</li>
                <li>Productos en oferta o liquidación (solo cambios por fallas)</li>
            </ul>

            <h3>Proceso de Devolución</h3>
            <ol>
                <li>Acercarse a nuestra tienda con el producto y la boleta</li>
                <li>Nuestro personal verificará el estado del producto</li>
                <li>Se realizará el cambio por otro producto o la devolución del dinero</li>
            </ol>
        </div>
    </section>

    <section class="policy-section">
        <h2>Retiro en Tienda</h2>
        <div class="policy-content">
            <h3>Ubicación</h3>
            <p>Local principal: Pumay, Maipú, Región Metropolitana</p>

            <h3>Horarios de Atención</h3>
            <table class="hours-table">
                <tr>
                    <td>Lunes a Viernes:</td>
                    <td>10:00 - 19:00</td>
                </tr>
                <tr>
                    <td>Sábados:</td>
                    <td>10:00 - 19:00</td>
                </tr>
                <tr>
                    <td>Domingos y Festivos:</td>
                    <td>Cerrado</td>
                </tr>
            </table>

            <h3>Proceso de Retiro</h3>
            <ol>
                <li>Realizar la compra y seleccionar "Retiro en tienda"</li>
                <li>Esperar confirmación de que el pedido está listo</li>
                <li>Presentar documento de identidad al retirar</li>
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
                <p>+56 9 0000 0000</p>
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
                <p>@milipet</p>
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