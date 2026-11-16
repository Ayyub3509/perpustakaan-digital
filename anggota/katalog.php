<?php
require_once "auth.php";
$page_title = "Katalog Buku - Perpustakaan Digital";

// Proses Pinjam Buku Mandiri oleh Anggota
if (isset($_POST['pinjam_buku'])) {
    $id_buku = (int)$_POST['id_buku'];

    // Cek batas maksimal pinjam aktif (maks 3 buku)
    $cek_limit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM peminjaman WHERE id_anggota = $id_anggota AND status = 'Dipinjam'"))['c'] ?? 0;
    if ($cek_limit >= 3) {
        notif("Batas maksimal peminjaman adalah 3 buku aktif. Kembalikan buku sebelumnya terlebih dahulu.", "katalog.php");
    }

    // Cek apakah anggota sedang meminjam judul buku yang sama
    $cek_sama = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM peminjaman WHERE id_anggota = $id_anggota AND id_buku = $id_buku AND status = 'Dipinjam'"))['c'] ?? 0;
    if ($cek_sama > 0) {
        notif("Kamu sedang meminjam buku ini.", "katalog.php");
    }

    // Cek stok buku
    $buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = $id_buku"));
    if (!$buku || (int)$buku['stok'] <= 0) {
        notif("Maaf, stok buku ini sedang habis.", "katalog.php");
    }

    $tgl_pinjam = date('Y-m-d');
    $tgl_tempo  = date('Y-m-d', strtotime('+7 days'));

    mysqli_begin_transaction($koneksi);
    try {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_jatuh_tempo, status) VALUES (?, ?, ?, ?, 'Dipinjam')");
        mysqli_stmt_bind_param($stmt, "iiss", $id_anggota, $id_buku, $tgl_pinjam, $tgl_tempo);
        mysqli_stmt_execute($stmt);

        mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = $id_buku");
        mysqli_commit($koneksi);
        notif("Buku berhasil dipinjam! Batas pengembalian 7 hari dari sekarang.", "riwayat.php");
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        notif("Gagal memproses peminjaman.", "katalog.php");
    }
}

$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
    $like = "%$cari%";
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM buku WHERE judul_buku LIKE ? OR pengarang LIKE ? OR kategori LIKE ? OR kode_buku LIKE ? ORDER BY judul_buku ASC");
    mysqli_stmt_bind_param($stmt, "ssss", $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $katalog = mysqli_stmt_get_result($stmt);
} else {
    $katalog = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY judul_buku ASC");
}

require_once "header.php";
?>

<div class="mb-4">
  <h3 class="fw-bold mb-1">Katalog Buku Perpustakaan</h3>
  <p class="text-muted mb-0">Temukan buku favoritmu dan ajukan peminjaman langsung (Durasi pinjam 7 hari)</p>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="get" class="row g-2">
      <div class="col-md-10">
        <input type="text" name="cari" class="form-control" placeholder="Cari judul buku, pengarang, atau kategori..." value="<?= e($cari) ?>">
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100">Cari</button>
        <?php if ($cari !== ''): ?>
          <a href="katalog.php" class="btn btn-outline-secondary">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<div class="row g-3">
  <?php if (mysqli_num_rows($katalog) === 0): ?>
    <div class="col-12">
      <div class="alert alert-info text-center">Buku yang kamu cari tidak ditemukan.</div>
    </div>
  <?php else: ?>
    <?php while ($b = mysqli_fetch_assoc($katalog)): ?>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="badge bg-primary-subtle text-primary"><?= e($b['kategori']) ?></span>
              <span class="badge bg-dark"><?= e($b['kode_buku']) ?></span>
            </div>
            <h5 class="card-title fw-bold mb-1"><?= e($b['judul_buku']) ?></h5>
            <p class="text-muted small mb-2">Oleh: <?= e($b['pengarang']) ?></p>
            <p class="text-muted small mb-3">Penerbit: <?= e($b['penerbit']) ?> (<?= e($b['tahun_terbit']) ?>)</p>

            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
              <span class="small fw-semibold <?= $b['stok'] > 0 ? 'text-success' : 'text-danger' ?>">
                Stok: <?= (int)$b['stok'] ?> tersedia
              </span>
              <?php if ((int)$b['stok'] > 0): ?>
                <form method="post" onsubmit="return confirm('Pinjam buku <?= e($b['judul_buku']) ?> selama 7 hari?')">
                  <input type="hidden" name="id_buku" value="<?= (int)$b['id_buku'] ?>">
                  <button type="submit" name="pinjam_buku" class="btn btn-sm btn-primary">
                    <i class="bi bi-journal-plus me-1"></i>Pinjam
                  </button>
                </form>
              <?php else: ?>
                <button class="btn btn-sm btn-secondary" disabled>Habis</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</div>

<?php require_once "footer.php"; ?>
