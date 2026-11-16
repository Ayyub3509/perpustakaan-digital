# Dokumentasi Sistem Informasi Perpustakaan Digital Sekolah

## 1. Gambaran Umum Sistem
**Aplikasi Perpustakaan Digital Sekolah** adalah sistem berbasis web yang dirancang untuk mempermudah pengelolaan sirkulasi buku di perpustakaan sekolah. Sistem membagi hak akses menjadi dua aktor utama:
1. **Administrator (Petugas Perpustakaan)**: Mengelola master data buku, master data anggota, transaksi peminjaman/pengembalian, perhitungan denda otomatis, serta pencetakan laporan.
2. **Anggota (Siswa)**: Melakukan pendaftaran mandiri, mencari katalog buku, meminjam buku secara mandiri (maksimal 3 buku aktif selama 7 hari), dan memantau riwayat peminjaman.

---

## 2. Arsitektur & Teknologi
- **Backend**: PHP 8.x (Prosedural terstruktur dengan MySQLi Prepared Statements & Database Transactions)
- **Database**: MySQL / MariaDB (`utf8mb4`)
- **Frontend**: HTML5, Bootstrap 5.3.3, Bootstrap Icons
- **Keamanan**:
  - Proteksi SQL Injection menggunakan `mysqli_prepare()` dan `mysqli_stmt_bind_param()`.
  - Proteksi Cross-Site Scripting (XSS) menggunakan fungsi helper `e()` (`htmlspecialchars`).
  - Proteksi Session Fixation menggunakan `session_regenerate_id(true)` saat login/registrasi.
  - Hashing kata sandi menggunakan `password_hash()` dan `password_verify()` (tetap kompatibel dengan akun contoh).

---

## 3. Struktur Database (Entity Relationship Diagram)

```text
+-------------------+       +------------------------+       +--------------------+
|      anggota      |       |       peminjaman       |       |        buku        |
+-------------------+       +------------------------+       +--------------------+
| PK id_anggota     |<------| FK id_anggota          |   +-->| PK id_buku         |
|    nis (UNIQUE)   |       | PK id_peminjaman       |   |   |    kode_buku (UQ)  |
|    nama_anggota   |       | FK id_buku             |---+   |    judul_buku      |
|    kelas          |       |    tanggal_pinjam      |       |    pengarang       |
|    username (UQ)  |       |    tanggal_jatuh_tempo |       |    penerbit        |
|    password       |       |    tanggal_kembali     |       |    tahun_terbit    |
|    created_at     |       |    status              |       |    kategori        |
+-------------------+       |    denda               |       |    stok            |
                            +------------------------+       +--------------------+

+-------------------+
|       admin       |
+-------------------+
| PK id_admin       |
|    nama_admin     |
|    username (UQ)  |
|    password       |
+-------------------+
```

---

## 4. Flowchart Alur Kerja Sistem

### A. Alur Pendaftaran & Login
```text
[Mulai] --> [Halaman Utama (index.php)]
               |-- Pilih Daftar Anggota --> [Isi Form NIS, Nama, Kelas, User, Pass]
               |                               |-- Cek NIS/Username Unik
               |                               |-- Simpan ke DB & Buat Session --> [Dashboard Anggota]
               |-- Pilih Login Anggota  --> [Validasi Akun] --> [Dashboard Anggota]
               |-- Pilih Login Admin    --> [Validasi Akun] --> [Dashboard Admin]
```

### B. Alur Peminjaman Buku
```text
[Anggota / Admin Memilih Buku]
       |
       v
[Cek Stok Buku > 0 & Kuota Pinjam < 3] --(Tidak)--> [Tampilkan Peringatan]
       |
     (Ya)
       v
[Mulai Transaksi Database]
  1. INSERT data ke tabel `peminjaman` (status = 'Dipinjam', jatuh tempo +7 hari)
  2. UPDATE tabel `buku` SET stok = stok - 1
[Commit Transaksi] --> [Selesai]
```

### C. Alur Pengembalian & Perhitungan Denda
```text
[Admin Klik "Kembalikan" pada Transaksi Aktif]
       |
       v
[Hitung Selisih Hari: Tanggal Kembali - Tanggal Jatuh Tempo]
  - Jika <= 0 hari --> Denda = Rp 0
  - Jika >  0 hari --> Denda = Selisih Hari x Rp 1.000
       |
       v
[Mulai Transaksi Database]
  1. UPDATE `peminjaman` SET status = 'Dikembalikan', tanggal_kembali = Hari Ini, denda = Hasil Hitung
  2. UPDATE `buku` SET stok = stok + 1
[Commit Transaksi] --> [Tampilkan Notifikasi Total Denda]
```
