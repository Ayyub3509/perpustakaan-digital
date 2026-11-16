<?php
// Konfigurasi koneksi database MySQL
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "perpustakaan";

$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
mysqli_set_charset($koneksi, "utf8mb4");

// Escape output agar aman dari serangan XSS
function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

// Tampilkan notifikasi alert lalu redirect
function notif($pesan, $url) {
    echo "<script>alert(" . json_encode($pesan) . ");window.location=" . json_encode($url) . ";</script>";
    exit;
}

// Verifikasi password (mendukung teks biasa bawaan contoh maupun password_hash)
function cek_password($input_password, $stored_password) {
    if (password_verify($input_password, $stored_password)) {
        return true;
    }
    return hash_equals((string)$stored_password, (string)$input_password);
}

// Format tanggal Indonesia (YYYY-MM-DD -> DD/MM/YYYY)
function tgl_indo($tanggal) {
    if (empty($tanggal)) return '-';
    return date('d/m/Y', strtotime($tanggal));
}

// Hitung estimasi denda keterlambatan (Rp 1.000 / hari)
function hitung_denda($tanggal_jatuh_tempo, $tanggal_acuan = null) {
    $acuan = $tanggal_acuan ? strtotime($tanggal_acuan) : strtotime(date('Y-m-d'));
    $tempo = strtotime($tanggal_jatuh_tempo);
    if ($acuan <= $tempo) {
        return 0;
    }
    $selisih_hari = (int) floor(($acuan - $tempo) / 86400);
    return $selisih_hari * 1000;
}
