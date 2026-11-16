<?php
require_once "auth.php";
$page_title = "Kelola Data Buku - Admin Perpustakaan";

// Tambah Buku
if (isset($_POST['tambah'])) {
    $kode      = trim($_POST['kode_buku']);
    $judul     = trim($_POST['judul_buku']);
    $pengarang = trim($_POST['pengarang']);
    $penerbit  = trim($_POST['penerbit']);
    $tahun     = (int)$_POST['tahun_terbit'];
    $kategori  = trim($_POST['kategori']);
    $stok      = max(0, (int)$_POST['stok']);

    $cek = mysqli_prepare($koneksi, "SELECT id_buku FROM buku WHERE kode_buku = ?");
    mysqli_stmt_bind_param($cek, "s", $kode);
    mysqli_stmt_execute($cek);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
        notif("Kode buku sudah digunakan!", "buku.php");
    }

    $stmt = mysqli_prepare($koneksi, "INSERT INTO buku (kode_buku, judul_buku, pengarang, penerbit, tahun_terbit, kategori, stok) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssisi", $kode, $judul, $pengarang, $penerbit, $tahun, $kategori, $stok);
    mysqli_stmt_execute($stmt);
    notif("Data buku berhasil ditambahkan.", "buku.php");
}

// Edit Buku
if (isset($_POST['edit'])) {
    $id        = (int)$_POST['id_buku'];
    $kode      = trim($_POST['kode_buku']);
    $judul     = trim($_POST['judul_buku']);
    $pengarang = trim($_POST['pengarang']);
    $penerbit  = trim($_POST['penerbit']);
    $tahun     = (int)$_POST['tahun_terbit'];
    $kategori  = trim($_POST['kategori']);
    $stok      = max(0, (int)$_POST['stok']);

    $stmt = mysqli_prepare($koneksi, "UPDATE buku SET kode_buku=?, judul_buku=?, pengarang=?, penerbit=?, tahun_terbit=?, kategori=?, stok=? WHERE id_buku=?");
    mysqli_stmt_bind_param($stmt, "ssssisii", $kode, $judul, $pengarang, $penerbit, $tahun, $kategori, $stok, $id);
    mysqli_stmt_execute($stmt);
    notif("Data buku berhasil diperbarui.", "buku.php");
}

// Hapus Buku
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $cek_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM peminjaman WHERE id_buku = $id AND status = 'Dipinjam'");
    if ((mysqli_fetch_assoc($cek_pinjam)['c'] ?? 0) > 0) {
        notif("Buku tidak dapat dihapus karena sedang dipinjam!", "buku.php");
    }
    $stmt = mysqli_prepare($koneksi, "DELETE FROM buku WHERE id_buku = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    notif("Data buku berhasil dihapus.", "buku.php");
}

$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
    $like = "%$cari%";
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM buku WHERE kode_buku LIKE ? OR judul_buku LIKE ? OR pengarang LIKE ? OR kategori LIKE ? ORDER BY id_buku DESC");
    mysqli_stmt_bind_param($stmt, "ssss", $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id_buku DESC");
}

$buku_list = [];
while ($r = mysqli_fetch_assoc($result)) {
    $buku_list[] = $r;
}

require_once "header.php";
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h3 class="fw-bold mb-1">Kelola Data Buku</h3>
    <p class="text-muted mb-0">Tambah, ubah, dan atur stok koleksi buku perpustakaan</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-plus-lg me-1"></i>Tambah Buku
  </button>
</div>

<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <form method="get" class="row g-2">
      <div class="col-md-10">
        <input type="text" name="cari" class="form-control" placeholder="Cari kode buku, judul, pengarang, atau kategori..." value="<?= e($cari) ?>">
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-dark w-100">Cari</button>
        <?php if ($cari !== ''): ?>
          <a href="buku.php" class="btn btn-outline-secondary">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Kode</th>
          <th>Judul Buku</th>
          <th>Pengarang</th>
          <th>Penerbit (Tahun)</th>
          <th>Kategori</th>
          <th class="text-center">Stok</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($buku_list)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Data buku tidak ditemukan.</td></tr>
        <?php else: ?>
          <?php foreach ($buku_list as $b): ?>
            <tr>
              <td><span class="badge bg-dark"><?= e($b['kode_buku']) ?></span></td>
              <td class="fw-semibold"><?= e($b['judul_buku']) ?></td>
              <td><?= e($b['pengarang']) ?></td>
              <td><?= e($b['penerbit']) ?> (<?= e($b['tahun_terbit']) ?>)</td>
              <td><span class="badge bg-info-subtle text-info-emphasis"><?= e($b['kategori']) ?></span></td>
              <td class="text-center">
                <span class="badge <?= $b['stok'] > 0 ? 'bg-success' : 'bg-danger' ?>"><?= (int)$b['stok'] ?></span>
              </td>
              <td class="text-end">
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $b['id_buku'] ?>">
                  <i class="bi bi-pencil"></i>
                </button>
                <a href="buku.php?hapus=<?= $b['id_buku'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus buku ini?')">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Buku -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Tambah Buku Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Kode Buku</label>
            <input type="text" name="kode_buku" class="form-control" placeholder="BK-007" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Kategori</label>
            <input type="text" name="kategori" class="form-control" placeholder="Teknologi / Fiksi / Pelajaran" required>
          </div>
          <div class="col-12">
            <label class="form-label">Judul Buku</label>
            <input type="text" name="judul_buku" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Pengarang</label>
            <input type="text" name="pengarang" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Penerbit</label>
            <input type="text" name="penerbit" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tahun Terbit</label>
            <input type="number" name="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>" value="<?= date('Y') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control" min="0" value="1" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="tambah" class="btn btn-primary">Simpan Buku</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Buku -->
<?php foreach ($buku_list as $b): ?>
<div class="modal fade" id="modalEdit<?= $b['id_buku'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="id_buku" value="<?= (int)$b['id_buku'] ?>">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Kode Buku</label>
            <input type="text" name="kode_buku" class="form-control" value="<?= e($b['kode_buku']) ?>" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Kategori</label>
            <input type="text" name="kategori" class="form-control" value="<?= e($b['kategori']) ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label">Judul Buku</label>
            <input type="text" name="judul_buku" class="form-control" value="<?= e($b['judul_buku']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Pengarang</label>
            <input type="text" name="pengarang" class="form-control" value="<?= e($b['pengarang']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Penerbit</label>
            <input type="text" name="penerbit" class="form-control" value="<?= e($b['penerbit']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tahun Terbit</label>
            <input type="number" name="tahun_terbit" class="form-control" value="<?= (int)$b['tahun_terbit'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control" min="0" value="<?= (int)$b['stok'] ?>" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="edit" class="btn btn-warning">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
<?php endforeach; ?>

<?php require_once "footer.php"; ?>
