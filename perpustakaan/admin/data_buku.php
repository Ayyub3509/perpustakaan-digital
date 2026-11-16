<?php $q = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id_buku DESC"); ?>
<div class="d-flex justify-content-between mb-3">
  <h5 class="mb-0">Data Buku</h5>
  <a href="dashboard.php?halaman=input_buku" class="btn btn-primary btn-sm">+ Tambah Buku</a>
</div>
<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
  <thead class="table-dark">
    <tr><th>No</th><th>Judul</th><th>Pengarang</th><th>Penerbit</th><th>Tahun</th><th>Status</th><th>Aksi</th></tr>
  </thead>
  <tbody>
  <?php $no = 1; while ($d = mysqli_fetch_assoc($q)) { ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= e($d['judul_buku']) ?></td>
      <td><?= e($d['pengarang']) ?></td>
      <td><?= e($d['penerbit']) ?></td>
      <td><?= e($d['tahun_terbit']) ?></td>
      <td><span class="badge bg-<?= $d['status'] == 'tersedia' ? 'success' : 'secondary' ?>"><?= e($d['status']) ?></span></td>
      <td class="text-nowrap">
        <a href="dashboard.php?halaman=edit_buku&id=<?= (int)$d['id_buku'] ?>" class="btn btn-warning btn-sm">Edit</a>
        <form action="hapus_buku.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
          <input type="hidden" name="id" value="<?= (int)$d['id_buku'] ?>">
          <button class="btn btn-danger btn-sm">Hapus</button>
        </form>
      </td>
    </tr>
  <?php } ?>
  <?php if ($no == 1) echo "<tr><td colspan='7' class='text-center text-muted'>Belum ada data buku</td></tr>"; ?>
  </tbody>
</table>
</div>
