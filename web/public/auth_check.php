<?php

function require_login()
{
    if (!isset($_SESSION['user']) || empty($_SESSION['token'])) {
        $_SESSION['errorMessage'] = "Anda harus login terlebih dahulu.";
        header("Location: /login.php");
        exit();
    }
}

function require_role($role)
{
    require_login();

    if ($_SESSION['user']['role'] === $role) {
        return;
    } else {
        $_SESSION['errorMessage'] = "Akses ditolak. Role Anda tidak sesuai.";
        header("Location: /dashboard.php");
        exit();
    }
}
