<?php
include '../includes/db.php';

session_start();

if (isset($_GET['id'])) {
    $id_penjualan = $_GET['id'];
    
    // Hapus detail penjualan terlebih dahulu
    $stmt = $pdo->prepare("DELETE FROM detail_penjualan WHERE id_penjualan = ?");
    $stmt->execute([$id_penjualan]);
    
    // Hapus penjualan
    $stmt = $pdo->prepare("DELETE FROM penjualan WHERE id = ?");
    $stmt->execute([$id_penjualan]);
    
    $_SESSION['success'] = "Nota berhasil dihapus!";
    $redirect = "laporan.php";
    if (isset($_GET['tanggal']) && $_GET['tanggal'] !== '') {
        $redirect .= "?tanggal=" . urlencode($_GET['tanggal']);
    }
    header("Location: $redirect");
    exit;
}
?>