<?php
require_once '../config/database.php';
requireSeller();

$pageTitle = 'Kelola Pesanan - GasKu';
$sellerId = $_SESSION['user_id'];
$db = getDB();

// Update status pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $pid = intval($_POST['pesanan_id']);
    $status = $_POST['status'];
    $allowed = ['pending', 'dikonfirmasi', 'dikirim', 'selesai', 'dibatalkan'];
    if (in_array($status, $allowed)) {
        $stmt = $db->prepare("UPDATE pesanan SET status=? WHERE id=? AND seller_id=?");
        $stmt->bind_param('sii', $status, $pid, $sellerId);
        $stmt->execute();
    }
    header('Location: pesanan.php?updated=1');
    exit;
}

// Filter
$filterStatus = $_GET['status'] ?? 'semua';
$allowedStatuses = ['semua', 'pending', 'dikonfirmasi', 'dikirim', 'selesai', 'dibatalkan'];
if (!in_array($filterStatus, $allowedStatuses)) {
    $filterStatus = 'semua';
}

// Ambil pesanan (Versi Debug/Perbaikan) - Menggunakan prepared statement untuk keamanan
$query = "
    SELECT p.*, 
           u.nama as nama_buyer, 
           u.no_telepon, 
           u.email as email_buyer,
           (SELECT COUNT(*) FROM detail_pesanan dp WHERE dp.pesanan_id = p.id) as jumlah_item
    FROM pesanan p
    LEFT JOIN users u ON p.buyer_id = u.id
    WHERE p.seller_id = ? " . ($filterStatus !== 'semua' ? "AND p.status = ?" : "") . "
    ORDER BY p.created_at DESC
";
$stmt = $db->prepare($query);
if ($filterStatus !== 'semua') {
    $stmt->bind_param('is', $sellerId, $filterStatus);
} else {
    $stmt->bind_param('i', $sellerId);
}
$stmt->execute();
$pesananList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$db->close();
include '../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="dashboard.php" class="btn btn-light btn-sm rounded-circle"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0" style="font-family:'Nunito',sans-serif">
                <i class="bi bi-list-check me-2" style="color:var(--primary)"></i>Kelola Pesanan
            </h4>
        </div>
        <span class="badge bg-primary rounded-pill px-3 py-2"><?= count($pesananList) ?> pesanan</span>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success rounded-3 border-0 alert-dismissible">
            <i class="bi bi-check-circle me-2"></i>Status pesanan berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Status -->
    <div class="d-flex gap-2 flex-wrap mb-4">
        <?php foreach (['semua' => 'Semua', 'pending' => 'Pending', 'dikonfirmasi' => 'Dikonfirmasi', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label): ?>
            <a href="pesanan.php?status=<?= $val ?>"
               class="btn btn-sm <?= $filterStatus === $val ? 'btn-primary-gashu' : 'btn-outline-secondary' ?> rounded-pill">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($pesananList)): ?>
        <div class="text-center py-5">
            <div style="font-size:4rem;opacity:0.3">📭</div>
            <h5 class="text-muted mt-3">Tidak ada pesanan</h5>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($pesananList as $p): ?>
                <div class="col-12">
                    <div class="card-gashu p-3 p-md-4">
                        <div class="row g-3">
                            <!-- Info Pesanan -->
                            <div class="col-md-4">
                                <div class="fw-bold"><?= htmlspecialchars($p['kode_pesanan']) ?></div>
                                <small class="text-muted"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></small>
                                <div class="mt-2">
                                    <span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
                                </div>
                            </div>

                            <!-- Info Pembeli -->
                            <div class="col-md-4">
                                <div class="small fw-semibold text-muted mb-1">PEMBELI</div>
                                <div class="fw-bold"><?= htmlspecialchars($p['nama_buyer']) ?></div>
                                <small class="text-muted d-block">
                                    <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($p['no_telepon'] ?? '-') ?>
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($p['email_buyer']) ?>
                                </small>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars(substr($p['alamat_pengiriman'], 0, 50)) ?>
                                </small>
                            </div>

                            <!-- Total & Aksi -->
                            <div class="col-md-4 text-md-end">
                                <div class="fw-bold fs-5 mb-1" style="color:var(--primary)">
                                    <?= formatRupiah($p['total_harga']) ?>
                                </div>
                                <small class="text-muted d-block mb-2">
                                    <?= $p['jumlah_item'] ?> item &bull; <?= ucfirst($p['metode_pembayaran']) ?>
                                </small>

                                <div class="d-flex gap-2 justify-content-md-end">
                                    <button class="btn btn-sm btn-outline-gashu"
                                            onclick="lihatDetail(<?= $p['id'] ?>)">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </button>

                                    <?php if ($p['status'] !== 'selesai' && $p['status'] !== 'dibatalkan'): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-primary-gashu dropdown-toggle" data-bs-toggle="dropdown">
                                                Update Status
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <?php
                                                $statusFlow = [
                                                    'pending' => [['dikonfirmasi', 'Konfirmasi'], ['dibatalkan', 'Batalkan']],
                                                    'dikonfirmasi' => [['dikirim', 'Tandai Dikirim'], ['dibatalkan', 'Batalkan']],
                                                    'dikirim' => [['selesai', 'Tandai Selesai']],
                                                ];
                                                $nextStatuses = $statusFlow[$p['status']] ?? [];
                                                foreach ($nextStatuses as [$s, $label]):
                                                ?>
                                                    <li>
                                                        <form method="POST" class="px-1">
                                                            <input type="hidden" name="update_status" value="1">
                                                            <input type="hidden" name="pesanan_id" value="<?= $p['id'] ?>">
                                                            <input type="hidden" name="status" value="<?= $s ?>">
                                                            <button type="submit" class="dropdown-item"
                                                                    onclick="return confirm('<?= $label ?> pesanan ini?')">
                                                                <?= $label ?>
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Detail Pesanan -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetailBody">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
function lihatDetail(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
    modal.show();
    fetch('detail_pesanan_seller.php?id=' + id)
        .then(r => r.text())
        .then(html => {
            document.getElementById('modalDetailBody').innerHTML = html;
        });
}
</script>
