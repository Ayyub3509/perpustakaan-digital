<?php
session_start();
include "koneksi.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = mysqli_prepare($koneksi, "SELECT id_admin, nama_admin FROM admin WHERE username=? AND password=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $username, $password);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($data) {
    session_regenerate_id(true);
    $_SESSION['id_admin']   = $data['id_admin'];
    $_SESSION['nama_admin'] = $data['nama_admin'];
    header("Location: admin/dashboard.php");
    exit;
}
notif("Login Gagal, Username / Password Salah", "login-admin.php");
