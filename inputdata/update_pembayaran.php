<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penjualan = $_POST['id_penjualan'];
    $tunai = $_POST['tunai'];
    $transfer = $_POST['transfer'];
    
    // Update pembayaran
    $stmt = $pdo->prepare("UPDATE penjualan SET tunai = ?, transfer = ? WHERE id = ?");
    $stmt->execute([$tunai, $transfer, $id_penjualan]);
    
    $_SESSION['success'] = "Pembayaran berhasil diupdate!";
    $redirect = "laporan.php";
    if (isset($_POST['tanggal']) && $_POST['tanggal'] !== '') {
        $redirect .= "?tanggal=" . urlencode($_POST['tanggal']);
    }
    header("Location: $redirect");
    exit;
}
?>