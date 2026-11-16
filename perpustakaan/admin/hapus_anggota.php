<?php
include "cek_admin.php";
$id = (int)($_POST['id'] ?? 0);

// Tidak boleh dihapus kalau masih punya pinjaman aktif
$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS jml FROM transaksi WHERE id_anggota=? AND status_transaksi='Peminjaman'");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if ($r['jml'] > 0) notif("Anggota masih memiliki buku yang dipinjam, tidak bisa dihapus", "dashboard.php?halaman=data_anggota");

$s1 = mysqli_prepare($koneksi, "DELETE FROM transaksi WHERE id_anggota=?");
mysqli_stmt_bind_param($s1, "i", $id); mysqli_stmt_execute($s1);
$s2 = mysqli_prepare($koneksi, "DELETE FROM anggota WHERE id_anggota=?");
mysqli_stmt_bind_param($s2, "i", $id); mysqli_stmt_execute($s2);

notif("Data anggota berhasil dihapus", "dashboard.php?halaman=data_anggota");
