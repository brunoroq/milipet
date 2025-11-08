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
function toggleFav(id){id=parseInt(id);if(!id)return;let f=getFavs();if(f.includes(id)){f=f.filter(x=>x!==id)}else{f.push(id)}saveFavs(f);syncHeaderCounts();updateFavButtons();}
function removeFromFavs(id){id=parseInt(id);let f=getFavs().filter(x=>x!==id);saveFavs(f);syncHeaderCounts();updateFavButtons();}

// --- UI helpers ---
function syncHeaderCounts(){try{const cc=getCart().length;const fc=getFavs().length;const cEl=document.getElementById('cart-count');const fEl=document.getElementById('fav-count');if(cEl){cEl.textContent=cc; cEl.style.display=cc? 'inline-block':'none'}if(fEl){fEl.textContent=fc; fEl.style.display=fc? 'inline-block':'none'}
  // Actualizar enlaces con IDs para vistas server-side
  const cartLink=document.getElementById('cart-link'); if(cartLink){const ids=getCart().join(','); const base=cartLink.getAttribute('data-base')||cartLink.href; cartLink.setAttribute('data-base', base.split('?')[0]); const hrefBase=cartLink.getAttribute('data-base'); cartLink.href=hrefBase + (ids? ('?r=cart&ids='+ids):'?r=cart');}
  const favLink=document.getElementById('fav-link'); if(favLink){const ids=getFavs().join(','); const base=favLink.getAttribute('data-base')||favLink.href; favLink.setAttribute('data-base', base.split('?')[0]); const hrefBase=favLink.getAttribute('data-base'); favLink.href=hrefBase + (ids? ('?r=favorites&ids='+ids):'?r=favorites');}
}catch(e){}}

function updateFavButtons(){try{const favs=new Set(getFavs());document.querySelectorAll('[data-fav-id]').forEach(btn=>{const id=parseInt(btn.getAttribute('data-fav-id'));if(favs.has(id)){btn.classList.add('active');btn.setAttribute('aria-pressed','true');}else{btn.classList.remove('active');btn.setAttribute('aria-pressed','false');}})}catch(e){}}

function toast(msg){try{if(window.bootstrap){const el=document.createElement('div');el.className='toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3';el.role='status';el.ariaLive='polite';el.innerHTML='<div class="d-flex"><div class="toast-body">'+msg+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';document.body.appendChild(el);new bootstrap.Toast(el,{delay:1800}).show();setTimeout(()=>el.remove(),2200);}else{alert(msg)}}catch(e){alert(msg)}}

// Dropdown hover support for desktop
document.addEventListener('DOMContentLoaded', function() {
  // Sincronizar carrito con servidor si hay sesión activa
  syncCartWithServer();
  
  if (window.innerWidth >= 992) {
    const dropdowns = document.querySelectorAll('.navbar .dropdown');
    dropdowns.forEach(dropdown => {
      let timeout;
      dropdown.addEventListener('mouseenter', function() {
        clearTimeout(timeout);
        const toggle = this.querySelector('[data-bs-toggle="dropdown"]');
        if (toggle && !this.querySelector('.dropdown-menu').classList.contains('show')) {
          toggle.click();
        }
      });
      dropdown.addEventListener('mouseleave', function() {
        const toggle = this.querySelector('[data-bs-toggle="dropdown"]');
        timeout = setTimeout(() => {
          if (toggle && this.querySelector('.dropdown-menu').classList.contains('show')) {
            toggle.click();
          }
        }, 300);
      });
    });
  }
  // Search overlay toggle
  const t=document.getElementById('search-toggle');
  const overlay=document.getElementById('search-overlay');
  const dim=document.getElementById('search-dim');
  if(t && overlay){
    t.addEventListener('click',()=>openSearchOverlay());
  }
  document.addEventListener('keydown',e=>{ if(e.key==='Escape' && overlay && overlay.classList.contains('active')){ closeSearchOverlay(); }});
  syncHeaderCounts();
  updateFavButtons();
});

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
