<?php
session_start();
if (!empty($_SESSION['id_admin'])) {
    header("Location: admin/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin - Perpustakaan Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
<div class="container" style="max-width:420px">
  <div class="card border-0 shadow-sm p-4">
    <div class="text-center mb-3">
      <div class="fs-1 text-dark"><i class="bi bi-shield-lock-fill"></i></div>
      <h4 class="fw-bold mb-1">Login Admin</h4>
      <p class="text-muted small">Masuk untuk mengelola perpustakaan</p>
    </div>
    <form action="proses-login-admin.php" method="post">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" placeholder="admin" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••" required>
      </div>
      <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">Masuk sebagai Admin</button>
    </form>
    <div class="text-center mt-3 small">
      <a href="login-anggota.php" class="text-decoration-none">Login Anggota</a> &middot;
      <a href="index.php" class="text-decoration-none">Beranda</a>
    </div>
  </div>
</div>
</body>
</html>
