<?php
// Reset session jika diminta
if (isset($_GET['do_reset'])) {
    unset($_SESSION['cart_bm'], $_SESSION['tanggal_masuk_bm'], $_SESSION['tanggal_faktur_bm'], $_SESSION['suplayer_bm'], $_SESSION['nomor_faktur_bm']);
    header('Location: barang_masuk.php');
    exit;
}
include '../includes/db.php';
include '../includes/header.php';
session_start();

if (!isset($_SESSION['cart_bm'])) {
    $_SESSION['cart_bm'] = [];
}
// Step 1: Input header (tanggal, nomor faktur, suplayer)
if (isset($_POST['set_header_bm'])) {
    $_SESSION['tanggal_masuk_bm'] = $_POST['tanggal_masuk'] ?? date('Y-m-d');
    $_SESSION['tanggal_faktur_bm'] = !empty($_POST['tanggal_faktur']) ? $_POST['tanggal_faktur'] : null;
    $_SESSION['suplayer_bm'] = $_POST['suplayer'] ?? '';
    $_SESSION['nomor_faktur_bm'] = $_POST['nomor_faktur'] ?? '';
    header('Location: barang_masuk.php');
    exit;
}

// Step 2: Tambah barang ke cart (PERBAIKAN DI SINI)
if (isset($_POST['add_item_bm'])) {
    if (!empty($_POST['nama_barang']) && $_POST['qty'] !== '' && $_POST['harga_modal'] !== '') {
        // Bersihkan harga dari format ribuan
        $harga_clean = str_replace('.', '', $_POST['harga_modal']);
        $harga = (float)$harga_clean;
        
        $item = [
            'nama_barang' => $_POST['nama_barang'],
            'qty' => (int)$_POST['qty'],
            'harga_modal' => $harga,
            'subtotal' => (int)$_POST['qty'] * $harga
        ];
        $_SESSION['cart_bm'][] = $item;
    }
    header('Location: barang_masuk.php');
    exit;
}

// Hapus item dari cart
if (isset($_GET['remove_bm'])) {
    if (isset($_SESSION['cart_bm'][$_GET['remove_bm']])) {
        unset($_SESSION['cart_bm'][$_GET['remove_bm']]);
        $_SESSION['cart_bm'] = array_values($_SESSION['cart_bm']);
    }
    header('Location: barang_masuk.php');
    exit;
}

