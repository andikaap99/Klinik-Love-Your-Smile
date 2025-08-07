<?php
include 'db_config.php';
header('Content-Type: application/json');
error_reporting(E_ALL);

$action = $_GET['action'] ?? 'read';

if ($action === 'read') {
    $sql = "SELECT id, nama_pelayanan, harga, deskripsi FROM pelayanan";
    $result = $conn->query($sql);
    $pemeriksaan = [];

    while ($row = $result->fetch_assoc()) {
        $pemeriksaan[] = $row;
    }

    echo json_encode($pemeriksaan);
}

else if ($action === 'create') {
    $nama = $_POST['nama_pelayanan'] ?? null;
    $harga = $_POST['harga'] ?? null;
    $deskripsi = $_POST['deskripsi'] ?? null;

    if ($nama && $harga && $deskripsi) {
        $stmt = $conn->prepare("INSERT INTO pelayanan (nama_pelayanan, harga, deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $nama, $harga, $deskripsi);
        $success = $stmt->execute();

        echo json_encode(["success" => $success]);
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    }
}

else if ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM pelayanan WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();

        echo json_encode(["success" => $success]);
    } else {
        echo json_encode(["success" => false, "message" => "ID tidak ditemukan"]);
    }
}

$conn->close();
?>
