# 📚 Aplikasi Perpustakaan Digital Sekolah

Aplikasi manajemen perpustakaan sekolah berbasis web menggunakan **PHP Native (MySQLi Prepared Statements)**, **MySQL**, dan **Bootstrap 5**.

---

## 🚀 Cara Menjalankan di VS Code & XAMPP / Laragon

### Langkah 1: Aktifkan Web Server & Database
1. Buka **XAMPP Control Panel** atau **Laragon**.
2. Jalankan service **Apache** dan **MySQL**.

### Langkah 2: Import Database
1. Buka browser dan kunjungi `http://localhost/phpmyadmin`.
2. Klik tab **Import** -> **Choose File** -> pilih file `sql/perpustakaan.sql` -> klik **Go** / **Import**.
3. Database `perpustakaan` beserta 4 tabel (`admin`, `anggota`, `buku`, `peminjaman`) dan data contoh akan otomatis dibuat.

### Langkah 3: Buka & Jalankan dari VS Code
1. Pindahkan folder `perpustakaan` ini ke dalam folder `htdocs` (`C:\xampp\htdocs\perpustakaan`) atau `www` (`C:\laragon\www\perpustakaan`).
2. Klik kanan folder `perpustakaan` -> **Open with Code** (atau buka lewat VS Code: `File > Open Folder`).
3. Jika konfigurasi MySQL berbeda, sesuaikan host/user/password di file `koneksi.php`.
4. Jalankan aplikasi melalui browser:
   - Via Apache: `http://localhost/perpustakaan/`
   - Atau via Terminal VS Code (`Ctrl + ~`): ketik `php -S localhost:8000` lalu buka `http://localhost:8000`.

---

## 🔑 Akun Contoh (Default)

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `admin` |
| **Anggota** | `senja` | `senja` |
| **Anggota 2** | `budi` | `budi` |

*(Anggota baru juga dapat mendaftar secara mandiri melalui halaman **Daftar Anggota**.)*

---

## 📁 Struktur Folder Proyek

```text
perpustakaan/
├── .vscode/
│   ├── settings.json           # Konfigurasi VS Code untuk PHP
│   └── tasks.json              # Task shortcut jalankan PHP built-in server
├── admin/
│   ├── auth.php                # Middleware proteksi halaman admin
│   ├── header.php              # Navbar & layout header admin
│   ├── footer.php              # Footer & script Bootstrap admin
│   ├── dashboard.php           # Ringkasan statistik & transaksi terbaru
│   ├── buku.php                # Kelola data buku (Tambah, Edit, Hapus, Cari)
│   ├── anggota.php             # Kelola data anggota (Tambah, Edit, Hapus, Cari)
│   ├── peminjaman.php          # Kelola transaksi pinjam & proses pengembalian + denda
│   └── laporan.php             # Laporan transaksi peminjaman & cetak laporan
├── anggota/
│   ├── auth.php                # Middleware proteksi halaman anggota
│   ├── header.php              # Navbar & layout header anggota
│   ├── footer.php              # Footer & script Bootstrap anggota
│   ├── dashboard.php           # Dashboard anggota & ringkasan status pinjaman
│   ├── katalog.php             # Katalog buku, pencarian & ajukan peminjaman
│   └── riwayat.php             # Riwayat peminjaman & status pengembalian
├── docs/
│   └── DOKUMENTASI_SISTEM.md   # Dokumentasi lengkap, ERD, Flowchart & Alur Sistem
├── sql/
│   └── perpustakaan.sql        # File database MySQL + data contoh
├── index.php                   # Halaman utama (Landing Page)
├── koneksi.php                 # Koneksi database, helper XSS, CSRF & format tanggal
├── login-admin.php             # Form & proses login admin
├── proses-login-admin.php      # Handler kompatibilitas login admin
├── login-anggota.php           # Form & proses login anggota
├── pendaftaran-anggota.php     # Form & proses registrasi anggota baru
└── logout.php                  # Hapus sesi login (Admin & Anggota)
```
