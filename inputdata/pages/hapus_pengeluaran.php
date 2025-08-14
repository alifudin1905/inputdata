<?php
include '../includes/db.php';
session_start();

$id = $_GET['id'] ?? null;
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM pengeluaran WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = 'Pengeluaran berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus pengeluaran.';
    }
}
header('Location: laporan.php?tanggal=' . urlencode($tanggal));
exit;
