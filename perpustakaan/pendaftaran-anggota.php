<?php
session_start();
include "koneksi.php";

if (isset($_POST['daftar'])) {
    $nis      = trim($_POST['nis']);
    $nama     = trim($_POST['nama_anggota']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $kelas    = trim($_POST['kelas']);

    // Username harus unik
    $cek = mysqli_prepare($koneksi, "SELECT id_anggota FROM anggota WHERE username=?");
    mysqli_stmt_bind_param($cek, "s", $username);
    mysqli_stmt_execute($cek);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
        notif("Username sudah dipakai, silakan pilih yang lain", "pendaftaran-anggota.php");
    }

    $stmt = mysqli_prepare($koneksi, "INSERT INTO anggota (nis,nama_anggota,username,password,kelas) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "sssss", $nis, $nama, $username, $password, $kelas);
    if (mysqli_stmt_execute($stmt)) {
        session_regenerate_id(true);
        $_SESSION['id_anggota']   = mysqli_insert_id($koneksi);
        $_SESSION['nama_anggota'] = $nama;
        notif("Pendaftaran berhasil!", "anggota/dashboard.php");
    }
    notif("Pendaftaran gagal, coba lagi", "pendaftaran-anggota.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pendaftaran Anggota</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width:450px">
  <div class="card p-4 mt-4 shadow-sm">
    <h4 class="text-center mb-3">Pendaftaran Anggota</h4>
    <form method="post">
      <div class="mb-3"><label class="form-label">NIS</label>
        <input type="number" name="nis" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama_anggota" class="form-control" maxlength="50" required></div>
      <div class="mb-3"><label class="form-label">Kelas</label>
        <input type="text" name="kelas" class="form-control" maxlength="31" placeholder="contoh: XII RPL 1" required></div>
      <div class="mb-3"><label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" maxlength="50" required></div>
      <div class="mb-3"><label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" maxlength="50" required></div>
      <button type="submit" name="daftar" class="btn btn-success w-100">Daftar</button>
    </form>
    <div class="text-center mt-3">
      <a href="login-anggota.php">Login anggota</a> · <a href="login-admin.php">Login admin</a>
    </div>
  </div>
</div>
</body>
</html>
