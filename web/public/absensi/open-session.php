<?php
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
    $action = $_POST["action"] ?? null;

    // Validasi action
    if (!$action || !in_array($action, ['open', 'close'])) {
        redirectWithMessage('', "Aksi tidak valid.");
    }

    $response = [];

    if ($action === "open") {
        $id_jadwal = $_POST["id_jadwal"] ?? null;
        if (!filter_var($id_jadwal, FILTER_VALIDATE_INT)) {
            redirectWithMessage('', "ID Jadwal tidak valid.");
        }

        $id_jadwal = (int)$id_jadwal;
        $response = openAbsensiSession($id_jadwal);

        if (!empty($response["success"])) {
            redirectWithMessage("✅ Sesi absensi berhasil dibuka.");
        } else {
            redirectWithMessage('', $response["error"] ?? "❌ Gagal membuka sesi.");
        }
    } elseif ($action === "close") {
        $id_session = $_POST["id_session"] ?? null;
        if (!filter_var($id_session, FILTER_VALIDATE_INT)) {
            redirectWithMessage('', "ID Sesi tidak valid.");
        }

        $id_session = (int)$id_session;
        $response = closeAbsensiSession($id_session);

        if (!empty($response["success"])) {
            redirectWithMessage("✅ Sesi absensi berhasil ditutup.");
        } else {
            redirectWithMessage('', $response["error"] ?? "❌ Gagal menutup sesi.");
        }
    }

    // Fallback (harusnya tidak sampai sini)
    redirectWithMessage('', "Terjadi kesalahan tak terduga.");
} else {
    redirectWithMessage('', "Akses tidak sah.");
}
