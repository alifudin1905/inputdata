<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="data:,">
    <title>Aplikasi Penjualan</title>
</head>
<body>
    <div class="container">
        <div class="nav-top">
            <a href="../pages/index.php">Dashboard</a>
            <a href="../pages/jual.php">Penjualan</a>
            <a href="../pages/pengeluaran.php">Pengeluaran</a>
            <a href="../pages/barang_masuk.php">Barang Masuk</a>
            <a href="../pages/bayar_tagihan.php">Bayar Tagihan</a>
            <div class="dropdown-laporan" style="display:inline-block;position:relative;">
                <a href="../pages/laporan.php" class="dropdown-toggle">Laporan ▼</a>
                <div class="dropdown-menu-laporan" style="display:none;position:absolute;left:0;top:100%;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.08);min-width:220px;z-index:20;">
                    <a href="../pages/laporan.php">Laporan Penjualan</a>
                    <a href="../pages/laporan_jual_detail.php">Laporan Jual Detail</a>
                    <a href="../pages/laporan_pembelian.php">Laporan Pembelian</a>
                    <a href="../pages/laporan_barang_masuk.php">Laporan Barang Masuk</a>
                </div>
            </div>
            <a href="../pages/karyawan.php">Karyawan</a>
        </div>
        <style>
        .dropdown-laporan:hover .dropdown-menu-laporan {
            display: block !important;
        }
        .dropdown-menu-laporan a {
            display: block;
            padding: 10px 18px;
            color: #1976d2;
            text-decoration: none;
            border-bottom: 1px solid #eee;
            background: #fff;
            transition: background 0.2s;
        }
        .dropdown-menu-laporan a:last-child {
            border-bottom: none;
        }
        .dropdown-menu-laporan a:hover {
            background: #e3f2fd;
        }
        .dropdown-toggle {
            cursor: pointer;
        }
        </style>
