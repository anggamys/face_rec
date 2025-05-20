<?php
session_start();

function require_login(): void
{
    if (
        !isset($_SESSION['user']) ||
        !is_array($_SESSION['user']) ||
        empty($_SESSION['user']['role']) ||
        empty($_SESSION['token'])
    ) {
        $_SESSION['errorMessage'] = "Anda harus login terlebih dahulu.";
        header("Location: /login.php");
        exit();
    }
}

function require_role(string $requiredRole): void
{
    require_login();

    $userRole = $_SESSION['user']['role'] ?? null;

    if ($userRole !== $requiredRole) {
        $_SESSION['errorMessage'] = "Akses ditolak. Role Anda tidak sesuai.";
        header("Location: /dashboard.php");
        exit();
    }
}
