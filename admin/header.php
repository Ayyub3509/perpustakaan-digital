<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title ?? 'Admin Perpustakaan') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: #fff !important; }
    }
  </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm no-print">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-book-half me-2"></i>Admin Perpustakaan</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navAdmin">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active fw-semibold' : '' ?>" href="dashboard.php">
            <i class="bi bi-speedometer2 me-1"></i>Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'buku.php' ? 'active fw-semibold' : '' ?>" href="buku.php">
            <i class="bi bi-journal-bookmark me-1"></i>Data Buku
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'anggota.php' ? 'active fw-semibold' : '' ?>" href="anggota.php">
            <i class="bi bi-people me-1"></i>Data Anggota
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'peminjaman.php' ? 'active fw-semibold' : '' ?>" href="peminjaman.php">
            <i class="bi bi-arrow-left-right me-1"></i>Peminjaman
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page === 'laporan.php' ? 'active fw-semibold' : '' ?>" href="laporan.php">
            <i class="bi bi-printer me-1"></i>Laporan
          </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-3 text-white">
        <span class="small"><i class="bi bi-person-circle me-1"></i><?= e($_SESSION['nama_admin']) ?></span>
        <a href="../logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin keluar?')">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    </div>
  </div>
</nav>

<main class="container py-4 flex-grow-1">
