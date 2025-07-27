<?php session_start(); ?>
<?php include 'templates/header.php'; ?>

<div class="container mt-5" style="max-width: 500px;">
    <h3 class="text-center mb-4">Login Koperasi</h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo $_GET['error']; ?></div>
    <?php endif; ?>
    <form method="POST" action="auth.php">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100" type="submit" name="login">Login</button>
    </form>
</div>

<?php include 'templates/footer.php'; ?>