<?php
session_start();
include "koneksi.php";

$jml_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM buku"))['total'] ?? 0;
$jml_anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM anggota"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Perpustakaan Digital Sekolah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-book-half me-2"></i>Perpustakaan Digital</a>
    <div class="ms-auto">
      <?php if (!empty($_SESSION['id_admin'])): ?>
        <a href="admin/dashboard.php" class="btn btn-light btn-sm fw-semibold">Dashboard Admin</a>
      <?php elseif (!empty($_SESSION['id_anggota'])): ?>
        <a href="anggota/dashboard.php" class="btn btn-light btn-sm fw-semibold">Dashboard Anggota</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main class="container my-auto py-5">
  <div class="row justify-content-center text-center">
    <div class="col-lg-8">
      <div class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-3 fw-semibold">
        Sistem Informasi Perpustakaan Sekolah
      </div>
      <h1 class="display-5 fw-bold text-dark mb-3">Selamat Datang di Perpustakaan Digital</h1>
      <p class="lead text-muted mb-4">
        Cari koleksi buku, ajukan peminjaman secara mandiri, dan pantau jadwal pengembalian dengan mudah dan cepat.
      </p>

      <div class="row justify-content-center g-3 mb-5">
        <div class="col-sm-4">
          <a href="login-anggota.php" class="btn btn-primary btn-lg w-100 shadow-sm">
            <i class="bi bi-person-badge me-2"></i>Login Anggota
          </a>
        </div>
        <div class="col-sm-4">
          <a href="pendaftaran-anggota.php" class="btn btn-success btn-lg w-100 shadow-sm">
            <i class="bi bi-person-plus me-2"></i>Daftar Anggota
          </a>
        </div>
        <div class="col-sm-4">
          <a href="login-admin.php" class="btn btn-dark btn-lg w-100 shadow-sm">
            <i class="bi bi-shield-lock me-2"></i>Login Admin
          </a>
        </div>
      </div>

      <div class="row g-3 justify-content-center">
        <div class="col-6 col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h3 class="fw-bold text-primary mb-0"><?= (int)$jml_buku ?></h3>
              <small class="text-muted">Judul Buku Tersedia</small>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h3 class="fw-bold text-success mb-0"><?= (int)$jml_anggota ?></h3>
              <small class="text-muted">Anggota Terdaftar</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="bg-white border-top py-3 text-center text-muted small">
  &copy; <?= date('Y') ?> Aplikasi Perpustakaan Digital Sekolah
</footer>

</body>
</html>
