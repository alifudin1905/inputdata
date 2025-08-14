<?php
include_once __DIR__ . '/../includes/db.php';
include_once __DIR__ . '/../includes/header.php';
session_start();

// Tambah karyawan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
	$stmt = $pdo->prepare("INSERT INTO karyawan (nama) VALUES (?)");
	$stmt->execute([$_POST['nama_karyawan']]);
	$_SESSION['success'] = "Karyawan berhasil ditambahkan!";
	header("Location: karyawan.php");
	exit;
}

// Edit karyawan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
	$stmt = $pdo->prepare("UPDATE karyawan SET nama = ? WHERE id = ?");
	$stmt->execute([$_POST['nama_karyawan'], $_POST['id']]);
	$_SESSION['success'] = "Karyawan berhasil diupdate!";
	header("Location: karyawan.php");
	exit;
}

// Hapus karyawan
if (isset($_GET['hapus'])) {
	$stmt = $pdo->prepare("DELETE FROM karyawan WHERE id = ?");
	$stmt->execute([$_GET['hapus']]);
	$_SESSION['success'] = "Karyawan berhasil dihapus!";
	header("Location: karyawan.php");
	exit;
}

// Ambil data karyawan
$stmt = $pdo->query("SELECT * FROM karyawan ORDER BY nama");
$karyawan = $stmt->fetchAll();
?>

<div class="container">
	<header>
		<h1>Manajemen Data Karyawan</h1>
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
		<h2>Tambah Karyawan Baru</h2>
		<form method="post">
			<label>Nama Karyawan: <input type="text" name="nama_karyawan" required></label>
			<button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
		</form>
	</div>
	<div class="card">
		<h2>Daftar Karyawan</h2>
		<table class="tabel-data">
			<thead>
				<tr>
					<th>ID</th>
					<th>Nama</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($karyawan as $k): ?>
				<tr>
					<td><?= $k['id'] ?></td>
					<td colspan="2" style="padding:0;">
						<div style="display:flex; align-items:center; gap:8px;">
							<form method="post" style="display:flex; align-items:center; gap:6px; margin:0;">
								<input type="hidden" name="id" value="<?= $k['id'] ?>">
								<input type="text" name="nama_karyawan" value="<?= htmlspecialchars($k['nama']) ?>" required style="width:120px; padding:4px 8px; font-size:0.95rem;">
								<button type="submit" name="edit" class="btn btn-primary" style="padding:4px 10px; font-size:0.95rem; min-width:unset; min-height:unset;">Edit</button>
							</form>
							<a href="karyawan.php?hapus=<?= $k['id'] ?>" onclick="return confirm('Hapus karyawan ini?')" class="btn btn-negative" style="padding:4px 10px; font-size:0.95rem; min-width:unset; min-height:unset;">Hapus</a>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
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

	// Atur tampilan awal
	pageUp.style.display = 'none';
	pageDown.style.display = 'block';
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
