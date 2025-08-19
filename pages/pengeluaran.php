<?php
include_once __DIR__ . '/../includes/db.php';
include_once __DIR__ . '/../includes/header.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bersihkan nominal dari format ribuan
    $nominal = isset($_POST['nominal']) ? str_replace('.', '', $_POST['nominal']) : 0;
    
    $stmt = $pdo->prepare("INSERT INTO pengeluaran (tanggal, rincian, nominal) VALUES (?, ?, ?)");
    $stmt->execute([
        date('Y-m-d'),
        $_POST['rincian'],
        $nominal
    ]);
    $_SESSION['success'] = "Pengeluaran berhasil disimpan!";
    header("Location: pengeluaran.php");
    exit;
}

// Hitung total pengeluaran hari ini
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT SUM(nominal) as total FROM pengeluaran WHERE tanggal = ?");
$stmt->execute([$today]);
$total_pengeluaran = $stmt->fetch()['total'] ?? 0;
?>

<div class="container">
    <header>
        <h1>Form Pengeluaran - Total Pengeluaran Hari Ini: <?= number_format($total_pengeluaran,0) ?></h1>
    </header>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <div class="card">
        <form method="post" id="form-pengeluaran">
            <label>Rincian: <input type="text" name="rincian" required></label>
            <label>Nominal: <input type="text" name="nominal" id="input-nominal" min="0" required oninput="formatRupiah(this)"></label>
            <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
        </form>
    </div>
    <div class="nav-bottom" style="text-align:center; margin:32px 0;">
        <a href="index.php" class="btn btn-negative" style="font-size:1.2rem; min-width:180px;">Kembali ke Penjualan</a>
    </div>
</div>

<!-- Tombol navigasi vertikal -->
<button id="pageUp" class="page-nav">&#8593;</button>
<button id="pageDown" class="page-nav">&#8595;</button>

<script>
    // Tombol navigasi halaman
    const pageUp = document.getElementById('pageUp');
    const pageDown = document.getElementById('pageDown');

    pageUp.addEventListener('click', () => {
        window.scrollBy({ top: -window.innerHeight, behavior: 'smooth' });
    });

    pageDown.addEventListener('click', () => {
        window.scrollBy({ top: window.innerHeight, behavior: 'smooth' });
    });

    // Sembunyikan tombol ketika tidak diperlukan
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            pageUp.style.display = 'block';
        } else {
            pageUp.style.display = 'none';
        }

        if ((window.innerHeight + window.scrollY) < document.body.offsetHeight - 100) {
            pageDown.style.display = 'block';
        } else {
            pageDown.style.display = 'none';
        }
    });

    function formatRupiah(input) {
        // Hapus semua karakter non-digit
        let value = input.value.replace(/\D/g, '');
        
        // Format dengan titik setiap 3 digit
        let formatted = '';
        while (value.length > 3) {
            formatted = '.' + value.substr(value.length - 3) + formatted;
            value = value.substr(0, value.length - 3);
        }
        formatted = value + formatted;
        
        input.value = formatted;
    }

    // Bersihkan format ribuan sebelum submit
    document.getElementById('form-pengeluaran').addEventListener('submit', function() {
        let nominalInput = document.getElementById('input-nominal');
        nominalInput.value = nominalInput.value.replace(/\./g, '');
    });
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>