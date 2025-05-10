<?php
session_start();
require_once "../auth_check.php";
require_once "../../action/absen-session.php";

require_role("dosen");

// Helper untuk redirect dengan pesan
function redirectWithMessage($success = '', $error = '')
{
    $_SESSION['successMessage'] = $success;
    $_SESSION['errorMessage'] = $error;
    header("Location: sesi-absensi.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_jadwal = $_POST["id_jadwal"] ?? null;
    $action = $_POST["action"] ?? null;

    // Validasi ID jadwal
    if (!filter_var($id_jadwal, FILTER_VALIDATE_INT)) {
        redirectWithMessage('', "ID Jadwal tidak valid.");
    }

    // Validasi action
    if (!$action || !in_array($action, ['open', 'close'])) {
        redirectWithMessage('', "Aksi tidak valid.");
    }

    $id_jadwal = (int)$id_jadwal;
    $response = [];

    if ($action === "open") {
        $response = openAbsensiSession($id_jadwal);
        if (!empty($response["success"]) && $response["success"] === true) {
            redirectWithMessage("Sesi absensi berhasil dibuka.");
        } else {
            redirectWithMessage('', $response["error"] ?? "Gagal membuka sesi.");
        }
    }

    if ($action === "close") {
        $response = closeAbsensiSession($id_jadwal);
        if (!empty($response["success"]) && $response["success"] === true) {
            redirectWithMessage("Sesi absensi berhasil ditutup.");
        } else {
            redirectWithMessage('', $response["error"] ?? "Gagal menutup sesi.");
        }
    }

    // Fallback (harusnya tidak pernah sampai sini)
    redirectWithMessage('', "Terjadi kesalahan tak terduga.");
} else {
    redirectWithMessage('', "Akses tidak sah.");
}
