<?php
$hostname = "localhost";
$username = 'root';
$pass   = '';
$db     = 'lat_ujikom_lin';

$koneksi = mysqli_connect($hostname, $username, $pass, $db);

if (!$koneksi) {
    echo "gagal menghubungkan database";
} else {
    // echo "Database Terkoneksi";
}
