<?php
session_start(); // Penting! Pastikan ini ada di awal

function require_login()
{
    if (!isset($_SESSION['user'])) {
        header("Location: /login.php");
        exit();
    }
}

function require_role($role)
{
    require_login(); // Pastikan user sudah login

    // Jika role pengguna sesuai dengan yang dibutuhkan, lanjutkan
    if ($_SESSION['user']['role'] === $role) {
        return; // Tidak ada redirect, biarkan akses ke halaman
    } else {
        // Jika tidak sesuai, redirect ke halaman login atau halaman tertentu
        header("Location: /dashboard.php");
        exit();
    }
}
