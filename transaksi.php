<?php
session_start();
include 'templates/header.php';
include 'templates/sidebar.php';
include 'db.php';

$session_id = session_id();
$id_sales = $_GET['id_sales'] ?? '';
$customer = null;

// Ambil data sales & customer
if ($id_sales) {
    $result = mysqli_query($koneksi, "SELECT s.*, c.nama_customer, c.alamat 
        FROM sales s 
        JOIN customer c ON s.id_customer = c.id_customer 
        WHERE s.id_sales = $id_sales");
    if ($result && mysqli_num_rows($result)) {
        $customer = mysqli_fetch_assoc($result);
    }

    // Cek apakah sudah pernah load item dari sales_detail ke transaction_temp
    $check_exist = mysqli_query($koneksi, "SELECT 1 FROM transaction_temp WHERE session_id='$session_id' AND remark='$id_sales'");
    if (mysqli_num_rows($check_exist) == 0) {
        $detail_result = mysqli_query($koneksi, "SELECT sd.id_item, i.nama_item, i.harga_jual, sd.quantity 
            FROM sales_detail sd 
            JOIN item i ON i.id_item = sd.id_item 
            WHERE sd.id_sales = $id_sales");

        while ($row = mysqli_fetch_assoc($detail_result)) {
            $quantity = $row['quantity'];
            $price = $row['harga_jual'];
            $amount = $quantity * $price;

            mysqli_query($koneksi, "INSERT INTO transaction_temp (id_item, quantity, price, amount, session_id, remark) 
                VALUES ({$row['id_item']}, $quantity, $price, $amount, '$session_id', '$id_sales')");
        }
    }
}

// Tambah item manual
if (isset($_POST['tambah_item'])) {
    $id_item = $_POST['id_item'];
    $quantity = $_POST['quantity'];

    $q = mysqli_query($koneksi, "SELECT harga_jual FROM item WHERE id_item = $id_item");
    $row = mysqli_fetch_assoc($q);
    $price = $row['harga_jual'];
    $amount = $price * $quantity;

    $check = mysqli_query($koneksi, "SELECT * FROM transaction_temp WHERE session_id='$session_id' AND remark='$id_sales' AND id_item=$id_item");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($koneksi, "INSERT INTO transaction_temp (id_item, quantity, price, amount, session_id, remark)
            VALUES ($id_item, $quantity, $price, $amount, '$session_id', '$id_sales')");
    } else {
        mysqli_query($koneksi, "UPDATE transaction_temp 
            SET quantity = quantity + $quantity, amount = (quantity + $quantity) * $price 
            WHERE session_id='$session_id' AND remark='$id_sales' AND id_item=$id_item");
    }

    header("Location: transaksi.php?id_sales=$id_sales");
    exit;
}

// Hapus item
if (isset($_GET['hapus'])) {
    $id_item = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM transaction_temp WHERE session_id='$session_id' AND id_item=$id_item AND remark='$id_sales'");
    header("Location: transaksi.php?id_sales=$id_sales");
    exit;
}

// Simpan transaksi
if (isset($_POST['simpan'])) {
    $temp = mysqli_query($koneksi, "SELECT * FROM transaction_temp WHERE session_id='$session_id' AND remark='$id_sales'");
    while ($row = mysqli_fetch_assoc($temp)) {
        mysqli_query($koneksi, "INSERT INTO transaction (id_item, quantity, price, amount) 
            VALUES ({$row['id_item']}, {$row['quantity']}, {$row['price']}, {$row['amount']})");
    }
    mysqli_query($koneksi, "DELETE FROM transaction_temp WHERE session_id='$session_id' AND remark='$id_sales'");
    echo "<script>alert('Transaksi berhasil disimpan!');window.location='transaksi.php';</script>";
    exit;
}
?>

<div class="flex-grow-1" style="margin-left: 173px;">
    <?php include "templates/header_dash.php"; ?>
    <div class="content-wrapper ms-3 mt-3 me-3">
        <h4>Transaksi</h4>

        <!-- Pilih Sales -->
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

            <!-- Form Tambah Item Manual -->
            <div class="card card-body mb-3">
                <h5>Tambah Item Manual</h5>
                <form method="post">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Pilih Item</label>
                            <select name="id_item" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <?php
                                $item_q = mysqli_query($koneksi, "SELECT * FROM item");
                                while ($item = mysqli_fetch_assoc($item_q)) {
                                    echo "<option value='{$item['id_item']}'>{$item['nama_item']} - Rp " . number_format($item['harga_jual'], 0, ',', '.') . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Qty</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-2 mt-4">
                            <button type="submit" name="tambah_item" class="btn btn-success">Tambah</button>
                        </div>
                    </div>
                </form>
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
                            JOIN item i ON t.id_item = i.id_item 
                            WHERE t.session_id='$session_id' AND t.remark='$id_sales'");
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