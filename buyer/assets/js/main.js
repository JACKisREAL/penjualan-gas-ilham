// ============================
//  GasKu — Main JS
// ============================

// ── Product Data ──────────────────────────────────────
const PRODUCTS = [
  {
    id: 'lpg-3kg',
    name: 'LPG 3 kg — Melon',
    shortName: 'LPG 3 kg',
    emoji: '🛢️',
    category: '3kg',
    price: 21000000,
    originalPrice: null,
    desc: 'Gas LPG bersubsidi resmi Pertamina, cocok untuk rumah tangga kecil dan warung.',
    stock: 45,
    badge: 'Subsidi',
    badgeClass: 'badge-green',
    weight: '3 kg',
    features: ['Bersegel resmi Pertamina', 'Untuk 1–2 orang', 'Berlaku subsidi pemerintah'],
  },
];

// ── Utilities ──────────────────────────────────────
function formatRupiah(num) {
  return 'Rp ' + num.toLocaleString('id-ID');
}

function showToast(msg, type = '') {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = msg;
  toast.className = 'toast show ' + type;
  setTimeout(() => { toast.className = 'toast'; }, 3000);
}

function generateProductCard(p, showAddBtn = true) {
  const stockLabel = p.stock < 10
    ? `<span class="product-stock low">⚠ Stok: ${p.stock}</span>`
    : `<span class="product-stock">Stok: ${p.stock}</span>`;
  const original = p.originalPrice
    ? `<del style="font-size:13px;color:var(--text-3);display:block">${formatRupiah(p.originalPrice)}</del>`
    : '';
  return `
    <div class="product-card" data-category="${p.category}">
      <div class="product-img">
        ${p.badge ? `<span class="product-badge ${p.badgeClass}">${p.badge}</span>` : ''}
        <span>${p.emoji}</span>
      </div>
      <div class="product-info">
        <div class="product-name">${p.name}</div>
        <div class="product-desc">${p.desc}</div>
        <div class="product-meta">
          <div>${original}<span class="product-price">${formatRupiah(p.price)}</span></div>
          ${stockLabel}
        </div>
        <div class="product-actions">
          ${showAddBtn ? `<button class="btn-addcart" onclick="addToCart('${p.id}')">+ Keranjang</button>` : ''}
          <button class="btn-detail" onclick="openModal('${p.id}')" title="Detail">👁</button>
        </div>
      </div>
    </div>`;
}

function addToCart(id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  Cart.add({ id: p.id, name: p.shortName, price: p.price, emoji: p.emoji });
  showToast(`✅ ${p.shortName} ditambahkan ke keranjang!`, 'success');
}

// ── Homepage Product Grid ─────────────────────────────
function renderHomeProducts() {
  const grid = document.getElementById('productsGrid');
  if (!grid) return;
  // Show only 3 featured
  const featured = PRODUCTS.slice(0, 3);
  grid.innerHTML = featured.map(p => generateProductCard(p)).join('');
}

// ── Modal ──────────────────────────────────────
function openModal(id) {
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  const overlay = document.getElementById('modalOverlay');
  const body = document.getElementById('modalBody');
  if (!overlay || !body) return;
  const featuresHtml = p.features
    .map(f => `<li style="margin-bottom:6px;font-size:14px;color:var(--text-2)">✅ ${f}</li>`)
    .join('');
  body.innerHTML = `
    <div style="text-align:center;font-size:80px;background:linear-gradient(135deg,#fff5f0,#ffe8da);padding:32px;border-radius:14px;margin-bottom:24px">${p.emoji}</div>
    <h2 style="font-family:var(--font-display);font-size:22px;font-weight:800;margin-bottom:8px">${p.name}</h2>
    <p style="color:var(--text-2);font-size:14px;line-height:1.6;margin-bottom:16px">${p.desc}</p>
    <ul style="list-style:none;margin-bottom:20px;padding:0">${featuresHtml}</ul>
    <div style="display:flex;justify-content:space-between;align-items:center;background:#f8f8fc;padding:16px 20px;border-radius:12px;margin-bottom:20px">
      <span style="font-size:13px;color:var(--text-2)">Berat: ${p.weight}</span>
      <span style="font-family:var(--font-display);font-size:22px;font-weight:800;color:var(--brand)">${formatRupiah(p.price)}</span>
    </div>
    <button class="btn-primary full-width" onclick="addToCart('${p.id}');closeModal()">Tambah ke Keranjang</button>
  `;
  overlay.classList.add('active');
}

function closeModal() {
  const overlay = document.getElementById('modalOverlay');
  if (overlay) overlay.classList.remove('active');
}

// ── Navbar scroll ─────────────────────────────────
function initNavbar() {
  const nav = document.getElementById('navbar');
  if (!nav) return;
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 20);
  });

  const hamburger = document.getElementById('hamburger');
  const navLinks = document.querySelector('.nav-links');
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => navLinks.classList.toggle('open'));
  }
}

// ── Init ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initNavbar();
  renderHomeProducts();

  const modalClose = document.getElementById('modalClose');
  if (modalClose) modalClose.addEventListener('click', closeModal);
  const modalOverlay = document.getElementById('modalOverlay');
  if (modalOverlay) modalOverlay.addEventListener('click', e => {
    if (e.target === modalOverlay) closeModal();
  });
});
