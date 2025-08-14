<?php
include '../includes/db.php';
session_start();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=laporan_penjualan_detail.csv');

$output = fopen('php://output', 'w');

// Header kolom
fputcsv($output, ['Tanggal', 'Karyawan', 'Nomor Nota', 'Nama Barang', 'Qty', 'Hrg Barang', 'Total', 'Tunai', 'Transfer']);

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
$sql = "SELECT p.tanggal, k.nama AS karyawan, p.nomor_nota, d.nama_barang, d.qty, d.harga, (d.qty*d.harga) AS subtotal, p.tunai, p.transfer
        FROM penjualan p
        JOIN karyawan k ON p.id_karyawan = k.id
        LEFT JOIN detail_penjualan d ON p.id = d.id_penjualan
        $where
        ORDER BY p.tanggal DESC, p.id DESC, d.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$last_nota = null;
foreach ($stmt as $row) {
    $row_out = [
        $row['tanggal'],
        $row['karyawan'],
        $row['nomor_nota'],
        $row['nama_barang'],
        $row['qty'],
        $row['harga'],
        $row['subtotal'],
        '', // Tunai
        ''  // Transfer
    ];
    // Jika ini baris terakhir untuk nomor nota ini, tampilkan tunai & transfer
    if ($last_nota !== $row['nomor_nota']) {
        $last_nota = $row['nomor_nota'];
        $nota_rows = [];
        $stmt2 = $pdo->prepare("SELECT d.nama_barang FROM detail_penjualan d JOIN penjualan p2 ON d.id_penjualan = p2.id WHERE p2.nomor_nota = ?");
        $stmt2->execute([$row['nomor_nota']]);
        $all_rows = $stmt2->fetchAll();
        $is_last = true;
        if (count($all_rows) > 1) $is_last = false;
        // Akan diisi di bawah
    }
    // Cek apakah ini baris terakhir untuk nomor nota ini
    $stmt3 = $pdo->prepare("SELECT COUNT(*) as cnt FROM detail_penjualan d JOIN penjualan p2 ON d.id_penjualan = p2.id WHERE p2.nomor_nota = ?");
    $stmt3->execute([$row['nomor_nota']]);
    $cnt = $stmt3->fetch()['cnt'];
    static $row_counter = [];
    if (!isset($row_counter[$row['nomor_nota']])) $row_counter[$row['nomor_nota']] = 1;
    else $row_counter[$row['nomor_nota']]++;
    if ($row_counter[$row['nomor_nota']] == $cnt) {
        $row_out[7] = $row['tunai'];
        $row_out[8] = $row['transfer'];
        $row_counter[$row['nomor_nota']] = 0; // reset untuk nota berikutnya
    }
    fputcsv($output, $row_out);
}
fclose($output);
exit;
