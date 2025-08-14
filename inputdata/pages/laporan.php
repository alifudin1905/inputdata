<?php
include '../includes/db.php';
include '../includes/header.php';
session_start();
echo "<div class='container'>";

// Judul laporan sederhana
echo '<h2 style="margin-bottom:24px;">Laporan Penjualan Sederhana</h2>';
// Ambil daftar karyawan untuk filter
$karyawan_list = $pdo->query("SELECT id, nama FROM karyawan ORDER BY nama")->fetchAll();

// Ambil filter dari GET
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$selected_karyawan = isset($_GET['karyawan']) ? $_GET['karyawan'] : '';

// Tombol Export CSV dengan pilihan rentang tanggal
echo '<div style="margin-bottom:12px;">';
echo '<div class="dropdown-export" style="display:inline-block;position:relative;">';
echo '<button class="btn btn-primary" onclick="toggleExportDropdown(event)">Export ▼</button>';
echo '<div id="exportDropdown" class="dropdown-content" style="display:none;position:absolute;z-index:10;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.08);min-width:240px;">';
echo '<form id="exportForm" style="padding:10px 18px 0 18px;display:flex;flex-direction:column;gap:8px;">';
echo '<label style="font-weight:normal;">Dari: <input type="date" id="export_tanggal_awal" name="export_tanggal_awal" value="' . htmlspecialchars($tanggal_awal) . '" style="width:100%;margin-bottom:0;"></label>';
echo '<label style="font-weight:normal;">Sampai: <input type="date" id="export_tanggal_akhir" name="export_tanggal_akhir" value="' . htmlspecialchars($tanggal_akhir) . '" style="width:100%;margin-bottom:0;"></label>';
echo '<input type="hidden" id="export_karyawan" value="' . htmlspecialchars($selected_karyawan) . '">';
echo '<a href="#" id="exportRingkas" class="btn btn-primary" style="margin-top:8px;">Export CSV Ringkas</a>';
echo '<a href="#" id="exportDetail" class="btn btn-primary">Export Penjualan Detail (CSV)</a>';
echo '</form>';
echo '</div>';
echo '</div>';
echo '</div>';
?>
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
		var karyawan = document.getElementById('export_karyawan').value;
		return 'tanggal_awal=' + encodeURIComponent(tglAwal) + '&tanggal_akhir=' + encodeURIComponent(tglAkhir) + '&karyawan=' + encodeURIComponent(karyawan);
	}
	document.getElementById('exportRingkas').onclick = function(e) {
		e.preventDefault();
		var params = getExportParams();
		window.open('export_laporan_penjualan.php?' + params, '_blank');
	};
	document.getElementById('exportDetail').onclick = function(e) {
		e.preventDefault();
		var params = getExportParams();
		window.open('export_laporan_penjualan_detail.php?' + params, '_blank');
	};
});
</script>
<style>
.dropdown-export .dropdown-content a {
  display: block;
  padding: 10px 18px;
  color: #1976d2;
  text-decoration: none;
  border-bottom: 1px solid #eee;
  background: #fff;
  transition: background 0.2s;
}
.dropdown-export .dropdown-content a:last-child {
  border-bottom: none;
}
.dropdown-export .dropdown-content a:hover {
  background: #e3f2fd;
}
</style>
<?php

// Form filter
echo '<form method="get" style="margin-bottom:24px;display:flex;gap:16px;align-items:center;flex-wrap:wrap;">';
echo '<label>Dari: <input type="date" name="tanggal_awal" value="' . htmlspecialchars($tanggal_awal) . '"></label>';
echo '<label>Sampai: <input type="date" name="tanggal_akhir" value="' . htmlspecialchars($tanggal_akhir) . '"></label>';
echo '<label>Karyawan: <select name="karyawan">';
echo '<option value="">Semua Karyawan</option>';
foreach ($karyawan_list as $k) {
	$sel = ($selected_karyawan == $k['id']) ? 'selected' : '';
	echo '<option value="' . $k['id'] . '" ' . $sel . '>' . htmlspecialchars($k['nama']) . '</option>';
}
echo '</select></label>';
echo '<button type="submit" class="btn btn-primary">Tampilkan</button>';
echo '</form>';

