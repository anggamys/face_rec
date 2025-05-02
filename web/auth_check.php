<?php
// components/auth.php
session_start();

function require_login()
{
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit();
    }
}

function require_role($allowedRoles)
{
    require_login();

    $userRole = $_SESSION["user"]["role"];

    if (!in_array($userRole, (array) $allowedRoles)) {
        // Redirect ke dashboard sesuai role
        switch ($userRole) {
            case "dosen":
                header("Location: dashboard_dosen.php");
                break;
            case "mahasiswa":
                header("Location: dashboard.php");
                break;
            default:
                header("Location: login.php");
                break;
        }
        exit();
    }
}
