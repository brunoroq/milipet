<?php
// Cargar la configuración desde la ruta raíz del proyecto
// app/views/layout -> views -> app -> (root) -> config/config.php
require_once __DIR__ . '/../../../config/config.php';
// Evitar notices si $config no está definido o no es array
$storeConfig = (isset($config) && is_array($config)) ? $config : [];
// Helper para renderizar íconos: usa Font Awesome si está disponible, si no, usa un emoji legible
if (!function_exists('mp_render_icon')) {
    function mp_render_icon(string $platform): string {
        $p = strtolower($platform);
        if (defined('FONTAWESOME_KIT') && FONTAWESOME_KIT) {
            return '<i class="fab fa-' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . '"></i>';
        }
        $emoji = [
            'instagram' => '📷',
            'facebook'  => '📘',
            'whatsapp'  => '💬',
        ];
        $char = $emoji[$p] ?? '🔗';
        return '<span class="icon-fallback" aria-hidden="true">' . $char . '</span>';
    }
}
?>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-info">
                <h3><?php echo $storeConfig['store']['name'] ?? 'MiliPet'; ?></h3>
                <p>Tu tienda de mascotas en Maipú</p>
                <p class="address"><?php echo $storeConfig['store']['address'] ?? ''; ?></p>
                <p class="hours">Lunes a Viernes: <?php echo $storeConfig['store']['business_hours']['monday_friday'] ?? ''; ?></p>
                <p class="hours">Sábado: <?php echo $storeConfig['store']['business_hours']['saturday'] ?? ''; ?></p>
                <p class="hours">Domingo: <?php echo $storeConfig['store']['business_hours']['sunday'] ?? ''; ?></p>
            </div>
            
            <div class="footer-social">
                <h3>Síguenos y Contáctanos</h3>
                <div class="social-buttons">
                    <?php if (isset($storeConfig['store']['social'])): ?>
                        <?php foreach ($storeConfig['store']['social'] as $platform => $url): ?>
                            <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" rel="noopener" class="social-btn <?php echo htmlspecialchars($platform); ?>">
                                <?php echo mp_render_icon($platform); ?>
                                <span><?php echo ucfirst($platform); ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-links">
                <h3>Enlaces</h3>
                <ul>
                    <li><a href="?r=catalog">Catálogo</a></li>
                    <li><a href="?r=static/about">Quiénes Somos</a></li>
                    <li><a href="?r=static/adoptions">Adopciones</a></li>
                    <li><a href="?r=static/policies">Políticas</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <small>© <?php echo date('Y'); ?> MiliPet — Todos los derechos reservados</small>
            <div class="social-floating">
                <?php if (isset($storeConfig['store']['social'])): ?>
                    <?php foreach ($storeConfig['store']['social'] as $platform => $url): ?>
                        <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" rel="noopener" class="float-btn <?php echo htmlspecialchars($platform); ?>">
                            <?php echo mp_render_icon($platform); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<style>
.footer {
    background: #2e7d32;
    color: white;
    padding: 3rem 0 1rem;
    margin-top: 3rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer h3 {
    color: white;
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.footer-info p {
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.footer-social .social-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.social-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    transition: transform 0.2s;
}

.social-btn:hover {
    transform: translateY(-2px);
}

.social-btn.instagram {
    background: #E1306C;
}

.social-btn.facebook {
    background: #4267B2;
}

.social-btn.whatsapp {
    background: #25D366;
}

.footer-links ul {
    list-style: none;
    padding: 0;
}

.footer-links a {
    color: white;
    text-decoration: none;
    opacity: 0.9;
    display: block;
    padding: 0.3rem 0;
}

.footer-links a:hover {
    opacity: 1;
}

.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 1rem;
    margin-top: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.social-floating {
    position: fixed;
    right: 20px;
    bottom: 20px;
    display: flex;
    flex-direction: column-reverse;
    gap: 1rem;
    z-index: 1000;
}

.float-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    font-size: 1.5rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    transition: transform 0.2s;
}

.float-btn:hover {
    transform: scale(1.1);
}

.float-btn.whatsapp {
    background: #25D366;
}

.float-btn.instagram {
    background: #E1306C;
}

.float-btn.facebook {
    background: #4267B2;
}

@media (max-width: 768px) {
    .footer {
        padding: 2rem 0 1rem;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .social-buttons {
        align-items: center;
    }
    
    .footer-bottom {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .social-floating .float-btn:not(.whatsapp) {
        display: none;
    }
}
</style>

<?php if (defined('FONTAWESOME_KIT') && FONTAWESOME_KIT): ?>
<script src="https://kit.fontawesome.com/<?php echo htmlspecialchars(FONTAWESOME_KIT, ENT_QUOTES, 'UTF-8'); ?>.js" crossorigin="anonymous"></script>
<?php endif; ?>
<!-- Bootstrap JS (bundle incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>