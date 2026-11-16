<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title ?? 'Portal Anggota Perpustakaan') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-book-half me-2"></i>Perpustakaan Digital</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAnggota">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navAnggota">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active fw-semibold' : '' ?>" href="dashboard.php">
            <i class="bi bi-house-door me-1"></i>Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'katalog.php' ? 'active fw-semibold' : '' ?>" href="katalog.php">
            <i class="bi bi-search me-1"></i>Katalog & Pinjam Buku
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'riwayat.php' ? 'active fw-semibold' : '' ?>" href="riwayat.php">
            <i class="bi bi-clock-history me-1"></i>Riwayat Pinjaman
          </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-3 text-white">
        <span class="small"><i class="bi bi-person-circle me-1"></i><?= e($_SESSION['nama_anggota']) ?></span>
        <a href="../logout.php" class="btn btn-light btn-sm text-danger fw-semibold" onclick="return confirm('Yakin ingin keluar?')">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    </div>
  </div>
</nav>

<main class="container py-4 flex-grow-1">
