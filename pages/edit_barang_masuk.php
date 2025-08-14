<?php
include '../includes/db.php';
include '../includes/header.php';
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1) die('ID tidak valid');

// Ambil data lama
$stmt = $pdo->prepare('SELECT * FROM barang_masuk WHERE id = ?');
$stmt->execute([$id]);
$data = $stmt->fetch();
if (!$data) die('Data tidak ditemukan');

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_masuk = $_POST['tanggal_masuk'] ?? $data['tanggal_masuk'];
    $tanggal_faktur = !empty($_POST['tanggal_faktur']) ? $_POST['tanggal_faktur'] : null;
    $suplayer = $_POST['suplayer'] ?? $data['suplayer'];
    $nomor_faktur = $_POST['nomor_faktur'] ?? $data['nomor_faktur'];
    $pembayaran = $_POST['pembayaran'] ?? $data['pembayaran'];
    $lama_tempo = $_POST['lama_tempo'] ?? $data['lama_tempo'];
    $jumlah_seluruh_faktur = $_POST['jumlah_seluruh_faktur'] ?? $data['jumlah_seluruh_faktur'];
    $stmt = $pdo->prepare('UPDATE barang_masuk SET tanggal_masuk=?, tanggal_faktur=?, suplayer=?, nomor_faktur=?, pembayaran=?, lama_tempo=?, jumlah_seluruh_faktur=? WHERE id=?');
    $stmt->execute([$tanggal_masuk, $tanggal_faktur, $suplayer, $nomor_faktur, $pembayaran, $lama_tempo, $jumlah_seluruh_faktur, $id]);
    header('Location: laporan_pembelian.php');
    exit;
}
?>
<div class="card" style="max-width:600px;margin:32px auto;">
    <h2>Edit Barang Masuk</h2>
    <form method="post">
        <label>Tanggal Masuk:<br><input type="date" name="tanggal_masuk" value="<?= htmlspecialchars($data['tanggal_masuk']) ?>" required></label><br><br>
        <label>Tanggal Faktur:<br><input type="date" name="tanggal_faktur" value="<?= htmlspecialchars($data['tanggal_faktur']) ?>"></label><br><br>
        <label>Nama Suplayer:<br><input type="text" name="suplayer" value="<?= htmlspecialchars($data['suplayer']) ?>" required></label><br><br>
        <label>Nomor Faktur:<br><input type="text" name="nomor_faktur" pattern="[A-Za-z0-9]+" value="<?= htmlspecialchars($data['nomor_faktur']) ?>" required></label><br><br>
        <label>Pembayaran:<br>
            <select name="pembayaran" id="pembayaran" required onchange="document.getElementById('lama_tempo_wrap').style.display = this.value === 'tempo' ? 'block' : 'none';">
                <option value="">Pilih</option>
                <option value="cash" <?= $data['pembayaran']==='cash'?'selected':'' ?>>Cash</option>
                <option value="tempo" <?= $data['pembayaran']==='tempo'?'selected':'' ?>>Tempo</option>
                <option value="transfer" <?= $data['pembayaran']==='transfer'?'selected':'' ?>>Transfer</option>
            </select>
        </label><br><br>
        <div id="lama_tempo_wrap" style="display:<?= $data['pembayaran']==='tempo'?'block':'none' ?>;">
            <label>Lama Jatuh Tempo (hari):<br><input type="number" name="lama_tempo" min="0" value="<?= htmlspecialchars($data['lama_tempo']) ?>"></label><br><br>
        </div>
        <label>Jumlah Seluruh Faktur:<br><input type="number" name="jumlah_seluruh_faktur" min="0" step="any" value="<?= htmlspecialchars($data['jumlah_seluruh_faktur']) ?>" required></label><br><br>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="laporan_pembelian.php" class="btn btn-negative">Kembali</a>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var pembayaran = document.getElementById('pembayaran');
        var lamaTempoWrap = document.getElementById('lama_tempo_wrap');
        if (pembayaran && pembayaran.value === 'tempo') {
            lamaTempoWrap.style.display = 'block';
        }
        pembayaran.addEventListener('change', function() {
            lamaTempoWrap.style.display = this.value === 'tempo' ? 'block' : 'none';
        });
    });
</script>
<?php include '../includes/footer.php'; ?>
