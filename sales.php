<?php
session_start();
include 'templates/header.php';
include 'templates/sidebar.php';
include 'db.php';

// Inisialisasi variabel
$id_sales = '';
$tgl_sales = '';
$id_customer = '';
$do_number = '';
$status = '';

// Ambil data saat edit
if (isset($_GET['id'])) {
    $id_sales = $_GET['id'];
    $result = mysqli_query($koneksi, "SELECT * FROM sales WHERE id_sales = $id_sales");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $tgl_sales = $row['tgl_sales'];
        $id_customer = $row['id_customer'];
        $do_number = $row['do_number'];
        $status = $row['status'];
    }
}

// Proses simpan atau update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl_sales = $_POST['tgl_sales'];
    $id_customer = $_POST['id_customer'];
    $do_number = $_POST['do_number'];
    $status = $_POST['status'];

    if (!empty($_POST['id_sales'])) {
        $id = $_POST['id_sales'];
        $query = "UPDATE sales SET tgl_sales='$tgl_sales', id_customer='$id_customer', do_number='$do_number', status='$status' WHERE id_sales=$id";
    } else {
        $query = "INSERT INTO sales (tgl_sales, id_customer, do_number, status) VALUES ('$tgl_sales', '$id_customer', '$do_number', '$status')";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>window.location='sales.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menyimpan data.</div>";
    }
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM sales WHERE id_sales = $id");
    echo "<script>window.location='sales.php';</script>";
}

// Query data sales
$query = mysqli_query($koneksi, "SELECT sales.*, customer.nama_customer 
                                 FROM sales 
                                 LEFT JOIN customer ON sales.id_customer = customer.id_customer 
                                 ORDER BY id_sales DESC");
?>

<div class="flex-grow-1" style="margin-left: 173px;">
    <?php include "templates/header_dash.php"; ?>
    <div class="content-wrapper ms-3 mt-3 me-3">
        <section class="content-header">
            <h1>Data Sales</h1>
        </section>

        <section class="content mb-4">
            <!-- Tombol untuk membuka form -->
            <button class="btn btn-success mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#formSales">
                <i class="fa fa-plus"></i> Add Data
            </button>

            <!-- Form tambah/edit -->
            <div class="collapse <?= isset($_GET['id']) ? 'show' : '' ?>" id="formSales">
                <div class="card card-body">
                    <form method="post" action="">
                        <input type="hidden" name="id_sales" value="<?= $id_sales ?>">

                        <div class="mb-2">
                            <label>Tanggal Sales</label>
                            <input type="date" name="tgl_sales" class="form-control" required value="<?= $tgl_sales ?>">
                        </div>
                        <div class="mb-2">
                            <label>Customer</label>
                            <select name="id_customer" class="form-select" required>
                                <option value="">-- Pilih Customer --</option>
                                <?php
                                $cust = mysqli_query($koneksi, "SELECT * FROM customer");
                                while ($c = mysqli_fetch_assoc($cust)) {
                                    $selected = $id_customer == $c['id_customer'] ? 'selected' : '';
                                    echo "<option value='{$c['id_customer']}' $selected>{$c['nama_customer']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>DO Number</label>
                            <input type="text" name="do_number" class="form-control" required value="<?= $do_number ?>">
                        </div>
                        <div class="mb-2">
                            <label>Status</label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="draft" <?= $status == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="selesai" <?= $status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= isset($_GET['id']) ? 'Update' : 'Simpan' ?></button>
                        <a href="sales.php" class="btn btn-secondary">Tutup</a>
                    </form>
                </div>
            </div>
        </section>

        <section class="content">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>DO Number</th>
                        <th>Status</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        echo "<tr>
                            <td>$no</td>
                            <td>{$row['tgl_sales']}</td>
                            <td>{$row['nama_customer']}</td>
                            <td>{$row['do_number']}</td>
                            <td>{$row['status']}</td>
                            <td>
                                <a href='sales.php?id={$row['id_sales']}' class='btn btn-warning btn-sm'><i class='fa fa-edit'></i></a>
                                <a href='sales_detail.php?id_sales={$row['id_sales']}' class='btn btn-info btn-sm'><i class='fa fa-list'></i></a>
                                <a href='sales.php?hapus={$row['id_sales']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin hapus?\")'><i class='fa fa-trash'></i></a>
                            </td>
                        </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
</div>

<?php include 'templates/footer.php'; ?>