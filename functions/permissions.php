<?php
// Define which roles can access which pages
$role_permissions = [
    'dokter' => [
        'pemeriksaan.php',
        'rekammedis.php',
        'resepobat.php'
    ],
    'resepsionis' => [
        'dashboard_resepsionis.php',
        'pembayaran.php',
        'perawatan.php',
        'antrian.php'
    ],
    'apoteker' => [
        'daftarresep.php'
    ]
];

// Define public pages that don't require authentication
$public_pages = [
    'login.php',
];
?>