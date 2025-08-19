<?php
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/db.php';
session_start();

// Ambil parameter filter jika ada
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';

// Query untuk mengambil data pengeluaran
$query = "(
            SELECT 
                'pengeluaran' AS jenis,
                id,
                tanggal,
                rincian AS keterangan,
                nominal,
                'Pengeluaran' AS kategori
            FROM pengeluaran
            WHERE tanggal BETWEEN ? AND ?
          )
          UNION ALL
          (
            SELECT 
                'tagihan' AS jenis,
                id,
                tanggal,
                CONCAT('Tagihan ', nama_tagihan) AS keterangan,
                nominal,
                'Tagihan' AS kategori
            FROM bayar_tagihan
            WHERE tanggal BETWEEN ? AND ?
          )";

// Parameter untuk query
$params = [$start_date, $end_date, $start_date, $end_date];

if ($kategori !== 'all') {
    $query .= " HAVING kategori = ?";
    $params[] = $kategori;
}

$query .= " ORDER BY tanggal DESC";

// Eksekusi query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total pengeluaran
$total = 0;
foreach ($laporan as $item) {
    $total += $item['nominal'];
}

// Proses hapus data
if (isset($_GET['hapus'])) {
    $jenis = $_GET['jenis'];
    $id = $_GET['id'];
    
    if ($jenis === 'pengeluaran') {
        $stmt = $pdo->prepare("DELETE FROM pengeluaran WHERE id = ?");
    } else if ($jenis === 'tagihan') {
        $stmt = $pdo->prepare("DELETE FROM bayar_tagihan WHERE id = ?");
    }
    
    if ($stmt) {
        $stmt->execute([$id]);
        $_SESSION['success'] = "Data berhasil dihapus!";
        
        // Redirect dengan parameter filter
        $query_params = http_build_query([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'kategori' => $kategori
        ]);
        
        header("Location: laporan_pengeluaran.php?" . $query_params);
        exit;
    }
}
?>

<div class="container">
    <h1>Laporan Pengeluaran</h1>
    
    <!-- Form Filter -->
    <div class="card" style="margin-bottom: 20px;">
        <h2>Filter Laporan</h2>
        <form method="get" action="laporan_pengeluaran.php">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <label>
                    Tanggal Mulai:
                    <input type="date" name="start_date" value="<?= $start_date ?>">
                </label>
                <label>
                    Tanggal Akhir:
                    <input type="date" name="end_date" value="<?= $end_date ?>">
                </label>
                <label>
                    Kategori:
                    <select name="kategori">
                        <option value="all" <?= $kategori === 'all' ? 'selected' : '' ?>>Semua Kategori</option>
                        <option value="Pengeluaran" <?= $kategori === 'Pengeluaran' ? 'selected' : '' ?>>Pengeluaran Operasional</option>
                        <option value="Tagihan" <?= $kategori === 'Tagihan' ? 'selected' : '' ?>>Pembayaran Tagihan</option>
                    </select>
                </label>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Terapkan Filter</button>
        </form>
    </div>
    
    <!-- Tampilkan total pengeluaran -->
    <div class="card" style="margin-bottom: 20px;">
        <h2>Total Pengeluaran: Rp <?= number_format($total, 0) ?></h2>
    </div>
    
    <!-- Tabel Laporan -->
    <div class="card">
        <h2>Daftar Pengeluaran</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (!empty($laporan)): ?>
            <table class="tabel-data">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($laporan as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['tanggal']) ?></td>
                        <td><?= htmlspecialchars($item['keterangan']) ?></td>
                        <td style="text-align:right;">Rp <?= number_format($item['nominal'], 0) ?></td>
                        <td><?= htmlspecialchars($item['kategori']) ?></td>
                        <td style="white-space: nowrap;">
                            <?php if ($item['jenis'] === 'pengeluaran'): ?>
                                <a href="pengeluaran.php?edit_id=<?= $item['id'] ?>" class="btn btn-warning">Edit</a>
                            <?php else: ?>
                                <a href="bayar_tagihan.php?edit_id=<?= $item['id'] ?>" class="btn btn-warning">Edit</a>
                            <?php endif; ?>
                            
                            <a href="laporan_pengeluaran.php?hapus=1&jenis=<?= $item['jenis'] ?>&id=<?= $item['id'] ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&kategori=<?= $kategori ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:right;"><b>TOTAL</b></td>
                        <td style="text-align:right;"><b>Rp <?= number_format($total, 0) ?></b></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <p>Tidak ada data pengeluaran untuk ditampilkan.</p>
        <?php endif; ?>
    </div>
    
    <div class="card" style="margin: 20px 0;">
        <h2>Export Laporan</h2>
        <p>Pilih jenis laporan yang akan diexport:</p>
        
        <div class="export-options">
            <a href="export_pengeluaran.php?jenis=all" class="btn btn-primary">
                Export Semua Pengeluaran (CSV)
            </a>
            <a href="export_pengeluaran.php?jenis=pengeluaran" class="btn btn-primary">
                Export Pengeluaran Operasional (CSV)
            </a>
            <a href="export_pengeluaran.php?jenis=tagihan" class="btn btn-primary">
                Export Pembayaran Tagihan (CSV)
            </a>
        </div>
    </div>
</div>

<style>
.export-options {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    margin-top: 20px;
}

@media (min-width: 768px) {
    .export-options {
        grid-template-columns: 1fr 1fr;
    }
}

.btn {
    display: inline-block;
    padding: 6px 12px;
    text-align: center;
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.3s;
    margin: 2px;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-warning {
    background: #f39c12;
    color: white;
}

.btn-danger {
    background: #e74c3c;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}

.tabel-data {
    width: 100%;
    border-collapse: collapse;
}

.tabel-data th, .tabel-data td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.tabel-data th {
    background-color: #f2f2f2;
}

.tabel-data tfoot tr {
    background-color: #e0e0e0;
}
</style>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>