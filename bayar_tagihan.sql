-- Tambahkan tabel bayar_tagihan untuk fitur pembayaran tagihan
CREATE TABLE bayar_tagihan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_tagihan VARCHAR(100) NOT NULL,
    nominal DECIMAL(18,2) NOT NULL,
    tanggal DATE NOT NULL,
    keterangan VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
