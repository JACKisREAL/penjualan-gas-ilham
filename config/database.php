<?php
// config/database.php

// SESUAIKAN: Ganti 'db' dengan nama service di docker-compose.yml kamu
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root');
define('DB_PASS', ''); // Sesuaikan dengan MYSQL_ROOT_PASSWORD di Docker
define('DB_NAME', 'gas_shop');

function getDB() {
    // Menggunakan static agar koneksi tidak dibuat berulang kali dalam satu request
    static $conn;
    
    if ($conn === NULL) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            // Jika Docker baru menyala, terkadang DB butuh waktu untuk 'ready'
            die(json_encode([
                'status' => 'error',
                'message' => 'Koneksi database gagal. Pastikan container database sudah jalan.'
            ]));
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

// Helper untuk format mata uang
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Helper untuk kode pesanan unik
function generateKodePesanan() {
    return 'GAS' . date('Ymd') . strtoupper(substr(uniqid(), -5));
}

function normalizeNomorWhatsApp($nomor) {
    $digits = preg_replace('/\D+/', '', $nomor ?? '');

    if ($digits === '') {
        return '';
    }

    if (strpos($digits, '0') === 0) {
        return '62' . substr($digits, 1);
    }

    if (strpos($digits, '8') === 0) {
        return '62' . $digits;
    }

    return $digits;
}

function buatLinkKonfirmasiBayar($nomorPenjual, $kodePesanan, $namaPembeli) {
    $nomor = normalizeNomorWhatsApp($nomorPenjual);
    $pesan = "Saya sudah bayar dengan ID pesanan {$kodePesanan} atas nama {$namaPembeli}";

    if ($nomor !== '') {
        return 'https://wa.me/' . $nomor . '?text=' . rawurlencode($pesan);
    }

    return 'https://web.whatsapp.com/send?text=' . rawurlencode($pesan);
}

// Mulai session jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- FUNGSI AUTHENTICATION & ACCESS CONTROL ---

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getRole() {
    return $_SESSION['role'] ?? null;
}

function isSeller() {
    return getRole() === 'seller';
}

function isBuyer() {
    return getRole() === 'buyer';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireSeller() {
    requireLogin();
    if (!isSeller()) {
        // Jika bukan seller tapi maksa masuk, lempar ke index buyer
        header('Location: /buyer/index.php');
        exit;
    }
}

function requireBuyer() {
    requireLogin();
    if (!isBuyer()) {
        // Jika bukan buyer tapi maksa masuk, lempar ke dashboard seller
        header('Location: /seller/dashboard.php');
        exit;
    }
}
?>
