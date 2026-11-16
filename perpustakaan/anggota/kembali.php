<?php
include "cek_anggota.php";
$id_anggota   = (int)$_SESSION['id_anggota'];
$id_transaksi = (int)($_POST['id'] ?? 0);

mysqli_begin_transaction($koneksi);
// Pastikan transaksi ini milik anggota yang sedang login
$s = mysqli_prepare($koneksi, "SELECT id_buku FROM transaksi WHERE id_transaksi=? AND id_anggota=? AND status_transaksi='Peminjaman'");
mysqli_stmt_bind_param($s, "ii", $id_transaksi, $id_anggota);
mysqli_stmt_execute($s);
$t = mysqli_fetch_assoc(mysqli_stmt_get_result($s));

if ($t) {
    $u1 = mysqli_prepare($koneksi, "UPDATE transaksi SET status_transaksi='Pengembalian', tgl_kembali=NOW() WHERE id_transaksi=?");
    mysqli_stmt_bind_param($u1, "i", $id_transaksi); mysqli_stmt_execute($u1);
    $u2 = mysqli_prepare($koneksi, "UPDATE buku SET status='tersedia' WHERE id_buku=?");
    mysqli_stmt_bind_param($u2, "i", $t['id_buku']); mysqli_stmt_execute($u2);
    mysqli_commit($koneksi);
    notif("Buku berhasil dikembalikan", "dashboard.php");
}
mysqli_rollback($koneksi);
notif("Data tidak ditemukan", "dashboard.php");
