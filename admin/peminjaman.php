<?php
require_once "auth.php";
$page_title = "Transaksi Peminjaman & Pengembalian - Admin";

// Tambah Transaksi Peminjaman oleh Admin
if (isset($_POST['pinjam'])) {
    $id_anggota = (int)$_POST['id_anggota'];
    $id_buku    = (int)$_POST['id_buku'];
    $lama_hari  = max(1, min(30, (int)($_POST['lama_hari'] ?? 7)));
    $tgl_pinjam = date('Y-m-d');
    $tgl_tempo  = date('Y-m-d', strtotime("+$lama_hari days"));

    $cek_stok = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = $id_buku");
    $buku = mysqli_fetch_assoc($cek_stok);
    if (!$buku || (int)$buku['stok'] <= 0) {
        notif("Stok buku habis, tidak dapat dipinjam!", "peminjaman.php");
    }

    mysqli_begin_transaction($koneksi);
    try {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_jatuh_tempo, status) VALUES (?, ?, ?, ?, 'Dipinjam')");
        mysqli_stmt_bind_param($stmt, "iiss", $id_anggota, $id_buku, $tgl_pinjam, $tgl_tempo);
        mysqli_stmt_execute($stmt);

        mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = $id_buku");
        mysqli_commit($koneksi);
        notif("Transaksi peminjaman berhasil dicatat!", "peminjaman.php");
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        notif("Terjadi kesalahan saat mencatat peminjaman.", "peminjaman.php");
    }
}

// Proses Pengembalian Buku
if (isset($_GET['kembali'])) {
    $id_pinjam = (int)$_GET['kembali'];
    $q = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman = $id_pinjam AND status = 'Dipinjam'");
    $data = mysqli_fetch_assoc($q);

    if (!$data) {
        notif("Data peminjaman tidak valid atau sudah dikembalikan.", "peminjaman.php");
    }

    $tgl_kembali = date('Y-m-d');
    $denda = hitung_denda($data['tanggal_jatuh_tempo'], $tgl_kembali);
    $id_buku = (int)$data['id_buku'];

    mysqli_begin_transaction($koneksi);
    try {
        $stmt = mysqli_prepare($koneksi, "UPDATE peminjaman SET status = 'Dikembalikan', tanggal_kembali = ?, denda = ? WHERE id_peminjaman = ?");
        mysqli_stmt_bind_param($stmt, "sii", $tgl_kembali, $denda, $id_pinjam);
        mysqli_stmt_execute($stmt);

        mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = $id_buku");
        mysqli_commit($koneksi);

        $info_denda = $denda > 0 ? " (Denda keterlambatan: Rp " . number_format($denda, 0, ',', '.') . ")" : " (Tepat waktu, tanpa denda)";
        notif("Buku berhasil dikembalikan!" . $info_denda, "peminjaman.php");
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        notif("Gagal memproses pengembalian.", "peminjaman.php");
    }
}

$filter = $_GET['status'] ?? 'semua';
$where = "";
if ($filter === 'Dipinjam') {
    $where = "WHERE p.status = 'Dipinjam'";
} elseif ($filter === 'Dikembalikan') {
    $where = "WHERE p.status = 'Dikembalikan'";
}

