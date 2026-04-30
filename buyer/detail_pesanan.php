<?php
require_once '../config/database.php';

if (!isBuyer()) exit;

$id = intval($_GET['id'] ?? 0);
$db = getDB();

$stmt = $db->prepare("
    SELECT p.*, u.nama as nama_seller, u.no_telepon as telp_seller
    FROM pesanan p JOIN users u ON p.seller_id = u.id
    WHERE p.id = ? AND p.buyer_id = ?
");
$stmt->bind_param('ii', $id, $_SESSION['user_id']);
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

$linkKonfirmasiBayar = buatLinkKonfirmasiBayar($pesanan['telp_seller'], $pesanan['kode_pesanan'], $_SESSION['nama']);
?>

<div>
    <div class="d-flex justify-content-between mb-3">
        <span class="fw-bold"><?= htmlspecialchars($pesanan['kode_pesanan']) ?></span>
        <span class="status-badge status-<?= $pesanan['status'] ?>"><?= ucfirst($pesanan['status']) ?></span>
    </div>

    <table class="table table-gashu table-sm rounded-3 overflow-hidden">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Jml</th>
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
                    <td class="text-center"><?= $item['jumlah'] ?></td>
                    <td class="text-end fw-semibold"><?= formatRupiah($item['subtotal']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="table-warning">
                <td colspan="2" class="fw-bold">Total</td>
                <td class="text-end fw-bold" style="color:var(--primary)"><?= formatRupiah($pesanan['total_harga']) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="small text-muted">
        <div class="mb-1"><strong>Penjual:</strong> <?= htmlspecialchars($pesanan['nama_seller']) ?> (<?= htmlspecialchars($pesanan['telp_seller']) ?>)</div>
        <div class="mb-1"><strong>Pembayaran:</strong> <?= ucfirst($pesanan['metode_pembayaran']) ?></div>
        <div class="mb-1"><strong>Alamat:</strong> <?= htmlspecialchars($pesanan['alamat_pengiriman']) ?></div>
        <?php if ($pesanan['catatan']): ?>
            <div class="mb-1"><strong>Catatan:</strong> <?= htmlspecialchars($pesanan['catatan']) ?></div>
        <?php endif; ?>
        <div><strong>Tanggal:</strong> <?= date('d M Y, H:i', strtotime($pesanan['created_at'])) ?></div>
    </div>

    <a class="btn btn-success w-100 mt-3"
       href="<?= htmlspecialchars($linkKonfirmasiBayar) ?>"
       target="_blank"
       rel="noopener">
        <i class="bi bi-whatsapp me-2"></i>Saya Sudah Bayar
    </a>
</div>
