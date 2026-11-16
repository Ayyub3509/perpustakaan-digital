<?php
if (isset($_POST['simpan'])) {
    $cek = mysqli_prepare($koneksi, "SELECT id_anggota FROM anggota WHERE username=?");
    mysqli_stmt_bind_param($cek, "s", $_POST['username']);
    mysqli_stmt_execute($cek);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
        notif("Username sudah dipakai", "dashboard.php?halaman=input_anggota");
    }
    $stmt = mysqli_prepare($koneksi, "INSERT INTO anggota (nis,nama_anggota,username,password,kelas) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "sssss", $_POST['nis'], $_POST['nama_anggota'], $_POST['username'], $_POST['password'], $_POST['kelas']);
    if (mysqli_stmt_execute($stmt)) {
        notif("Data anggota berhasil disimpan", "dashboard.php?halaman=data_anggota");
    }
    notif("Data anggota gagal disimpan", "dashboard.php?halaman=input_anggota");
}
?>
<h5>Input Data Anggota</h5>
<form method="post">
  <div class="mb-3"><label class="form-label">NIS</label>
    <input type="number" name="nis" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Nama Anggota</label>
    <input type="text" name="nama_anggota" class="form-control" maxlength="50" required></div>
  <div class="mb-3"><label class="form-label">Username</label>
    <input type="text" name="username" class="form-control" maxlength="50" required></div>
  <div class="mb-3"><label class="form-label">Password</label>
    <input type="text" name="password" class="form-control" maxlength="50" required></div>
  <div class="mb-3"><label class="form-label">Kelas</label>
    <input type="text" name="kelas" class="form-control" maxlength="31" required></div>
  <button type="submit" name="simpan" class="btn btn-primary">SIMPAN</button>
  <a href="dashboard.php?halaman=data_anggota" class="btn btn-secondary">Kembali</a>
</form>
