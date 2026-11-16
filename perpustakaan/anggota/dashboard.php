<?php
include "cek_anggota.php";
$id_anggota = (int)$_SESSION['id_anggota'];
$halaman = ($_GET['halaman'] ?? '') === 'history' ? 'history' : 'buku';
$cari = trim($_GET['cari'] ?? '');

// Buku yang sedang dipinjam anggota ini
$stmt = mysqli_prepare($koneksi, "SELECT t.id_transaksi, t.tgl_pinjam, b.judul_buku
    FROM transaksi t JOIN buku b ON b.id_buku=t.id_buku
    WHERE t.id_anggota=? AND t.status_transaksi='Peminjaman' ORDER BY t.id_transaksi DESC");
mysqli_stmt_bind_param($stmt, "i", $id_anggota);
mysqli_stmt_execute($stmt);
$sedang = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Anggota</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-3">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h4 class="mb-0">Halo, <?= e($_SESSION['nama_anggota']) ?> 👋</h4>
    <div class="d-flex gap-2">
      <a href="dashboard.php" class="btn btn-primary btn-sm">Daftar Buku</a>
      <a href="dashboard.php?halaman=history" class="btn btn-secondary btn-sm">History</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>

<?php if ($halaman == 'history') {
    $stmt = mysqli_prepare($koneksi, "SELECT t.tgl_pinjam, t.tgl_kembali, b.judul_buku
        FROM transaksi t JOIN buku b ON b.id_buku=t.id_buku
        WHERE t.id_anggota=? AND t.status_transaksi='Pengembalian' ORDER BY t.id_transaksi DESC");
    mysqli_stmt_bind_param($stmt, "i", $id_anggota);
    mysqli_stmt_execute($stmt);
    $hist = mysqli_stmt_get_result($stmt);
?>
  <div class="card p-3 mt-3">
    <h5>History Peminjaman</h5>
    <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark"><tr><th>No</th><th>Judul Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th></tr></thead>
      <tbody>
      <?php $no = 1; while ($d = mysqli_fetch_assoc($hist)) { ?>
        <tr><td><?= $no++ ?></td><td><?= e($d['judul_buku']) ?></td><td><?= e($d['tgl_pinjam']) ?></td><td><?= e($d['tgl_kembali']) ?></td></tr>
      <?php } ?>
      <?php if ($no == 1) echo "<tr><td colspan='4' class='text-center text-muted'>Belum ada riwayat pengembalian</td></tr>"; ?>
      </tbody>
    </table>
    </div>
  </div>

<?php } else { ?>
  <div class="card p-3 mt-3">
    <h5>Buku yang Sedang Dipinjam</h5>
    <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark"><tr><th>No</th><th>Judul Buku</th><th>Tgl Pinjam</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php $no = 1; while ($d = mysqli_fetch_assoc($sedang)) { ?>
        <tr>
          <td><?= $no++ ?></td><td><?= e($d['judul_buku']) ?></td><td><?= e($d['tgl_pinjam']) ?></td>
          <td>
            <form action="kembali.php" method="post" onsubmit="return confirm('Kembalikan buku ini?')">
              <input type="hidden" name="id" value="<?= (int)$d['id_transaksi'] ?>">
              <button class="btn btn-success btn-sm">Pengembalian</button>
            </form>
          </td>
        </tr>
      <?php } ?>
      <?php if ($no == 1) echo "<tr><td colspan='4' class='text-center text-muted'>Tidak ada buku yang sedang dipinjam</td></tr>"; ?>
      </tbody>
    </table>
    </div>
  </div>

  <div class="card p-3 mt-3 mb-4">
    <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
      <h5 class="mb-0">Daftar Buku</h5>
      <form method="get" class="d-flex gap-2">
        <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari judul buku..." value="<?= e($cari) ?>">
        <button class="btn btn-primary btn-sm">Cari</button>
        <?php if ($cari !== '') echo '<a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Reset</a>'; ?>
      </form>
    </div>
    <?php
    $like = "%" . $cari . "%";
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM buku WHERE judul_buku LIKE ? ORDER BY judul_buku");
    mysqli_stmt_bind_param($stmt, "s", $like);
    mysqli_stmt_execute($stmt);
    $buku = mysqli_stmt_get_result($stmt);
    ?>
    <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark"><tr><th>No</th><th>Judul</th><th>Pengarang</th><th>Penerbit</th><th>Tahun</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php $no = 1; while ($d = mysqli_fetch_assoc($buku)) { ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= e($d['judul_buku']) ?></td>
          <td><?= e($d['pengarang']) ?></td>
          <td><?= e($d['penerbit']) ?></td>
          <td><?= e($d['tahun_terbit']) ?></td>
          <td><span class="badge bg-<?= $d['status'] == 'tersedia' ? 'success' : 'secondary' ?>"><?= e($d['status']) ?></span></td>
          <td>
            <?php if ($d['status'] == 'tersedia') { ?>
              <form action="pinjam.php" method="post" onsubmit="return confirm('Pinjam buku ini?')">
                <input type="hidden" name="id" value="<?= (int)$d['id_buku'] ?>">
                <button class="btn btn-primary btn-sm">Pinjam Buku</button>
              </form>
            <?php } else { ?>
              <button class="btn btn-secondary btn-sm" disabled>Tidak Tersedia</button>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
      <?php if ($no == 1) echo "<tr><td colspan='7' class='text-center text-muted'>Buku tidak ditemukan</td></tr>"; ?>
      </tbody>
    </table>
    </div>
  </div>
<?php } ?>
</div>
</body>
</html>
