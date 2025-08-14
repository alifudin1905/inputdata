<?php
include '../includes/db.php';
session_start();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=laporan_pembelian.csv');

$output = fopen('php://output', 'w');

// Header kolom
fputcsv($output, ['Tanggal Masuk', 'Suplayer', 'Nomor Faktur', 'Pembayaran', 'Jatuh Tempo', 'Jumlah Faktur']);

// Ambil filter dari GET
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$suplayer = isset($_GET['suplayer']) ? $_GET['suplayer'] : '';

$params = [$tanggal_awal, $tanggal_akhir];
$where = 'WHERE tanggal_masuk BETWEEN ? AND ?';
if ($suplayer) {
    $where .= ' AND suplayer = ?';
    $params[] = $suplayer;
}
$sql = "SELECT * FROM barang_masuk $where ORDER BY tanggal_masuk DESC, id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
while ($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['tanggal_masuk'],
        $row['suplayer'],
        $row['nomor_faktur'],
        $row['pembayaran'],
        $row['pembayaran']==='tempo' ? ($row['lama_tempo'].' hari') : '-',
        $row['jumlah_seluruh_faktur']
    ]);
}
fclose($output);
exit;
