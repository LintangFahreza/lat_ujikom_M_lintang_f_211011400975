<?php
session_start();
include 'templates/header.php';
include 'templates/sidebar.php';
?>

<div class="flex-grow-1" style="margin-left: 173px;">
    <?php
    include "templates/header_dash.php";
    ?>

    <div class=" container mt-4 ms-5">
        <h3>Dashboard</h3>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Customer</h5>
                        <p class="card-text fs-4">
                            <?php
                            include 'db.php';
                            $count = $koneksi->query("SELECT COUNT(*) AS total FROM customer")->fetch_assoc();
                            echo $count['total'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <!-- Tambah kolom statistik lain jika perlu -->
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>