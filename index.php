<?php
require_once 'config/database.php';

$pageTitle = 'GasKu - Toko Gas Online Terpercaya';

// Ambil produk aktif
$db = getDB();
$produk = $db->query("SELECT p.*, u.nama as nama_seller FROM produk p JOIN users u ON p.seller_id = u.id WHERE p.status = 'aktif' ORDER BY p.id ASC");
$products = $produk->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<!-- HERO -->
<section class="hero-section">
    <div class="container position-relative" style="z-index:2">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <p class="text-warning fw-semibold mb-2 fs-6">⚡ Platform Gas LPG #1 di Yogyakarta</p>
                <h1 class="hero-title mb-3">
                    Gas Murah,<br>Antar Cepat,<br>Stok Selalu Ada!
                </h1>
                <p class="fs-5 text-white-50 mb-4">Order gas LPG kapan saja, dari mana saja. Proses mudah, harga transparan, pengiriman terjamin.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-warning fw-bold px-4 py-2 rounded-3">
                            <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                        </a>
                        <a href="login.php" class="btn btn-outline-light fw-bold px-4 py-2 rounded-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                        </a>
                    <?php else: ?>
                        <a href="#produk" class="btn btn-warning fw-bold px-4 py-2 rounded-3">
                            <i class="bi bi-cart-plus me-2"></i>Beli Sekarang
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center mt-4 mt-lg-0">
                <div style="font-size:10rem; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">🔥</div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-3 mt-4">
            <?php
            $dbStats = getDB();
            $totalProduk = $dbStats->query("SELECT COUNT(*) as c FROM produk WHERE status='aktif'")->fetch_assoc()['c'];
            $totalPesanan = $dbStats->query("SELECT COUNT(*) as c FROM pesanan WHERE status='selesai'")->fetch_assoc()['c'];
            $totalBuyer = $dbStats->query("SELECT COUNT(*) as c FROM users WHERE role='buyer'")->fetch_assoc()['c'];
            $dbStats->close();
            ?>
            <div class="col-4">
                <div class="text-center text-white">
                    <div class="fs-2 fw-bold"><?= $totalProduk ?>+</div>
                    <div class="small text-white-50">Jenis Gas</div>
                </div>
            </div>
            <div class="col-4">
                <div class="text-center text-white">
                    <div class="fs-2 fw-bold"><?= $totalPesanan ?>+</div>
                    <div class="small text-white-50">Pesanan Selesai</div>
                </div>
            </div>
            <div class="col-4">
                <div class="text-center text-white">
                    <div class="fs-2 fw-bold"><?= $totalBuyer ?>+</div>
                    <div class="small text-white-50">Pelanggan</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRODUK -->
