# 🔥 GasKu - Website Penjualan Gas LPG

Website penjualan gas LPG berbasis PHP + MySQL dengan tampilan Bootstrap 5 yang responsif.

---

## 📁 Struktur File

```
gas-shop/
├── config/
│   └── database.php          ← Konfigurasi koneksi DB & helper functions
├── includes/
│   ├── header.php             ← Template navbar & head HTML
│   └── footer.php             ← Template footer & JS global
├── buyer/
│   ├── keranjang.php          ← Halaman keranjang belanja
│   ├── keranjang_action.php   ← API handler keranjang (AJAX)
│   ├── checkout.php           ← Proses checkout (AJAX)
│   ├── pesanan.php            ← Riwayat pesanan buyer
│   └── detail_pesanan.php     ← Detail pesanan (partial/modal)
├── seller/
│   ├── dashboard.php          ← Dashboard seller (statistik)
│   ├── produk.php             ← Kelola produk gas (CRUD + update stok)
│   ├── pesanan.php            ← Lihat & kelola pesanan masuk
│   └── detail_pesanan_seller.php ← Detail pesanan (partial/modal)
├── index.php                  ← Halaman utama / toko
├── login.php                  ← Halaman login
├── register.php               ← Halaman registrasi
├── logout.php                 ← Proses logout
└── database.sql               ← Schema + data awal database
```

---

## ⚙️ Cara Instalasi

### 1. Persyaratan
- PHP 7.4+ (disarankan PHP 8.0+)
- MySQL 5.7+ atau MariaDB 10+
- Web Server: Apache/Nginx (atau XAMPP/Laragon/WAMP untuk lokal)

### 2. Setup Database
```sql
-- Buka phpMyAdmin atau MySQL CLI, lalu jalankan:
SOURCE /path/to/gas-shop/database.sql;
```
Atau copy-paste isi `database.sql` ke phpMyAdmin → tab SQL → Execute.

### 3. Konfigurasi Koneksi
Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ganti sesuai username MySQL Anda
define('DB_PASS', '');           // ganti sesuai password MySQL Anda
define('DB_NAME', 'gas_shop');
```

### 4. Letakkan di Web Server
- **XAMPP**: Salin folder `gas-shop/` ke `C:/xampp/htdocs/`
- **Laragon**: Salin ke `C:/laragon/www/`
- **Linux**: Salin ke `/var/www/html/`

### 5. Akses Website
Buka browser: `http://localhost/gas-shop/`

---

## 🔐 Akun Demo (Password: `password`)

| Role   | Email                | Password |
|--------|----------------------|----------|
| Seller | seller@gasshop.com   | password |
| Buyer  | buyer@gasshop.com    | password |

---

## ✨ Fitur Lengkap

### 👤 Buyer (Pembeli)
- ✅ Register & Login sebagai Pembeli
- ✅ Melihat katalog produk gas dengan stok real-time
- ✅ Memilih jumlah tabung (qty control +/-)
- ✅ Kalkulasi harga otomatis per item & total
- ✅ Keranjang belanja (session-based)
- ✅ Update/hapus item di keranjang
- ✅ Checkout dengan alamat, metode pembayaran, catatan
- ✅ Riwayat pesanan dengan status terkini
- ✅ Lihat detail pesanan per order

### 🏪 Seller (Penjual)
- ✅ Register & Login sebagai Penjual
- ✅ Dashboard dengan statistik (produk, pesanan, pendapatan, stok rendah)
- ✅ Notifikasi pesanan baru menunggu konfirmasi
- ✅ Tambah / Edit / Hapus produk gas
- ✅ **Update stok cepat** langsung dari tabel (tanpa perlu form penuh)
- ✅ Melihat semua pesanan masuk beserta info pembeli lengkap
- ✅ Filter pesanan by status (pending, dikonfirmasi, dikirim, selesai, dibatalkan)
- ✅ Update status pesanan dengan alur: Pending → Dikonfirmasi → Dikirim → Selesai
- ✅ Lihat detail pesanan (produk yang dibeli, alamat, kontak pembeli)

### 🛡️ Sistem
- ✅ Autentikasi session PHP
- ✅ Role-based access control (seller/buyer)
- ✅ Validasi stok saat checkout (race condition safe dengan MySQL transaction)
- ✅ Kode pesanan unik otomatis (format: GAS + tanggal + random)
- ✅ Password hashing bcrypt

---

## 📱 Responsif Mobile
Website menggunakan Bootstrap 5 yang fully responsive:
- Navbar collapsible untuk layar kecil
- Grid system otomatis menyesuaikan ukuran layar
- Tombol keranjang floating di mobile
- Tabel dengan horizontal scroll di mobile
- Font size adaptive

---

## 🛠️ Teknologi
- **Backend**: PHP 8 (vanilla, tanpa framework)
- **Database**: MySQL dengan prepared statements
- **Frontend**: Bootstrap 5.3 + Bootstrap Icons
- **Font**: Google Fonts (Nunito + Poppins)
- **AJAX**: Fetch API (vanilla JS)

---

## 📝 Catatan Development
- Keranjang disimpan di PHP session (bukan database)
- Checkout menggunakan MySQL transaction untuk keamanan stok
- Semua input user di-sanitasi dengan `htmlspecialchars()` dan prepared statements
- Untuk production: aktifkan HTTPS, gunakan environment variables untuk kredensial DB
