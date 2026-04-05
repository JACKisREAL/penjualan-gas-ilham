<?php
require_once '../config/database.php';

if (!isSeller()) exit;

$id = intval($_GET['id'] ?? 0);
$sellerId = $_SESSION['user_id'];
$db = getDB();

$stmt = $db->prepare("
    SELECT p.*, u.nama as nama_buyer, u.no_telepon, u.email as email_buyer, u.alamat as alamat_buyer
    FROM pesanan p JOIN users u ON p.buyer_id = u.id
    WHERE p.id = ? AND p.seller_id = ?
");
$stmt->bind_param('ii', $id, $sellerId);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

if (!$pesanan) { echo '<p class="text-danger">Pesanan tidak ditemukan.</p>'; exit; }

$detail = $db->prepare("
    SELECT dp.*, pr.nama_gas, pr.ukuran
    FROM detail_pesanan dp JOIN produk pr ON dp.produk_id = pr.id
    WHERE dp.pesanan_id = ?
");
$detail->bind_param('i', $id);
$detail->execute();
$items = $detail->get_result()->fetch_all(MYSQLI_ASSOC);
$db->close();
?>

<div>
    <!-- Pembeli -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="p-3 rounded-3" style="background:#f8f9fa">
                <div class="small fw-bold text-muted mb-2">INFORMASI PEMBELI</div>
                <div class="fw-bold"><?= htmlspecialchars($pesanan['nama_buyer']) ?></div>
                <div class="small text-muted"><?= htmlspecialchars($pesanan['email_buyer']) ?></div>
                <div class="small text-muted"><?= htmlspecialchars($pesanan['no_telepon'] ?? '-') ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 rounded-3" style="background:#f8f9fa">
                <div class="small fw-bold text-muted mb-2">PENGIRIMAN</div>
                <div class="small"><?= htmlspecialchars($pesanan['alamat_pengiriman']) ?></div>
                <div class="small text-muted mt-1">Metode: <?= ucfirst($pesanan['metode_pembayaran']) ?></div>
            </div>
        </div>
    </div>

    <!-- Detail Item -->
    <table class="table table-gashu table-sm rounded-3 overflow-hidden">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Harga Satuan</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($item['nama_gas']) ?>
                        <small class="text-muted d-block"><?= htmlspecialchars($item['ukuran']) ?></small>
                    </td>
                    <td class="text-center"><?= $item['jumlah'] ?> tabung</td>
                    <td class="text-end"><?= formatRupiah($item['harga_satuan']) ?></td>
                    <td class="text-end fw-semibold"><?= formatRupiah($item['subtotal']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr style="background:#FFF0EB">
                <td colspan="3" class="fw-bold">TOTAL</td>
                <td class="text-end fw-bold fs-5" style="color:var(--primary)"><?= formatRupiah($pesanan['total_harga']) ?></td>
            </tr>
        </tbody>
    </table>

    <?php if ($pesanan['catatan']): ?>
        <div class="alert alert-light rounded-3 small">
            <strong>Catatan pembeli:</strong> <?= htmlspecialchars($pesanan['catatan']) ?>
        </div>
    <?php endif; ?>

    <div class="text-muted small">
        Tanggal pesanan: <?= date('d M Y, H:i', strtotime($pesanan['created_at'])) ?>
    </div>
</div>
