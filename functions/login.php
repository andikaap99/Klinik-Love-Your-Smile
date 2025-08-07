<?php
session_start();
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['role']; // This comes from your select dropdown

    // 1. Check in user table first
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND tipe_user = ?");
    $stmt->bind_param("ss", $username, $user_type);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // 2. Get user details from their respective table
        $table = $user['tipe_user'];
        $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->bind_param("i", $user['id_user']);
        $stmt->execute();
        $user_details = $stmt->get_result()->fetch_assoc();

        // 3. Store session data
        $_SESSION['auth'] = true;
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['tipe_user'];
        $_SESSION['user_details'] = $user_details;

        // 4. Redirect to appropriate dashboard
        if ($_SESSION['user_type'] == 'dokter') {
            header('Location: ../pages/dokter/pemeriksaan.php');
        } elseif ($_SESSION['user_type'] == 'resepsionis') {
            header('Location: ../pages/resepsionis/dashboard_resepsionis.php');
        } elseif ($_SESSION['user_type'] == 'apoteker') {
            header('Location: ../pages/apoteker/daftarresep.php');
        }
        exit();
    } else {
        die("Login failed: Invalid credentials");
    }
}
?>