<?php
session_start();

// Hapus semua session
$_SESSION = [];
session_unset();
session_destroy();

// Redirect ke halaman login (misal login.php)
header('Location: ../login.php');
exit();
?>
