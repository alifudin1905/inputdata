<?php
// Detail barang masuk per faktur (untuk cart)
// id_barang_masuk diambil dari GET
include '../includes/db.php';
session_start();

$id_barang_masuk = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_barang_masuk < 1) die('ID tidak valid');
$stmt = $pdo->prepare('SELECT * FROM barang_masuk WHERE id = ?');
$stmt->execute([$id_barang_masuk]);
$header = $stmt->fetch();
if (!$header) die('Data tidak ditemukan');
$stmt = $pdo->prepare('SELECT * FROM detail_barang_masuk WHERE id_barang_masuk = ?');
$stmt->execute([$id_barang_masuk]);
$detail = $stmt->fetchAll();
?>
<div class="card" style="max-width:700px;margin:32px auto;">
    <h2>Detail Barang Masuk</h2>
    <div><b>Suplayer:</b> <?= htmlspecialchars($header['suplayer']) ?> | <b>Nomor Faktur:</b> <?= htmlspecialchars($header['nomor_faktur']) ?></div>
    <div><b>Tanggal Masuk:</b> <?= htmlspecialchars($header['tanggal_masuk']) ?> | <b>Tanggal Faktur:</b> <?= htmlspecialchars($header['tanggal_faktur']) ?></div>
    <div><b>Pembayaran:</b> <?= htmlspecialchars($header['pembayaran']) ?><?php if($header['pembayaran']==='tempo') echo ' ('.$header['lama_tempo'].' hari)'; ?></div>
    <div><b>Jumlah Seluruh Faktur:</b> <?= number_format($header['jumlah_seluruh_faktur'],0) ?></div>
    <hr>
    <table class="tabel-data">
        <thead><tr><th>Nama Barang</th><th>Qty</th><th>Harga Modal</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach($detail as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['nama_barang']) ?></td>
            <td><?= $d['qty'] ?></td>
            <td><?= number_format($d['harga_modal'],0) ?></td>
            <td><?= number_format($d['qty']*$d['harga_modal'],0) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
