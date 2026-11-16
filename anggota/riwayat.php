<?php
require_once "auth.php";
$page_title = "Riwayat Peminjaman - Perpustakaan Digital";

$riwayat = mysqli_query($koneksi, "
    SELECT p.*, b.kode_buku, b.judul_buku, b.pengarang
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_anggota = $id_anggota
    ORDER BY p.id_peminjaman DESC
");

require_once "header.php";
?>

<div class="mb-4">
  <h3 class="fw-bold mb-1">Riwayat Peminjaman Buku</h3>
  <p class="text-muted mb-0">Daftar seluruh buku yang sedang maupun pernah kamu pinjam</p>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>Buku</th>
          <th>Tgl Pinjam</th>
          <th>Jatuh Tempo</th>
          <th>Tgl Kembali</th>
          <th>Denda</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($riwayat) === 0): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Belum ada riwayat peminjaman buku.</td></tr>
        <?php else: ?>
          <?php $no = 1; while ($r = mysqli_fetch_assoc($riwayat)): ?>
            <?php
              $denda = $r['status'] === 'Dipinjam' ? hitung_denda($r['tanggal_jatuh_tempo']) : (int)$r['denda'];
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <span class="badge bg-dark"><?= e($r['kode_buku']) ?></span>
                <span class="fw-semibold"><?= e($r['judul_buku']) ?></span>
                <div class="small text-muted"><?= e($r['pengarang']) ?></div>
              </td>
              <td><?= tgl_indo($r['tanggal_pinjam']) ?></td>
              <td><?= tgl_indo($r['tanggal_jatuh_tempo']) ?></td>
              <td><?= tgl_indo($r['tanggal_kembali']) ?></td>
              <td>
                <?php if ($denda > 0): ?>
                  <span class="text-danger fw-semibold">Rp <?= number_format($denda, 0, ',', '.') ?></span>
                <?php else: ?>
                  <span class="text-muted">Rp 0</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($r['status'] === 'Dikembalikan'): ?>
                  <span class="badge bg-success">Dikembalikan</span>
                <?php elseif ($denda > 0): ?>
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
