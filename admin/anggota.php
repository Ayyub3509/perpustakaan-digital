<?php
require_once "auth.php";
$page_title = "Kelola Data Anggota - Admin Perpustakaan";

// Tambah Anggota
if (isset($_POST['tambah'])) {
    $nis      = trim($_POST['nis']);
    $nama     = trim($_POST['nama_anggota']);
    $kelas    = trim($_POST['kelas']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_prepare($koneksi, "SELECT id_anggota FROM anggota WHERE nis = ? OR username = ?");
    mysqli_stmt_bind_param($cek, "ss", $nis, $username);
    mysqli_stmt_execute($cek);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
        notif("NIS atau Username sudah terdaftar!", "anggota.php");
    }

    $stmt = mysqli_prepare($koneksi, "INSERT INTO anggota (nis, nama_anggota, kelas, username, password) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $nis, $nama, $kelas, $username, $password);
    mysqli_stmt_execute($stmt);
    notif("Anggota baru berhasil ditambahkan.", "anggota.php");
}

// Edit Anggota
if (isset($_POST['edit'])) {
    $id       = (int)$_POST['id_anggota'];
    $nis      = trim($_POST['nis']);
    $nama     = trim($_POST['nama_anggota']);
    $kelas    = trim($_POST['kelas']);
    $username = trim($_POST['username']);
    $pass_baru= $_POST['password'] ?? '';

    if ($pass_baru !== '') {
        $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($koneksi, "UPDATE anggota SET nis=?, nama_anggota=?, kelas=?, username=?, password=? WHERE id_anggota=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $nis, $nama, $kelas, $username, $hash, $id);
    } else {
        $stmt = mysqli_prepare($koneksi, "UPDATE anggota SET nis=?, nama_anggota=?, kelas=?, username=? WHERE id_anggota=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $nis, $nama, $kelas, $username, $id);
    }
    mysqli_stmt_execute($stmt);
    notif("Data anggota berhasil diperbarui.", "anggota.php");
}

// Hapus Anggota
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $cek_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM peminjaman WHERE id_anggota = $id AND status = 'Dipinjam'");
    if ((mysqli_fetch_assoc($cek_pinjam)['c'] ?? 0) > 0) {
        notif("Anggota tidak dapat dihapus karena masih memiliki pinjaman aktif!", "anggota.php");
    }
    $stmt = mysqli_prepare($koneksi, "DELETE FROM anggota WHERE id_anggota = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    notif("Data anggota berhasil dihapus.", "anggota.php");
}

$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
    $like = "%$cari%";
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM anggota WHERE nis LIKE ? OR nama_anggota LIKE ? OR kelas LIKE ? OR username LIKE ? ORDER BY id_anggota DESC");
    mysqli_stmt_bind_param($stmt, "ssss", $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY id_anggota DESC");
}

$anggota_list = [];
while ($r = mysqli_fetch_assoc($result)) {
    $anggota_list[] = $r;
}

require_once "header.php";
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h3 class="fw-bold mb-1">Kelola Data Anggota</h3>
    <p class="text-muted mb-0">Daftar siswa yang terdaftar sebagai anggota perpustakaan</p>
  </div>
  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-person-plus me-1"></i>Tambah Anggota
  </button>
</div>

<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <form method="get" class="row g-2">
      <div class="col-md-10">
        <input type="text" name="cari" class="form-control" placeholder="Cari NIS, nama anggota, kelas, atau username..." value="<?= e($cari) ?>">
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-dark w-100">Cari</button>
        <?php if ($cari !== ''): ?>
          <a href="anggota.php" class="btn btn-outline-secondary">Reset</a>
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
          <th>No</th>
          <th>NIS</th>
          <th>Nama Lengkap</th>
          <th>Kelas</th>
          <th>Username</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($anggota_list)): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">Data anggota tidak ditemukan.</td></tr>
        <?php else: ?>
          <?php $no = 1; foreach ($anggota_list as $a): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><span class="badge bg-secondary"><?= e($a['nis']) ?></span></td>
              <td class="fw-semibold"><?= e($a['nama_anggota']) ?></td>
              <td><?= e($a['kelas']) ?></td>
              <td><code><?= e($a['username']) ?></code></td>
              <td class="text-end">
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $a['id_anggota'] ?>">
                  <i class="bi bi-pencil"></i>
                </button>
                <a href="anggota.php?hapus=<?= $a['id_anggota'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus anggota ini?')">
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

<!-- Modal Tambah Anggota -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Tambah Anggota Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">NIS</label>
          <input type="text" name="nis" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama_anggota" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Kelas</label>
          <input type="text" name="kelas" class="form-control" placeholder="Contoh: XII RPL 1" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="tambah" class="btn btn-success">Simpan Anggota</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Anggota -->
<?php foreach ($anggota_list as $a): ?>
<div class="modal fade" id="modalEdit<?= $a['id_anggota'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="id_anggota" value="<?= (int)$a['id_anggota'] ?>">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit Data Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">NIS</label>
          <input type="text" name="nis" class="form-control" value="<?= e($a['nis']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama_anggota" class="form-control" value="<?= e($a['nama_anggota']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Kelas</label>
          <input type="text" name="kelas" class="form-control" value="<?= e($a['kelas']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" value="<?= e($a['username']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password Baru <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
          <input type="password" name="password" class="form-control">
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
