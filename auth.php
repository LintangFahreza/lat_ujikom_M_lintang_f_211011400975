<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek di tabel petugas
    $query = "SELECT * FROM petugas WHERE username='$username' AND password=MD5('$password')";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user;
        $_SESSION['level'] = 'petugas';
        header("Location: dashboard.php");
        exit;
    }

    // Cek di tabel manager
    $query2 = "SELECT * FROM manager WHERE username='$username' AND password=MD5('$password')";
    $result2 = $koneksi->query($query2);

    if ($result2->num_rows > 0) {
        $user = $result2->fetch_assoc();
        $_SESSION['user'] = $user;
        $_SESSION['level'] = 'manager';
        header("Location: dashboard.php");
        exit;
    }

    // Jika tidak ditemukan
    header("Location: login.php?error=Username atau Password salah");
}
