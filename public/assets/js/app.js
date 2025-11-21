const CART_KEY='milipet_cart';
const FAV_KEY='milipet_favs';

// --- Cart helpers ---
function getCart(){try{return JSON.parse(localStorage.getItem(CART_KEY))||[]}catch(e){return[]}}
function saveCart(c){localStorage.setItem(CART_KEY,JSON.stringify(c))}
function addToCart(id){id=parseInt(id);if(!id)return;const c=getCart();if(!c.includes(id)){c.push(id);saveCart(c);syncHeaderCounts();toast('Producto añadido al carrito.')}else{toast('Ya está en el carrito.')}}
function removeFromCart(id){id=parseInt(id);const c=getCart().filter(x=>x!==id);saveCart(c);syncHeaderCounts();}

// --- Favorites helpers ---
function getFavs(){try{return JSON.parse(localStorage.getItem(FAV_KEY))||[]}catch(e){return[]}}
function saveFavs(f){localStorage.setItem(FAV_KEY,JSON.stringify(f))}
function toggleFav(id){
  id = parseInt(id);
  if (!id) return;
  let f = getFavs();
  const wasFav = f.includes(id);
  let added = false;
  if (wasFav) {
    f = f.filter(x => x !== id);
  } else {
    f.push(id);
    added = true;
  }
  saveFavs(f);
  syncHeaderCounts();
  // Update all buttons for this product id with flash then final state
  try {
    const buttons = document.querySelectorAll(`[data-fav-id="${id}"]`);
    buttons.forEach(btn => {
      // brief white flash
      btn.classList.add('fav-flash');
      setTimeout(() => {
        btn.classList.remove('fav-flash');
        // state classes and aria
        btn.classList.toggle('btn-favorite--active', added); // backward compatibility
        btn.classList.toggle('fav-active', added);
        btn.classList.remove('active'); // legacy class cleanup
        btn.setAttribute('aria-pressed', added ? 'true' : 'false');
        // icon
        const icon = btn.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-regular','fa-solid');
          icon.classList.add(added ? 'fa-solid' : 'fa-regular', 'fa-heart');
        }
        // label
        const label = btn.querySelector('span');
        if (label) {
          label.textContent = added ? 'En favoritos' : 'Agregar a favoritos';
        }
        // pop animation
        btn.classList.remove('btn-favorite--pop');
        void btn.offsetWidth; // reflow to restart animation
        btn.classList.add('btn-favorite--pop');
        setTimeout(() => btn.classList.remove('btn-favorite--pop'), 220);
      }, 170);
    });
  } catch(e) {}
  // Header heart pulse when adding only
  if (added) {
    const headerFavIcon = document.getElementById('header-fav-icon') || document.querySelector('#fav-link i.fa-heart');
    if (headerFavIcon) {
      headerFavIcon.classList.remove('fav-pulse');
      void headerFavIcon.offsetWidth; // restart animation
      headerFavIcon.classList.add('fav-pulse');
      setTimeout(() => headerFavIcon.classList.remove('fav-pulse'), 400);
    }
  }
}
function removeFromFavs(id){id=parseInt(id);let f=getFavs().filter(x=>x!==id);saveFavs(f);syncHeaderCounts();updateFavButtons();}

// --- UI helpers ---
function syncHeaderCounts(){try{const cc=getCart().length;const fc=getFavs().length;const cEl=document.getElementById('cart-count');const fEl=document.getElementById('fav-count');if(cEl){cEl.textContent=cc; cEl.style.display=cc? 'inline-block':'none'}if(fEl){fEl.textContent=fc; fEl.style.display=fc? 'inline-block':'none'}
  // Actualizar enlaces con IDs para vistas server-side
  const cartLink=document.getElementById('cart-link'); if(cartLink){const ids=getCart().join(','); const base=cartLink.getAttribute('data-base')||cartLink.href; cartLink.setAttribute('data-base', base.split('?')[0]); const hrefBase=cartLink.getAttribute('data-base'); cartLink.href=hrefBase + (ids? ('?r=cart&ids='+ids):'?r=cart');}
  const favLink=document.getElementById('fav-link'); if(favLink){const ids=getFavs().join(','); const base=favLink.getAttribute('data-base')||favLink.href; favLink.setAttribute('data-base', base.split('?')[0]); const hrefBase=favLink.getAttribute('data-base'); favLink.href=hrefBase + (ids? ('?r=favorites&ids='+ids):'?r=favorites');}
}catch(e){}}