$transaksi = mysqli_query($koneksi, "
    SELECT p.*, a.nis, a.nama_anggota, a.kelas, b.kode_buku, b.judul_buku
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    $where
    ORDER BY FIELD(p.status, 'Dipinjam', 'Dikembalikan'), p.id_peminjaman DESC
");

$anggota_opt = mysqli_query($koneksi, "SELECT id_anggota, nis, nama_anggota, kelas FROM anggota ORDER BY nama_anggota ASC");
$buku_opt    = mysqli_query($koneksi, "SELECT id_buku, kode_buku, judul_buku, stok FROM buku WHERE stok > 0 ORDER BY judul_buku ASC");

require_once "header.php";
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h3 class="fw-bold mb-1">Peminjaman & Pengembalian</h3>
    <p class="text-muted mb-0">Kelola peminjaman buku dan proses pengembalian (Denda keterlambatan Rp 1.000/hari)</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPinjam">
    <i class="bi bi-plus-circle me-1"></i>Catat Peminjaman
  </button>
</div>

<div class="mb-3 d-flex gap-2">
  <a href="peminjaman.php?status=semua" class="btn btn-sm <?= $filter === 'semua' ? 'btn-dark' : 'btn-outline-dark' ?>">Semua</a>
  <a href="peminjaman.php?status=Dipinjam" class="btn btn-sm <?= $filter === 'Dipinjam' ? 'btn-warning' : 'btn-outline-warning' ?>">Sedang Dipinjam</a>
  <a href="peminjaman.php?status=Dikembalikan" class="btn btn-sm <?= $filter === 'Dikembalikan' ? 'btn-success' : 'btn-outline-success' ?>">Sudah Dikembalikan</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>Anggota</th>
          <th>Buku</th>
          <th>Tgl Pinjam</th>
          <th>Jatuh Tempo</th>
          <th>Tgl Kembali</th>
          <th>Denda</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($transaksi) === 0): ?>
          <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data transaksi.</td></tr>
        <?php else: ?>
          <?php $no = 1; while ($t = mysqli_fetch_assoc($transaksi)): ?>
            <?php
              $estimasi_denda = $t['status'] === 'Dipinjam' ? hitung_denda($t['tanggal_jatuh_tempo']) : (int)$t['denda'];
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <div class="fw-semibold"><?= e($t['nama_anggota']) ?></div>
                <small class="text-muted"><?= e($t['nis']) ?> &bull; <?= e($t['kelas']) ?></small>
              </td>
              <td>
                <span class="badge bg-dark"><?= e($t['kode_buku']) ?></span>
                <?= e($t['judul_buku']) ?>
              </td>
              <td><?= tgl_indo($t['tanggal_pinjam']) ?></td>
              <td><?= tgl_indo($t['tanggal_jatuh_tempo']) ?></td>
              <td><?= tgl_indo($t['tanggal_kembali']) ?></td>
              <td>
                <?php if ($estimasi_denda > 0): ?>
                  <span class="text-danger fw-semibold">Rp <?= number_format($estimasi_denda, 0, ',', '.') ?></span>
                <?php else: ?>
                  <span class="text-muted">Rp 0</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($t['status'] === 'Dikembalikan'): ?>
                  <span class="badge bg-success">Dikembalikan</span>
                <?php elseif ($estimasi_denda > 0): ?>
                  <span class="badge bg-danger">Terlambat</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Dipinjam</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <?php if ($t['status'] === 'Dipinjam'): ?>
                  <a href="peminjaman.php?kembali=<?= (int)$t['id_peminjaman'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Proses pengembalian buku ini?')">
                    <i class="bi bi-check2-circle me-1"></i>Kembalikan
                  </a>
                <?php else: ?>
                  <span class="text-muted small">Selesai</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Catat Peminjaman -->
<div class="modal fade" id="modalPinjam" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Catat Peminjaman Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Pilih Anggota</label>
          <select name="id_anggota" class="form-select" required>
            <option value="">-- Pilih Siswa --</option>
            <?php while ($a = mysqli_fetch_assoc($anggota_opt)): ?>
              <option value="<?= (int)$a['id_anggota'] ?>"><?= e($a['nis']) ?> - <?= e($a['nama_anggota']) ?> (<?= e($a['kelas']) ?>)</option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Pilih Buku (Stok Tersedia)</label>
          <select name="id_buku" class="form-select" required>
            <option value="">-- Pilih Buku --</option>
            <?php while ($b = mysqli_fetch_assoc($buku_opt)): ?>
              <option value="<?= (int)$b['id_buku'] ?>">[<?= e($b['kode_buku']) ?>] <?= e($b['judul_buku']) ?> (Stok: <?= (int)$b['stok'] ?>)</option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Durasi Peminjaman (Hari)</label>
          <input type="number" name="lama_hari" class="form-control" min="1" max="30" value="7" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="pinjam" class="btn btn-primary">Simpan Transaksi</button>
      </div>
    </form>
  </div>
</div>

<?php require_once "footer.php"; ?>
