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
    require_login(); // Jaga-jaga kalau belum login

    if ($_SESSION['user']['role'] !== $role) {
        // Redirect ke halaman yang sesuai dengan role mereka
        if ($_SESSION['user']['role'] === 'dosen') {
            header("Location: /dashboard_dosen.php");
        } elseif ($_SESSION['user']['role'] === 'mahasiswa') {
            header("Location: /dashboard.php");
        } else {
            header("Location: /login.php");
        }
        exit();
    }
    // Kalau role sesuai, lanjut aja tanpa redirect
}
