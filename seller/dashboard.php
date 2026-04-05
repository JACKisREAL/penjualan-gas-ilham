<?php
require_once '../config/database.php';
requireSeller();

$pageTitle = 'Dashboard Seller - GasKu';
$sellerId = $_SESSION['user_id'];
$db = getDB();

// Stats
$totalProduk = $db->query("SELECT COUNT(*) as c FROM produk WHERE seller_id = $sellerId")->fetch_assoc()['c'];
$totalPesanan = $db->query("SELECT COUNT(*) as c FROM pesanan WHERE seller_id = $sellerId")->fetch_assoc()['c'];
$pendingPesanan = $db->query("SELECT COUNT(*) as c FROM pesanan WHERE seller_id = $sellerId AND status = 'pending'")->fetch_assoc()['c'];
$pendapatan = $db->query("SELECT COALESCE(SUM(total_harga),0) as total FROM pesanan WHERE seller_id = $sellerId AND status = 'selesai'")->fetch_assoc()['total'];
$stokRendah = $db->query("SELECT COUNT(*) as c FROM produk WHERE seller_id = $sellerId AND stok < 10 AND status = 'aktif'")->fetch_assoc()['c'];

// Pesanan terbaru
$pesananBaru = $db->query("
    SELECT p.*, u.nama as nama_buyer, u.no_telepon
    FROM pesanan p JOIN users u ON p.buyer_id = u.id
    WHERE p.seller_id = $sellerId
    ORDER BY p.created_at DESC LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Produk stok rendah
$produkStokRendah = $db->query("
    SELECT * FROM produk WHERE seller_id = $sellerId AND stok < 10 AND status = 'aktif' ORDER BY stok ASC
")->fetch_all(MYSQLI_ASSOC);

$db->close();
include '../includes/header.php';
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="font-family:'Nunito',sans-serif">
                Halo, <?= htmlspecialchars($_SESSION['nama']) ?> 👋
            </h4>
            <p class="text-muted mb-0 small">Kelola toko gas Anda dengan mudah</p>
        </div>
        <div class="d-flex gap-2">
            <a href="produk.php?action=tambah" class="btn btn-primary-gashu">
                <i class="bi bi-plus-circle me-2"></i>Tambah Produk
            </a>
        </div>
    </div>

    <?php if ($pendingPesanan > 0): ?>
        <div class="alert border-0 rounded-3 mb-4" style="background:#FFF3CD;border-left:4px solid #FFD700!important">
            <i class="bi bi-bell-fill text-warning me-2"></i>
            Ada <strong><?= $pendingPesanan ?> pesanan baru</strong> menunggu konfirmasi!
            <a href="pesanan.php" class="fw-bold ms-2">Lihat Pesanan →</a>
        </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?= $totalProduk ?></div>
                <div class="small text-muted">Total Produk</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-top-color:#27AE60">
                <div class="stat-number" style="color:#27AE60"><?= $totalPesanan ?></div>
                <div class="small text-muted">Total Pesanan</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-top-color:#E74C3C">
                <div class="stat-number" style="color:#E74C3C"><?= $pendingPesanan ?></div>
                <div class="small text-muted">Menunggu Konfirmasi</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card" style="border-top-color:#3498DB">
                <div class="stat-number" style="color:#3498DB;font-size:1.4rem">
                    <?= $pendapatan > 0 ? 'Rp '.number_format($pendapatan/1000000, 1).'jt' : 'Rp 0' ?>
                </div>
                <div class="small text-muted">Pendapatan</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Pesanan Terbaru -->
        <div class="col-lg-7">
            <div class="card-gashu p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Pesanan Terbaru</h6>
                    <a href="pesanan.php" class="btn btn-sm btn-outline-gashu">Lihat Semua</a>
                </div>
                <?php if (empty($pesananBaru)): ?>
                    <p class="text-muted small text-center py-3">Belum ada pesanan</p>
                <?php else: ?>
                    <?php foreach ($pesananBaru as $p): ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold small"><?= htmlspecialchars($p['kode_pesanan']) ?></div>
                                <small class="text-muted">
                                    <?= htmlspecialchars($p['nama_buyer']) ?> &bull;
                                    <?= date('d M, H:i', strtotime($p['created_at'])) ?>
                                </small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold small" style="color:var(--primary)"><?= formatRupiah($p['total_harga']) ?></div>
                                <span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stok Rendah -->
        <div class="col-lg-5">
            <div class="card-gashu p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">⚠️ Stok Rendah</h6>
                    <a href="produk.php" class="btn btn-sm btn-outline-gashu">Kelola</a>
                </div>
                <?php if (empty($produkStokRendah)): ?>
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle text-success fs-3"></i>
                        <p class="text-muted small mt-2">Semua stok aman</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($produkStokRendah as $pr): ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold small"><?= htmlspecialchars($pr['nama_gas']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($pr['ukuran']) ?></small>
                            </div>
                            <div class="text-end">
                                <span class="badge badge-stock <?= $pr['stok'] == 0 ? 'low' : 'medium' ?>">
                                    <?= $pr['stok'] ?> tabung
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
