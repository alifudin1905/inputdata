<?php
include __DIR__ . '/includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_pengeluaran'] ?? null;
    $rincian = trim($_POST['rincian'] ?? '');
    $nominal = $_POST['nominal'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

    if ($id && $rincian !== '' && is_numeric($nominal)) {
        $stmt = $pdo->prepare("UPDATE pengeluaran SET rincian = ?, nominal = ? WHERE id = ?");
        if ($stmt->execute([$rincian, $nominal, $id])) {
            $_SESSION['success'] = 'Pengeluaran berhasil diupdate.';
        } else {
            $_SESSION['error'] = 'Gagal update pengeluaran.';
        }
    } else {
        $_SESSION['error'] = 'Data tidak lengkap.';
    }
    header('Location: pages/laporan.php?tanggal=' . urlencode($tanggal));
    exit;
}
header('Location: pages/laporan.php');
exit;
