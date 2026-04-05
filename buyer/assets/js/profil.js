// ============================
//  GasKu — Profile Page
// ============================

const ADDRESSES = [
  { id: 1, name: 'Rumah Utama', text: 'Jl. Malioboro No. 15, RT 02/RW 04, Kel. Sosromenduran, Kec. Gedongtengen, Yogyakarta 55271', default: true },
  { id: 2, name: 'Kantor', text: 'Jl. Gejayan No. 88, Kel. Caturtunggal, Kec. Depok, Sleman 55281', default: false },
];

function renderAddresses() {
  const container = document.getElementById('addressList');
  if (!container) return;
  container.innerHTML = ADDRESSES.map(a => `
    <div class="address-item ${a.default ? 'default' : ''}">
      <div class="addr-icon">📍</div>
      <div class="addr-info">
        <div class="addr-name">${a.name} ${a.default ? '<span class="addr-tag">Utama</span>' : ''}</div>
        <div class="addr-text">${a.text}</div>
      </div>
      <div class="addr-actions">
        <button class="btn-sm btn-sm-ghost" onclick="showToast('Fitur edit dalam pengembangan')">✏️</button>
        ${!a.default ? `<button class="btn-sm btn-sm-ghost" style="color:var(--danger);border-color:var(--danger)" onclick="showToast('Alamat dihapus')">🗑</button>` : ''}
      </div>
    </div>`).join('');
}

document.addEventListener('DOMContentLoaded', () => {
  renderAddresses();

  // Tab switching
  document.querySelectorAll('.pmenu-item[data-section]').forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      document.querySelectorAll('.pmenu-item').forEach(m => m.classList.remove('active'));
      item.classList.add('active');
      const section = item.dataset.section;
      document.querySelectorAll('.profile-section').forEach(s => s.classList.remove('active'));
      document.getElementById('section-' + section)?.classList.add('active');
    });
  });

  // Edit info toggle
  const editBtn = document.getElementById('editInfoBtn');
  const cancelBtn = document.getElementById('cancelInfoBtn');
  const saveBtn = document.getElementById('saveInfoBtn');
  const actions = document.getElementById('infoActions');

  editBtn?.addEventListener('click', () => {
    document.querySelectorAll('#infoForm input, #infoForm select').forEach(i => i.disabled = false);
    document.querySelectorAll('#genderGroup input').forEach(i => i.disabled = false);
    actions.style.display = 'flex';
    editBtn.style.display = 'none';
  });

  cancelBtn?.addEventListener('click', () => {
    document.querySelectorAll('#infoForm input, #infoForm select').forEach(i => i.disabled = true);
    document.querySelectorAll('#genderGroup input').forEach(i => i.disabled = true);
    actions.style.display = 'none';
    editBtn.style.display = '';
  });

  saveBtn?.addEventListener('click', () => {
    document.querySelectorAll('#infoForm input, #infoForm select').forEach(i => i.disabled = true);
    document.querySelectorAll('#genderGroup input').forEach(i => i.disabled = true);
    actions.style.display = 'none';
    editBtn.style.display = '';
    showToast('✅ Profil berhasil disimpan!', 'success');
  });

  // Add address
  document.getElementById('addAddrBtn')?.addEventListener('click', () => {
    showToast('Fitur tambah alamat dalam pengembangan');
  });
});
