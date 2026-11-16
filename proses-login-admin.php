<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login-admin.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = mysqli_prepare($koneksi, "SELECT id_admin, nama_admin, password FROM admin WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($data && cek_password($password, $data['password'])) {
    session_regenerate_id(true);
    $_SESSION['id_admin']   = $data['id_admin'];
    $_SESSION['nama_admin'] = $data['nama_admin'];
    header("Location: admin/dashboard.php");
    exit;
}

notif("Login Gagal! Username atau Password salah.", "login-admin.php");
