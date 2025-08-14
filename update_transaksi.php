<?php
// update_transaksi.php
include_once __DIR__ . '/includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penjualan = $_POST['id_penjualan'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $karyawan = $_POST['karyawan'] ?? '';
    $tunai = $_POST['tunai'] ?? 0;
    $transfer = $_POST['transfer'] ?? 0;
    $id_detail = $_POST['id_detail'] ?? [];
    $nama_barang = $_POST['nama_barang'] ?? [];
    $qty = $_POST['qty'] ?? [];
    $harga = $_POST['harga'] ?? [];

    if (!$id_penjualan || empty($id_detail) || empty($nama_barang) || empty($qty) || empty($harga)) {
        $_SESSION['error'] = 'Data tidak lengkap.';
        header('Location: pages/laporan.php');
        exit;
    }

    // Update pembayaran
    $stmt = $pdo->prepare('UPDATE penjualan SET tunai = ?, transfer = ? WHERE id = ?');
    $stmt->execute([$tunai, $transfer, $id_penjualan]);

    // Update detail barang
    for ($i = 0; $i < count($id_detail); $i++) {
        $stmt = $pdo->prepare('UPDATE detail_penjualan SET nama_barang = ?, qty = ?, harga = ? WHERE id = ?');
        $stmt->execute([
            $nama_barang[$i],
            $qty[$i],
            $harga[$i],
            $id_detail[$i]
        ]);
    }

    $_SESSION['success'] = 'Transaksi berhasil diupdate.';
    $redirect = 'Location: pages/laporan.php?tanggal=' . urlencode($tanggal);
    if ($karyawan) $redirect .= '&karyawan=' . urlencode($karyawan);
    header($redirect);
    exit;
}

$_SESSION['error'] = 'Permintaan tidak valid.';
header('Location: pages/laporan.php');
exit;
