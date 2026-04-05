<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isBuyer()) {
    echo json_encode(['success' => false, 'message' => 'Tidak diizinkan']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$alamat = trim($data['alamat'] ?? '');
$metode = $data['metode'] ?? 'transfer';
$catatan = $data['catatan'] ?? '';
$keranjang = $_SESSION['keranjang'] ?? [];

if (empty($keranjang)) {
    echo json_encode(['success' => false, 'message' => 'Keranjang kosong']);
    exit;
}

if (empty($alamat)) {
    echo json_encode(['success' => false, 'message' => 'Alamat pengiriman wajib diisi']);
    exit;
}

$db = getDB();
$db->begin_transaction();

try {
    // Hitung total & ambil seller_id dari item pertama
    $totalHarga = 0;
    $sellerId = null;

    foreach ($keranjang as $pid => $item) {
        // Cek stok terkini
        $cek = $db->prepare("SELECT stok, seller_id FROM produk WHERE id = ? AND status = 'aktif' FOR UPDATE");
        $cek->bind_param('i', $pid);
        $cek->execute();
        $produkDB = $cek->get_result()->fetch_assoc();

        if (!$produkDB || $produkDB['stok'] < $item['jumlah']) {
            throw new Exception("Stok {$item['nama']} tidak mencukupi!");
        }

        $totalHarga += $item['harga'] * $item['jumlah'];
        if (!$sellerId) $sellerId = $produkDB['seller_id'];
    }

    // Buat pesanan
    $kode = generateKodePesanan();
    $buyerId = $_SESSION['user_id'];

    $ins = $db->prepare("INSERT INTO pesanan (kode_pesanan, buyer_id, seller_id, total_harga, metode_pembayaran, alamat_pengiriman, catatan) VALUES (?,?,?,?,?,?,?)");
    $ins->bind_param('siidsss', $kode, $buyerId, $sellerId, $totalHarga, $metode, $alamat, $catatan);
    $ins->execute();
    $pesananId = $db->insert_id;

    // Simpan detail & kurangi stok
    foreach ($keranjang as $pid => $item) {
        $subtotal = $item['harga'] * $item['jumlah'];

        $det = $db->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal) VALUES (?,?,?,?,?)");
        $det->bind_param('iiids', $pesananId, $pid, $item['jumlah'], $item['harga'], $subtotal);
        $det->execute();

        $upd = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");
        $upd->bind_param('ii', $item['jumlah'], $pid);
        $upd->execute();
    }

    $db->commit();
    $_SESSION['keranjang'] = [];

    echo json_encode(['success' => true, 'kode' => $kode]);

} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$db->close();
?>
