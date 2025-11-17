<section class="main-section py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-5">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
          <div class="card-body p-4 p-md-5">
            <!-- Header del login -->
            <div class="text-center mb-4">
              <div class="login-icon mb-3">
                <i class="fas fa-user-circle" style="font-size: 4rem; color: var(--green);"></i>
              </div>
              <h1 class="h2 fw-bold mb-2" style="color: var(--green);">Iniciar sesión</h1>
              <p class="text-muted mb-0">Accede con tu cuenta de MiliPet</p>
            </div>
            
            <!-- Alertas de error -->
            <?php if ($msg = flash('error')): ?>
              <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div><?= htmlspecialchars($msg) ?></div>
              </div>
            <?php endif; ?>
            
            <!-- Formulario de login -->
            <form method="post" action="<?= url(['r'=>'auth/login_post']) ?>">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
              
              <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                <div class="input-group">
                  <span class="input-group-text bg-white">
                    <i class="fas fa-envelope text-muted"></i>
                  </span>
                  <input 
                    type="email" 
                    name="email" 
                    id="email"
                    class="form-control" 
                    placeholder="tu@email.com" 
                    required 
                    autocomplete="username">
                </div>
              </div>
              
              <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                  <span class="input-group-text bg-white">
                    <i class="fas fa-lock text-muted"></i>
                  </span>
                  <input 
                    type="password" 
                    name="password" 
                    id="password"
                    class="form-control" 
                    placeholder="••••••••" 
                    required 
                    autocomplete="current-password">
                </div>
              </div>
              
              <div class="form-check mb-4">
                <input 
                  type="checkbox" 
                  name="remember" 
                  value="1" 
                  id="remember"
                  class="form-check-input">
                <label class="form-check-label text-muted" for="remember">
                  Recordarme por 30 días
                </label>
              </div>
              
              <div class="d-grid">
                <button class="btn btn-primary-home btn-lg" type="submit">
                  <i class="fas fa-sign-in-alt me-2"></i>Iniciar sesión
                </button>
              </div>
            </form>
            
            <!-- Info adicional -->
            <div class="text-center mt-4 pt-3 border-top">
              <p class="text-muted small mb-0">
                <i class="fas fa-info-circle me-1"></i>
                ¿Problemas para acceder? Contacta al administrador.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
