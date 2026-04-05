// ============================
//  GasKu — Cart Manager
// ============================
const Cart = (function () {
  const KEY = 'gasku_cart';

  function getAll() {
    try { return JSON.parse(localStorage.getItem(KEY)) || []; }
    catch { return []; }
  }

  function save(cart) { localStorage.setItem(KEY, JSON.stringify(cart)); }

  function add(product, qty = 1) {
    const cart = getAll();
    const idx = cart.findIndex(i => i.id === product.id);
    if (idx > -1) cart[idx].qty += qty;
    else cart.push({ ...product, qty });
    save(cart);
    updateBadge();
  }

  function remove(id) {
    save(getAll().filter(i => i.id !== id));
    updateBadge();
  }

  function updateQty(id, qty) {
    const cart = getAll();
    const idx = cart.findIndex(i => i.id === id);
    if (idx > -1) {
      if (qty <= 0) cart.splice(idx, 1);
      else cart[idx].qty = qty;
    }
    save(cart);
    updateBadge();
  }

  function clear() { save([]); updateBadge(); }

  function total() {
    return getAll().reduce((s, i) => s + i.price * i.qty, 0);
  }

  function count() {
    return getAll().reduce((s, i) => s + i.qty, 0);
  }

  function updateBadge() {
    const badge = document.getElementById('cartBadge');
    if (badge) {
      const c = count();
      badge.textContent = c;
      badge.style.display = c > 0 ? 'flex' : 'none';
    }
  }

  return { getAll, add, remove, updateQty, clear, total, count, updateBadge };
})();

// init badge on every page
document.addEventListener('DOMContentLoaded', () => Cart.updateBadge());
