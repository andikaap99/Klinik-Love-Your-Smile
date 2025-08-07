<?php
require 'db_config.php'; // Your database connection file

// Dummy user data
$users = [
    [
        'username' => 'apoteker',
        'password' => 'admin',
        'user_type' => 'apoteker',
        'user_id' => 1  // Must match an existing ID in apoteker table
    ],
    [
        'username' => 'resepsionis',
        'password' => 'admin',
        'user_type' => 'resepsionis',
        'user_id' => 1  // Must match an existing ID in resepsionis table
    ],
    [
        'username' => 'dokter',
        'password' => 'admin',
        'user_type' => 'dokter',
        'user_id' => 1  // Must match an existing ID in resepsionis table
    ]
];

foreach ($users as $user) {
    // Hash the password
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
    
    // Insert into user table
    $stmt = $conn->prepare("INSERT INTO user (username, password, tipe_user, id_user) 
                           VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", 
        $user['username'],
        $hashedPassword,
        $user['user_type'],
        $user['user_id']
    );
    
    if ($stmt->execute()) {
        echo "User {$user['username']} created successfully!<br>";
    } else {
        echo "Error creating {$user['username']}: " . $conn->error . "<br>";
    }
}

echo "Dummy users creation complete!";
?>