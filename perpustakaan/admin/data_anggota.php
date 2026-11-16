<?php $q = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY id_anggota DESC"); ?>
<div class="d-flex justify-content-between mb-3">
  <h5 class="mb-0">Data Anggota</h5>
  <a href="dashboard.php?halaman=input_anggota" class="btn btn-primary btn-sm">+ Tambah Anggota</a>
</div>
<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
  <thead class="table-dark">
    <tr><th>No</th><th>NIS</th><th>Nama</th><th>Username</th><th>Kelas</th><th>Aksi</th></tr>
  </thead>
  <tbody>
  <?php $no = 1; while ($d = mysqli_fetch_assoc($q)) { ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= e($d['nis']) ?></td>
      <td><?= e($d['nama_anggota']) ?></td>
      <td><?= e($d['username']) ?></td>
      <td><?= e($d['kelas']) ?></td>
      <td class="text-nowrap">
        <a href="dashboard.php?halaman=edit_anggota&id=<?= (int)$d['id_anggota'] ?>" class="btn btn-warning btn-sm">Edit</a>
        <form action="hapus_anggota.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">
          <input type="hidden" name="id" value="<?= (int)$d['id_anggota'] ?>">
          <button class="btn btn-danger btn-sm">Hapus</button>
        </form>
      </td>
    </tr>
  <?php } ?>
  <?php if ($no == 1) echo "<tr><td colspan='6' class='text-center text-muted'>Belum ada data anggota</td></tr>"; ?>
  </tbody>
</table>
</div>