// Step 3: Simpan barang masuk (PERBAIKAN DI SINI)
if (isset($_POST['submit_barang_masuk'])) {
    $tanggal_masuk = $_SESSION['tanggal_masuk_bm'] ?? date('Y-m-d');
    $tanggal_faktur = $_SESSION['tanggal_faktur_bm'] ?? null;
    $suplayer = $_SESSION['suplayer_bm'] ?? '';
    $nomor_faktur = $_SESSION['nomor_faktur_bm'] ?? '';
    $pembayaran = $_POST['pembayaran'] ?? '';
    $lama_tempo = isset($_POST['lama_tempo']) && $_POST['lama_tempo'] !== '' ? (int)$_POST['lama_tempo'] : null;
    
    // Bersihkan jumlah seluruh faktur dari format ribuan
    $jumlah_seluruh_faktur = isset($_POST['jumlah_seluruh_faktur']) ? str_replace('.', '', $_POST['jumlah_seluruh_faktur']) : 0;
    $jumlah_seluruh_faktur = (float)$jumlah_seluruh_faktur;
    
    $stmt = $pdo->prepare('INSERT INTO barang_masuk (tanggal_masuk, tanggal_faktur, suplayer, nomor_faktur, pembayaran, lama_tempo, jumlah_seluruh_faktur) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$tanggal_masuk, $tanggal_faktur, $suplayer, $nomor_faktur, $pembayaran, $lama_tempo, $jumlah_seluruh_faktur]);
    $id_bm = $pdo->lastInsertId();
    
    foreach ($_SESSION['cart_bm'] as $item) {
        $stmt = $pdo->prepare('INSERT INTO detail_barang_masuk (id_barang_masuk, nama_barang, qty, harga_modal) VALUES (?, ?, ?, ?)');
        $stmt->execute([$id_bm, $item['nama_barang'], $item['qty'], $item['harga_modal']]);
    }
    
    $_SESSION['cart_bm'] = [];
    unset($_SESSION['tanggal_masuk_bm'], $_SESSION['tanggal_faktur_bm'], $_SESSION['suplayer_bm'], $_SESSION['nomor_faktur_bm']);
    echo "<div class='alert alert-success'>Barang masuk berhasil disimpan!</div>";
}

// Form input barang masuk
?>

<div class="container">
    <header style="position:sticky;top:0;z-index:100;background:#fff;padding-top:12px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <h1>Input Barang Masuk</h1>
    </header>

    <?php if (!isset($_SESSION['tanggal_masuk_bm'])): ?>
    <div class="card" style="max-width:500px;margin:32px 0 32px 0;">
        <form method="post">
            <label>Tanggal Masuk:
                <input type="date" name="tanggal_masuk" value="<?= date('Y-m-d') ?>" required>
            </label>
            <label>Tanggal Faktur:
                <input type="date" name="tanggal_faktur">
            </label>
            <label>Nama Suplayer:
                <input type="text" name="suplayer" required>
            </label>
            <label>Nomor Faktur:
                <input type="text" name="nomor_faktur" required>
            </label>
            <button type="submit" name="set_header_bm" class="btn btn-primary">Lanjutkan</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['tanggal_masuk_bm'])): ?>
    <div class="card" style="max-width:600px;margin:32px 0 18px 0;">
        <div style="margin-bottom:10px;">
            <b>Tanggal Masuk:</b> <?= htmlspecialchars($_SESSION['tanggal_masuk_bm']) ?> |
            <b>Tanggal Faktur:</b> <?= htmlspecialchars($_SESSION['tanggal_faktur_bm'] ?? '-') ?> |
            <b>Suplayer:</b> <?= htmlspecialchars($_SESSION['suplayer_bm']) ?> |
            <b>Nomor Faktur:</b> <?= htmlspecialchars($_SESSION['nomor_faktur_bm']) ?>
        </div>
        <!-- Step 2: Form tambah barang -->
        <form method="post" id="form-barang-masuk">
            <label>Nama Barang:
                <input type="text" name="nama_barang" id="input-nama-barang" required>
            </label>
            <label>Jumlah:
                <input type="number" name="qty" min="1" required>
            </label>
            <label>Harga Modal:
                <input type="text" name="harga_modal" min="0" required oninput="this.value=formatRupiah(this.value)">
            </label>
            <button type="submit" name="add_item_bm" class="btn btn-primary">Tambah ke Cart</button>
        </form>
    </div>

    <!-- Cart Barang Masuk -->
    <div class="cart-container card" style="max-width:700px;margin:0 0 18px 0;">
        <h2>Keranjang Barang Masuk</h2>
        <?php if (!empty($_SESSION['cart_bm'])): ?>
            <table class="tabel-data">
                <thead>
                    <tr><th>Nama Barang</th><th>Qty</th><th>Harga Modal</th><th>Subtotal</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($_SESSION['cart_bm'] as $i => $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                    <td><?= $item['qty'] ?></td>
                    <!-- PERBAIKAN FORMAT ANGKA DI SINI -->
                    <td><?= number_format($item['harga_modal'], 0, ',', '.') ?></td>
                    <td><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    <td><a href="barang_masuk.php?remove_bm=<?= $i ?>" class="btn btn-negative" onclick="return confirm('Hapus item ini?')">Hapus</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align:left;font-size:0.98em;">
                            <b>Jumlah Nota:</b> <?= count($_SESSION['cart_bm']) ?> |
                            <b>Total Qty:</b> <?= array_sum(array_column($_SESSION['cart_bm'], 'qty')) ?> |
                            <b>Total Barang:</b> <?= count(array_unique(array_column($_SESSION['cart_bm'], 'nama_barang'))) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php $total_harga = array_sum(array_column($_SESSION['cart_bm'], 'subtotal')); ?>
            <div class="success" style="margin-top:10px;font-size:1.12em;max-width:340px;">
              <!-- PERBAIKAN FORMAT ANGKA DI SINI -->
              <b>Total Harga Barang Masuk:</b> <?= number_format($total_harga, 0, ',', '.') ?>
            </div>
        <?php else: ?>
            <p>Keranjang kosong</p>
        <?php endif; ?>
    </div>

    <!-- Step 3: Form pembayaran/faktur -->
    <form method="post" id="form-pembayaran">
        <div class="payment-section card" style="max-width:500px;margin:0 0 32px 0;">
            <h2>Detail Faktur</h2>
            <label>Jumlah Seluruh Faktur:
                <input type="text" name="jumlah_seluruh_faktur" min="0" required oninput="this.value=formatRupiah(this.value)">
            </label>
            <label>Pembayaran:
                <select name="pembayaran" id="pembayaran" required onchange="document.getElementById('lama_tempo_wrap').style.display = this.value === 'tempo' ? 'block' : 'none';">
                    <option value="">Pilih</option>
                    <option value="cash">Cash</option>
                    <option value="tempo">Tempo</option>
                    <option value="transfer">Transfer</option>
                </select>
            </label>
            <div id="lama_tempo_wrap" style="display:none;">
                <label>Lama Jatuh Tempo (hari):
                    <input type="number" name="lama_tempo" min="0">
                </label>
            </div>
            <button type="submit" name="submit_barang_masuk" class="btn btn-primary">Simpan Barang Masuk</button>
            <a href="barang_masuk.php?reset=1" class="btn btn-negative" style="margin-left:10px;">Reset</a>
        </div>
    </form>
    <?php endif; ?>
</div>
<script>
// PERBAIKAN FUNGSI FORMAT RUPIAH DI SINI
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

// Tampilkan input lama_tempo jika pembayaran = tempo
document.addEventListener('DOMContentLoaded', function() {
    var pembayaran = document.getElementById('pembayaran');
    var lamaTempoWrap = document.getElementById('lama_tempo_wrap');
    if (pembayaran && pembayaran.value === 'tempo') {
        lamaTempoWrap.style.display = 'block';
    }
    // Fokus otomatis ke input nama barang jika form barang masuk tampil
    var inputNamaBarang = document.getElementById('input-nama-barang');
    if (inputNamaBarang) {
        inputNamaBarang.focus();
    }
    // Shortcut: tekan F2 atau Ctrl+T untuk fokus ke input tanggal masuk
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2' || (e.ctrlKey && (e.key === 't' || e.key === 'T'))) {
            var tglMasuk = document.querySelector('input[name="tanggal_masuk"]');
            if (tglMasuk) {
                e.preventDefault();
                tglMasuk.focus();
            }
        }
    });
});
// Reset session jika klik reset
if (window.location.search.indexOf('reset=1') !== -1) {
    fetch('barang_masuk.php?do_reset=1').then(() => window.location.href = 'barang_masuk.php');
}
</script>
<?php include '../includes/footer.php'; ?>