function updateFavButtons(){
  try {
    const favs = new Set(getFavs());
    document.querySelectorAll('[data-fav-id]').forEach(btn => {
      const id = parseInt(btn.getAttribute('data-fav-id'));
      const isFav = favs.has(id);
      btn.classList.toggle('btn-favorite--active', isFav);
      btn.classList.toggle('fav-active', isFav);
      btn.classList.remove('active'); // legacy cleanup
      btn.setAttribute('aria-pressed', isFav ? 'true' : 'false');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.remove('fa-regular','fa-solid');
        icon.classList.add(isFav ? 'fa-solid' : 'fa-regular', 'fa-heart');
      }
      const label = btn.querySelector('span');
      if (label) {
        label.textContent = isFav ? 'En favoritos' : 'Agregar a favoritos';
      }
    });
  } catch(e) {}
}

function toast(msg){try{if(window.bootstrap){const el=document.createElement('div');el.className='toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3';el.role='status';el.ariaLive='polite';el.innerHTML='<div class="d-flex"><div class="toast-body">'+msg+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';document.body.appendChild(el);new bootstrap.Toast(el,{delay:1800}).show();setTimeout(()=>el.remove(),2200);}else{alert(msg)}}catch(e){alert(msg)}}

// Dropdown hover support for desktop
document.addEventListener('DOMContentLoaded', function() {
  // Sincronizar carrito con servidor si hay sesión activa
  syncCartWithServer();

  // === Hover estable para mega-menú Catálogo (desktop) ===
  (function initCatalogHover(){
    const catalogItem = document.querySelector('.catalog-nav-item');
    const catalogLink = catalogItem?.querySelector('.catalog-toggle');
    const catalogMenu = catalogItem?.querySelector('.catalog-megamenu, .catalog-dropdown');
    
    if (!catalogItem || !catalogLink || !catalogMenu) {
      console.warn('[CATALOG MENU] Elementos no encontrados:', {catalogItem, catalogLink, catalogMenu});
      return;
    }
    
    console.log('[CATALOG MENU] Inicializado correctamente', {
      menu: catalogMenu.className,
      hasContent: catalogMenu.innerHTML.length > 100
    });

    let hideTimeout = null;
    let bound = false;

    const openMenu = () => {
      clearTimeout(hideTimeout);
      catalogItem.classList.add('show');
      catalogMenu.classList.add('show');
      catalogLink.setAttribute('aria-expanded', 'true');
    };
    const closeMenu = () => {
      hideTimeout = setTimeout(() => {
        catalogItem.classList.remove('show');
        catalogMenu.classList.remove('show');
        catalogLink.setAttribute('aria-expanded', 'false');
      }, 180);
    };
    const enter = () => { openMenu(); };
    const leave = (e) => {
      // Si el mouse pasa del toggle al panel o viceversa, no cerrar
      if (catalogItem.contains(e.relatedTarget) || catalogMenu.contains(e.relatedTarget)) return;
      closeMenu();
    };
    
    // Keyboard navigation support
    const handleKeyDown = (e) => {
      if (e.key === 'Escape' && catalogItem.classList.contains('show')) {
        closeMenu();
        catalogLink.focus();
      }
      if (e.key === 'Tab' && catalogItem.classList.contains('show')) {
        // Trap focus within menu for full accessibility
        const focusable = catalogMenu.querySelectorAll('a[href], button:not([disabled])');
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          catalogLink.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          closeMenu();
        }
      }
    };
    
    function bindHover(){
      if (bound) return;
      catalogItem.addEventListener('pointerenter', enter);
      catalogItem.addEventListener('pointerleave', leave);
      catalogMenu.addEventListener('pointerenter', enter);
      catalogMenu.addEventListener('pointerleave', leave);
      catalogLink.addEventListener('keydown', handleKeyDown);
      catalogMenu.addEventListener('keydown', handleKeyDown);
      bound = true;
    }
    function unbindHover(){
      if (!bound) return;
      catalogItem.removeEventListener('pointerenter', enter);
      catalogItem.removeEventListener('pointerleave', leave);
      catalogMenu.removeEventListener('pointerenter', enter);
      catalogMenu.removeEventListener('pointerleave', leave);
      catalogLink.removeEventListener('keydown', handleKeyDown);
      catalogMenu.removeEventListener('keydown', handleKeyDown);
      bound = false;
      clearTimeout(hideTimeout);
      hideTimeout = null;
      catalogItem.classList.remove('show');
      catalogMenu.classList.remove('show');
      catalogLink.setAttribute('aria-expanded','false');
    }
    function setup(){
      if (window.innerWidth >= 992){ bindHover(); } else { unbindHover(); }
    }
    setup();
    window.addEventListener('resize', setup);
    // Importante: NO se previene el click del enlace principal; navega normal.
  })();

  // === Cambio dinámico de categorías en mega-menú Catálogo ===
  (function initCatalogSpeciesSwitch(){
    const dataEl = document.getElementById('catalogData');
    const mapping = dataEl ? safeParseJSON(dataEl.textContent) : null;
    if (!mapping) return; // nada que hacer
    const speciesButtons = document.querySelectorAll('.catalog-species-item');
    const categoriesList = document.getElementById('catalogCategories');
    const titleEl = document.getElementById('catalogCategoryTitle');
    if (!speciesButtons.length || !categoriesList) return;

    function renderCategories(speciesSlug){
      const cats = mapping[speciesSlug] || [];
      categoriesList.innerHTML = '';
      cats.forEach(cat => {
        const li = document.createElement('li');
        li.className = 'mb-1';
        const a = document.createElement('a');
        a.className = 'catalog-menu-link d-inline-block py-1 px-2 rounded-pill text-decoration-none';
        a.textContent = cat.name;
        a.href = `/?r=catalog&species=${encodeURIComponent(speciesSlug)}&category=${encodeURIComponent(cat.slug)}`;
        li.appendChild(a);
        categoriesList.appendChild(li);
      });
      if (titleEl) {
        const activeBtn = document.querySelector(`.catalog-species-item.active`);
        const spName = activeBtn ? activeBtn.textContent.trim() : 'Categorías';
        titleEl.textContent = `Categorías - ${spName.replace(/^[^A-Za-zÁÉÍÓÚáéíóúÑñ]+/, '')}`;
      }
    }

    speciesButtons.forEach(btn => {
      const slug = btn.getAttribute('data-species');
      const activate = () => {
        speciesButtons.forEach(b => { b.classList.remove('active'); b.setAttribute('aria-selected','false'); });
        btn.classList.add('active');
        btn.setAttribute('aria-selected','true');
        renderCategories(slug);
      };
      btn.addEventListener('mouseenter', activate);
      btn.addEventListener('focus', activate);
      btn.addEventListener('click', activate);
    });
  })();

  // Search overlay toggle
  const t=document.getElementById('search-toggle');
  const overlay=document.getElementById('search-overlay');
  const dim=document.getElementById('search-dim');
  if(t && overlay){ t.addEventListener('click',()=>openSearchOverlay()); }
  document.addEventListener('keydown',e=>{ if(e.key==='Escape' && overlay && overlay.classList.contains('active')){ closeSearchOverlay(); }});
  syncHeaderCounts();
  updateFavButtons();
});

function safeParseJSON(str){
  try { return JSON.parse(str); } catch(e){ return null; }
}

function headerSearchSubmit(e){
  return true;
}

function openSearchOverlay(){
  const overlay=document.getElementById('search-overlay');
  const dim=document.getElementById('search-dim');
  if(!overlay) return;
  overlay.classList.add('active');
  if(dim) dim.classList.add('active');
  overlay.setAttribute('aria-hidden','false');
  const input=document.getElementById('header-search-input');
  if(input){ setTimeout(()=>input.focus(),50); }
  document.body.classList.add('search-open');
}
function closeSearchOverlay(){
  const overlay=document.getElementById('search-overlay');
  const dim=document.getElementById('search-dim');
  if(!overlay) return;
  overlay.classList.remove('active');
  if(dim) dim.classList.remove('active');
  overlay.setAttribute('aria-hidden','true');
  document.body.classList.remove('search-open');
}

// Page helpers for favorites/cart pages
function refreshFavPage(){const favLink=document.getElementById('fav-link'); if(favLink){window.location.href=favLink.href;}}
function refreshCartPage(){const cartLink=document.getElementById('cart-link'); if(cartLink){window.location.href=cartLink.href;}}

// Sincroniza carrito localStorage con servidor (si hay sesión)
async function syncCartWithServer() {
  try {
    const localCart = getCart();
    if (localCart.length === 0) return;
    const res = await fetch('/api/cart_sync.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({productIds: localCart})
    });
    if (res.ok) {
      const data = await res.json();
      if (data.success && data.cartIds) {
        // Opcional: reemplazar localStorage con IDs del servidor
        saveCart(data.cartIds);
        syncHeaderCounts();
      }
    }
  } catch(e) {
    // Sin sesión o error de red, continuar con localStorage
  }
}
