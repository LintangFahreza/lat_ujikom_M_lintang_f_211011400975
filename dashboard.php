<?php
session_start();

// Jika belum login, redirect ke halaman login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'templates/header.php';
include 'templates/sidebar.php';
include 'db.php';
?>

<div class="flex-grow-1" style="margin-left: 130px;">
    <?php
    include "templates/header_dash.php";
    ?>

    <div class=" container mt-4 ms-5 me-5 pe-5">
        <h3>Dashboard</h3>
        <div class="row">
            <!-- Total Customer -->
            <div class="col-md-3 mb-4">
                <div class="card shadow border-0 bg-primary text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Total Customer</h6>
                            <h4 class="fw-bold">
                                <?php
                                $count = $koneksi->query("SELECT COUNT(*) AS total FROM customer")->fetch_assoc();
                                echo $count['total'];
                                ?>
                            </h4>
                        </div>
                        <i class="fa fa-users fa-2x"></i>
                    </div>
                </div>
            </div>

            <!-- Total Sales -->
            <div class="col-md-3 mb-4">
                <div class="card shadow border-0 bg-success text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Total Sales</h6>
                            <h4 class="fw-bold">
                                <?php
                                $count = $koneksi->query("SELECT COUNT(*) AS total FROM sales")->fetch_assoc();
                                echo $count['total'];
                                ?>
                            </h4>
                        </div>
                        <i class="fa fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>

            <!-- Total Item -->
            <div class="col-md-3 mb-4">
                <div class="card shadow border-0 bg-warning text-dark">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Total Item</h6>
                            <h4 class="fw-bold">
                                <?php
                                $count = $koneksi->query("SELECT COUNT(*) AS total FROM item")->fetch_assoc();
                                echo $count['total'];
                                ?>
                            </h4>
                        </div>
                        <i class="fa fa-boxes fa-2x"></i>
                    </div>
                </div>
            </div>

            <!-- Total Transaksi -->
            <div class="col-md-3 mb-4">
                <div class="card shadow border-0 bg-danger text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase">Total Transaksi</h6>
                            <h4 class="fw-bold">
                                <?php
                                $count = $koneksi->query("SELECT COUNT(*) AS total FROM transaction")->fetch_assoc();
                                echo $count['total'];
                                ?>
                            </h4>
                        </div>
                        <i class="fa fa-file-invoice-dollar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tambah kolom statistik lain jika perlu -->
    </div>
</div>

<?php include 'templates/footer.php'; ?>