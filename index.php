<?php
// session_start();
// header("Location: dashboard.php");
// exit(); // Tambahkan exit agar proses berhenti setelah redirec
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
