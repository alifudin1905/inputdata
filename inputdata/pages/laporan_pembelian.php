<?php
include '../includes/db.php';
include '../includes/header.php';
session_start();

// Ambil filter dari GET
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$suplayer = isset($_GET['suplayer']) ? $_GET['suplayer'] : '';

// Ambil daftar suplayer unik
$suplayer_list = $pdo->query("SELECT DISTINCT suplayer FROM barang_masuk WHERE suplayer IS NOT NULL AND suplayer != '' ORDER BY suplayer")->fetchAll();

// Query data barang masuk
$params = [$tanggal_awal, $tanggal_akhir];
$where = 'WHERE tanggal_masuk BETWEEN ? AND ?';
if ($suplayer) {
    $where .= ' AND suplayer = ?';
    $params[] = $suplayer;
}
$sql = "SELECT * FROM barang_masuk $where ORDER BY tanggal_masuk DESC, id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();
?>
<div class="container">
    <div class="card laporan">
        <h1>Laporan Pembelian</h1>
        <form method="get" class="form-inline" style="margin-bottom:24px;gap:16px;align-items:center;flex-wrap:wrap;">
            <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                <label>Dari: <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>"></label>
                <label>Sampai: <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>"></label>
                <label>Suplayer: <select name="suplayer">
                    <option value="">Semua Suplayer</option>
                    <?php foreach ($suplayer_list as $s): $sel = ($suplayer == $s['suplayer']) ? 'selected' : ''; ?>
                        <option value="<?= htmlspecialchars($s['suplayer']) ?>" <?= $sel ?>><?= htmlspecialchars($s['suplayer']) ?></option>
                    <?php endforeach; ?>
                </select></label>
            </div>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>
        <div style="margin-bottom:12px;">
            <div class="dropdown-export">
                <button class="btn btn-primary" onclick="toggleExportDropdown(event)">Export ▼</button>
                <div id="exportDropdown" class="dropdown-content">
                    <form id="exportForm" style="padding:10px 18px 0 18px;display:flex;flex-direction:column;gap:8px;">
                        <label style="font-weight:normal;">Dari: <input type="date" id="export_tanggal_awal" name="export_tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>" style="width:100%;margin-bottom:0;"></label>
                        <label style="font-weight:normal;">Sampai: <input type="date" id="export_tanggal_akhir" name="export_tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>" style="width:100%;margin-bottom:0;"></label>
                        <input type="hidden" id="export_suplayer" value="<?= htmlspecialchars($suplayer) ?>">
                        <a href="#" id="exportPembelian" class="btn btn-primary" style="margin-top:8px;">Export CSV</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="card laporan" style="margin-bottom:0;">
            <table class="tabel-data">
            <thead>
                <tr>
                    <th>Tanggal Masuk</th>
                    <th>Suplayer</th>
                    <th>Nomor Faktur</th>
                    <th>Pembayaran</th>
                    <th>Jatuh Tempo</th>
                    <th style="text-align:right;">Jumlah Faktur</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['tanggal_masuk']) ?></td>
                    <td><?= htmlspecialchars($row['suplayer']) ?></td>
                    <td><?= htmlspecialchars($row['nomor_faktur']) ?></td>
                    <td><?= htmlspecialchars($row['pembayaran']) ?></td>
                    <td><?= $row['pembayaran']==='tempo' ? ($row['lama_tempo'].' hari') : '-' ?></td>
                    <td style="text-align:right;"><?= number_format($row['jumlah_seluruh_faktur'],0) ?></td>
                    <td>
                        <a href="edit_barang_masuk.php?id=<?= $row['id'] ?>" class="btn btn-primary">Edit</a>
                        <a href="hapus_barang_masuk.php?id=<?= $row['id'] ?>" class="btn btn-negative" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
            <?php if (empty($data)): ?>
                <tr><td colspan="7" style="text-align:center;color:#888;padding:16px;">Tidak ada data pembelian/barang masuk/tagihan pada rentang ini.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<script>
function toggleExportDropdown(e) {
    e.preventDefault();
    var dd = document.getElementById('exportDropdown');
    if (dd.style.display === 'block') dd.style.display = 'none';
    else dd.style.display = 'block';
}
document.addEventListener('click', function(e) {
    var btn = document.querySelector('.dropdown-export button');
    var dd = document.getElementById('exportDropdown');
    if (!btn.contains(e.target) && !dd.contains(e.target)) {
        dd.style.display = 'none';
    }
});

// Export dengan rentang tanggal custom
document.addEventListener('DOMContentLoaded', function() {
    function getExportParams() {
        var tglAwal = document.getElementById('export_tanggal_awal').value;
        var tglAkhir = document.getElementById('export_tanggal_akhir').value;
        var suplayer = document.getElementById('export_suplayer').value;
        return 'tanggal_awal=' + encodeURIComponent(tglAwal) + '&tanggal_akhir=' + encodeURIComponent(tglAkhir) + '&suplayer=' + encodeURIComponent(suplayer);
    }
    document.getElementById('exportPembelian').onclick = function(e) {
        e.preventDefault();
        var params = getExportParams();
        window.open('export_laporan_pembelian.php?' + params, '_blank');
    };
});
</script>
