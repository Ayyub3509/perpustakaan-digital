<?php session_start(); if (!empty($_SESSION['id_admin'])) { header("Location:admin/dashboard.php"); exit; } ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width:400px">
  <div class="card p-4 mt-5 shadow-sm">
    <h4 class="text-center mb-3">Login Admin</h4>
    <form action="proses-login-admin.php" method="post">
      <div class="mb-3"><label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required autofocus></div>
      <div class="mb-3"><label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required></div>
      <button type="submit" class="btn btn-dark w-100">Login</button>
    </form>
    <div class="text-center mt-3"><a href="login-anggota.php">Login sebagai anggota</a> · <a href="index.php">Beranda</a></div>
  </div>
</div>
</body>
</html>
