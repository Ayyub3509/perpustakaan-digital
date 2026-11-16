<?php
require_once "auth.php";
$page_title = "Dashboard Anggota - Perpustakaan Digital";

$profil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = $id_anggota"));
$aktif  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM peminjaman WHERE id_anggota = $id_anggota AND status = 'Dipinjam'"))['c'] ?? 0;
$selesai= mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM peminjaman WHERE id_anggota = $id_anggota AND status = 'Dikembalikan'"))['c'] ?? 0;

$pinjaman_aktif = mysqli_query($koneksi, "
    SELECT p.*, b.kode_buku, b.judul_buku, b.pengarang
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_anggota = $id_anggota AND p.status = 'Dipinjam'
    ORDER BY p.tanggal_jatuh_tempo ASC
");

require_once "header.php";
?>

<div class="card border-0 shadow-sm bg-primary text-white mb-4">
  <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
      <h4 class="fw-bold mb-1">Selamat Datang, <?= e($profil['nama_anggota']) ?>!</h4>
      <p class="mb-0 opacity-75">NIS: <?= e($profil['nis']) ?> &bull; Kelas: <?= e($profil['kelas']) ?></p>
    </div>
    <a href="katalog.php" class="btn btn-light fw-semibold text-primary">
      <i class="bi bi-journal-plus me-1"></i>Pinjam Buku Sekarang
    </a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card border-0 shadow-sm border-start border-4 border-warning">
      <div class="card-body">
        <div class="text-muted small">Buku Sedang Dipinjam (Maks. 3 buku)</div>
        <h3 class="fw-bold mb-0 mt-1"><?= (int)$aktif ?> <span class="fs-6 fw-normal text-muted">buku</span></h3>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm border-start border-4 border-success">
      <div class="card-body">
        <div class="text-muted small">Total Buku Pernah Dikembalikan</div>
        <h3 class="fw-bold mb-0 mt-1"><?= (int)$selesai ?> <span class="fs-6 fw-normal text-muted">buku</span></h3>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white py-3">
    <h6 class="fw-bold mb-0"><i class="bi bi-bookmark-check me-2"></i>Pinjaman Aktif Kamu</h6>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Kode</th>
          <th>Judul Buku</th>
          <th>Tanggal Pinjam</th>
          <th>Batas Kembali</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($pinjaman_aktif) === 0): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">Kamu tidak memiliki pinjaman buku aktif saat ini.</td></tr>
        <?php else: ?>
          <?php while ($p = mysqli_fetch_assoc($pinjaman_aktif)): ?>
            <?php $denda = hitung_denda($p['tanggal_jatuh_tempo']); ?>
            <tr>
              <td><span class="badge bg-dark"><?= e($p['kode_buku']) ?></span></td>
              <td>
                <div class="fw-semibold"><?= e($p['judul_buku']) ?></div>
                <small class="text-muted"><?= e($p['pengarang']) ?></small>
              </td>
              <td><?= tgl_indo($p['tanggal_pinjam']) ?></td>
              <td><?= tgl_indo($p['tanggal_jatuh_tempo']) ?></td>
              <td>
                <?php if ($denda > 0): ?>
                  <span class="badge bg-danger">Terlambat (Denda Rp <?= number_format($denda, 0, ',', '.') ?>)</span>
                <?php else: ?>
                  <span class="badge bg-info text-dark">Masa Pinjam Aktif</span>
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
