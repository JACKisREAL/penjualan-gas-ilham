// ============================
//  GasKu — Products Page
// ============================
document.addEventListener('DOMContentLoaded', () => {
  let filtered = [...PRODUCTS];
  let activeFilter = 'all';

  function render(list) {
    const grid = document.getElementById('allProducts');
    if (!grid) return;
    if (!list.length) {
      grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-2)"><div style="font-size:48px;margin-bottom:12px">🔍</div><h3 style="font-family:var(--font-display);font-size:18px;font-weight:700">Produk tidak ditemukan</h3></div>`;
      return;
    }
    grid.innerHTML = list.map(p => generateProductCard(p)).join('');
  }

  function applyFilters() {
    const q = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const sort = document.getElementById('sortSelect')?.value || 'default';
    let list = PRODUCTS.filter(p => {
      const matchCat = activeFilter === 'all' || p.category === activeFilter;
      const matchQ = p.name.toLowerCase().includes(q) || p.desc.toLowerCase().includes(q);
      return matchCat && matchQ;
    });
    if (sort === 'price-asc') list.sort((a, b) => a.price - b.price);
    else if (sort === 'price-desc') list.sort((a, b) => b.price - a.price);
    else if (sort === 'name') list.sort((a, b) => a.name.localeCompare(b.name));
    render(list);
  }

  // Chip filters
  document.querySelectorAll('.chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      activeFilter = chip.dataset.filter;
      applyFilters();
    });
  });

  document.getElementById('searchInput')?.addEventListener('input', applyFilters);
  document.getElementById('sortSelect')?.addEventListener('change', applyFilters);

  applyFilters();
});
