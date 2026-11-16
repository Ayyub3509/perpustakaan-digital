<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "SELECT * FROM buku WHERE id_buku=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$d = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$d) { notif("Data buku tidak ditemukan", "dashboard.php?halaman=data_buku"); }

if (isset($_POST['simpan'])) {
    $tahun = (int)$_POST['tahun_terbit'];
    if ($tahun < 1901 || $tahun > 2155) {
        notif("Tahun terbit tidak valid", "dashboard.php?halaman=edit_buku&id=$id");
    }
    $stmt = mysqli_prepare($koneksi, "UPDATE buku SET judul_buku=?, pengarang=?, penerbit=?, tahun_terbit=? WHERE id_buku=?");
    mysqli_stmt_bind_param($stmt, "sssii", $_POST['judul_buku'], $_POST['pengarang'], $_POST['penerbit'], $tahun, $id);
    if (mysqli_stmt_execute($stmt)) {
        notif("Data buku berhasil diubah", "dashboard.php?halaman=data_buku");
    }
    notif("Data buku gagal diubah", "dashboard.php?halaman=edit_buku&id=$id");
}
?>
<h5>Edit Data Buku</h5>
<form method="post">
  <div class="mb-3"><label class="form-label">Judul Buku</label>
    <input type="text" name="judul_buku" class="form-control" maxlength="100" value="<?= e($d['judul_buku']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Pengarang</label>
    <input type="text" name="pengarang" class="form-control" maxlength="100" value="<?= e($d['pengarang']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Penerbit</label>
    <input type="text" name="penerbit" class="form-control" maxlength="100" value="<?= e($d['penerbit']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Tahun Terbit</label>
    <input type="number" name="tahun_terbit" class="form-control" min="1901" max="2155" value="<?= e($d['tahun_terbit']) ?>" required></div>
  <button type="submit" name="simpan" class="btn btn-primary">SIMPAN</button>
  <a href="dashboard.php?halaman=data_buku" class="btn btn-secondary">Kembali</a>
</form>
