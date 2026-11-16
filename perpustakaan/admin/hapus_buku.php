<?php
include "cek_admin.php";
$id = (int)($_POST['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT status FROM buku WHERE id_buku=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$d = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$d) notif("Data buku tidak ditemukan", "dashboard.php?halaman=data_buku");
if ($d['status'] == 'tidak') notif("Buku sedang dipinjam, tidak bisa dihapus", "dashboard.php?halaman=data_buku");

// Hapus riwayat transaksi buku ini lalu bukunya
$s1 = mysqli_prepare($koneksi, "DELETE FROM transaksi WHERE id_buku=?");
mysqli_stmt_bind_param($s1, "i", $id); mysqli_stmt_execute($s1);
$s2 = mysqli_prepare($koneksi, "DELETE FROM buku WHERE id_buku=?");
mysqli_stmt_bind_param($s2, "i", $id); mysqli_stmt_execute($s2);

notif("Data buku berhasil dihapus", "dashboard.php?halaman=data_buku");
