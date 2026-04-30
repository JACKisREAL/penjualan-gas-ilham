<?php
require_once '../config/database.php';
requireBuyer();

$pageTitle = 'Pesanan Saya - GasKu';

$db = getDB();
$stmt = $db->prepare("
    SELECT p.*, u.nama as nama_seller, u.no_telepon as telp_seller,
           (SELECT GROUP_CONCAT(pr.nama_gas, ' x', dp.jumlah SEPARATOR ', ')
            FROM detail_pesanan dp JOIN produk pr ON dp.produk_id = pr.id
            WHERE dp.pesanan_id = p.id) as items
    FROM pesanan p
    JOIN users u ON p.seller_id = u.id
    WHERE p.buyer_id = ?
    ORDER BY p.created_at DESC
");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$pesanans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$db->close();

$pesananSukses = null;
if (isset($_GET['success'], $_GET['kode'])) {
    foreach ($pesanans as $p) {
        if ($p['kode_pesanan'] === $_GET['kode']) {
            $pesananSukses = $p;
            break;
        }
    }
}

include '../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="../index.php" class="btn btn-light btn-sm rounded-circle">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0" style="font-family:'Nunito',sans-serif">
            <i class="bi bi-bag me-2" style="color:var(--primary)"></i>Pesanan Saya
        </h4>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success rounded-3 border-0 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Pesanan berhasil!</strong> Kode pesanan Anda: <strong><?= htmlspecialchars($_GET['kode']) ?></strong>
            <br><small>Seller akan segera mengkonfirmasi pesanan Anda.</small>
            <?php if ($pesananSukses): ?>
                <?php $linkSuksesBayar = buatLinkKonfirmasiBayar($pesananSukses['telp_seller'], $pesananSukses['kode_pesanan'], $_SESSION['nama']); ?>
                <div class="mt-3">
                    <a class="btn btn-success btn-sm"
                       href="<?= htmlspecialchars($linkSuksesBayar) ?>"
                       target="_blank"
                       rel="noopener">
                        <i class="bi bi-whatsapp me-1"></i>Saya Sudah Bayar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($pesanans)): ?>
        <div class="text-center py-5">
            <div style="font-size:5rem;opacity:0.3">📦</div>
            <h5 class="text-muted mt-3">Belum ada pesanan</h5>
            <a href="../index.php" class="btn btn-primary-gashu mt-3">Belanja Sekarang</a>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($pesanans as $p): ?>
                <?php $linkKonfirmasiBayar = buatLinkKonfirmasiBayar($p['telp_seller'], $p['kode_pesanan'], $_SESSION['nama']); ?>
                <div class="col-12">
                    <div class="card-gashu p-3 p-md-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <span class="fw-bold"><?= htmlspecialchars($p['kode_pesanan']) ?></span>
                                <span class="status-badge status-<?= $p['status'] ?> ms-2">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <?= date('d M Y, H:i', strtotime($p['created_at'])) ?>
                            </small>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Produk</small>
                                <span class="fw-semibold small"><?= htmlspecialchars($p['items'] ?? '-') ?></span>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Penjual</small>
                                <span class="fw-semibold small"><?= htmlspecialchars($p['nama_seller']) ?></span>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <small class="text-muted d-block">Total</small>
                                <span class="fw-bold" style="color:var(--primary)"><?= formatRupiah($p['total_harga']) ?></span>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars(substr($p['alamat_pengiriman'], 0, 60)) ?>...
                            </small>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-success"
                                   href="<?= htmlspecialchars($linkKonfirmasiBayar) ?>"
                                   target="_blank"
                                   rel="noopener">
                                    <i class="bi bi-whatsapp me-1"></i>Saya Sudah Bayar
                                </a>
                                <button class="btn btn-sm btn-outline-gashu" onclick="lihatDetail(<?= $p['id'] ?>)">
                                    Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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
    fetch('detail_pesanan.php?id=' + id)
        .then(r => r.text())
        .then(html => {
            document.getElementById('modalDetailBody').innerHTML = html;
        });
}
</script>
