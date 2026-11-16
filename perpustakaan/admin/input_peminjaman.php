<?php
if (isset($_POST['simpan'])) {
    $id_anggota = (int)$_POST['id_anggota'];
    $id_buku    = (int)$_POST['id_buku'];
    $tgl        = str_replace('T', ' ', $_POST['tgl_pinjam']) . ':00';

    mysqli_begin_transaction($koneksi);
    // Hanya berhasil kalau buku memang masih tersedia
    $u = mysqli_prepare($koneksi, "UPDATE buku SET status='tidak' WHERE id_buku=? AND status='tersedia'");
    mysqli_stmt_bind_param($u, "i", $id_buku);
    mysqli_stmt_execute($u);

    if (mysqli_stmt_affected_rows($u) === 1) {
        $i = mysqli_prepare($koneksi, "INSERT INTO transaksi (id_anggota,id_buku,tgl_pinjam,status_transaksi) VALUES (?,?,?,'Peminjaman')");
        mysqli_stmt_bind_param($i, "iis", $id_anggota, $id_buku, $tgl);
        if (mysqli_stmt_execute($i)) {
            mysqli_commit($koneksi);
            notif("Data peminjaman berhasil disimpan", "dashboard.php?halaman=data_peminjaman");
        }
    }
    mysqli_rollback($koneksi);
    notif("Peminjaman gagal, buku mungkin sudah dipinjam", "dashboard.php?halaman=input_peminjaman");
}

$anggota = mysqli_query($koneksi, "SELECT id_anggota, nama_anggota, kelas FROM anggota ORDER BY nama_anggota");
$buku    = mysqli_query($koneksi, "SELECT id_buku, judul_buku FROM buku WHERE status='tersedia' ORDER BY judul_buku");
?>
<h5>Input Peminjaman</h5>
<form method="post">
  <div class="mb-3"><label class="form-label">Nama Anggota</label>
    <select name="id_anggota" class="form-select" required>
      <option value="">-- Pilih Anggota --</option>
      <?php while ($a = mysqli_fetch_assoc($anggota)) { ?>
        <option value="<?= (int)$a['id_anggota'] ?>"><?= e($a['nama_anggota']) ?> (<?= e($a['kelas']) ?>)</option>
      <?php } ?>
    </select></div>
  <div class="mb-3"><label class="form-label">Buku <small class="text-muted">(hanya yang tersedia)</small></label>
    <select name="id_buku" class="form-select" required>
      <option value="">-- Pilih Buku --</option>
      <?php while ($b = mysqli_fetch_assoc($buku)) { ?>
        <option value="<?= (int)$b['id_buku'] ?>"><?= e($b['judul_buku']) ?></option>
      <?php } ?>
    </select></div>
  <div class="mb-3"><label class="form-label">Tanggal Pinjam</label>
    <input type="datetime-local" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required></div>
  <button type="submit" name="simpan" class="btn btn-primary">SIMPAN</button>
  <a href="dashboard.php?halaman=data_peminjaman" class="btn btn-secondary">Kembali</a>
</form>
