<?php
session_start();
include 'templates/header.php';
include 'templates/sidebar.php';
include 'db.php';

// Inisialisasi variabel
$id_customer = '';
$nama_customer = '';
$alamat = '';
$telp = '';
$fax = '';
$email = '';

// Ambil data saat edit
if (isset($_GET['id'])) {
    $id_customer = $_GET['id'];
    $result = mysqli_query($koneksi, "SELECT * FROM customer WHERE id_customer = $id_customer");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $nama_customer = $row['nama_customer'];
        $alamat = $row['alamat'];
        $telp = $row['telp'];
        $fax = $row['fax'];
        $email = $row['email'];
    }
}

// Proses simpan atau update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_customer = $_POST['nama_customer'];
    $alamat = $_POST['alamat'];
    $telp = $_POST['telp'];
    $fax = $_POST['fax'];
    $email = $_POST['email'];

    if (!empty($_POST['id_customer'])) {
        $id = $_POST['id_customer'];
        $query = "UPDATE customer SET nama_customer='$nama_customer', alamat='$alamat', telp='$telp', fax='$fax', email='$email' WHERE id_customer=$id";
    } else {
        $query = "INSERT INTO customer (nama_customer, alamat, telp, fax, email) VALUES ('$nama_customer', '$alamat', '$telp', '$fax', '$email')";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>window.location='customer.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menyimpan data.</div>";
    }
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM customer WHERE id_customer = $id");
    echo "<script>window.location='customer.php';</script>";
}

$query = mysqli_query($koneksi, "SELECT * FROM customer");
?>

<div class="flex-grow-1" style="margin-left: 173px;">
    <?php include "templates/header_dash.php"; ?>
    <div class="content-wrapper ms-3 mt-3 me-3">
        <section class="content-header">
            <h1>Data Customer</h1>
        </section>

        <section class="content mb-4">
            <!-- Tombol untuk membuka form -->
            <button class="btn btn-success mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#formCustomer">
                <i class="fa fa-plus"></i> Add Data
            </button>

            <!-- Form tambah/edit -->
            <div class="collapse <?= isset($_GET['id']) ? 'show' : '' ?>" id="formCustomer">
                <div class="card card-body">
                    <form method="post" action="">
                        <input type="hidden" name="id_customer" value="<?= $id_customer ?>">

                        <div class="mb-2">
                            <label>Nama Customer</label>
                            <input type="text" name="nama_customer" class="form-control" required value="<?= $nama_customer ?>">
                        </div>
                        <div class="mb-2">
                            <label>Alamat</label>
                            <input type="text" name="alamat" class="form-control" value="<?= $alamat ?>">
                        </div>
                        <div class="mb-2">
                            <label>Telp</label>
                            <input type="text" name="telp" class="form-control" value="<?= $telp ?>">
                        </div>
                        <div class="mb-2">
                            <label>Fax</label>
                            <input type="text" name="fax" class="form-control" value="<?= $fax ?>">
                        </div>
                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= $email ?>">
                        </div>
                        <button type="submit" class="btn btn-primary"><?= isset($_GET['id']) ? 'Update' : 'Simpan' ?></button>
                        <a href="customer.php" class="btn btn-secondary">Tutup</a>
                    </form>
                </div>
            </div>
        </section>

        <section class="content">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Customer</th>
                        <th>Nama Customer</th>
                        <th>Alamat</th>
                        <th>Telp</th>
                        <th>Fax</th>
                        <th>Email</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        echo "<tr>
                            <td>$no</td>
                            <td>CU" . str_pad($row['id_customer'], 4, '0', STR_PAD_LEFT) . "</td>
                            <td>{$row['nama_customer']}</td>
                            <td>{$row['alamat']}</td>
                            <td>{$row['telp']}</td>
                            <td>{$row['fax']}</td>
                            <td>{$row['email']}</td>
                            <td>
                                <a href='customer.php?id={$row['id_customer']}' class='btn btn-warning btn-sm'><i class='fa fa-edit'></i></a>
                                <a href='customer.php?hapus={$row['id_customer']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin hapus?\")'><i class='fa fa-trash'></i></a>
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