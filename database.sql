-- ============================================
-- DATABASE: gas_shop
-- Website Penjualan Gas
-- ============================================

CREATE DATABASE IF NOT EXISTS gas_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gas_shop;

-- Tabel Users (Seller & Buyer)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('seller', 'buyer') NOT NULL DEFAULT 'buyer',
    no_telepon VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk Gas
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_gas VARCHAR(100) NOT NULL,
    ukuran VARCHAR(50) NOT NULL,       -- e.g. 3kg, 5.5kg, 12kg
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    deskripsi TEXT,
    gambar VARCHAR(255),
    seller_id INT NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Pesanan
CREATE TABLE pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_pesanan VARCHAR(20) UNIQUE NOT NULL,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    total_harga DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'dikonfirmasi', 'dikirim', 'selesai', 'dibatalkan') DEFAULT 'pending',
    metode_pembayaran VARCHAR(50) DEFAULT 'transfer',
    alamat_pengiriman TEXT,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (seller_id) REFERENCES users(id)
);

-- Tabel Detail Pesanan
CREATE TABLE detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);

-- ============================================
-- DATA AWAL (SEED)
-- ============================================

-- Seller default
INSERT INTO users (nama, email, password, role, no_telepon, alamat) VALUES
('Toko Gas Pak Budi', 'seller@gasshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', '08123456789', 'Jl. Magelang No. 10, Yogyakarta'),
('Budi Santoso', 'buyer@gasshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer', '08987654321', 'Jl. Godean No. 5, Yogyakarta');

-- Produk Gas
INSERT INTO produk (nama_gas, ukuran, harga, stok, deskripsi, seller_id) VALUES
('Gas LPG Melon', '3 kg', 22000, 150, 'Gas LPG 3kg subsidi, cocok untuk rumah tangga kecil', 1),
('Gas LPG Biru', '5.5 kg', 75000, 80, 'Gas LPG non-subsidi 5.5kg, cocok untuk rumah tangga menengah', 1),
('Gas LPG Pink', '12 kg', 165000, 50, 'Gas LPG non-subsidi 12kg, ideal untuk rumah tangga besar', 1),
('Gas LPG Bright', '50 kg', 650000, 20, 'Gas LPG industri 50kg untuk usaha restoran/industri', 1);

-- Password default untuk akun di atas: "password"
