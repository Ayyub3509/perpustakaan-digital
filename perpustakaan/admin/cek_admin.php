<?php
// Dipakai oleh file proses (hapus, pengembalian) agar hanya admin yang bisa akses
session_start();
if (empty($_SESSION['id_admin'])) {
    header("Location: ../login-admin.php");
    exit;
}
include "../koneksi.php";
