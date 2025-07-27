<?php
session_start();
include 'templates/header.php';
include 'templates/sidebar.php';
include 'db.php';

// Validasi parameter id_sales
if (!isset($_GET['id_sales']) || empty($_GET['id_sales'])) {
    echo "<script>alert('Sales tidak ditemukan.'); window.location='sales.php';</script>";
    exit;
}

$id_sales = (int)$_GET['id_sales'];

// Ambil info sales
$sales = mysqli_query($koneksi, "SELECT s.*, c.nama_customer FROM sales s JOIN customer c ON s.id_customer = c.id_customer WHERE s.id_sales = $id_sales");
$sales_data = mysqli_fetch_assoc($sales);

// Tambah item ke sales_detail
if (isset($_POST['tambah'])) {
    $id_item = $_POST['id_item'];
    $quantity = $_POST['quantity'];

    // Ambil harga dari tabel item
    $item = mysqli_query($koneksi, "SELECT harga_jual FROM item WHERE id_item = $id_item");
    $item_data = mysqli_fetch_assoc($item);
    $price = $item_data['harga_jual'];
    $amount = $quantity * $price;

    mysqli_query($koneksi, "INSERT INTO sales_detail (id_sales, id_item, quantity, price, amount) 
        VALUES ($id_sales, $id_item, $quantity, $price, $amount)");

    header("Location: sales_detail.php?id_sales=$id_sales");
    exit;
}

// Hapus item dari sales_detail
if (isset($_GET['hapus'])) {
    $id_item = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM sales_detail WHERE id_sales=$id_sales AND id_item=$id_item");
    header("Location: sales_detail.php?id_sales=$id_sales");
    exit;
}
?>

<div class="flex-grow-1" style="margin-left: 173px;">
    <?php include "templates/header_dash.php"; ?>
    <div class="content-wrapper ms-3 mt-3 me-3">
        <h4>Detail Sales - DO Number: <?= $sales_data['do_number'] ?> (<?= $sales_data['nama_customer'] ?>)</h4>

        <!-- Form tambah item -->
        <form method="post" class="row g-2 mb-3">
            <div class="col-md-6">
                <label>Item</label>
                <select name="id_item" class="form-select" required>
                    <option value="">-- Pilih Item --</option>
                    <?php
                    $items = mysqli_query($koneksi, "SELECT * FROM item");
                    while ($i = mysqli_fetch_assoc($items)) {
                        echo "<option value='{$i['id_item']}'>{$i['nama_item']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Qty</label>
                <input type="number" name="quantity" class="form-control" required>
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
            </div>
        </form>

        <!-- Tabel detail item -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $detail = mysqli_query($koneksi, "SELECT sd.*, i.nama_item FROM sales_detail sd 
                    JOIN item i ON i.id_item = sd.id_item 
                    WHERE sd.id_sales = $id_sales");

                $total = 0;
                while ($row = mysqli_fetch_assoc($detail)) {
                    $total += $row['amount'];
                    echo "<tr>
                        <td>{$row['nama_item']}</td>
                        <td>{$row['quantity']}</td>
                        <td>Rp " . number_format($row['price'], 0, ',', '.') . "</td>
                        <td>Rp " . number_format($row['amount'], 0, ',', '.') . "</td>
                        <td>
                            <a href='sales_detail.php?id_sales=$id_sales&hapus={$row['id_item']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Hapus item ini?\")'>
                                <i class='fa fa-trash'></i>
                            </a>
                        </td>
                    </tr>";
                }
                ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td colspan="2"><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
                </tr>
            </tbody>
        </table>

        <a href="sales.php" class="btn btn-secondary">Kembali ke Sales</a>
        <a href="transaksi.php?id_sales=<?= $id_sales ?>" class="btn btn-success">Lanjut ke Transaksi</a>
    </div>
</div>

<?php include 'templates/footer.php'; ?>