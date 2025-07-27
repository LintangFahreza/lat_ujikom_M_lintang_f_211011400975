<div class="sidebar bg-dark text-white p-3 position-fixed" style="min-height: 100vh;">
    <h5 class="text-center mb-4">KOPERASI</h5>
    <a href="dashboard.php" class="d-block text-white mb-2">
        <i class="bi bi-house"></i> Dashboard
    </a>

    <!-- Master Menu -->
    <a class="dropdown-toggle text-white d-block mb-2" data-bs-toggle="collapse" href="#masterMenu" role="button" aria-expanded="false" aria-controls="masterMenu">
        <i class="bi bi-folder2"></i> Master
    </a>
    <div class="collapse ps-3" id="masterMenu">
        <a href="user-view.php" class="d-block text-white mb-1"><i class="bi bi-person-circle"></i> User Acc</a>
        <a href="sales.php" class="d-block text-white mb-1"><i class="bi bi-receipt"></i> Sales</a>
        <a href="sales_detail.php" class="d-block text-white mb-1"><i class="bi bi-cart4"></i> DO</a>
        <a href="customer.php" class="d-block text-white mb-1"><i class="bi bi-people"></i> Customer</a>
        <a href="item.php" class="d-block text-white mb-1"><i class="bi bi-box"></i> Item</a>
    </div>

    <!-- Transaksi Menu -->
    <a class="dropdown-toggle text-white d-block mb-2" data-bs-toggle="collapse" href="#transaksiMenu" role="button" aria-expanded="false" aria-controls="transaksiMenu">
        <i class="bi bi-repeat"></i> Transaksi
    </a>
    <div class="collapse ps-3" id="transaksiMenu">
        <a href="transaksi.php" class="d-block text-white mb-1"><i class="bi bi-file-earmark-text"></i> Transaksi</a>

    </div>

    <!-- Laporan -->
    <a href="laporan_transaksi.php" class="d-block text-white mt-2">
        <i class="bi bi-bar-chart"></i> Laporan
    </a>
</div>