<section id="produk" class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="font-family:'Nunito',sans-serif">Pilih Gas Anda</h2>
                <p class="text-muted mb-0">Tersedia berbagai ukuran untuk semua kebutuhan</p>
            </div>
            <?php if (isBuyer()): ?>
                <a href="buyer/keranjang.php" class="btn btn-primary-gashu d-none d-md-block">
                    <i class="bi bi-cart3 me-2"></i>Keranjang
                </a>
            <?php endif; ?>
        </div>

        <?php if (!isLoggedIn()): ?>
            <div class="alert alert-warning rounded-3 border-0" style="background:#FFF3CD">
                <i class="bi bi-info-circle me-2"></i>
                Silakan <a href="login.php" class="fw-bold">masuk</a> atau <a href="register.php" class="fw-bold">daftar</a> terlebih dahulu untuk memesan gas.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 border-0">
                <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php foreach ($products as $p): ?>
                <?php
                $stockClass = $p['stok'] > 50 ? 'high' : ($p['stok'] > 10 ? 'medium' : 'low');
                $stockLabel = $p['stok'] > 50 ? 'Stok Banyak' : ($p['stok'] > 10 ? 'Stok Terbatas' : 'Stok Menipis');
                ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="card-gashu h-100 d-flex flex-column">
                        <!-- Card Header -->
                        <div style="background:linear-gradient(135deg,#FF6B35,#C0392B);padding:30px;text-align:center">
                            <div style="font-size:4rem">🔥</div>
                            <span class="badge badge-stock <?= $stockClass ?> mt-2">
                                <?= $stockLabel ?>
                            </span>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($p['nama_gas']) ?></h5>
                            <p class="text-muted small mb-1"><i class="bi bi-box me-1"></i><?= htmlspecialchars($p['ukuran']) ?></p>
                            <p class="text-muted small mb-3 flex-grow-1"><?= htmlspecialchars($p['deskripsi']) ?></p>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold fs-5" style="color:var(--primary)"><?= formatRupiah($p['harga']) ?></span>
                                <small class="text-muted">Stok: <strong><?= $p['stok'] ?></strong></small>
                            </div>

                            <?php if (isBuyer() && $p['stok'] > 0): ?>
                                <div class="qty-control mb-3">
                                    <button class="qty-btn qty-minus">-</button>
                                    <input type="number" class="qty-input" value="1" min="1" max="<?= $p['stok'] ?>"
                                           data-harga="<?= $p['harga'] ?>" data-produk="<?= $p['id'] ?>">
                                    <button class="qty-btn qty-plus">+</button>
                                </div>
                                <button class="btn btn-primary-gashu w-100 btn-tambah-keranjang"
                                        data-id="<?= $p['id'] ?>" data-nama="<?= htmlspecialchars($p['nama_gas']) ?>"
                                        data-harga="<?= $p['harga'] ?>" data-stok="<?= $p['stok'] ?>">
                                    <i class="bi bi-cart-plus me-2"></i>Tambah ke Keranjang
                                </button>
                            <?php elseif ($p['stok'] == 0): ?>
                                <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
                            <?php elseif (!isLoggedIn()): ?>
                                <a href="login.php" class="btn btn-outline-gashu w-100">Masuk untuk Beli</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FITUR -->
<section class="py-5" style="background:#fff">
    <div class="container">
        <h2 class="text-center fw-bold mb-5" style="font-family:'Nunito',sans-serif">Kenapa Pilih GasKu?</h2>
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="p-4">
                    <div style="font-size:2.5rem;margin-bottom:12px">⚡</div>
                    <h6 class="fw-bold">Pesan Cepat</h6>
                    <p class="text-muted small">Proses pemesanan hanya dalam hitungan menit</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4">
                    <div style="font-size:2.5rem;margin-bottom:12px">💰</div>
                    <h6 class="fw-bold">Harga Transparan</h6>
                    <p class="text-muted small">Tidak ada biaya tersembunyi, harga sesuai yang tertera</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4">
                    <div style="font-size:2.5rem;margin-bottom:12px">📦</div>
                    <h6 class="fw-bold">Stok Terjamin</h6>
                    <p class="text-muted small">Stok selalu diperbarui real-time oleh seller</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4">
                    <div style="font-size:2.5rem;margin-bottom:12px">🤝</div>
                    <h6 class="fw-bold">Terpercaya</h6>
                    <p class="text-muted small">Seller terverifikasi, transaksi aman</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Floating Cart Button (Mobile) -->
<?php if (isBuyer()): ?>
    <a href="buyer/keranjang.php" class="btn btn-primary-gashu d-md-none"
       style="position:fixed;bottom:24px;right:24px;border-radius:50%;width:58px;height:58px;display:flex!important;align-items:center;justify-content:center;font-size:1.4rem;z-index:999;box-shadow:0 6px 20px rgba(255,107,53,0.5)">
        <i class="bi bi-cart3"></i>
    </a>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

<script>
// Tambah ke keranjang via localStorage (sederhana)
document.querySelectorAll('.btn-tambah-keranjang').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const nama = this.dataset.nama;
        const harga = parseFloat(this.dataset.harga);
        const stok = parseInt(this.dataset.stok);
        const input = this.closest('.p-3').querySelector('.qty-input');
        const qty = parseInt(input ? input.value : 1);

        // Simpan ke session via AJAX
        fetch('buyer/keranjang_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action:'tambah', produk_id:id, jumlah:qty})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(`${nama} (${qty} tabung) ditambahkan ke keranjang!`);
            } else {
                showToast(data.message || 'Gagal menambahkan', 'danger');
            }
        });
    });
});
</script>
