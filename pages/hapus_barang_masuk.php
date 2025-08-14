<?php
include '../includes/db.php';
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    // Hapus detail barang masuk jika ada
    $stmt = $pdo->prepare('DELETE FROM detail_barang_masuk WHERE id_barang_masuk = ?');
    $stmt->execute([$id]);
    // Hapus header barang masuk
    $stmt = $pdo->prepare('DELETE FROM barang_masuk WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: laporan_pembelian.php');
exit;
