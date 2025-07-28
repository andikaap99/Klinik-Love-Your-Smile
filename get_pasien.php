<?php
// get_pasien.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include 'db_config.php';

$sql = "SELECT id, nama, no_telp, alamat FROM pasien";
$result = $conn->query($sql);

$pasien = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pasien[] = $row;
    }
}

echo json_encode($pasien);
$conn->close();
?>
