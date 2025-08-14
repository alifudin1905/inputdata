<?php
include __DIR__ . '/includes/db.php';
header('Content-Type: application/json');
$id_karyawan = isset($_GET['id_karyawan']) ? $_GET['id_karyawan'] : '';
$next_nota = 1;
if ($id_karyawan !== '') {
    $stmt = $pdo->prepare("SELECT MAX(CAST(nomor_nota AS UNSIGNED)) as max_nota FROM penjualan WHERE id_karyawan = ?");
    $stmt->execute([$id_karyawan]);
    $max_nota = $stmt->fetch()['max_nota'];
    $next_nota = ($max_nota !== null && $max_nota !== '') ? ((int)$max_nota + 1) : 1;
}
echo json_encode(['next_nota' => $next_nota]);
