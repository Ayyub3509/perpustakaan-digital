-- Database: perpustakaan
CREATE DATABASE IF NOT EXISTS `perpustakaan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `perpustakaan`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `peminjaman`;
DROP TABLE IF EXISTS `buku`;
DROP TABLE IF EXISTS `anggota`;
DROP TABLE IF EXISTS `admin`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- 1. Tabel Admin
-- --------------------------------------------------------
CREATE TABLE `admin` (
  `id_admin` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_admin` VARCHAR(50) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `uk_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin` (`id_admin`, `nama_admin`, `username`, `password`) VALUES
(1, 'Administrator Perpustakaan', 'admin', 'admin');

-- --------------------------------------------------------
-- 2. Tabel Anggota
-- --------------------------------------------------------
CREATE TABLE `anggota` (
  `id_anggota` INT(11) NOT NULL AUTO_INCREMENT,
  `nis` VARCHAR(20) NOT NULL,
  `nama_anggota` VARCHAR(50) NOT NULL,
  `kelas` VARCHAR(31) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_anggota`),
  UNIQUE KEY `uk_anggota_nis` (`nis`),
  UNIQUE KEY `uk_anggota_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `anggota` (`id_anggota`, `nis`, `nama_anggota`, `kelas`, `username`, `password`) VALUES
(1, '2026001', 'Senja Putri Ramadhani', 'XII RPL 1', 'senja', 'senja'),
(2, '2026002', 'Budi Santoso', 'XI TKJ 2', 'budi', 'budi'),
(3, '2026003', 'Citra Lestari', 'X IPA 1', 'citra', 'citra');

-- --------------------------------------------------------
-- 3. Tabel Buku
-- --------------------------------------------------------
CREATE TABLE `buku` (
  `id_buku` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_buku` VARCHAR(20) NOT NULL,
  `judul_buku` VARCHAR(100) NOT NULL,
  `pengarang` VARCHAR(80) NOT NULL,
  `penerbit` VARCHAR(80) NOT NULL,
  `tahun_terbit` YEAR NOT NULL,
  `kategori` VARCHAR(40) NOT NULL DEFAULT 'Umum',
  `stok` INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_buku`),
  UNIQUE KEY `uk_kode_buku` (`kode_buku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `buku` (`id_buku`, `kode_buku`, `judul_buku`, `pengarang`, `penerbit`, `tahun_terbit`, `kategori`, `stok`) VALUES
(1, 'BK-001', 'Pemrograman Web Modern dengan PHP & MySQL', 'Budi Raharjo', 'Informatika', 2024, 'Teknologi', 5),
(2, 'BK-002', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Fiksi', 4),
(3, 'BK-003', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 2005, 'Sastra', 3),
(4, 'BK-004', 'Dasar-Dasar Algoritma & Struktur Data', 'Rinaldi Munir', 'Informatika', 2023, 'Teknologi', 6),
(5, 'BK-005', 'Filosofi Teras', 'Henry Manampiring', 'Kompas', 2019, 'Pengembangan Diri', 4),
(6, 'BK-006', 'Sejarah Lengkap Indonesia Modern', 'M.C. Ricklefs', 'Serambi', 2020, 'Sejarah', 2);

-- --------------------------------------------------------
-- 4. Tabel Peminjaman
-- --------------------------------------------------------
CREATE TABLE `peminjaman` (
  `id_peminjaman` INT(11) NOT NULL AUTO_INCREMENT,
  `id_anggota` INT(11) NOT NULL,
  `id_buku` INT(11) NOT NULL,
  `tanggal_pinjam` DATE NOT NULL,
  `tanggal_jatuh_tempo` DATE NOT NULL,
  `tanggal_kembali` DATE DEFAULT NULL,
  `status` ENUM('Dipinjam', 'Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
  `denda` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_peminjaman`),
  KEY `fk_peminjaman_anggota` (`id_anggota`),
  KEY `fk_peminjaman_buku` (`id_buku`),
  CONSTRAINT `fk_peminjaman_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_peminjaman_buku` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `peminjaman` (`id_peminjaman`, `id_anggota`, `id_buku`, `tanggal_pinjam`, `tanggal_jatuh_tempo`, `tanggal_kembali`, `status`, `denda`) VALUES
(1, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), NULL, 'Dipinjam', 0),
(2, 1, 2, DATE_SUB(CURDATE(), INTERVAL 12 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'Dikembalikan', 0),
(3, 2, 5, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), NULL, 'Dipinjam', 0);
