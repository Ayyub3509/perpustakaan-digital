<?php
$sql = "SELECT t.*, a.nama_anggota, a.kelas, b.judul_buku
        FROM transaksi t
        JOIN anggota a ON a.id_anggota = t.id_anggota
        JOIN buku b ON b.id_buku = t.id_buku
        WHERE t.status_transaksi = ?
        ORDER BY t.id_transaksi DESC";

$stmt = mysqli_prepare($koneksi, $sql);
$status = 'Peminjaman';
mysqli_stmt_bind_param($stmt, "s", $status);
mysqli_stmt_execute($stmt);
$pinjam = mysqli_stmt_get_result($stmt);

$stmt2 = mysqli_prepare($koneksi, $sql);
$status2 = 'Pengembalian';
mysqli_stmt_bind_param($stmt2, "s", $status2);
mysqli_stmt_execute($stmt2);
$kembali = mysqli_stmt_get_result($stmt2);
?>
<div class="d-flex justify-content-between mb-3">
  <h5 class="mb-0">Data Peminjaman</h5>
  <a href="dashboard.php?halaman=input_peminjaman" class="btn btn-primary btn-sm">+ Tambah Peminjaman</a>
</div>
<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
  <thead class="table-dark">
    <tr><th>No</th><th>Anggota</th><th>Kelas</th><th>Buku</th><th>Tgl Pinjam</th><th>Aksi</th></tr>
  </thead>
  <tbody>
  <?php $no = 1; while ($d = mysqli_fetch_assoc($pinjam)) { ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= e($d['nama_anggota']) ?></td>
      <td><?= e($d['kelas']) ?></td>
      <td><?= e($d['judul_buku']) ?></td>
      <td><?= e($d['tgl_pinjam']) ?></td>
      <td class="text-nowrap">
        <form action="proses_pengembalian.php" method="post" class="d-inline" onsubmit="return confirm('Proses pengembalian buku ini?')">
          <input type="hidden" name="id" value="<?= (int)$d['id_transaksi'] ?>">
          <button class="btn btn-success btn-sm">Pengembalian</button>
        </form>
        <form action="hapus.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
          <input type="hidden" name="id" value="<?= (int)$d['id_transaksi'] ?>">
          <button class="btn btn-danger btn-sm">Hapus</button>
        </form>
      </td>
    </tr>
  <?php } ?>
  <?php if ($no == 1) echo "<tr><td colspan='6' class='text-center text-muted'>Tidak ada buku yang sedang dipinjam</td></tr>"; ?>
  </tbody>
</table>
</div>

<h5 class="mt-4">Data Pengembalian</h5>
<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
  <thead class="table-dark">
    <tr><th>No</th><th>Anggota</th><th>Kelas</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Aksi</th></tr>
  </thead>
  <tbody>
  <?php $no = 1; while ($d = mysqli_fetch_assoc($kembali)) { ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= e($d['nama_anggota']) ?></td>
      <td><?= e($d['kelas']) ?></td>
      <td><?= e($d['judul_buku']) ?></td>
      <td><?= e($d['tgl_pinjam']) ?></td>
      <td><?= e($d['tgl_kembali']) ?></td>
      <td>
        <form action="hapus.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
          <input type="hidden" name="id" value="<?= (int)$d['id_transaksi'] ?>">
          <button class="btn btn-danger btn-sm">Hapus</button>
        </form>
      </td>
    </tr>
  <?php } ?>
  <?php if ($no == 1) echo "<tr><td colspan='7' class='text-center text-muted'>Belum ada data pengembalian</td></tr>"; ?>
  </tbody>
</table>
</div>
