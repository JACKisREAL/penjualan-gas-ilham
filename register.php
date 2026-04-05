<?php
require_once 'config/database.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'buyer';
    $no_telepon = trim($_POST['no_telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Semua field wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $db = getDB();
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (nama, email, password, role, no_telepon, alamat) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('ssssss', $nama, $email, $hash, $role, $no_telepon, $alamat);
            if ($stmt->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'Terjadi kesalahan, coba lagi.';
            }
        }
        $db->close();
    }
}

$pageTitle = 'Daftar - GasKu';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card-gashu p-4 p-md-5">
                <div class="text-center mb-4">
                    <div style="font-size:3rem">🔥</div>
                    <h3 class="fw-bold mt-2" style="font-family:'Nunito',sans-serif">Buat Akun GasKu</h3>
                    <p class="text-muted small">Bergabung sebagai Pembeli atau Penjual</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-3 border-0 small"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Role -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Daftar Sebagai</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleBuyer" value="buyer" checked>
                                <label class="btn btn-outline-secondary w-100 rounded-3" for="roleBuyer">
                                    <i class="bi bi-person-check d-block fs-4 mb-1"></i>Pembeli
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleSeller" value="seller">
                                <label class="btn btn-outline-secondary w-100 rounded-3" for="roleSeller">
                                    <i class="bi bi-shop d-block fs-4 mb-1"></i>Penjual
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Anda"
                               value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="contoh@email.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" placeholder="08xxxxxxxxxx"
                               value="<?= htmlspecialchars($_POST['no_telepon'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                    </div>
                    <button type="submit" class="btn btn-primary-gashu w-100 py-2">
                        <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                    </button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">Sudah punya akun?
                        <a href="login.php" class="fw-bold" style="color:var(--primary)">Masuk</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
