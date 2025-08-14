<?php
include_once __DIR__ . '/../includes/db.php';
include_once __DIR__ . '/../includes/header.php';
session_start();

if (!isset($_SESSION['user'])) {
	header("Location: login.php");
	exit;
}

if (!isset($_SESSION['cart'])) {
	$_SESSION['cart'] = [];
}

// Tambah item ke cart
if (isset($_POST['add_item'])) {
	if (!empty($_POST['nama_barang']) && !empty($_POST['qty']) && !empty($_POST['harga'])) {
		$item = [
			'nama_barang' => $_POST['nama_barang'],
			'qty' => (int)$_POST['qty'],
			'harga' => (int)$_POST['harga'],
			'subtotal' => (int)$_POST['qty'] * (int)$_POST['harga']
		];
		$_SESSION['cart'][] = $item;
	}
	header("Location: index.php");
	exit;
}

// Hapus item dari cart
if (isset($_GET['remove'])) {
	if (isset($_SESSION['cart'][$_GET['remove']])) {
		unset($_SESSION['cart'][$_GET['remove']]);
		$_SESSION['cart'] = array_values($_SESSION['cart']);
	}
	header("Location: index.php");
	exit;
}

// Proses penjualan
if (isset($_POST['submit_penjualan'])) {
	if (empty($_SESSION['cart'])) {
		$_SESSION['error'] = "Cart tidak boleh kosong!";
		header("Location: index.php");
		exit;
	}
	$stmt = $pdo->prepare("INSERT INTO penjualan (nomor_nota, id_karyawan, tunai, transfer) VALUES (?, ?, ?, ?)");
	$stmt->execute([
		$_POST['nomor_nota'],
		$_POST['id_karyawan'],
		$_POST['tunai'],
		$_POST['transfer']
	]);
	$id_penjualan = $pdo->lastInsertId();
	foreach ($_SESSION['cart'] as $item) {
		$stmt = $pdo->prepare("INSERT INTO detail_penjualan (id_penjualan, nama_barang, qty, harga) VALUES (?, ?, ?, ?)");
		$stmt->execute([$id_penjualan, $item['nama_barang'], $item['qty'], $item['harga']]);
	}
	$_SESSION['cart'] = [];
	$_SESSION['success'] = "Penjualan berhasil disimpan!";
	header("Location: index.php");
	exit;
}

// Hitung total pendapatan hari ini
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT SUM(tunai) as total_tunai, SUM(transfer) as total_transfer FROM penjualan WHERE DATE(tanggal) = ?");
$stmt->execute([$today]);
$row = $stmt->fetch();
$total_tunai = $row['total_tunai'] ?? 0;
$total_transfer = $row['total_transfer'] ?? 0;

$stmt = $pdo->prepare("SELECT SUM(nominal) as total_pengeluaran FROM pengeluaran WHERE tanggal = ?");
$stmt->execute([$today]);
$total_pengeluaran = $stmt->fetch()['total_pengeluaran'] ?? 0;

$saldo_tunai = $total_tunai - $total_pengeluaran;
?>
<div class="container">
	<header>
		<h1>Dashboard Kas Hari Ini</h1>
	</header>
	<div class="card" style="margin-bottom:32px; text-align:center;">
		<h2 style="font-size:2rem; margin-bottom:24px;">Rekap Kas Hari Ini (<?= date('d-m-Y') ?>)</h2>
		<div style="font-size:1.3rem; margin-bottom:10px;">Tunai: <b>Rp <?= number_format($total_tunai, 2) ?></b></div>
		<div style="font-size:1.3rem; margin-bottom:10px;">Transfer: <b>Rp <?= number_format($total_transfer, 2) ?></b></div>
		<div style="font-size:1.3rem; margin-bottom:10px;">Pengeluaran: <b>Rp <?= number_format($total_pengeluaran, 2) ?></b></div>
		<div style="font-size:2.2rem; margin-top:30px; color:#d84315; font-weight:bold;">Saldo Tunai (Tunai - Pengeluaran):<br>Rp <?= number_format($saldo_tunai, 2) ?></div>
	</div>
	<?php if (isset($_SESSION['error'])): ?>
		<div class="error"><?= $_SESSION['error'] ?></div>
		<?php unset($_SESSION['error']); ?>
	<?php endif; ?>
	<?php if (isset($_SESSION['success'])): ?>
		<div class="success"><?= $_SESSION['success'] ?></div>
		<?php unset($_SESSION['success']); ?>
	<?php endif; ?>

</div>

<script>
// Fokus otomatis ke input nama barang setelah reload (tambah ke cart)
window.addEventListener('DOMContentLoaded', function() {
	var inputNama = document.getElementById('input-nama-barang');
	if (inputNama) inputNama.focus();
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
