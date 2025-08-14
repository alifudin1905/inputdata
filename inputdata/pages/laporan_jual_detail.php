<?php
// Laporan Penjualan Detail: per nota, tampilkan semua barang, qty, harga, subtotal

include '../includes/db.php';
include '../includes/header.php';
session_start();

$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$selected_karyawan = isset($_GET['karyawan']) ? $_GET['karyawan'] : '';

$where = 'WHERE DATE(p.tanggal) BETWEEN ? AND ?';
$params = [$tanggal_awal, $tanggal_akhir];
if (!empty($selected_karyawan)) {
    $where .= ' AND k.id = ?';
    $params[] = $selected_karyawan;
}
// Ambil juga id penjualan untuk aksi edit/hapus
$sql = "SELECT p.id AS id_penjualan, k.nama AS karyawan, p.nomor_nota, p.tanggal, d.nama_barang, d.qty, d.harga, (d.qty*d.harga) as subtotal, p.tunai, p.transfer
    FROM penjualan p
    JOIN karyawan k ON p.id_karyawan = k.id
    JOIN detail_penjualan d ON p.id = d.id_penjualan
    $where
    ORDER BY p.tanggal DESC, p.id DESC, d.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Tampilkan menu tab laporan
echo '<div class="laporan-tabs">';
echo '<a href="laporan.php" class="">Laporan Penjualan</a>';
echo '<a href="laporan_jual_detail.php" class="active">Laporan Jual Detail</a>';
echo '<a href="laporan_pembelian.php" class="">Laporan Pembelian</a>';
echo '</div>';

?>
<div class="container">
    <div class="card laporan">
        <h1>Laporan Penjualan Detail</h1>
        <?php
        // Ambil daftar karyawan untuk filter
        $karyawan_list = $pdo->query("SELECT id, nama FROM karyawan ORDER BY nama")->fetchAll();
        ?>
        <form method="get" class="form-inline" style="margin-bottom:18px;gap:12px;align-items:center;flex-wrap:wrap;">
            <label>Dari: <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>"></label>
            <label>Sampai: <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>"></label>
            <label>Karyawan:
                <select name="karyawan">
                    <option value="">Semua Karyawan</option>
                    <?php foreach ($karyawan_list as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($selected_karyawan == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>
        <table class="tabel-data">
        <thead>
        <tr>
            <th>Nama</th>
            <th>Nota</th>
            <th>Tanggal</th>
            <th>Barang</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Harga</th>
            <th class="text-right">Subtotal</th>
            <th class="text-right">Tunai</th>
            <th class="text-right">Transfer</th>
            <th>Opsi</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $last_nota = null;
        $last_id_penjualan = null;
        while($row = $stmt->fetch()):
            $is_first = $last_id_penjualan !== $row['id_penjualan'];
        ?>
            <tr>
                <td style="padding:5,5px 9,5px;<?= $is_first ? '' : 'color:#888;' ?>"><?= $is_first ? htmlspecialchars($row['karyawan']) : '' ?></td>
                <td style="padding:5,5px 9,5px;<?= $is_first ? '' : 'color:#888;' ?>"><?= $is_first ? htmlspecialchars($row['nomor_nota']) : '' ?></td>
                <td style="padding:5,5px 9,5px;<?= $is_first ? '' : 'color:#888;' ?>"><?= $is_first ? htmlspecialchars($row['tanggal']) : '' ?></td>
                <td style="padding:5,5px 9,5px;"><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td style="padding:5,5px 9,5px;text-align:center;"><?= $row['qty'] ?></td>
                <td style="padding:5,5px 9,5px;text-align:right;"><?= number_format($row['harga'],0) ?></td>
                <td style="padding:5,5px 9,5px;text-align:right;"><?= number_format($row['subtotal'],0) ?></td>
                <td style="padding:5,5px 9,5px;text-align:right;"><?= $is_first ? number_format($row['tunai'],0) : '' ?></td>
                <td style="padding:5,5px 9,5px;text-align:right;"><?= $is_first ? number_format($row['transfer'],0) : '' ?></td>
                <td style="padding:5,5px 10px;">
                <?php if ($is_first): ?>
                    <a href="../edit_transaksi.php?id_penjualan=<?= $row['id_penjualan'] ?>" class="btn btn-primary" style="padding:4px 10px;font-size:0.95em;">Edit</a>
                    <a href="../pages/hapus_nota.php?id=<?= $row['id_penjualan'] ?>" class="btn btn-negative" style="padding:4px 10px;font-size:0.95em;" onclick="return confirm('Hapus nota ini?')">Hapus</a>
                <?php endif; ?>
                </td>
            </tr>
        <?php $last_id_penjualan = $row['id_penjualan']; endwhile; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
