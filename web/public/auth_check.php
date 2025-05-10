<?php

// Fungsi untuk memeriksa apakah pengguna sudah login (dengan valid token)
function require_login()
{
    // Cek apakah sesi memiliki data user dan token yang valid
    if (!isset($_SESSION['user']) || empty($_SESSION['token'])) {
        // Jika tidak ada user atau token, arahkan ke halaman login
        $_SESSION['errorMessage'] = "Anda harus login terlebih dahulu.";
        header("Location: /login.php");
        exit();
    }
}

// Fungsi untuk memeriksa apakah pengguna memiliki role tertentu
function require_role($role)
{
    require_login(); // Pastikan user sudah login

    // Cek apakah role pengguna sesuai dengan yang dibutuhkan
    if ($_SESSION['user']['role'] === $role) {
        return; // Tidak ada redirect, biarkan akses ke halaman
    } else {
        // Jika tidak sesuai, redirect ke halaman yang sesuai
        $_SESSION['errorMessage'] = "Akses ditolak. Role Anda tidak sesuai.";
        header("Location: /dashboard.php");
        exit();
    }
}
