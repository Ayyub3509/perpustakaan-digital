<?php
require_once "auth.php";
$page_title = "Dashboard Admin - Perpustakaan Digital";

$total_judul    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM buku"))['c'] ?? 0;
$total_eksemplar= mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(stok),0) AS c FROM buku"))['c'] ?? 0;
$total_anggota  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM anggota"))['c'] ?? 0;
$sedang_pinjam  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM peminjaman WHERE status='Dipinjam'"))['c'] ?? 0;
$terlambat      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM peminjaman WHERE status='Dipinjam' AND tanggal_jatuh_tempo < CURDATE()"))['c'] ?? 0;

$transaksi_terbaru = mysqli_query($koneksi, "
    SELECT p.*, a.nama_anggota, a.kelas, b.judul_buku, b.kode_buku
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    ORDER BY p.id_peminjaman DESC
    LIMIT 7
");

require_once "header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-bold mb-1">Dashboard Admin</h3>
    <p class="text-muted mb-0">Halo, <strong><?= e($_SESSION['nama_admin']) ?></strong>! Berikut ringkasan aktivitas perpustakaan hari ini.</p>
  </div>
  <a href="peminjaman.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Transaksi Baru</a>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-3 col-sm-6">
    <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
      <div class="card-body">
        <div class="text-muted small">Judul Buku / Total Stok</div>
        <h3 class="fw-bold mb-0 mt-1"><?= (int)$total_judul ?> <span class="fs-6 fw-normal text-muted">/ <?= (int)$total_eksemplar ?> eks</span></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
      <div class="card-body">
        <div class="text-muted small">Anggota Terdaftar</div>
        <h3 class="fw-bold mb-0 mt-1"><?= (int)$total_anggota ?> <span class="fs-6 fw-normal text-muted">siswa</span></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
      <div class="card-body">
        <div class="text-muted small">Sedang Dipinjam</div>
        <h3 class="fw-bold mb-0 mt-1"><?= (int)$sedang_pinjam ?> <span class="fs-6 fw-normal text-muted">buku</span></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
      <div class="card-body">
        <div class="text-muted small">Terlambat Kembali</div>
        <h3 class="fw-bold text-danger mb-0 mt-1"><?= (int)$terlambat ?> <span class="fs-6 fw-normal text-muted">transaksi</span></h3>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Transaksi Peminjaman Terbaru</h6>
    <a href="peminjaman.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>Anggota</th>
          <th>Buku</th>
          <th>Tgl Pinjam</th>
          <th>Jatuh Tempo</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($transaksi_terbaru) === 0): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data peminjaman.</td></tr>
        <?php else: ?>
          <?php $no = 1; while ($row = mysqli_fetch_assoc($transaksi_terbaru)): ?>
            <?php
              $telat = ($row['status'] === 'Dipinjam' && strtotime($row['tanggal_jatuh_tempo']) < strtotime(date('Y-m-d')));
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <div class="fw-semibold"><?= e($row['nama_anggota']) ?></div>
                <small class="text-muted"><?= e($row['kelas']) ?></small>
              </td>
              <td>
                <span class="badge bg-secondary"><?= e($row['kode_buku']) ?></span>
                <?= e($row['judul_buku']) ?>
              </td>
              <td><?= tgl_indo($row['tanggal_pinjam']) ?></td>
              <td><?= tgl_indo($row['tanggal_jatuh_tempo']) ?></td>
              <td>
                <?php if ($row['status'] === 'Dikembalikan'): ?>
                  <span class="badge bg-success">Dikembalikan</span>
                <?php elseif ($telat): ?>
                  <span class="badge bg-danger">Terlambat</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Dipinjam</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once "footer.php"; ?>
