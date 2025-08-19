CREATE DATABASE laporan_harian;
USE laporan_harian;

-- Tabel karyawan
CREATE TABLE karyawan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL
);

-- Tabel penjualan
CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_nota VARCHAR(50) NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_karyawan INT NOT NULL,
    tunai DECIMAL(10,2) NOT NULL,
    transfer DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_karyawan) REFERENCES karyawan(id)
);

-- Tabel detail_penjualan
CREATE TABLE detail_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    qty INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_penjualan) REFERENCES penjualan(id)
);

-- Tabel pengeluaran
CREATE TABLE pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    rincian VARCHAR(255) NOT NULL,
    nominal DECIMAL(10,2) NOT NULL
);

-- Tabel barang_masuk (header)
CREATE TABLE barang_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal_masuk DATE NOT NULL,
    tanggal_faktur DATE,
    suplayer VARCHAR(100),
    nomor_faktur VARCHAR(50),
    pembayaran VARCHAR(20),
    lama_tempo INT NULL,
    jumlah_seluruh_faktur DECIMAL(18,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel detail_barang_masuk (detail per faktur, opsional)
CREATE TABLE detail_barang_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang_masuk INT NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    qty INT NOT NULL,
    harga_modal DECIMAL(18,2) NOT NULL,
    FOREIGN KEY (id_barang_masuk) REFERENCES barang_masuk(id)
);

CREATE TABLE bayar_tagihan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_tagihan VARCHAR(100) NOT NULL,
    nominal DECIMAL(12,2) NOT NULL,
    tanggal DATE NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    rincian TEXT NOT NULL,
    nominal DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);