// Inisialisasi $where dan $params untuk query rekap dan ringkas
$where = 'WHERE DATE(p.tanggal) BETWEEN ? AND ?';
$params = [$tanggal_awal, $tanggal_akhir];
if (!empty($selected_karyawan)) {
    $where .= ' AND k.id = ?';
    $params[] = $selected_karyawan;
}


	// Ambil rekap total tunai dan transfer seluruh karyawan
	$sql_rekap = "SELECT SUM(p.tunai) as total_tunai, SUM(p.transfer) as total_transfer FROM penjualan p JOIN karyawan k ON p.id_karyawan = k.id $where";
	$stmt_rekap = $pdo->prepare($sql_rekap);
	$stmt_rekap->execute($params);
	$rekap = $stmt_rekap->fetch();
	echo "<div class='card laporan'><b>Total Tunai:</b>" . number_format($rekap['total_tunai'],0) . " &nbsp; <b>Total Transfer:</b> " . number_format($rekap['total_transfer'],0) . "</div>";

	// List per nomor nota
	$sql_nota = "SELECT p.id as id_penjualan, p.nomor_nota, k.nama as karyawan, p.tunai, p.transfer, p.tanggal FROM penjualan p JOIN karyawan k ON p.id_karyawan = k.id $where ORDER BY p.tanggal DESC, p.id DESC";
	$stmt_nota = $pdo->prepare($sql_nota);
	$stmt_nota->execute($params);
	echo "<div class='card laporan'><b>List Transaksi per Nomor Nota</b><br><table class='tabel-data' style='margin-top:8px;'><thead><tr><th>Nomor Nota</th><th>Nama Karyawan</th><th>Tunai</th><th>Transfer</th><th>Tanggal</th><th>Aksi</th></tr></thead><tbody>";
	while($row = $stmt_nota->fetch()) {
		echo "<tr>";
		echo "<td style='padding:8px 16px;'>".htmlspecialchars($row['nomor_nota'])."</td>";
		echo "<td style='padding:8px 16px;'>".htmlspecialchars($row['karyawan'])."</td>";
		echo "<td style='padding:8px 16px;text-align:right;'>" . number_format($row['tunai'],0) . "</td>";
		echo "<td style='padding:8px 16px;text-align:right;'>" . number_format($row['transfer'],0) . "</td>";
		echo "<td style='padding:8px 16px;'>".htmlspecialchars($row['tanggal'])."</td>";
		echo "<td style='padding:8px 10px;'>";
		echo "<a href='../edit_transaksi.php?id_penjualan=".$row['id_penjualan']."' class='btn btn-primary' style='padding:4px 10px;font-size:0.95em;'>Edit</a> ";
		echo "<a href='../pages/hapus_nota.php?id=".$row['id_penjualan']."' class='btn btn-negative' style='padding:4px 10px;font-size:0.95em;' onclick=\"return confirm('Hapus nota ini?')\">Hapus</a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</tbody></table></div>";

	// Ambil data ringkas per karyawan
	$sql = "SELECT k.id AS karyawan_id, k.nama, SUM(p.tunai) as total_tunai, SUM(p.transfer) as total_transfer, COUNT(p.id) as jumlah_transaksi FROM karyawan k JOIN penjualan p ON k.id = p.id_karyawan $where GROUP BY k.id, k.nama ORDER BY k.nama";
	$stmt = $pdo->prepare($sql);
	$stmt->execute($params);
	echo "<div class='card laporan'><table class='tabel-data'><thead><tr><th>Nama Karyawan</th><th>Jumlah Tunai</th><th>Jumlah Transfer</th><th>Jumlah Transaksi</th></tr></thead><tbody>";
	while($row = $stmt->fetch()) {
		echo "<tr>";
		echo "<td style='padding:8px 16px;'>".htmlspecialchars($row['nama'])."</td>";
		echo "<td style='padding:8px 16px;text-align:right;'>" .number_format($row['total_tunai'],0)."</td>";
		echo "<td style='padding:8px 16px;text-align:right;'>".number_format($row['total_transfer'],0)."</td>";
		echo "<td style='padding:8px 16px;text-align:center;'>".$row['jumlah_transaksi']."</td>";
		echo "</tr>";
	}
	echo "</tbody></table></div>";

$stmt_pengeluaran = $pdo->prepare("SELECT id, rincian, nominal, tanggal FROM pengeluaran WHERE tanggal BETWEEN ? AND ?");
$stmt_pengeluaran->execute([$tanggal_awal, $tanggal_akhir]);
$pengeluaran_list = $stmt_pengeluaran->fetchAll();
if (count($pengeluaran_list) > 0) {
	echo "<div class='pengeluaran-section card laporan' style='margin-top:30px;'>";
	echo "<h2>Rincian Pengeluaran</h2>";
	echo "<ul style='list-style:none;padding:0;'>";
	foreach ($pengeluaran_list as $pengeluaran) {
		$id_pengeluaran = $pengeluaran['id'];
		echo "<li style='margin-bottom:10px;'>";
		echo "<span style='color:#b00;font-weight:bold;'>-" . number_format($pengeluaran['nominal'], 0) . "</span> - <span>" . htmlspecialchars($pengeluaran['rincian']) . "</span> ";
		echo "<button class='btn btn-primary' onclick=\"document.getElementById('edit-pengeluaran-{$id_pengeluaran}').style.display='block';\" style='margin-left:10px;'>Edit</button>";
		echo "<button class='btn btn-negative' onclick=\"if(confirm('Hapus pengeluaran ini?')) window.location.href='../pages/hapus_pengeluaran.php?id={$id_pengeluaran}&tanggal_awal=" . urlencode($tanggal_awal) . "&tanggal_akhir=" . urlencode($tanggal_akhir) . "';\" style='margin-left:5px;'>Hapus</button>";
		echo "<div class='edit-form' id='edit-pengeluaran-{$id_pengeluaran}' style='display:none;margin-top:5px;'>";
		echo "<form method='post' action='../update_pengeluaran.php' style='display:inline-block;'>";
		echo "<input type='hidden' name='id_pengeluaran' value='{$id_pengeluaran}'>";
		echo "<input type='hidden' name='tanggal_awal' value='" . htmlspecialchars($tanggal_awal) . "'>";
		echo "<input type='hidden' name='tanggal_akhir' value='" . htmlspecialchars($tanggal_akhir) . "'>";
		echo "<label>Rincian: <input type='text' name='rincian' value='" . htmlspecialchars($pengeluaran['rincian']) . "' required></label> ";
		echo "<label>Nominal: <input type='number' name='nominal' value='{$pengeluaran['nominal']}' step='any' min='0' required></label> ";
		echo "<button type='submit' class='btn btn-primary'>Simpan</button>";
		echo "<button type='button' class='btn btn-negative' onclick=\"document.getElementById('edit-pengeluaran-{$id_pengeluaran}').style.display='none';\">Batal</button>";
		echo "</form>";
		echo "</div>";
		echo "</li>";
	}
	echo "</ul>";
	echo "</div>";
}
?>

<div class="nav-bottom">
	<div style="text-align:center; margin:32px 0;">
		<a href="index.php" class="btn btn-negative" style="font-size:1.2rem; min-width:180px;">Kembali ke Penjualan</a>
	</div>
</div>

<!-- Tombol navigasi vertikal -->
<button id="pageUp" class="page-nav">↑</button>
<button id="pageDown" class="page-nav">↓</button>

<script>
	// Tampilkan tanggal sesuai lokal perangkat
	(function() {
		var span = document.getElementById('tanggal-lokal');

		var input = document.querySelector('input[type="date"][name="tanggal"]');
		if (span && input && input.value) {
			var tgl = new Date(input.value);
			if (!isNaN(tgl.getTime())) {
				var formatted = tgl.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
				span.textContent = 'Tanggal: ' + formatted;
			}
		}
	})();

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
echo "</div>"; // close .container
<?php include '../includes/footer.php'; ?>
