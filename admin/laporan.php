<?php
require_once "auth.php";
$page_title = "Laporan Perpustakaan - Admin";

$dari   = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

$stmt = mysqli_prepare($koneksi, "
    SELECT p.*, a.nis, a.nama_anggota, a.kelas, b.kode_buku, b.judul_buku
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.tanggal_pinjam BETWEEN ? AND ?
    ORDER BY p.tanggal_pinjam DESC, p.id_peminjaman DESC
");
mysqli_stmt_bind_param($stmt, "ss", $dari, $sampai);
mysqli_stmt_execute($stmt);
$data_laporan = mysqli_stmt_get_result($stmt);

require_once "header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
  <div>
    <h3 class="fw-bold mb-1">Laporan Transaksi Perpustakaan</h3>
    <p class="text-muted mb-0">Filter berdasarkan rentang tanggal peminjaman dan cetak laporan</p>
  </div>
  <button onclick="window.print()" class="btn btn-dark"><i class="bi bi-printer me-1"></i>Cetak Laporan</button>
</div>

<div class="card border-0 shadow-sm mb-4 no-print">
  <div class="card-body">
    <form method="get" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Dari Tanggal</label>
        <input type="date" name="dari" class="form-control" value="<?= e($dari) ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Sampai Tanggal</label>
        <input type="date" name="sampai" class="form-control" value="<?= e($sampai) ?>" required>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary w-100">Tampilkan Laporan</button>
      </div>
    </form>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="text-center mb-4">
      <h5 class="fw-bold mb-1">LAPORAN PEMINJAMAN BUKU PERPUSTAKAAN SEKOLAH</h5>
      <p class="text-muted small mb-0">Periode: <?= tgl_indo($dari) ?> s/d <?= tgl_indo($sampai) ?></p>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>NIS / Nama Anggota</th>
            <th>Kelas</th>
            <th>Kode & Judul Buku</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Status</th>
            <th>Denda</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; $total_denda = 0; ?>
          <?php if (mysqli_num_rows($data_laporan) === 0): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada transaksi pada periode ini.</td></tr>
          <?php else: ?>
            <?php while ($r = mysqli_fetch_assoc($data_laporan)): ?>
              <?php $total_denda += (int)$r['denda']; ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= e($r['nis']) ?> - <?= e($r['nama_anggota']) ?></td>
                <td><?= e($r['kelas']) ?></td>
                <td>[<?= e($r['kode_buku']) ?>] <?= e($r['judul_buku']) ?></td>
                <td><?= tgl_indo($r['tanggal_pinjam']) ?></td>
                <td><?= tgl_indo($r['tanggal_kembali']) ?></td>
                <td><?= e($r['status']) ?></td>
                <td>Rp <?= number_format((int)$r['denda'], 0, ',', '.') ?></td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr class="table-light fw-bold">
            <td colspan="7" class="text-end">Total Denda Terkumpul</td>
            <td>Rp <?= number_format($total_denda, 0, ',', '.') ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<?php require_once "footer.php"; ?>
