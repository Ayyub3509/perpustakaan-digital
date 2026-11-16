<?php
include "cek_anggota.php";
$id_anggota = (int)$_SESSION['id_anggota'];
$id_buku    = (int)($_POST['id'] ?? 0);

mysqli_begin_transaction($koneksi);
// Berhasil hanya jika buku masih tersedia (aman dari dua orang pinjam bersamaan)
$u = mysqli_prepare($koneksi, "UPDATE buku SET status='tidak' WHERE id_buku=? AND status='tersedia'");
mysqli_stmt_bind_param($u, "i", $id_buku);
mysqli_stmt_execute($u);

if (mysqli_stmt_affected_rows($u) === 1) {
    $i = mysqli_prepare($koneksi, "INSERT INTO transaksi (id_anggota,id_buku,tgl_pinjam,status_transaksi) VALUES (?,?,NOW(),'Peminjaman')");
    mysqli_stmt_bind_param($i, "ii", $id_anggota, $id_buku);
    if (mysqli_stmt_execute($i)) {
        mysqli_commit($koneksi);
        notif("Buku berhasil dipinjam", "dashboard.php");
    }
}
mysqli_rollback($koneksi);
notif("Buku tidak tersedia", "dashboard.php");
