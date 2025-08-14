<?php
include '../includes/db.php';
session_start();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=laporan_penjualan.csv');

$output = fopen('php://output', 'w');

// Header kolom
fputcsv($output, ['Tanggal', 'Karyawan', 'Nomor Nota', 'Tunai', 'Transfer', 'Total']);

// Ambil filter dari GET
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$karyawan = isset($_GET['karyawan']) ? $_GET['karyawan'] : '';

$params = [$tanggal_awal, $tanggal_akhir];
$where = 'WHERE DATE(p.tanggal) BETWEEN ? AND ?';
if ($karyawan) {
    $where .= ' AND k.id = ?';
    $params[] = $karyawan;
}
$sql = "SELECT p.tanggal, k.nama AS karyawan, p.nomor_nota, p.tunai, p.transfer FROM penjualan p JOIN karyawan k ON p.id_karyawan = k.id $where ORDER BY p.tanggal DESC, p.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
while ($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['tanggal'],
        $row['karyawan'],
        $row['nomor_nota'],
        $row['tunai'],
        $row['transfer'],
        $row['tunai'] + $row['transfer']
    ]);
}
fclose($output);
exit;
