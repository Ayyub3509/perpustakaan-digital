CREATE DATABASE IF NOT EXISTS perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE perpustakaan;

CREATE TABLE IF NOT EXISTS admin (
  id_admin INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(50) NOT NULL,
  nama_admin VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_admin)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS anggota (
  id_anggota INT(11) NOT NULL AUTO_INCREMENT,
  nis VARCHAR(20) NOT NULL,
  nama_anggota VARCHAR(50) NOT NULL,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(50) NOT NULL,
  kelas VARCHAR(31) NOT NULL,
  PRIMARY KEY (id_anggota)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS buku (
  id_buku INT(11) NOT NULL AUTO_INCREMENT,
  judul_buku VARCHAR(100) NOT NULL,
  pengarang VARCHAR(100) NOT NULL,
  penerbit VARCHAR(100) NOT NULL,
  tahun_terbit YEAR(4) NOT NULL,
  status ENUM('tersedia','tidak') NOT NULL DEFAULT 'tersedia',
  PRIMARY KEY (id_buku)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transaksi (
  id_transaksi INT(11) NOT NULL AUTO_INCREMENT,
  id_anggota INT(11) NOT NULL,
  id_buku INT(11) NOT NULL,
  tgl_pinjam DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tgl_kembali DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status_transaksi ENUM('Peminjaman','Pengembalian') NOT NULL DEFAULT 'Peminjaman',
  PRIMARY KEY (id_transaksi)
) ENGINE=InnoDB;

-- Data awal (login admin: admin / admin, login anggota: senja / senja)
INSERT INTO admin (username,password,nama_admin) VALUES ('admin','admin','Administrator Perpustakaan');
INSERT INTO anggota (nis,nama_anggota,username,password,kelas) VALUES ('12345','Siti','senja','senja','XII RPL');
INSERT INTO buku (judul_buku,pengarang,penerbit,tahun_terbit,status) VALUES
 ('Pemrograman Web dengan PHP','Budi Raharjo','Informatika',2022,'tersedia'),
 ('Basis Data MySQL','Rosa A.S.','Modula',2020,'tersedia'),
 ('Buku Cetak Bahasa Indonesia','Tim Kemdikbud','Kemdikbud',2021,'tersedia');
