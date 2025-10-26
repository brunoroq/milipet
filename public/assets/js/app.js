const CART_KEY='milipet_cart';function getCart(){try{return JSON.parse(localStorage.getItem(CART_KEY))||[]}catch(e){return[]}}function saveCart(c){localStorage.setItem(CART_KEY,JSON.stringify(c))}function addToCart(id){const c=getCart();if(!c.includes(id)){c.push(id);saveCart(c);alert('Producto añadido al carrito.')}else{alert('Ya está en el carrito.')}}

// Dropdown hover support for desktop
document.addEventListener('DOMContentLoaded', function() {
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
});
