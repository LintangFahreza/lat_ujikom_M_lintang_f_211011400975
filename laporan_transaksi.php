<?php
session_start();
include 'templates/header.php';
include 'templates/sidebar.php';
include 'db.php';

// Ambil filter tanggal jika ada
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';

// Query dasar
$where = "";
if ($tgl_awal && $tgl_akhir) {
    $where = "WHERE s.tgl_sales BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query = mysqli_query($koneksi, "
    SELECT 
        s.id_sales, s.tgl_sales, s.do_number, c.nama_customer,
        COUNT(sd.id_item) AS total_item,
        SUM(sd.amount) AS total_harga
    FROM sales s
    LEFT JOIN customer c ON s.id_customer = c.id_customer
    LEFT JOIN sales_detail sd ON s.id_sales = sd.id_sales
    $where
    GROUP BY s.id_sales
    HAVING COUNT(sd.id_item) >= 1
    ORDER BY s.tgl_sales DESC
");


?>

<div class="flex-grow-1" style="margin-left: 173px;">
    <?php include "templates/header_dash.php"; ?>
    <div class="content-wrapper ms-3 mt-3 me-3">
        <section class="content-header">
            <h1>Laporan Transaksi</h1>
        </section>

        <section class="content mb-3">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <label>Tanggal Awal</label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Cari</button>
                    <a href="laporan_transaksi.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </section>

        <section class="content">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>DO Number</th>
                        <th>Jumlah Item</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $grand_total = 0;
                    while ($row = mysqli_fetch_assoc($query)) {
                        $total_harga = $row['total_harga'] ?? 0;
                        $grand_total += $total_harga;

                        echo "<tr>
                            <td>$no</td>
                            <td>{$row['tgl_sales']}</td>
                            <td>{$row['nama_customer']}</td>
                            <td>{$row['do_number']}</td>
                            <td>{$row['total_item']}</td>
                            <td>Rp. " . number_format($total_harga, 0, ',', '.') . "</td>
                        </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Grand Total</th>
                        <th>Rp. <?= number_format($grand_total, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </section>
    </div>
</div>

<?php include 'templates/footer.php'; ?>