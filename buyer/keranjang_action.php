<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isBuyer()) {
    echo json_encode(['success' => false, 'message' => 'Silakan masuk sebagai pembeli']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? $_POST['action'] ?? '';

// Inisialisasi keranjang di session
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

if ($action === 'tambah') {
    $produk_id = intval($data['produk_id'] ?? 0);
    $jumlah = intval($data['jumlah'] ?? 1);

    if ($produk_id <= 0 || $jumlah <= 0) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
        exit;
    }

    // Cek stok
    $db = getDB();
    $produk = $db->prepare("SELECT * FROM produk WHERE id = ? AND status = 'aktif'");
    $produk->bind_param('i', $produk_id);
    $produk->execute();
    $p = $produk->get_result()->fetch_assoc();
    $db->close();

    if (!$p) {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
        exit;
    }

    $sudahDiKeranjang = $_SESSION['keranjang'][$produk_id]['jumlah'] ?? 0;
    $totalJumlah = $sudahDiKeranjang + $jumlah;

    if ($totalJumlah > $p['stok']) {
        echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi. Tersedia: ' . $p['stok']]);
        exit;
    }

    if (isset($_SESSION['keranjang'][$produk_id])) {
        $_SESSION['keranjang'][$produk_id]['jumlah'] += $jumlah;
    } else {
        $_SESSION['keranjang'][$produk_id] = [
            'produk_id' => $produk_id,
            'nama' => $p['nama_gas'],
            'ukuran' => $p['ukuran'],
            'harga' => $p['harga'],
            'jumlah' => $jumlah,
            'stok' => $p['stok'],
            'seller_id' => $p['seller_id'],
        ];
    }

    echo json_encode(['success' => true, 'message' => 'Ditambahkan ke keranjang', 'count' => count($_SESSION['keranjang'])]);

} elseif ($action === 'update') {
    $produk_id = intval($data['produk_id'] ?? 0);
    $jumlah = intval($data['jumlah'] ?? 0);

    if ($jumlah <= 0) {
        unset($_SESSION['keranjang'][$produk_id]);
    } else {
        if (isset($_SESSION['keranjang'][$produk_id])) {
            $_SESSION['keranjang'][$produk_id]['jumlah'] = $jumlah;
        }
    }
    echo json_encode(['success' => true]);

} elseif ($action === 'hapus') {
    $produk_id = intval($data['produk_id'] ?? 0);
    unset($_SESSION['keranjang'][$produk_id]);
    echo json_encode(['success' => true]);

} elseif ($action === 'kosongkan') {
    $_SESSION['keranjang'] = [];
    echo json_encode(['success' => true]);

} else {
    echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenal']);
}
?>
