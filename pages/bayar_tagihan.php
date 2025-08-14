<?php
include '../includes/db.php';
include '../includes/header.php';
session_start();

// Cek apakah tabel bayar_tagihan ada
$tableExists = false;
try {
    $result = $pdo->query("SELECT 1 FROM bayar_tagihan LIMIT 1");
    $tableExists = true;
} catch (PDOException $e) {
    $tableExists = false;
}

// Proses tambah pembayaran tagihan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_tagihan = trim($_POST['nama_tagihan'] ?? '');
    $nominal_str = str_replace('.', '', $_POST['nominal'] ?? '0');
    $nominal = (float)$nominal_str;
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    if (empty($nama_tagihan)) {
        $_SESSION['error'] = "Nama tagihan harus diisi!";
    } elseif ($nominal <= 0) {
        $_SESSION['error'] = "Nominal harus lebih dari 0!";
    } else {
        try {
            if ($tableExists) {
                $stmt = $pdo->prepare('INSERT INTO bayar_tagihan (nama_tagihan, nominal, tanggal, keterangan) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nama_tagihan, $nominal, $tanggal, $keterangan]);
            }
            
            // Masukkan ke pengeluaran
            $stmt2 = $pdo->prepare('INSERT INTO pengeluaran (tanggal, rincian, nominal, keterangan) VALUES (?, ?, ?, ?)');
            $stmt2->execute([
                $tanggal, 
                'Pembayaran tagihan: ' . $nama_tagihan, 
                $nominal,
                $keterangan
            ]);
            
            $_SESSION['success'] = "Pembayaran tagihan berhasil disimpan.";
            header("Location: bayar_tagihan.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header("Location: bayar_tagihan.php");
            exit;
        }
    }
}

// Form input bayar tagihan
?>
<div class="container">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <div class="card" style="max-width:500px;">
        <h2>Bayar Tagihan</h2>
        <form method="post">
            <label>Nama Tagihan:
                <input type="text" name="nama_tagihan" value="<?= htmlspecialchars($_POST['nama_tagihan'] ?? '') ?>" required>
            </label>
            <label>Nominal:
                <input type="text" name="nominal" id="input-nominal" value="<?= htmlspecialchars($_POST['nominal'] ?? '') ?>" required oninput="this.value=formatRupiah(this.value)">
            </label>
            <label>Tanggal:
                <input type="date" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')) ?>" required>
            </label>
            <label>Keterangan:
                <input type="text" name="keterangan" value="<?= htmlspecialchars($_POST['keterangan'] ?? '') ?>">
            </label>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
<script>
function formatRupiah(angka) {
    // Handle minus
    let isNegative = false;
    if (angka.startsWith('-')) {
        isNegative = true;
        angka = angka.substring(1);
    }
    
    let number_string = angka.replace(/[^\d]/g, '').toString();
    let sisa = number_string.length % 3;
    let rupiah = number_string.substr(0, sisa);
    let ribuan = number_string.substr(sisa).match(/\d{3}/g);
        
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    return isNegative ? '-' + rupiah : rupiah;
}

// Fokus ke input nama tagihan saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('input[name="nama_tagihan"]').focus();
});
</script>
<?php include '../includes/footer.php'; ?>