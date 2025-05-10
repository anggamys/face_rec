<?php
session_start();

if (isset($_SESSION['token'])) {
    $token = $_SESSION['token'];
    $ch = curl_init('http://localhost:8000/auth/logout');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);
    curl_exec($ch);
    curl_close($ch);
}

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke halaman login
header('Location: login.php');
exit();
