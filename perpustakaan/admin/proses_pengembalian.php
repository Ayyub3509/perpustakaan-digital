<?php
include "cek_admin.php";
$id = (int)($_POST['id'] ?? 0);

mysqli_begin_transaction($koneksi);
$s = mysqli_prepare($koneksi, "SELECT id_buku FROM transaksi WHERE id_transaksi=? AND status_transaksi='Peminjaman'");
mysqli_stmt_bind_param($s, "i", $id);
mysqli_stmt_execute($s);
$t = mysqli_fetch_assoc(mysqli_stmt_get_result($s));

if ($t) {
    $u1 = mysqli_prepare($koneksi, "UPDATE transaksi SET status_transaksi='Pengembalian', tgl_kembali=NOW() WHERE id_transaksi=?");
    mysqli_stmt_bind_param($u1, "i", $id); mysqli_stmt_execute($u1);
    $u2 = mysqli_prepare($koneksi, "UPDATE buku SET status='tersedia' WHERE id_buku=?");
    mysqli_stmt_bind_param($u2, "i", $t['id_buku']); mysqli_stmt_execute($u2);
    mysqli_commit($koneksi);
    notif("Buku berhasil dikembalikan", "dashboard.php?halaman=data_peminjaman");
}
mysqli_rollback($koneksi);
notif("Data tidak ditemukan", "dashboard.php?halaman=data_peminjaman");
