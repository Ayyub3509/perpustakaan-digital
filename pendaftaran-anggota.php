<?php
session_start();
include "koneksi.php";

if (!empty($_SESSION['id_anggota'])) {
    header("Location: anggota/dashboard.php");
    exit;
}

if (isset($_POST['daftar'])) {
    $nis      = trim($_POST['nis']);
    $nama     = trim($_POST['nama_anggota']);
    $kelas    = trim($_POST['kelas']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($nis === '' || $nama === '' || $kelas === '' || $username === '' || $password === '') {
        notif("Semua kolom wajib diisi!", "pendaftaran-anggota.php");
    }

    // Cek NIS & Username unik
    $cek = mysqli_prepare($koneksi, "SELECT id_anggota, nis, username FROM anggota WHERE username = ? OR nis = ? LIMIT 1");
    mysqli_stmt_bind_param($cek, "ss", $username, $nis);
    mysqli_stmt_execute($cek);
    $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));

    if ($existing) {
        if ($existing['nis'] === $nis) {
            notif("NIS sudah terdaftar! Silakan login atau hubungi admin.", "pendaftaran-anggota.php");
        }
        notif("Username sudah dipakai, silakan pilih username lain.", "pendaftaran-anggota.php");
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO anggota (nis, nama_anggota, kelas, username, password) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $nis, $nama, $kelas, $username, $hash);

    if (mysqli_stmt_execute($stmt)) {
        session_regenerate_id(true);
        $_SESSION['id_anggota']   = mysqli_insert_id($koneksi);
        $_SESSION['nama_anggota'] = $nama;
        $_SESSION['kelas']        = $kelas;
        notif("Pendaftaran berhasil! Selamat datang di Perpustakaan Digital.", "anggota/dashboard.php");
    }
    notif("Pendaftaran gagal, silakan coba lagi.", "pendaftaran-anggota.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pendaftaran Anggota - Perpustakaan Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-4">
<div class="container" style="max-width:460px">
  <div class="card border-0 shadow-sm p-4">
    <div class="text-center mb-3">
      <div class="fs-1 text-success"><i class="bi bi-person-plus-fill"></i></div>
      <h4 class="fw-bold mb-1">Pendaftaran Anggota</h4>
      <p class="text-muted small">Lengkapi data siswa untuk membuat akun perpustakaan</p>
    </div>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">NIS</label>
        <input type="text" name="nis" class="form-control" maxlength="20" pattern="[0-9]+" title="Masukkan angka NIS" placeholder="Contoh: 2026004" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama_anggota" class="form-control" maxlength="50" placeholder="Nama lengkap siswa" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Kelas</label>
        <input type="text" name="kelas" class="form-control" maxlength="31" placeholder="Contoh: XII RPL 1" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" maxlength="50" placeholder="Username untuk login" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" maxlength="50" placeholder="Minimal 4 karakter" minlength="4" required>
      </div>
      <button type="submit" name="daftar" class="btn btn-success w-100 py-2 fw-semibold">Daftar Sekarang</button>
    </form>
    <div class="text-center mt-3 small">
      Sudah punya akun? <a href="login-anggota.php" class="text-decoration-none">Login anggota</a> &middot;
      <a href="index.php" class="text-decoration-none">Beranda</a>
    </div>
  </div>
</div>
</body>
</html>
