<?php
// Perbaikan path include
include_once __DIR__ . '/../includes/db.php';

// Mulai session jika diperlukan
session_start();

// Ambil parameter jenis laporan
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'all';

// Set nama file berdasarkan jenis
$filename = "laporan_pengeluaran_" . date('Ymd') . ".csv";

// Query berdasarkan jenis laporan
switch ($jenis) {
    case 'pengeluaran':
        $query = "SELECT 
                    tanggal,
                    rincian AS keterangan,
                    nominal,
                    'Pengeluaran' AS kategori
                  FROM pengeluaran
                  ORDER BY tanggal DESC";
        $filename = "laporan_pengeluaran_operasional_" . date('Ymd') . ".csv";
        break;
    
    case 'tagihan':
        $query = "SELECT 
                    tanggal,
                    CONCAT('Tagihan ', nama_tagihan) AS keterangan,
                    nominal,
                    'Tagihan' AS kategori
                  FROM bayar_tagihan
                  ORDER BY tanggal DESC";
        $filename = "laporan_pembayaran_tagihan_" . date('Ymd') . ".csv";
        break;
    
    default: // Semua pengeluaran
        $query = "(
                    SELECT 
                        tanggal,
                        rincian AS keterangan,
                        nominal,
                        'Pengeluaran' AS kategori
                    FROM pengeluaran
                  ) 
                  UNION ALL
                  (
                    SELECT 
                        tanggal,
                        CONCAT('Tagihan ', nama_tagihan) AS keterangan,
                        nominal,
                        'Tagihan' AS kategori
                    FROM bayar_tagihan
                  )
                  ORDER BY tanggal DESC";
        $filename = "laporan_semua_pengeluaran_" . date('Ymd') . ".csv";
}

// Pastikan koneksi database tersedia
if (!isset($pdo)) {
    die("Koneksi database tidak tersedia");
}

// Eksekusi query
$stmt = $pdo->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set header untuk download file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buat output stream
$output = fopen('php://output', 'w');

// Header CSV
fputcsv($output, ['Tanggal', 'Keterangan', 'Nominal', 'Kategori']);

// Isi data
foreach ($results as $row) {
    fputcsv($output, [
        $row['tanggal'],
        $row['keterangan'],
        number_format($row['nominal'], 2),
        $row['kategori']
    ]);
}

fclose($output);
exit;