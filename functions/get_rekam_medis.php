<?php
include 'db_config.php';
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);

$action = $_GET['action'] ?? 'read';

if ($action === 'read') {
    // Load Rekam Medis dengan relasi Pemeriksaan -> Pasien -> Dokter
    $sql = "SELECT rekam_medis.id, pemeriksaan.id AS id_pemeriksaan, pasien.nama AS nama_pasien, dokter.nama AS nama_dokter, rekam_medis.diagnosa, pelayanan.nama_pelayanan, resep_dokter.resep
            FROM rekam_medis
            JOIN resep_dokter ON rekam_medis.kode_resep = resep_dokter.kode_resep
            JOIN pelayanan ON rekam_medis.id_pelayanan = pelayanan.id
            JOIN pemeriksaan ON rekam_medis.id_pemeriksaan = pemeriksaan.id
            JOIN pasien ON pemeriksaan.id_pasien = pasien.id
            JOIN dokter ON rekam_medis.id_dokter = dokter.id
            ORDER BY rekam_medis.id DESC";

    $result = $conn->query($sql);
    
    if (!$result) {
        echo json_encode(["error" => $conn->error]);
        exit;
    }
    
    $rekamMedis = [];
    while ($row = $result->fetch_assoc()) {
        $rekamMedis[] = $row;
    }

    echo json_encode($rekamMedis);
}

else if ($action === 'get_dokter') {
    if (isset($_SESSION['user_id'])) {
        // Load Data Dokter untuk Dropdown
        $stmt = $conn->prepare("SELECT id, nama FROM dokter WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();

        // Get the result set
        $result = $stmt->get_result();

        // Fetch the data
        if ($row = $result->fetch_assoc()) {
            // Return both id and nama in JSON format
            echo json_encode([
                [
                    'id' => $row['id'],
                    'nama' => $row['nama']
                ]
            ]);
        } else {
            // Return error if no doctor found
            echo json_encode([
                'message' => 'Data dokter tidak ditemukan'
            ]);
        }
    } else {
        // Load Data Dokter untuk Dropdown
        $sql = "SELECT id, nama FROM dokter";
        $result = $conn->query($sql);

        $dokter = [];
        while ($row = $result->fetch_assoc()) {
            $dokter[] = $row;
        }

        echo json_encode($dokter);
    }
}

else if ($action === 'get_pemeriksaan') {
    // Load Data Pemeriksaan yang status "Sedang Diperiksa"
    $sql = "SELECT pemeriksaan.id, pasien.nama 
            FROM pemeriksaan 
            JOIN pasien ON pemeriksaan.id_pasien = pasien.id
            WHERE pemeriksaan.status = 'Sedang Diperiksa'";
    $result = $conn->query($sql);

    $pemeriksaan = [];
    while ($row = $result->fetch_assoc()) {
        $pemeriksaan[] = $row;
    }

    echo json_encode($pemeriksaan);
}

else if ($action === 'get_obat') {
    $sql = "SELECT id, nama_obat FROM obat";
    $result = $conn->query($sql);

    $obat = [];
    while ($row = $result->fetch_assoc()) {
        $obat[] = $row;
    }

    echo json_encode($obat);
}

else if ($action === 'add2') {
    $id_pelayanan = $_POST['id_pelayanan'] ?? null;
    $id_obat = $_POST['id_obat'] ?? null;

    if ($id_pelayanan && $id_obat) {
        // Simpan ke rekam_medis (buat simple aja: id_pemeriksaan & id_dokter diset NULL)
        $stmt = $conn->prepare("INSERT INTO rekam_medis (id_pemeriksaan, id_dokter, id_pelayanan, id_obat, diagnosa) VALUES (NULL, NULL, ?, ?, NULL)");
        $stmt->bind_param("ii", $id_pelayanan, $id_obat);
        $success = $stmt->execute();

        echo json_encode(["success" => $success]);
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    }
}

else if ($action === 'add_full') {
    $id_pemeriksaan = $_POST['id_pemeriksaan'] ?? null;
    $id_dokter = $_POST['id_dokter'] ?? null;
    $diagnosa = $_POST['diagnosa'] ?? null;
    $id_pelayanan = $_POST['id_pelayanan'] ?? null;
    $kode_resep = $_POST['kode_resep'] ?? null;

    if ($id_pemeriksaan && $id_dokter && $diagnosa && $id_pelayanan && $kode_resep) {
        $stmt = $conn->prepare("INSERT INTO rekam_medis (id_pemeriksaan, id_dokter, diagnosa, id_pelayanan, kode_resep) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $id_pemeriksaan, $id_dokter, $diagnosa, $id_pelayanan, $kode_resep);

        $success = $stmt->execute();

        if ($success) {
            // Update Status Pemeriksaan menjadi Selesai
            $stmt2 = $conn->prepare("UPDATE pemeriksaan SET status = 'Selesai' WHERE id = ?");
            $stmt2->bind_param("i", $id_pemeriksaan);
            $stmt2->execute();

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $stmt->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => [$id_pemeriksaan,$id_dokter,$diagnosa,$id_pelayanan,$kode_resep]]);
    }
}


else if ($action === 'get_pelayanan') {
    // Load Data Pemeriksaan yang status "Sedang Diperiksa"
    $sql = "SELECT pelayanan.id, pelayanan.nama_pelayanan 
            FROM pelayanan";
    $result = $conn->query($sql);

    $pelayanan = [];
    while ($row = $result->fetch_assoc()) {
        $pelayanan[] = $row;
    }

    echo json_encode($pelayanan);
}

else if ($action === 'add') {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $id_pemeriksaan = $_POST['id_pemeriksaan'] ?? null;
    $id_dokter = $_POST['id_dokter'] ?? null;
    $id_pelayanan = $_POST['id_pelayanan'] ?? null;
    $diagnosa = $_POST['diagnosa'] ?? null;

    if ($id_pemeriksaan && $id_dokter && $id_pelayanan && $diagnosa) {
        $conn->begin_transaction();

        try {
            // Insert Rekam Medis dengan id_pelayanan
            $stmt = $conn->prepare("INSERT INTO rekam_medis (id_pemeriksaan, id_dokter, id_pelayanan, diagnosa) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $id_pemeriksaan, $id_dokter, $id_pelayanan, $diagnosa);
            $stmt->execute();

            // Update Status Pemeriksaan menjadi Selesai
            $stmt2 = $conn->prepare("UPDATE pemeriksaan SET status = 'Selesai' WHERE id = ?");
            $stmt2->bind_param("i", $id_pemeriksaan);
            $stmt2->execute();

            $conn->commit();
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["success" => false, "message" => "Gagal menyimpan data"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    }
}

else if ($action === 'update') {
    $id = $_POST['id'] ?? null;
    $diagnosa = $_POST['diagnosa'] ?? null;
    $id_dokter = $_POST['id_dokter'] ?? null;

    if ($id && $diagnosa && $id_dokter) {
        $stmt = $conn->prepare("UPDATE rekam_medis SET diagnosa = ?, id_dokter = ? WHERE id = ?");
        $stmt->bind_param("sii", $diagnosa,  $id_dokter, $id);
        $success = $stmt->execute();

        echo json_encode(["success" => $success]);
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    }
}

else if ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM rekam_medis WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();

        echo json_encode(["success" => $success]);
    } else {
        echo json_encode(["success" => false, "message" => "ID tidak ditemukan"]);
    }
}

$conn->close();
?>
