<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "SELECT * FROM anggota WHERE id_anggota=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$d = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$d) { notif("Data anggota tidak ditemukan", "dashboard.php?halaman=data_anggota"); }

if (isset($_POST['simpan'])) {
    $cek = mysqli_prepare($koneksi, "SELECT id_anggota FROM anggota WHERE username=? AND id_anggota<>?");
    mysqli_stmt_bind_param($cek, "si", $_POST['username'], $id);
    mysqli_stmt_execute($cek);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
        notif("Username sudah dipakai", "dashboard.php?halaman=edit_anggota&id=$id");
    }
    $stmt = mysqli_prepare($koneksi, "UPDATE anggota SET nis=?, nama_anggota=?, username=?, password=?, kelas=? WHERE id_anggota=?");
    mysqli_stmt_bind_param($stmt, "sssssi", $_POST['nis'], $_POST['nama_anggota'], $_POST['username'], $_POST['password'], $_POST['kelas'], $id);
    if (mysqli_stmt_execute($stmt)) {
        notif("Data anggota berhasil diubah", "dashboard.php?halaman=data_anggota");
    }
    notif("Data anggota gagal diubah", "dashboard.php?halaman=edit_anggota&id=$id");
}
?>
<h5>Edit Data Anggota</h5>
<form method="post">
  <div class="mb-3"><label class="form-label">NIS</label>
    <input type="number" name="nis" class="form-control" value="<?= e($d['nis']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Nama Anggota</label>
    <input type="text" name="nama_anggota" class="form-control" maxlength="50" value="<?= e($d['nama_anggota']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Username</label>
    <input type="text" name="username" class="form-control" maxlength="50" value="<?= e($d['username']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Password</label>
    <input type="text" name="password" class="form-control" maxlength="50" value="<?= e($d['password']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Kelas</label>
    <input type="text" name="kelas" class="form-control" maxlength="31" value="<?= e($d['kelas']) ?>" required></div>
  <button type="submit" name="simpan" class="btn btn-primary">SIMPAN</button>
  <a href="dashboard.php?halaman=data_anggota" class="btn btn-secondary">Kembali</a>
</form>
