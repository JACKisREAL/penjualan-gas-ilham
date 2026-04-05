<?php
require_once '../config/database.php';
requireBuyer();

$pageTitle = 'Keranjang Belanja - GasKu';

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

$keranjang = $_SESSION['keranjang'];
$totalHarga = 0;
foreach ($keranjang as $item) {
    $totalHarga += $item['harga'] * $item['jumlah'];
}

// Ambil alamat user
$db = getDB();
$user = $db->prepare("SELECT * FROM users WHERE id = ?");
$user->bind_param('i', $_SESSION['user_id']);
$user->execute();
$userData = $user->get_result()->fetch_assoc();
$db->close();

include '../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="../index.php" class="btn btn-light btn-sm rounded-circle">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0" style="font-family:'Nunito',sans-serif">
            <i class="bi bi-cart3 text-primary me-2" style="color:var(--primary)!important"></i>
            Keranjang Belanja
        </h4>
    </div>

    <?php if (empty($keranjang)): ?>
        <div class="text-center py-5">
            <div style="font-size:5rem;opacity:0.3">🛒</div>
            <h5 class="text-muted mt-3">Keranjang masih kosong</h5>
            <p class="text-muted">Yuk mulai belanja gas untuk kebutuhan Anda!</p>
            <a href="../index.php" class="btn btn-primary-gashu mt-2">
                <i class="bi bi-shop me-2"></i>Lihat Produk
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Item Keranjang -->
            <div class="col-lg-8">
                <div class="card-gashu p-3 p-md-4">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom">
                        <?= count($keranjang) ?> produk dalam keranjang
                    </h6>
                    <?php foreach ($keranjang as $pid => $item): ?>
                        <div class="cart-item d-flex align-items-center gap-3 mb-3" id="cart-item-<?= $pid ?>">
                            <div style="background:linear-gradient(135deg,#FF6B35,#C0392B);border-radius:12px;padding:15px;flex-shrink:0">
                                <span style="font-size:1.8rem">🔥</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold"><?= htmlspecialchars($item['nama']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($item['ukuran']) ?> &bull; <?= formatRupiah($item['harga']) ?>/tabung</small>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <div class="qty-control">
                                        <button class="qty-btn qty-minus-cart" data-id="<?= $pid ?>">-</button>
                                        <input type="number" class="qty-input cart-qty"
                                               value="<?= $item['jumlah'] ?>"
                                               min="1" max="<?= $item['stok'] ?>"
                                               data-harga="<?= $item['harga'] ?>"
                                               data-id="<?= $pid ?>">
                                        <button class="qty-btn qty-plus-cart" data-id="<?= $pid ?>">+</button>
                                    </div>
                                    <span class="fw-bold item-subtotal-<?= $pid ?>" style="color:var(--primary)">
                                        <?= formatRupiah($item['harga'] * $item['jumlah']) ?>
                                    </span>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-link text-danger p-0 btn-hapus-item" data-id="<?= $pid ?>">
                                <i class="bi bi-trash fs-5"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Ringkasan & Checkout -->
            <div class="col-lg-4">
                <div class="sidebar">
                    <h6 class="sidebar-title fw-bold">Ringkasan Pesanan</h6>

                    <?php foreach ($keranjang as $pid => $item): ?>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted"><?= htmlspecialchars($item['nama']) ?> x<span class="qty-label-<?= $pid ?>"><?= $item['jumlah'] ?></span></span>
                            <span class="fw-semibold item-subtotal-display-<?= $pid ?>"><?= formatRupiah($item['harga'] * $item['jumlah']) ?></span>
                        </div>
                    <?php endforeach; ?>

                    <hr>
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total</span>
                        <span style="color:var(--primary)" id="totalHarga"><?= formatRupiah($totalHarga) ?></span>
                    </div>

                    <!-- Alamat Pengiriman -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alamat Pengiriman</label>
                        <textarea id="alamatPengiriman" class="form-control" rows="2" placeholder="Alamat pengiriman"><?= htmlspecialchars($userData['alamat'] ?? '') ?></textarea>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Metode Pembayaran</label>
                        <select id="metodePembayaran" class="form-select">
                            <option value="transfer">Transfer Bank</option>
                            <option value="tunai">Bayar di Tempat (COD)</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Catatan (opsional)</label>
                        <input type="text" id="catatan" class="form-control" placeholder="Catatan untuk penjual">
                    </div>

                    <button id="btnCheckout" class="btn btn-primary-gashu w-100 py-2">
                        <i class="bi bi-bag-check me-2"></i>Pesan Sekarang
                    </button>

                    <button id="btnKosongkan" class="btn btn-outline-danger w-100 mt-2 py-2">
                        <i class="bi bi-trash me-2"></i>Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

<script>
const hargaData = <?= json_encode(array_combine(
    array_keys($keranjang),
    array_column($keranjang, 'harga')
)) ?>;

// Update qty cart
function updateCartItem(id, qty) {
    fetch('keranjang_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action:'update', produk_id: id, jumlah: qty})
    }).then(r => r.json()).then(data => {
        recalcTotal();
    });
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.cart-qty').forEach(input => {
        const id = input.dataset.id;
        const harga = parseFloat(hargaData[id] || 0);
        const qty = parseInt(input.value);
        const sub = harga * qty;
        total += sub;
        document.querySelector(`.item-subtotal-${id}`).textContent = 'Rp ' + sub.toLocaleString('id-ID');
        document.querySelector(`.item-subtotal-display-${id}`).textContent = 'Rp ' + sub.toLocaleString('id-ID');
        const ql = document.querySelector(`.qty-label-${id}`);
        if (ql) ql.textContent = qty;
    });
    document.getElementById('totalHarga').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

document.querySelectorAll('.qty-minus-cart').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const input = document.querySelector(`.cart-qty[data-id="${id}"]`);
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            updateCartItem(id, parseInt(input.value));
        }
    });
});

document.querySelectorAll('.qty-plus-cart').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const input = document.querySelector(`.cart-qty[data-id="${id}"]`);
        const max = parseInt(input.getAttribute('max'));
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
            updateCartItem(id, parseInt(input.value));
        }
    });
});

document.querySelectorAll('.cart-qty').forEach(input => {
    input.addEventListener('change', function() {
        updateCartItem(this.dataset.id, parseInt(this.value));
    });
});

document.querySelectorAll('.btn-hapus-item').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        if (confirm('Hapus item ini dari keranjang?')) {
            fetch('keranjang_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'hapus', produk_id: id})
            }).then(r => r.json()).then(data => {
                document.getElementById(`cart-item-${id}`).remove();
                recalcTotal();
            });
        }
    });
});

document.getElementById('btnKosongkan')?.addEventListener('click', function() {
    if (confirm('Kosongkan seluruh keranjang?')) {
        fetch('keranjang_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'kosongkan'})
        }).then(() => location.reload());
    }
});

document.getElementById('btnCheckout')?.addEventListener('click', function() {
    const alamat = document.getElementById('alamatPengiriman').value.trim();
    const metode = document.getElementById('metodePembayaran').value;
    const catatan = document.getElementById('catatan').value.trim();

    if (!alamat) {
        showToast('Masukkan alamat pengiriman!', 'danger');
        return;
    }

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

    fetch('checkout.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({alamat, metode, catatan})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'pesanan.php?success=1&kode=' + data.kode;
        } else {
            showToast(data.message || 'Gagal checkout', 'danger');
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-bag-check me-2"></i>Pesan Sekarang';
        }
    });
});
</script>
