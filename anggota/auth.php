<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['id_anggota'])) {
    header("Location: ../login-anggota.php");
    exit;
}
require_once __DIR__ . "/../koneksi.php";
$current_page = basename($_SERVER['PHP_SELF']);
$id_anggota   = (int)$_SESSION['id_anggota'];
