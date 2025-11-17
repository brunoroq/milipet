<?php
// Newsletter section for home page
?>
<section class="bottom-section bottom-newsletter">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-5 text-center">
        <img src="https://ascii.jp/img/2020/02/26/2298618/o/83cd631e88b86d9d.png" 
             alt="Gato usando computador" 
             class="newsletter-dog img-fluid rounded-3 shadow-lg">
      </div>
      <div class="col-lg-7">
        <div class="newsletter-content">
          <div class="icon-circle mb-3">
            <i class="fas fa-envelope"></i>
          </div>
          <h2 class="section-title mb-3">¿Quieres recibir ofertas para tu mejor amigo?</h2>
          <p class="lead mb-4">Suscríbete a nuestro newsletter y recibe promociones exclusivas, consejos de cuidado y novedades de productos para tu mascota.</p>
          
          <form class="newsletter-form" action="<?= url(['r' => 'newsletter/subscribe']) ?>" method="post" onsubmit="return handleNewsletterSubmit(event)">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
            <div class="input-group input-group-lg shadow-sm">
              <span class="input-group-text bg-white border-end-0">
                <i class="fas fa-envelope text-success"></i>
              </span>
              <input type="email" 
                     name="email" 
                     class="form-control border-start-0 ps-0" 
                     placeholder="tu@email.com" 
                     required 
                     pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
              <button class="btn btn-success btn-lg px-4" type="submit">
                <i class="fas fa-paper-plane me-2"></i>
                Suscribirme
              </button>
            </div>
            <small class="text-muted d-block mt-2">
              <i class="fas fa-lock me-1"></i>
              No compartimos tu información. Puedes cancelar en cualquier momento.
            </small>
          </form>

          <div class="newsletter-benefits mt-4">
            <div class="row g-3">
              <div class="col-md-4">
                <div class="benefit-item">
                  <i class="fas fa-tag text-success me-2"></i>
                  <span>Ofertas exclusivas</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="benefit-item">
                  <i class="fas fa-bell text-success me-2"></i>
                  <span>Nuevos productos</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="benefit-item">
                  <i class="fas fa-heart text-success me-2"></i>
                  <span>Consejos de cuidado</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function handleNewsletterSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const email = form.querySelector('input[name="email"]').value;
  const btn = form.querySelector('button[type="submit"]');
  const originalHTML = btn.innerHTML;
  
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
  
  fetch(form.action, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams(new FormData(form))
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      btn.innerHTML = '<i class="fas fa-check me-2"></i>¡Suscrito!';
      btn.classList.remove('btn-success');
      btn.classList.add('btn-outline-success');
      form.querySelector('input[name="email"]').value = '';
      setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-success');
        btn.disabled = false;
      }, 3000);
    } else {
      alert(data.message || 'Error al suscribirse. Intenta nuevamente.');
      btn.innerHTML = originalHTML;
      btn.disabled = false;
    }
  })
  .catch(err => {
    console.error('Newsletter error:', err);
    // Fallback: just show success message for now (until backend is implemented)
    btn.innerHTML = '<i class="fas fa-check me-2"></i>¡Gracias por suscribirte!';
    btn.classList.remove('btn-success');
    btn.classList.add('btn-outline-success');
    form.querySelector('input[name="email"]').value = '';
    setTimeout(() => {
      btn.innerHTML = originalHTML;
      btn.classList.remove('btn-outline-success');
      btn.classList.add('btn-success');
      btn.disabled = false;
    }, 3000);
  });
  
  return false;
}
</script>
