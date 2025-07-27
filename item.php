<?php
session_start();
include 'templates/header.php';
include 'templates/sidebar.php';
include 'db.php';

$edit = false;
$id_item = "";
$nama_item = "";
$uom = "";
$harga_beli = "";
$harga_jual = "";

// Handle Save
if (isset($_POST['simpan'])) {
    $id = $_POST['id_item'];
    $nama = $_POST['nama_item'];
    $uom = $_POST['uom'];
    $beli = $_POST['harga_beli'];
    $jual = $_POST['harga_jual'];

    if ($_POST['edit'] == "true") {
        mysqli_query($koneksi, "UPDATE item SET nama_item='$nama', uom='$uom', harga_beli='$beli', harga_jual='$jual' WHERE id_item='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO item (nama_item, uom, harga_beli, harga_jual) VALUES ('$nama', '$uom', '$beli', '$jual')");
    }
    header("Location: item.php");
    exit;
}

// Handle Edit
if (isset($_GET['edit'])) {
    $edit = true;
    $id_item = $_GET['edit'];
    $q = mysqli_query($koneksi, "SELECT * FROM item WHERE id_item='$id_item'");
    $d = mysqli_fetch_assoc($q);
    $nama_item = $d['nama_item'];
    $uom = $d['uom'];
    $harga_beli = $d['harga_beli'];
    $harga_jual = $d['harga_jual'];
}

// Handle Delete
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM item WHERE id_item='$id'");
    header("Location: item.php");
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM item");
?>

<div class="flex-grow-1" style="margin-left: 173px;">
    <?php include "templates/header_dash.php"; ?>
    <div class="content-wrapper ms-3 mt-3 me-3 overflow-auto" style="height: calc(100vh - 56px);">
        <section class="content-header mb-3">
            <h1>Data Item</h1>
        </section>

        <section class="content">
            <a href="#" class="btn btn-success mb-3" onclick="toggleForm();"><i class="fa fa-plus"></i> Add Data</a>

            <!-- Form Input -->
            <div id="formItem" class="card p-3 mb-3" style="display: <?= $edit ? 'block' : 'none' ?>;">
                <form method="post">
                    <input type="hidden" name="edit" value="<?= $edit ? 'true' : 'false' ?>">
                    <input type="hidden" name="id_item" value="<?= $id_item ?>">
                    <div class="mb-2">
                        <label>Nama Item</label>
                        <input type="text" name="nama_item" class="form-control" required value="<?= $nama_item ?>">
                    </div>
                    <div class="mb-2">
                        <label>UOM</label>
                        <input type="text" name="uom" class="form-control" required value="<?= $uom ?>">
                    </div>
                    <div class="mb-2">
                        <label>Harga Beli</label>
                        <input type="number" name="harga_beli" class="form-control" required value="<?= $harga_beli ?>">
                    </div>
                    <div class="mb-2">
                        <label>Harga Jual</label>
                        <input type="number" name="harga_jual" class="form-control" required value="<?= $harga_jual ?>">
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="item.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>

            <!-- Tabel -->
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>UOM</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        echo "<tr>
                            <td>$no</td>
                            <td>{$row['nama_item']}</td>
                            <td>{$row['uom']}</td>
                            <td>{$row['harga_beli']}</td>
                            <td>{$row['harga_jual']}</td>
                            <td>
                                <a href='item.php?edit={$row['id_item']}' class='btn btn-warning btn-sm'><i class='fa fa-edit'></i></a>
                                <a href='item.php?hapus={$row['id_item']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin hapus?\")'><i class='fa fa-trash'></i></a>
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

<script>
    function toggleForm() {
        const form = document.getElementById("formItem");
        form.style.display = form.style.display === "none" ? "block" : "none";
    }
</script>

<?php include 'templates/footer.php'; ?>