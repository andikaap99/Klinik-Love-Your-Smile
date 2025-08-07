<?php
include 'db_config.php';
header('Content-Type: application/json');
error_reporting(E_ALL);

$action = $_GET['action'] ?? 'read';

if ($action === 'read') {
    // Ambil data pasien
    $sql = "SELECT id, nama, no_telp, alamat FROM pasien";
    $result = $conn->query($sql);

    $pasien = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pasien[] = $row;
        }
    }
    echo json_encode($pasien);
}

else if ($action === 'create') {
    // Tambah data pasien
    $nama = $_POST['nama'] ?? null;
    $no_telp = $_POST['no_telp'] ?? null;
    $alamat = $_POST['alamat'] ?? null;

    if ($nama && $no_telp && $alamat) {
        $stmt = $conn->prepare("INSERT INTO pasien (nama, no_telp, alamat) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $no_telp, $alamat);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => $stmt->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap."]);
    }
}

$conn->close();
?>
