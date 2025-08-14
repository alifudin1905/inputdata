<?php
// laporan_barang_masuk.php
include '../includes/db.php';
include '../includes/header.php';
session_start();


$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$selected_suplayer = isset($_GET['suplayer']) ? $_GET['suplayer'] : '';

// Ambil daftar suplayer
$suplayer_list = $pdo->query("SELECT DISTINCT suplayer FROM barang_masuk WHERE suplayer IS NOT NULL AND suplayer != '' ORDER BY suplayer")->fetchAll();

$where = 'WHERE bm.tanggal_masuk BETWEEN ? AND ?';
$params = [$tanggal_awal, $tanggal_akhir];
if (!empty($selected_suplayer)) {
    $where .= ' AND bm.suplayer = ?';
    $params[] = $selected_suplayer;
}
$sql = "SELECT bm.tanggal_masuk, bm.nomor_faktur, bm.suplayer, dbm.nama_barang, dbm.qty, dbm.harga_modal FROM barang_masuk bm JOIN detail_barang_masuk dbm ON bm.id = dbm.id_barang_masuk $where ORDER BY bm.tanggal_masuk DESC, bm.id DESC, dbm.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Tampilkan filter
?>
<div class="container">
    <h1>Laporan Barang Masuk</h1>
    <form method="get" style="margin-bottom:18px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <label>Dari: <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>"></label>
        <label>Sampai: <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>"></label>
        <label>Suplayer:
            <select name="suplayer">
                <option value="">Semua Suplayer</option>
                <?php foreach ($suplayer_list as $s): ?>
                    <option value="<?= htmlspecialchars($s['suplayer']) ?>" <?= ($selected_suplayer == $s['suplayer']) ? 'selected' : '' ?>><?= htmlspecialchars($s['suplayer']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>
    <table class="tabel-data">
        <thead>
            <tr>
                <th>Tanggal Masuk</th>
                <th>No. Faktur</th>
                <th>Suplayer</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Harga Modal</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $data = $stmt->fetchAll();
        $total_faktur = 0;
        $total_suplayer = 0;
        $grand_total = 0;
        foreach ($data as $i => $row):
            $subtotal = $row['qty'] * $row['harga_modal'];
            $grand_total += $subtotal;
            $show_faktur_total = false;
            $show_suplayer_total = false;
            $total_faktur += $subtotal;
            $total_suplayer += $subtotal;
            $next = isset($data[$i+1]) ? $data[$i+1] : null;
            if (!$next || $next['nomor_faktur'] !== $row['nomor_faktur']) {
                $show_faktur_total = true;
            }
            if (!$next || $next['suplayer'] !== $row['suplayer']) {
                $show_suplayer_total = true;
            }
        ?>
            <tr>
                <td><?= htmlspecialchars($row['tanggal_masuk']) ?></td>
                <td><?= htmlspecialchars($row['nomor_faktur']) ?></td>
                <td><?= htmlspecialchars($row['suplayer']) ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td style="text-align:center;"><?= $row['qty'] ?></td>
                <td style="text-align:right;"><?= number_format($row['harga_modal'],0) ?></td>
                <td style="text-align:right;"><?= number_format($subtotal,0) ?></td>
            </tr>
            <?php if ($show_faktur_total): ?>
            <tr class="total-nota">
                <td colspan="6" style="text-align:right;">Total Faktur <?= htmlspecialchars($row['nomor_faktur']) ?>:</td>
                <td style="text-align:right;"><?= number_format($total_faktur,0) ?></td>
            </tr>
            <?php $total_faktur = 0; endif; ?>
            <?php if ($show_suplayer_total): ?>
            <tr class="total-karyawan">
                <td colspan="6" style="text-align:right;">Total Suplayer <?= htmlspecialchars($row['suplayer']) ?>:</td>
                <td style="text-align:right;"><?= number_format($total_suplayer,0) ?></td>
            </tr>
            <tr><td colspan="7" style="height:18px;background:transparent;"></td></tr>
            <?php $total_suplayer = 0; endif; ?>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="success">
                <td colspan="6" style="text-align:right;">Grand Total:</td>
                <td style="text-align:right;"><?= number_format($grand_total,0) ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
