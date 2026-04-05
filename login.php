<?php
require_once 'config/database.php';

if (isLoggedIn()) {
    header('Location: ' . (isSeller() ? 'seller/dashboard.php' : 'index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $db->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header('Location: ' . ($user['role'] === 'seller' ? 'seller/dashboard.php' : 'index.php'));
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}

$pageTitle = 'Masuk - GasKu';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card-gashu p-4 p-md-5">
                <div class="text-center mb-4">
                    <div style="font-size:3rem">🔥</div>
                    <h3 class="fw-bold mt-2" style="font-family:'Nunito',sans-serif">Masuk ke GasKu</h3>
                    <p class="text-muted small">Pesan gas LPG dengan mudah</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-3 border-0 small"><?= $error ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success rounded-3 border-0 small">
                        <i class="bi bi-check-circle me-1"></i>Registrasi berhasil! Silakan masuk.
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="contoh@email.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary-gashu w-100 py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">Belum punya akun?
                        <a href="register.php" class="fw-bold" style="color:var(--primary)">Daftar sekarang</a>
                    </small>
                </div>

                <hr class="my-4">
                <div class="text-center">
                    <p class="small text-muted mb-2">Akun Demo:</p>
                    <code class="small d-block">Seller: seller@gasshop.com</code>
                    <code class="small d-block">Buyer: buyer@gasshop.com</code>
                    <code class="small d-block">Password: password</code>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
