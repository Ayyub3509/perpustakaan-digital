<?php
session_start();
include "koneksi.php";

if (!empty($_SESSION['id_anggota'])) {
    header("Location: anggota/dashboard.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($koneksi, "SELECT id_anggota, nama_anggota, kelas, password FROM anggota WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($data && cek_password($password, $data['password'])) {
        session_regenerate_id(true);
        $_SESSION['id_anggota']   = $data['id_anggota'];
        $_SESSION['nama_anggota'] = $data['nama_anggota'];
        $_SESSION['kelas']        = $data['kelas'];
        header("Location: anggota/dashboard.php");
        exit;
    }
    notif("Login Gagal! Username atau Password salah.", "login-anggota.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Anggota - Perpustakaan Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
<div class="container" style="max-width:420px">
  <div class="card border-0 shadow-sm p-4">
    <div class="text-center mb-3">
      <div class="fs-1 text-primary"><i class="bi bi-person-circle"></i></div>
      <h4 class="fw-bold mb-1">Login Anggota</h4>
      <p class="text-muted small">Masuk untuk meminjam dan melihat riwayat buku</p>
    </div>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" placeholder="senja" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••" required>
      </div>
      <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-semibold">Masuk sebagai Anggota</button>
    </form>
    <div class="text-center mt-3 small">
      <a href="pendaftaran-anggota.php" class="text-decoration-none">Daftar anggota baru</a> &middot;
      <a href="login-admin.php" class="text-decoration-none">Login admin</a> &middot;
      <a href="index.php" class="text-decoration-none">Beranda</a>
    </div>
  </div>
</div>
</body>
</html>
