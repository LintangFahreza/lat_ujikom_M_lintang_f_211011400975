<?php
session_start();
include 'templates/header.php';
include 'templates/sidebar.php';
include 'db.php';

$session_id = session_id();
$id_sales = $_GET['id_sales'] ?? '';
$customer = null;
$sales_items = [];

// Ambil data sales
if ($id_sales) {
    // Ambil customer
    $result = mysqli_query($koneksi, "SELECT s.*, c.nama_customer, c.alamat FROM sales s JOIN customer c ON s.id_customer = c.id_customer WHERE s.id_sales = $id_sales");
    if ($result && mysqli_num_rows($result)) {
        $customer = mysqli_fetch_assoc($result);
    }

    // Ambil detail item dari sales (simulasi jika sudah ada tabel sales_detail, atau isi manual)
    $detail_result = mysqli_query($koneksi, "SELECT i.id_item, i.nama_item, i.harga, 1 AS quantity 
        FROM item i 
        JOIN sales_detail sd ON i.id_item = sd.id_item 
        WHERE sd.id_sales = $id_sales");

    while ($row = mysqli_fetch_assoc($detail_result)) {
        // Insert sementara ke transaction_temp jika belum ada
        $cek = mysqli_query($koneksi, "SELECT * FROM transaction_temp WHERE session_id='$session_id' AND id_item={$row['id_item']}");
        if (mysqli_num_rows($cek) == 0) {
            $quantity = $row['quantity'];
            $price = $row['harga'];
            $amount = $quantity * $price;
            mysqli_query($koneksi, "INSERT INTO transaction_temp (id_item, quantity, price, amount, session_id) VALUES 
                ({$row['id_item']}, $quantity, $price, $amount, '$session_id')");
        }
    }
}

// Hapus item
if (isset($_GET['hapus'])) {
    $id_item = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM transaction_temp WHERE session_id='$session_id' AND id_item=$id_item");
    header("Location: transaksi.php?id_sales=$id_sales");
}

// Simpan transaksi ke tabel utama
if (isset($_POST['simpan'])) {
    $temp = mysqli_query($koneksi, "SELECT * FROM transaction_temp WHERE session_id='$session_id'");
    while ($row = mysqli_fetch_assoc($temp)) {
        mysqli_query($koneksi, "INSERT INTO transaction (id_item, quantity, price, amount) VALUES 
            ({$row['id_item']}, {$row['quantity']}, {$row['price']}, {$row['amount']})");
    }
    mysqli_query($koneksi, "DELETE FROM transaction_temp WHERE session_id='$session_id'");
    echo "<script>alert('Transaksi berhasil disimpan!');window.location='transaksi.php';</script>";
}
?>

<div class="flex-grow-1" style="margin-left: 173px;">
    <?php include "templates/header_dash.php"; ?>
    <div class="content-wrapper ms-3 mt-3 me-3">
        <h4>Transaksi</h4>

        <!-- Form Pilih Sales -->
        <form method="get" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <label>Pilih No DO (Sales)</label>
                    <select name="id_sales" class="form-control" onchange="this.form.submit()" required>
                        <option value="">-- Pilih --</option>
                        <?php
                        $sales = mysqli_query($koneksi, "SELECT * FROM sales");
                        while ($row = mysqli_fetch_assoc($sales)) {
                            $selected = $row['id_sales'] == $id_sales ? 'selected' : '';
                            echo "<option value='{$row['id_sales']}' $selected>{$row['do_number']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </form>

        <?php if ($customer): ?>
            <!-- Info Customer -->
            <div class="card card-body mb-3">
                <p><strong>Nama Customer:</strong> <?= $customer['nama_customer'] ?></p>
                <p><strong>Alamat:</strong> <?= $customer['alamat'] ?></p>
            </div>

            <!-- Tabel Item -->
            <form method="post">
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
                        $temp = mysqli_query($koneksi, "SELECT t.*, i.nama_item FROM transaction_temp t 
                            JOIN item i ON t.id_item = i.id_item WHERE t.session_id='$session_id'");
                        $total = 0;
                        while ($row = mysqli_fetch_assoc($temp)) {
                            $total += $row['amount'];
                            echo "<tr>
                                <td>{$row['nama_item']}</td>
                                <td>{$row['quantity']}</td>
                                <td>Rp " . number_format($row['price'], 0, ',', '.') . "</td>
                                <td>Rp " . number_format($row['amount'], 0, ',', '.') . "</td>
                                <td>
                                    <a href='transaksi.php?id_sales=$id_sales&hapus={$row['id_item']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus?\")'>
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

                <button type="submit" name="simpan" class="btn btn-primary">Simpan Transaksi</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>