<?php
session_start();
if (empty($_SESSION['id_admin'])) {
    header("Location:../login-admin.php");
    exit;
}
include "../koneksi.php";

// Daftar halaman yang boleh dibuka (whitelist, aman dari path traversal)
$halaman_ok = ['data_buku','input_buku','edit_buku','data_anggota','input_anggota','edit_anggota','data_peminjaman','input_peminjaman'];
$halaman = isset($_GET['halaman']) ? $_GET['halaman'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-3">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h4 class="mb-0">Dashboard Admin — <?= e($_SESSION['nama_admin']) ?></h4>
    <div class="d-flex gap-2 flex-wrap">
      <a href="dashboard.php" class="btn btn-secondary btn-sm">Dashboard</a>
      <a href="dashboard.php?halaman=data_buku" class="btn btn-primary btn-sm">Buku</a>
      <a href="dashboard.php?halaman=data_anggota" class="btn btn-primary btn-sm">Anggota</a>
      <a href="dashboard.php?halaman=data_peminjaman" class="btn btn-primary btn-sm">Peminjaman</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>

  <div class="card p-3 mt-3">
    <?php
    if (in_array($halaman, $halaman_ok, true) && file_exists($halaman . ".php")) {
        include $halaman . ".php";
    } else {
        echo "<h5>Selamat datang, " . e($_SESSION['nama_admin']) . " 👋</h5>";
        echo "<p class='text-muted mb-0'>Aplikasi Perpustakaan Digital Sekolah untuk mengelola data buku, anggota, serta transaksi peminjaman dan pengembalian buku.</p>";
    }
    ?>
  </div>
</div>
</body>
</html>
