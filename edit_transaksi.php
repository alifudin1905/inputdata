<?php
// edit_transaksi.php
include_once __DIR__ . '/includes/db.php';
session_start();
$id_penjualan = isset($_GET['id_penjualan']) ? intval($_GET['id_penjualan']) : 0;
if (!$id_penjualan) {
    echo '<div style="color:red;">ID penjualan tidak ditemukan.</div>';
    exit;
}
// Ambil data penjualan
$stmt = $pdo->prepare('SELECT p.*, k.nama as nama_karyawan FROM penjualan p JOIN karyawan k ON p.id_karyawan = k.id WHERE p.id = ?');
$stmt->execute([$id_penjualan]);
$penjualan = $stmt->fetch();
if (!$penjualan) {
    echo '<div style="color:red;">Data penjualan tidak ditemukan.</div>';
    exit;
}
// Ambil detail barang
$stmt = $pdo->prepare('SELECT * FROM detail_penjualan WHERE id_penjualan = ?');
$stmt->execute([$id_penjualan]);
$detail = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi Penjualan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Edit Transaksi Penjualan</h2>
    <form method="post" action="update_transaksi.php">
        <input type="hidden" name="id_penjualan" value="<?= $penjualan['id'] ?>">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($penjualan['tanggal']) ?>">
        <input type="hidden" name="karyawan" value="<?= htmlspecialchars($penjualan['id_karyawan']) ?>">
        <table class="tabel-data" id="tabel-barang">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($detail as $i => $d): ?>
                <tr>
                    <td>
                        <input type="hidden" name="id_detail[]" value="<?= $d['id'] ?>">
                        <input type="text" name="nama_barang[]" value="<?= htmlspecialchars($d['nama_barang']) ?>" required style="width:140px;">
                    </td>
                    <td><input type="number" name="qty[]" value="<?= $d['qty'] ?>" min="1" required style="width:60px;" class="input-qty"></td>
                    <td><input type="number" name="harga[]" value="<?= $d['harga'] ?>" min="0" required style="width:90px;" class="input-harga"></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div style="margin-top:16px;display:flex;gap:18px;align-items:center;">
            <label>Tunai: <input type="number" name="tunai" id="input-tunai" value="<?= $penjualan['tunai'] ?>" min="0" required></label>
            <label>Transfer: <input type="number" name="transfer" id="input-transfer" value="<?= $penjualan['transfer'] ?>" min="0" required></label>
        </div>
        <div id="info-total" style="margin-top:18px;font-weight:bold;font-size:1.08em;background:#f8f8f8;padding:12px 18px;border-radius:6px;max-width:420px;">
            <!-- Total info will be rendered here -->
        </div>
        <div style="margin-top:18px;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="pages/laporan_jual_detail.php" class="btn">Batal</a>
        </div>
        <script>
        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        function updateTotalInfo() {
            let subtotal = 0;
            document.querySelectorAll('#tabel-barang tbody tr').forEach(function(row) {
                let qty = parseFloat(row.querySelector('.input-qty').value) || 0;
                let harga = parseFloat(row.querySelector('.input-harga').value) || 0;
                subtotal += qty * harga;
            });
            let tunai = parseFloat(document.getElementById('input-tunai').value) || 0;
            let transfer = parseFloat(document.getElementById('input-transfer').value) || 0;
            let total_bayar = tunai + transfer;
            let selisih = total_bayar - subtotal;
            let html = '';
            html += 'Total Barang: <span style="color:#007700">' + formatRupiah(subtotal) + '</span><br>';
            html += 'Total Pembayaran: <span style="color:#0055aa">' + formatRupiah(total_bayar) + '</span><br>';
            html += 'Selisih: <span style="color:' + (selisih === 0 ? '#333' : (selisih > 0 ? '#007700' : '#bb0000')) + '">' + formatRupiah(selisih) + '</span>';
            document.getElementById('info-total').innerHTML = html;
        }
        document.querySelectorAll('.input-qty, .input-harga, #input-tunai, #input-transfer').forEach(function(input) {
            input.addEventListener('input', updateTotalInfo);
        });
        // Initial render
        updateTotalInfo();
        </script>
    </form>
</div>
</body>
</html>
