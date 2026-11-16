<?php
if (isset($_POST['simpan'])) {
    $tahun = (int)$_POST['tahun_terbit'];
    if ($tahun < 1901 || $tahun > 2155) {
        notif("Tahun terbit tidak valid", "dashboard.php?halaman=input_buku");
    }
    $stmt = mysqli_prepare($koneksi, "INSERT INTO buku (judul_buku,pengarang,penerbit,tahun_terbit,status) VALUES (?,?,?,?, 'tersedia')");
    mysqli_stmt_bind_param($stmt, "sssi", $_POST['judul_buku'], $_POST['pengarang'], $_POST['penerbit'], $tahun);
    if (mysqli_stmt_execute($stmt)) {
        notif("Data buku berhasil disimpan", "dashboard.php?halaman=data_buku");
    }
    notif("Data buku gagal disimpan", "dashboard.php?halaman=input_buku");
}
?>
<h5>Input Data Buku</h5>
<form method="post">
  <div class="mb-3"><label class="form-label">Judul Buku</label>
    <input type="text" name="judul_buku" class="form-control" maxlength="100" required></div>
  <div class="mb-3"><label class="form-label">Pengarang</label>
    <input type="text" name="pengarang" class="form-control" maxlength="100" required></div>
  <div class="mb-3"><label class="form-label">Penerbit</label>
    <input type="text" name="penerbit" class="form-control" maxlength="100" required></div>
  <div class="mb-3"><label class="form-label">Tahun Terbit</label>
    <input type="number" name="tahun_terbit" class="form-control" min="1901" max="2155" required></div>
  <button type="submit" name="simpan" class="btn btn-primary">SIMPAN</button>
  <a href="dashboard.php?halaman=data_buku" class="btn btn-secondary">Kembali</a>
</form>
