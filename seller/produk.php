<?php
require_once '../config/database.php';
requireSeller();

$pageTitle = 'Kelola Produk - GasKu';
$sellerId = $_SESSION['user_id'];
$db = getDB();
$msg = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$editId = intval($_GET['id'] ?? 0);

// Handle POST: simpan/update produk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_gas'] ?? '');
    $ukuran = trim($_POST['ukuran'] ?? '');
    $harga = floatval($_POST['harga'] ?? 0);
    $stok = intval($_POST['stok'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status = $_POST['status'] ?? 'aktif';
    $pid = intval($_POST['produk_id'] ?? 0);

    if (empty($nama) || empty($ukuran) || $harga <= 0) {
        $error = 'Nama, ukuran, dan harga wajib diisi.';
        $action = $pid ? 'edit' : 'tambah';
        $editId = $pid;
    } else {
        if ($pid > 0) {
            // Update
            $stmt = $db->prepare("UPDATE produk SET nama_gas=?, ukuran=?, harga=?, stok=?, deskripsi=?, status=?, updated_at=NOW() WHERE id=? AND seller_id=?");
            $stmt->bind_param('ssdissii', $nama, $ukuran, $harga, $stok, $deskripsi, $status, $pid, $sellerId);
            $stmt->execute();
            $msg = 'Produk berhasil diupdate!';
        } else {
            // Tambah
            $stmt = $db->prepare("INSERT INTO produk (nama_gas, ukuran, harga, stok, deskripsi, status, seller_id) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('ssdissi', $nama, $ukuran, $harga, $stok, $deskripsi, $status, $sellerId);
            $stmt->execute();
            $msg = 'Produk berhasil ditambahkan!';
        }
        $action = 'list';
    }
}

// Handle hapus
if (isset($_GET['hapus'])) {
    $pid = intval($_GET['hapus']);
    $db->prepare("DELETE FROM produk WHERE id=? AND seller_id=?")->bind_param('ii', $pid, $sellerId) && true;
    $stmt = $db->prepare("DELETE FROM produk WHERE id=? AND seller_id=?");
    $stmt->bind_param('ii', $pid, $sellerId);
    $stmt->execute();
    $msg = 'Produk berhasil dihapus!';
    $action = 'list';
}

// Update stok cepat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stok_cepat'])) {
    $pid = intval($_POST['produk_id_stok']);
    $stokBaru = intval($_POST['stok_baru']);
    $stmt = $db->prepare("UPDATE produk SET stok=? WHERE id=? AND seller_id=?");
    $stmt->bind_param('iii', $stokBaru, $pid, $sellerId);
    $stmt->execute();
    header('Location: produk.php?msg=stok_updated');
    exit;
}

// Data untuk edit
$produkEdit = null;
if ($action === 'edit' && $editId > 0) {
    $stmt = $db->prepare("SELECT * FROM produk WHERE id=? AND seller_id=?");
    $stmt->bind_param('ii', $editId, $sellerId);
    $stmt->execute();
    $produkEdit = $stmt->get_result()->fetch_assoc();
}

// List produk
$produkList = $db->query("SELECT * FROM produk WHERE seller_id=$sellerId ORDER BY status DESC, id DESC")->fetch_all(MYSQLI_ASSOC);
$db->close();

include '../includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="dashboard.php" class="btn btn-light btn-sm rounded-circle"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0" style="font-family:'Nunito',sans-serif">
                <i class="bi bi-box-seam me-2" style="color:var(--primary)"></i>Kelola Produk Gas
            </h4>
        </div>
        <?php if ($action === 'list'): ?>
            <a href="produk.php?action=tambah" class="btn btn-primary-gashu">
                <i class="bi bi-plus-circle me-2"></i>Tambah Produk
            </a>
        <?php else: ?>
            <a href="produk.php" class="btn btn-outline-gashu">
                <i class="bi bi-list me-2"></i>Kembali ke Daftar
            </a>
        <?php endif; ?>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success rounded-3 border-0 alert-dismissible">
            <i class="bi bi-check-circle me-2"></i><?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'stok_updated'): ?>
        <div class="alert alert-success rounded-3 border-0 alert-dismissible">
            <i class="bi bi-check-circle me-2"></i>Stok berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($action === 'tambah' || $action === 'edit'): ?>
        <!-- Form Produk -->
        <div class="card-gashu p-4 mb-4">
            <h5 class="fw-bold mb-4"><?= $action === 'tambah' ? 'Tambah Produk Baru' : 'Edit Produk' ?></h5>
            <?php if ($error): ?>
                <div class="alert alert-danger rounded-3 border-0 small"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="produk_id" value="<?= $produkEdit['id'] ?? 0 ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Gas</label>
                        <input type="text" name="nama_gas" class="form-control"
                               placeholder="e.g. Gas LPG Melon"
                               value="<?= htmlspecialchars($produkEdit['nama_gas'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Ukuran</label>
                        <select name="ukuran" class="form-select">
                            <?php foreach (['3 kg', '5.5 kg', '12 kg', '50 kg'] as $uk): ?>
                                <option value="<?= $uk ?>" <?= ($produkEdit['ukuran'] ?? '') === $uk ? 'selected' : '' ?>>
                                    <?= $uk ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif" <?= ($produkEdit['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= ($produkEdit['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control"
                               placeholder="22000" min="1000" step="500"
                               value="<?= $produkEdit['harga'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Stok (tabung)</label>
                        <input type="number" name="stok" class="form-control"
                               placeholder="0" min="0"
                               value="<?= $produkEdit['stok'] ?? 0 ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"
                                  placeholder="Deskripsi produk gas..."><?= htmlspecialchars($produkEdit['deskripsi'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary-gashu px-5">
                            <i class="bi bi-save me-2"></i><?= $action === 'tambah' ? 'Tambahkan Produk' : 'Simpan Perubahan' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Daftar Produk -->
    <div class="card-gashu overflow-hidden">
        <div class="table-responsive">
            <table class="table table-gashu mb-0">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Update Stok Cepat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produkList)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada produk</td></tr>
                    <?php endif; ?>
                    <?php foreach ($produkList as $pr): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($pr['nama_gas']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($pr['ukuran']) ?></small>
                            </td>
                            <td class="fw-semibold"><?= formatRupiah($pr['harga']) ?></td>
                            <td>
                                <span class="badge badge-stock <?= $pr['stok'] > 50 ? 'high' : ($pr['stok'] > 10 ? 'medium' : 'low') ?>">
                                    <?= $pr['stok'] ?> tabung
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $pr['status'] === 'aktif' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($pr['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="d-flex gap-2 align-items-center">
                                    <input type="hidden" name="stok_cepat" value="1">
                                    <input type="hidden" name="produk_id_stok" value="<?= $pr['id'] ?>">
                                    <input type="number" name="stok_baru" class="form-control form-control-sm" style="width:80px"
                                           value="<?= $pr['stok'] ?>" min="0">
                                    <button type="submit" class="btn btn-sm btn-outline-gashu">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="produk.php?action=edit&id=<?= $pr['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="produk.php?hapus=<?= $pr['id'] ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Hapus produk ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
