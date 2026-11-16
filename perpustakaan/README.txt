APLIKASI PERPUSTAKAAN DIGITAL SEKOLAH (PHP + MySQL + Bootstrap 5)

CARA PASANG
1. Start Apache + MySQL (XAMPP / Laragon).
2. Buka http://localhost/phpmyadmin -> tab "Import" -> pilih sql/perpustakaan.sql -> Go.
   (Otomatis membuat database "perpustakaan" + 4 tabel + data contoh.)
3. Copy folder ini ke  C:\xampp\htdocs\perpustakaan  (XAMPP)  atau  C:\laragon\www\perpustakaan  (Laragon).
4. Kalau nama database / user / password MySQL kamu berbeda, ubah di koneksi.php.
5. Buka http://localhost/perpustakaan/

AKUN CONTOH
Admin   : admin / admin
Anggota : senja / senja   (atau daftar baru lewat halaman pendaftaran)

CATATAN
- Bootstrap dipanggil lewat CDN (butuh internet). Kalau mau offline, taruh folder css & js
  Bootstrap di folder proyek lalu ganti link di tiap file ke ../css/bootstrap.min.css.
