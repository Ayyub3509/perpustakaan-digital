<?php
// Sesuaikan nama database dengan yang kamu buat di phpMyAdmin
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($koneksi, "utf8mb4");

// Escape output agar aman dari XSS
function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

// Tampilkan pemberitahuan lalu pindah halaman
function notif($pesan, $url) {
    echo "<script>alert(" . json_encode($pesan) . ");window.location=" . json_encode($url) . ";</script>";
    exit;
}
