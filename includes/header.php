<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'GasKu - Toko Gas Online' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #E85A24;
            --secondary: #1A1A2E;
            --accent: #FFD700;
            --success: #27AE60;
            --light-bg: #FFF8F5;
            --card-shadow: 0 4px 20px rgba(255, 107, 53, 0.12);
            --radius: 16px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: var(--secondary);
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar-gashu {
            background: linear-gradient(135deg, #FF6B35 0%, #E85A24 100%);
            box-shadow: 0 4px 20px rgba(255, 107, 53, 0.35);
            padding: 12px 0;
        }

        .navbar-gashu .navbar-brand {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: 1.6rem;
            color: #fff !important;
            letter-spacing: -0.5px;
        }

        .navbar-gashu .navbar-brand span {
            color: var(--accent);
        }

        .navbar-gashu .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 6px 14px !important;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .navbar-gashu .nav-link:hover,
        .navbar-gashu .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,0.2);
        }

        .navbar-gashu .navbar-toggler {
            border-color: rgba(255,255,255,0.5);
        }

        .navbar-gashu .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.85%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .btn-navbar-login {
            background: rgba(255,255,255,0.2);
            color: #fff !important;
            border: 2px solid rgba(255,255,255,0.6);
            border-radius: 10px;
            padding: 6px 18px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-navbar-login:hover {
            background: #fff;
            color: var(--primary) !important;
        }

        /* CARDS */
        .card-gashu {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            transition: transform 0.25s, box-shadow 0.25s;
            overflow: hidden;
            background: #fff;
        }

        .card-gashu:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 35px rgba(255, 107, 53, 0.2);
        }

        /* BUTTONS */
        .btn-primary-gashu {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 24px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.35);
        }

        .btn-primary-gashu:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(255, 107, 53, 0.45);
            color: #fff;
        }

        .btn-outline-gashu {
            border: 2px solid var(--primary);
            color: var(--primary);
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 22px;
            background: transparent;
            transition: all 0.2s;
        }

        .btn-outline-gashu:hover {
            background: var(--primary);
            color: #fff;
        }

        /* BADGE */
        .badge-stock {
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .badge-stock.high { background: #d4edda; color: #155724; }
        .badge-stock.medium { background: #fff3cd; color: #856404; }
        .badge-stock.low { background: #f8d7da; color: #721c24; }

        /* HERO */
        .hero-section {
            background: linear-gradient(135deg, #FF6B35 0%, #E85A24 60%, #C0392B 100%);
            color: white;
            padding: 60px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 350px;
            height: 350px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }

        .hero-title {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: clamp(2rem, 5vw, 3.5rem);
            line-height: 1.15;
        }

        /* STATS CARD */
        .stat-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 20px;
            text-align: center;
            box-shadow: var(--card-shadow);
            border-top: 4px solid var(--primary);
        }

        .stat-number {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: 2rem;
            color: var(--primary);
        }

        /* FORM */
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 10px 14px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.15);
        }

        /* TABLE */
        .table-gashu th {
            background: #FFF0EB;
            color: var(--primary-dark);
            font-weight: 700;
            border: none;
            padding: 12px 16px;
        }

        .table-gashu td {
            padding: 12px 16px;
            vertical-align: middle;
            border-color: #f5e8e4;
        }

        /* SIDEBAR */
        .sidebar {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            padding: 20px;
            position: sticky;
            top: 80px;
        }

        .sidebar-title {
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            color: var(--secondary);
            border-bottom: 3px solid var(--primary);
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        /* CART */
        .cart-item {
            background: #fff8f5;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid rgba(255,107,53,0.15);
        }

        /* STATUS BADGE */
        .status-pending { background: #fff3cd; color: #856404; }
        .status-dikonfirmasi { background: #cce5ff; color: #004085; }
        .status-dikirim { background: #d1ecf1; color: #0c5460; }
        .status-selesai { background: #d4edda; color: #155724; }
        .status-dibatalkan { background: #f8d7da; color: #721c24; }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* FOOTER */
        footer {
            background: var(--secondary);
            color: rgba(255,255,255,0.7);
            padding: 30px 0;
            margin-top: 60px;
        }

        /* QUANTITY CONTROL */
        .qty-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 2px solid var(--primary);
            background: #fff;
            color: var(--primary);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .qty-input {
            width: 55px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 4px;
            font-weight: 700;
        }

        /* TOAST */
        .toast-container { z-index: 9999; }

        /* RESPONSIVE FIXES */
        @media (max-width: 576px) {
            .hero-section { padding: 40px 0 60px; }
            .stat-card { margin-bottom: 12px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-gashu sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= isset($_SESSION['role']) && $_SESSION['role'] === 'seller' ? 'seller/dashboard.php' : 'index.php' ?>">
            <i class="bi bi-fire me-2"></i>Gas<span>Ku</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <?php if (isLoggedIn()): ?>
                    <?php if (isBuyer()): ?>
                        <li class="nav-item"><a class="nav-link" href="/index.php"><i class="bi bi-house me-1"></i>Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="/buyer/pesanan.php"><i class="bi bi-bag me-1"></i>Pesanan Saya</a></li>
                        <li class="nav-item"><a class="nav-link" href="/buyer/keranjang.php"><i class="bi bi-cart3 me-1"></i>Keranjang</a></li>
                    <?php elseif (isSeller()): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="produk.php"><i class="bi bi-box-seam me-1"></i>Produk</a></li>
                        <li class="nav-item"><a class="nav-link" href="pesanan.php"><i class="bi bi-list-check me-1"></i>Pesanan</a></li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['nama']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link btn-navbar-login ms-2" href="login.php">Masuk</a></li>
                    <li class="nav-item"><a class="nav-link btn-navbar-login ms-1" href="register.php">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
