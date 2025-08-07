<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_config.php';
header('Content-Type: application/json');
error_reporting(E_ALL);

$action = $_GET['action'] ?? 'read';
$id_rekam_medis = $_GET['id_rekam_medis'] ?? null;

if ($action === 'read') {
    $sql = "SELECT 
        t.id,
        p.nama AS nama_pasien,
        r.nama AS nama_resepsionis,
        t.no_invoice,
        t.tanggal,
        pel.nama_pelayanan AS nama_pelayanan,
        rs.resep AS nama_obat,
        t.harga_total
    FROM transaksi t    
    JOIN pasien p ON t.id_pasien = p.id
    JOIN resepsionis r ON t.id_resepsionis = r.id
    JOIN rekam_medis rm ON t.id_rekam_medis = rm.id
    JOIN pelayanan pel ON pel.id = rm.id_pelayanan
    JOIN resep_dokter rs ON rm.kode_resep = rs.kode_resep";


    $result = $conn->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
}

// Ambil list Rekam Medis yang belum ada di tabel transaksi (agar tidak double bayar)
else if ($action === 'rekam_medis_list') {
    $sql = "SELECT rm.id as id_rekam_medis, p.nama as nama_pasien
            FROM rekam_medis rm
            JOIN pemeriksaan pem ON rm.id_pemeriksaan = pem.id
            JOIN pasien p ON pem.id_pasien = p.id
            WHERE rm.id NOT IN (SELECT id_rekam_medis FROM transaksi)";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

// Ambil detail Rekam Medis (untuk isi otomatis)
else if ($action === 'rekam_medis_full_detail') {
    $id_rekam_medis = $_GET['id_rekam_medis'] ?? null;
    if ($id_rekam_medis) {
        $stmt = $conn->prepare("
            SELECT 
                p.id AS id_pasien,
                rm.id AS id_rekam_medis,
                pel.id AS id_pelayanan,
                pel.nama_pelayanan AS nama_pelayanan,
                rs.kode_resep,
                pel.harga AS harga_total
            FROM rekam_medis rm
            JOIN pemeriksaan pem ON rm.id_pemeriksaan = pem.id
            JOIN pasien p ON pem.id_pasien = p.id
            JOIN pelayanan pel ON pel.id = rm.id_pelayanan
            JOIN resep_dokter rs ON rs.kode_resep = rm.kode_resep
            WHERE rm.id = ?
        ");

        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("i", $id_rekam_medis);
        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "Execute failed: " . $stmt->error]);
            exit;
        }

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if ($data) {
            echo json_encode(["success" => true] + $data);
        } else {
            echo json_encode(["success" => false, "message" => "Data tidak ditemukan"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID Rekam Medis tidak dikirim"]);
    }
} else if ($action === 'resepsionis_list') {
    $result = $conn->query("SELECT id, nama FROM resepsionis");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

else if ($action === 'create') {
    $id_pasien = $_POST['id_pasien'] ?? null;
    $id_resepsionis = $_POST['id_resepsionis'] ?? null;
    $id_rekam_medis = $_POST['id_rekam_medis'] ?? null;
    $no_invoice = $_POST['no_invoice'] ?? null;
    $tanggal = $_POST['tanggal'] ?? null;
    $id_pelayanan = $_POST['id_pelayanan'] ?? null;
    $kode_resep = $_POST['kode_resep'] ?? null;

    if ($id_pasien && $id_resepsionis && $id_rekam_medis && $id_pelayanan && $kode_resep && $no_invoice && $tanggal) {
        
        // 1. Ambil harga pelayanan
        $stmt = $conn->prepare("SELECT harga FROM pelayanan WHERE id = ?");
        $stmt->bind_param("i", $id_pelayanan);
        $stmt->execute();
        $stmt->bind_result($harga_pelayanan);
        $stmt->fetch();
        $stmt->close();

        if ($harga_pelayanan === null) {
            echo json_encode(["success" => false, "message" => "Harga pelayanan tidak ditemukan"]);
            exit;
        }

        // 2. Ambil harga resep_dokter
        $stmt = $conn->prepare("SELECT harga FROM resep_dokter WHERE kode_resep = ?");
        $stmt->bind_param("s", $kode_resep);
        $stmt->execute();

        // Cek apakah ada hasil
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            echo json_encode(["success" => false, "message" => "Kode resep tidak ditemukan di database", "kode_resep" => $kode_resep]);
            exit;
        }

        $stmt->bind_result($harga_resep);
        $stmt->fetch();
        $stmt->close();

        // Cek apakah harga_resep NULL atau 0
        if ($harga_resep === null || $harga_resep == 0) {
            echo json_encode(["success" => false, "message" => "Harga resep tidak ditemukan atau bernilai 0"]);
            exit;
        }


        // 3. Hitung total harga
        $harga_total = $harga_pelayanan + $harga_resep;

        // 4. Insert ke transaksi
        $stmt = $conn->prepare("INSERT INTO transaksi (id_pasien, id_resepsionis, id_rekam_medis, id_pelayanan, kode_resep, no_invoice, tanggal, harga_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiisssi", $id_pasien, $id_resepsionis, $id_rekam_medis, $id_pelayanan, $kode_resep, $no_invoice, $tanggal, $harga_total);

        $success = $stmt->execute();

        if (!$success) {
            echo json_encode(["success" => false, "message" => "Execute failed: " . $stmt->error]);
        } else {
            echo json_encode(["success" => true]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap", "debug" => $_POST]);
    }
}

else if ($action === 'delete') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $conn->prepare("DELETE FROM transaksi WHERE id = ?");
        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("i", $id);
        $success = $stmt->execute();

        if ($success && $stmt->affected_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Data tidak ditemukan atau gagal dihapus"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "ID tidak ditemukan"]);
    }
}



$conn->close();