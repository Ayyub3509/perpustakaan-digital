<?php
session_start();
include "koneksi.php";

if (!empty($_SESSION['id_anggota'])) { header("Location:anggota/dashboard.php"); exit; }

if (isset($_POST['login'])) {
    $stmt = mysqli_prepare($koneksi, "SELECT id_anggota, nama_anggota FROM anggota WHERE username=? AND password=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $_POST['username'], $_POST['password']);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($data) {
        session_regenerate_id(true);
        $_SESSION['id_anggota']   = $data['id_anggota'];
        $_SESSION['nama_anggota'] = $data['nama_anggota'];
        header("Location: anggota/dashboard.php");
        exit;
    }
    notif("Login Gagal, Username / Password Salah", "login-anggota.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Anggota</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width:400px">
  <div class="card p-4 mt-5 shadow-sm">
    <h4 class="text-center mb-3">Login Anggota</h4>
    <form method="post">
      <div class="mb-3"><label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required autofocus></div>
      <div class="mb-3"><label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required></div>
      <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="text-center mt-3">
      <a href="pendaftaran-anggota.php">Daftar anggota</a> · <a href="login-admin.php">Login admin</a>
    </div>
  </div>
</div>
</body>
</html>
