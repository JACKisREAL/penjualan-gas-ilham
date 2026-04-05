// ============================
//  GasKu — History Page
// ============================

const MOCK_HISTORY = [
  { id: 'GK109283', items: [{ name: 'LPG 3 kg', emoji: '🛢️', qty: 3 }], total: 68000, date: '2025-05-01T10:00:00', status: 'done' },
  { id: 'GK100293', items: [{ name: 'LPG 12 kg', emoji: '🔴', qty: 1 }, { name: 'LPG 3 kg', emoji: '🛢️', qty: 1 }], total: 241000, date: '2025-04-22T14:30:00', status: 'done' },
  { id: 'GK091827', items: [{ name: 'LPG 5,5 kg', emoji: '🔵', qty: 2 }], total: 181000, date: '2025-04-10T09:15:00', status: 'done' },
  { id: 'GK082736', items: [{ name: 'LPG 3 kg', emoji: '🛢️', qty: 4 }], total: 89000, date: '2025-03-30T11:45:00', status: 'done' },
  { id: 'GK073645', items: [{ name: 'LPG 12 kg', emoji: '🔴', qty: 1 }], total: 220000, date: '2025-03-15T08:00:00', status: 'done' },
  { id: 'GK064554', items: [{ name: 'LPG 3 kg', emoji: '🛢️', qty: 2 }], total: 47000, date: '2025-02-28T16:00:00', status: 'cancelled' },
];

function formatDate(iso) {
  return new Date(iso).toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
}

function renderHistory(list) {
  const container = document.getElementById('historyList');
  if (!container) return;
  if (!list.length) {
    container.innerHTML = `<div style="text-align:center;padding:60px;color:var(--text-2)"><div style="font-size:48px;margin-bottom:12px">📄</div><h3 style="font-family:var(--font-display);font-weight:700">Belum ada riwayat</h3></div>`;
    return;
  }
  container.innerHTML = list.map(o => {
    const itemText = o.items.map(i => `${i.emoji} ${i.name} ×${i.qty}`).join(', ');
    const sDone = o.status === 'done';
    return `
      <div class="history-row">
        <div class="history-icon">${o.items[0]?.emoji || '🛢️'}</div>
        <div class="history-info">
          <div class="history-order-id">#${o.id}</div>
          <div class="history-date">${formatDate(o.date)}</div>
          <div class="history-items">${itemText}</div>
        </div>
        <span class="status-badge ${sDone ? 'status-done' : 'status-cancelled'}" style="align-self:center">${sDone ? 'Selesai' : 'Dibatalkan'}</span>
        <div class="history-total">${formatRupiah(o.total)}</div>
        ${sDone ? `<button class="history-reorder" onclick="reorderHistory(${JSON.stringify(o.items).replace(/"/g,"'")})">🔄 Pesan Lagi</button>` : ''}
      </div>`;
  }).join('');
}

function updateStats(list) {
  const done = list.filter(o => o.status === 'done');
  document.getElementById('totalOrders').textContent = done.length;
  document.getElementById('totalSpend').textContent = formatRupiah(done.reduce((s, o) => s + o.total, 0));
  document.getElementById('totalTabung').textContent = done.reduce((s, o) => s + o.items.reduce((ss, i) => ss + i.qty, 0), 0);
}

function reorderHistory(items) {
  items.forEach(i => Cart.add({ id: i.name.replace(/\s/g,'').toLowerCase(), name: i.name, emoji: i.emoji || '🛢️', price: 21000 }));
  showToast('✅ Ditambahkan ke keranjang!', 'success');
}

document.addEventListener('DOMContentLoaded', () => {
  // Merge mock + real
  const realOrders = JSON.parse(localStorage.getItem('gasku_orders') || '[]');
  const doneCancelled = realOrders.filter(o => ['done','cancelled'].includes(o.status));
  const all = [...MOCK_HISTORY, ...doneCancelled].sort((a,b) => new Date(b.date) - new Date(a.date));

  updateStats(all);
  renderHistory(all);

  const searchEl = document.getElementById('historySearch');
  const filterEl = document.getElementById('filterMonth');

  function applyFilters() {
    const q = searchEl?.value.toLowerCase() || '';
    let list = all.filter(o => {
      const matchQ = o.id.toLowerCase().includes(q) || o.items.some(i => i.name.toLowerCase().includes(q));
      return matchQ;
    });
    renderHistory(list);
    updateStats(list);
  }

  searchEl?.addEventListener('input', applyFilters);
  filterEl?.addEventListener('change', applyFilters);
});
