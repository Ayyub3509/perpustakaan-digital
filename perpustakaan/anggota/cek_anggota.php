<?php
session_start();
if (empty($_SESSION['id_anggota'])) {
    header("Location: ../login-anggota.php");
    exit;
}
include "../koneksi.php";
