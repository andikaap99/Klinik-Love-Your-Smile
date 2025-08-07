<?php
include 'db_config.php';
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to generate random string
function generateRandomString($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

$action = $_GET['action'] ?? 'none';

if ($action == 'none'){
    exit();
} 

else if ($action == 'simpan') {
    $kode_resep = 'RSP-' . generateRandomString(8);
    $id_dokter = $_POST['id_dokter'] ?? null;
    $id_apoteker = null;
    $resep = $_POST['resep'] ?? null;
    $status = 'proses';
    
    if ($id_dokter) {
        // Corrected - now has 5 placeholders for 5 columns
        $stmt = $conn->prepare("INSERT INTO resep_dokter (kode_resep, id_dokter, id_apoteker, resep, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiss", $kode_resep, $id_dokter, $id_apoteker, $resep, $status);
        $success = $stmt->execute();
        
        echo json_encode([
            "success" => $success,
            "kode_resep" => $kode_resep // Return the generated code
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "ID Dokter tidak valid"
        ]);
    }
}

else if ($action == 'get_resep') {
    $stmt = $conn->prepare("
        SELECT r.id, r.kode_resep, r.id_dokter, r.resep, r.status 
        FROM resep_dokter r
        WHERE r.kode_resep NOT IN (
            SELECT DISTINCT kode_resep 
            FROM rekam_medis 
            WHERE kode_resep IS NOT NULL
        )
        ORDER BY r.kode_resep
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $resepList = [];
    while ($row = $result->fetch_assoc()) {
        $resepList[] = $row;
    }
    echo json_encode($resepList);
    exit();
}

else if ($action == 'simpan') {
    $id_dokter = $_POST['id_dokter'] ?? null;
    $id_apoteker = null;
    $resep = $_POST['resep'] ?? null;
    $status = 'proses';

    if ($id_dokter) {
        // Simpan ke resep_dokter
        $stmt = $conn->prepare("INSERT INTO resep_dokter (id_dokter, id_apoteker, resep, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $id_dokter, $id_apoteker, $resep, $status);
        $success = $stmt->execute();
        echo json_encode(["success" => $success]);
    } else {
        echo $id_dokter;
        echo json_encode(["success" => false, "message" => "Error memasukkan data"]);
    }
}

if ($action === 'read') {
    $sql = "SELECT resep_dokter.id, pasien.nama as nama_pasien, dokter.nama as nama_dokter, resep_dokter.kode_resep, resep_dokter.resep, resep_dokter.status
            FROM pemeriksaan 
            JOIN pasien ON pemeriksaan.id_pasien = pasien.id
            JOIN rekam_medis ON pemeriksaan.id = rekam_medis.id_pemeriksaan
            JOIN resep_dokter ON rekam_medis.kode_resep = resep_dokter.kode_resep
            JOIN dokter ON resep_dokter.id_dokter = dokter.id
            WHERE resep_dokter.status = 'Proses'";

    $result = $conn->query($sql);
    $antrian = [];

    while ($row = $result->fetch_assoc()) {
        $antrian[] = $row;
    }

    echo json_encode($antrian);
}

else if ($action === 'detail') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $conn->prepare("SELECT id, kode_resep, resep FROM resep_dokter WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            echo json_encode(["success" => true, "resep" => $result]);
        } else {
            echo json_encode(["success" => false, "message" => "Resep tidak ditemukan"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID tidak diberikan"]);
    }
}

else if ($action === 'update') {
    $id = $_POST['id'] ?? null;
    $harga = intval($_POST['harga'] ?? 0);
    $id_apoteker = $_SESSION['user_id'] ?? null;

    if ($id && $harga !== null) {
        $stmt = $conn->prepare("UPDATE resep_dokter SET status = 'Selesai', id_apoteker = ?, harga = ? WHERE id = ?");
        $stmt->bind_param("iii", $id_apoteker, $harga, $id);
        $success = $stmt->execute();
        echo json_encode(["success" => $success]);
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    }
}
