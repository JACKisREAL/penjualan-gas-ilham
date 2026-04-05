// ============================
//  GasKu — Cart Page
// ============================
const PROMO_CODES = { 'GASKU10': 10, 'NEWUSER': 15, 'GRATIS': 5 };
let appliedDiscount = 0;

function renderCart() {
  const items = Cart.getAll();
  const container = document.getElementById('cartItems');
  if (!container) return;

  if (!items.length) {
    container.innerHTML = `
      <div class="empty-cart">
        <div class="empty-icon">🛒</div>
        <h3>Keranjang Masih Kosong</h3>
        <p style="color:var(--text-2);margin-bottom:20px">Yuk, tambahkan gas yang Anda butuhkan</p>
        <a href="produk.html" class="btn-primary">Pilih Produk</a>
      </div>`;
    updateSummary([]);
    return;
  }

  container.innerHTML = items.map(item => `
    <div class="cart-item" id="item-${item.id}">
      <div class="cart-item-img">${item.emoji}</div>
      <div class="cart-item-info">
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-sub">${formatRupiah(item.price)} / tabung</div>
        <div class="cart-item-price">${formatRupiah(item.price * item.qty)}</div>
      </div>
      <div class="qty-control">
        <button class="qty-btn" onclick="changeQty('${item.id}', -1)">−</button>
        <span class="qty-val">${item.qty}</span>
        <button class="qty-btn" onclick="changeQty('${item.id}', 1)">+</button>
      </div>
      <button class="btn-remove" onclick="removeItem('${item.id}')" title="Hapus">🗑</button>
    </div>`).join('');

  updateSummary(items);
}

function changeQty(id, delta) {
  const items = Cart.getAll();
  const item = items.find(i => i.id === id);
  if (item) Cart.updateQty(id, item.qty + delta);
  renderCart();
}

function removeItem(id) {
  Cart.remove(id);
  renderCart();
  showToast('Item dihapus dari keranjang');
}

function updateSummary(items) {
  const rows = document.getElementById('summaryRows');
  const totalEl = document.getElementById('summaryTotal');
  if (!rows || !totalEl) return;

  const subtotal = items.reduce((s, i) => s + i.price * i.qty, 0);
  const ongkir = items.length ? 5000 : 0;
  const diskon = Math.round(subtotal * appliedDiscount / 100);
  const total = subtotal + ongkir - diskon;

  rows.innerHTML = `
    <div class="summary-row"><span>Subtotal (${items.reduce((s,i)=>s+i.qty,0)} tabung)</span><span>${formatRupiah(subtotal)}</span></div>
    <div class="summary-row"><span>Ongkir</span><span>${ongkir ? formatRupiah(ongkir) : '<span style="color:var(--success)">Gratis</span>'}</span></div>
    ${diskon ? `<div class="summary-row discount"><span>Diskon Promo (${appliedDiscount}%)</span><span>−${formatRupiah(diskon)}</span></div>` : ''}
  `;
  totalEl.innerHTML = `<span>Total</span><span>${formatRupiah(total > 0 ? total : 0)}</span>`;
}

document.addEventListener('DOMContentLoaded', () => {
  renderCart();

  // Promo
  document.getElementById('applyPromo')?.addEventListener('click', () => {
    const code = document.getElementById('promoCode')?.value.trim().toUpperCase();
    if (PROMO_CODES[code]) {
      appliedDiscount = PROMO_CODES[code];
      showToast(`🎉 Promo ${code} berhasil! Diskon ${appliedDiscount}%`, 'success');
      renderCart();
    } else {
      showToast('❌ Kode promo tidak valid', 'error');
    }
  });

  // Checkout
  document.getElementById('checkoutBtn')?.addEventListener('click', () => {
    const items = Cart.getAll();
    if (!items.length) { showToast('Keranjang masih kosong!', 'error'); return; }
    const name = document.getElementById('receiverName')?.value.trim();
    const phone = document.getElementById('receiverPhone')?.value.trim();
    const addr = document.getElementById('addressFull')?.value.trim();
    if (!name || !phone || !addr) { showToast('Lengkapi data pengiriman!', 'error'); return; }

    // Simulate order
    const orderId = 'GK' + Date.now().toString().slice(-6);
    const orders = JSON.parse(localStorage.getItem('gasku_orders') || '[]');
    const payment = document.querySelector('input[name="payment"]:checked')?.value || 'cod';
    orders.push({
      id: orderId,
      items: [...items],
      total: Cart.total() + 5000,
      status: 'active',
      payment,
      address: addr,
      receiver: name,
      phone,
      date: new Date().toISOString(),
    });
    localStorage.setItem('gasku_orders', JSON.stringify(orders));
    Cart.clear();

    document.getElementById('orderIdBox').textContent = '# ' + orderId;
    document.getElementById('successOverlay').style.display = 'flex';
  });
});
