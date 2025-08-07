<?php
include 'db_config.php';
header('Content-Type: application/json');
error_reporting(E_ALL);

$action = $_GET['action'] ?? 'read';

if ($action === 'get_pasien') {
    // Load Data pasien untuk Dropdown
    $sql = "SELECT id, nama FROM pasien";
    $result = $conn->query($sql);

    $pasien = [];
    while ($row = $result->fetch_assoc()) {
        $pasien[] = $row;
    }

    echo json_encode($pasien);
}

else if ($action === 'create') {
    $id_pasien = $_POST['id_pasien'] ?? null;
    $keluhan = $_POST['keluhan'] ?? null;
    $no_antrian = $_POST['no_antrian'] ?? null;

    if ($id_pasien && $keluhan && $no_antrian) {
        $stmt = $conn->prepare("INSERT INTO pemeriksaan (id_pasien, keluhan, no_antrian) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_pasien, $keluhan, $no_antrian);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menyimpan data: " . $stmt->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    }
}
