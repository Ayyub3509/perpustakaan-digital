<?php
// Dipakai untuk menghapus data peminjaman maupun pengembalian
include "cek_admin.php";
$id = (int)($_POST['id'] ?? 0);

mysqli_begin_transaction($koneksi);
$s = mysqli_prepare($koneksi, "SELECT id_buku, status_transaksi FROM transaksi WHERE id_transaksi=?");
mysqli_stmt_bind_param($s, "i", $id);
mysqli_stmt_execute($s);
$t = mysqli_fetch_assoc(mysqli_stmt_get_result($s));

if ($t) {
    // Kalau yang dihapus masih berstatus dipinjam, buku dikembalikan jadi tersedia
    if ($t['status_transaksi'] == 'Peminjaman') {
        $u = mysqli_prepare($koneksi, "UPDATE buku SET status='tersedia' WHERE id_buku=?");
        mysqli_stmt_bind_param($u, "i", $t['id_buku']); mysqli_stmt_execute($u);
    }
    $d = mysqli_prepare($koneksi, "DELETE FROM transaksi WHERE id_transaksi=?");
    mysqli_stmt_bind_param($d, "i", $id); mysqli_stmt_execute($d);
    mysqli_commit($koneksi);
    notif("Data berhasil dihapus", "dashboard.php?halaman=data_peminjaman");
}
mysqli_rollback($koneksi);
notif("Data tidak ditemukan", "dashboard.php?halaman=data_peminjaman");
