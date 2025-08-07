<?php
// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rpl-lys";

// Create connection (procedural style)
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check connection
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, 'utf8');
?>