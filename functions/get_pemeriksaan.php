<?php
include 'db_config.php';
header('Content-Type: application/json');
error_reporting(E_ALL);

$action = $_GET['action'] ?? 'read';

if ($action === 'read') {
    $sql = "SELECT pemeriksaan.id, pasien.nama, pemeriksaan.keluhan, pemeriksaan.status, pemeriksaan.no_antrian 
            FROM pemeriksaan 
            JOIN pasien ON pemeriksaan.id_pasien = pasien.id
            WHERE pemeriksaan.status = 'Dalam Antrian'";

    $result = $conn->query($sql);
    $antrian = [];

    while ($row = $result->fetch_assoc()) {
        $antrian[] = $row;
    }

    echo json_encode($antrian);
}

else if ($action === 'update') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $conn->prepare("UPDATE pemeriksaan SET status = 'Sedang Diperiksa' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        echo json_encode(["success" => $success]);
    } else {
        echo json_encode(["success" => false, "message" => "ID tidak ditemukan"]);
    }
}

$conn->close();
?>
