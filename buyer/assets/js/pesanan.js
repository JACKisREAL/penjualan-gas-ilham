// ============================
//  GasKu — Orders Page
// ============================

// Mock orders if none exist
function seedOrders() {
  const existing = JSON.parse(localStorage.getItem('gasku_orders') || '[]');
  if (existing.length) return;
  const mock = [
    { id: 'GK291847', items: [{ name: 'LPG 3 kg', emoji: '🛢️', qty: 2, price: 21000 }], total: 47000, status: 'delivery', payment: 'cod', address: 'Jl. Malioboro No. 15, Yogyakarta', receiver: 'Budi Santoso', date: new Date(Date.now() - 3600000).toISOString() },
    { id: 'GK184726', items: [{ name: 'LPG 12 kg', emoji: '🔴', qty: 1, price: 215000 }], total: 220000, status: 'process', payment: 'transfer', address: 'Jl. Malioboro No. 15, Yogyakarta', receiver: 'Budi Santoso', date: new Date(Date.now() - 7200000).toISOString() },
  ];
  localStorage.setItem('gasku_orders', JSON.stringify(mock));
}

const STATUS_MAP = {
  active:    { label: 'Menunggu Konfirmasi', cls: 'status-active' },
  process:   { label: 'Sedang Diproses',     cls: 'status-process' },
  delivery:  { label: 'Dalam Pengiriman',    cls: 'status-delivery' },
  done:      { label: 'Selesai',             cls: 'status-done' },
  cancelled: { label: 'Dibatalkan',          cls: 'status-cancelled' },
};

function formatDate(iso) {
  return new Date(iso).toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
}

let currentTab = 'active';

function renderOrders() {
  const list = document.getElementById('ordersList');
  if (!list) return;
  const all = JSON.parse(localStorage.getItem('gasku_orders') || '[]');
  const filtered = currentTab === 'active'
    ? all.filter(o => ['active','process','delivery'].includes(o.status))
    : all.filter(o => o.status === currentTab);

  if (!filtered.length) {
    list.innerHTML = `<div style="text-align:center;padding:60px 24px;color:var(--text-2)"><div style="font-size:56px;margin-bottom:12px">📦</div><h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin-bottom:6px">Belum ada pesanan</h3><p style="font-size:14px">Yuk, pesan gas sekarang!</p><a href="produk.html" class="btn-primary" style="margin-top:16px;display:inline-block">Pesan Sekarang</a></div>`;
    return;
  }

  list.innerHTML = filtered.reverse().map(o => {
    const s = STATUS_MAP[o.status] || STATUS_MAP.active;
    const itemsHtml = o.items.map(i => `<div class="order-item-pill">${i.emoji} ${i.name} ×${i.qty}</div>`).join('');
    const actions = o.status === 'delivery'
      ? `<button class="btn-sm btn-sm-brand" onclick="openTrack('${o.id}')">🛵 Lacak</button>`
      : o.status === 'done'
      ? `<button class="btn-sm btn-sm-ghost" onclick="reorder('${o.id}')">🔄 Pesan Lagi</button>`
      : o.status === 'active'
      ? `<button class="btn-sm btn-sm-ghost" style="color:var(--danger);border-color:var(--danger)" onclick="cancelOrder('${o.id}')">❌ Batalkan</button>`
      : '';
    return `
      <div class="order-card" id="oc-${o.id}">
        <div class="order-card-head">
          <div>
            <div class="order-id">#${o.id}</div>
            <div class="order-date">${formatDate(o.date)}</div>
          </div>
          <span class="status-badge ${s.cls}">${s.label}</span>
        </div>
        <div class="order-items">${itemsHtml}</div>
        <div class="order-card-foot">
          <div class="order-total">${formatRupiah(o.total)}</div>
          <div class="order-actions">${actions}</div>
        </div>
      </div>`;
  }).join('');
}

function openTrack(id) {
  const overlay = document.getElementById('trackOverlay');
  const content = document.getElementById('trackContent');
  const steps = [
    { label: 'Pesanan Diterima', time: 'Hari ini, 09:15', done: true },
    { label: 'Pesanan Diproses', time: 'Hari ini, 09:30', done: true },
    { label: 'Kurir Berangkat', time: 'Hari ini, 10:00', done: true, current: true },
    { label: 'Pesanan Tiba', time: 'Estimasi 10:30–11:00', done: false },
  ];
  content.innerHTML = `<div class="track-steps">${steps.map((s, i) => `
    <div class="track-step">
      <div class="track-dot-wrap">
        <div class="track-dot ${s.done ? (s.current ? 'current' : 'done') : ''}"></div>
        ${i < steps.length - 1 ? `<div class="track-line ${s.done && !s.current ? 'done' : ''}"></div>` : ''}
      </div>
      <div class="track-info">
        <strong>${s.label}</strong>
        <span>${s.time}</span>
      </div>
    </div>`).join('')}
  </div>
  <div style="background:#f8f8fc;border-radius:12px;padding:16px;margin-top:16px;font-size:14px;color:var(--text-2)">
    🛵 Kurir: <strong style="color:var(--text)">Pak Joko</strong> · 0812-9999-0000
  </div>`;
  if (overlay) overlay.style.display = 'flex';
  setTimeout(() => overlay?.classList.add('active'), 10);
}

function cancelOrder(id) {
  if (!confirm('Yakin ingin membatalkan pesanan ini?')) return;
  const orders = JSON.parse(localStorage.getItem('gasku_orders') || '[]');
  const idx = orders.findIndex(o => o.id === id);
  if (idx > -1) { orders[idx].status = 'cancelled'; localStorage.setItem('gasku_orders', JSON.stringify(orders)); }
  renderOrders();
  showToast('Pesanan dibatalkan', 'error');
}

function reorder(id) {
  const orders = JSON.parse(localStorage.getItem('gasku_orders') || '[]');
  const order = orders.find(o => o.id === id);
  if (order) { order.items.forEach(i => Cart.add(i)); }
  showToast('✅ Ditambahkan ke keranjang!', 'success');
}

document.addEventListener('DOMContentLoaded', () => {
  seedOrders();
  renderOrders();

  document.querySelectorAll('.order-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.order-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      currentTab = tab.dataset.tab;
      renderOrders();
    });
  });

  document.getElementById('trackClose')?.addEventListener('click', () => {
    const overlay = document.getElementById('trackOverlay');
    overlay?.classList.remove('active');
    setTimeout(() => { if (overlay) overlay.style.display = 'none'; }, 300);
  });
});
