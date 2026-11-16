<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['id_admin'])) {
    header("Location: ../login-admin.php");
    exit;
}
require_once __DIR__ . "/../koneksi.php";
$current_page = basename($_SERVER['PHP_SELF']);
