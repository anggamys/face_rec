<?php
require_once __DIR__ . "/../libs/helper.php";

$absensessionurl = "http://localhost:8000/absen-session";

function openAbsensiSession($id_jadwal)
{
    global $absensessionurl;

    try {
        $response = sendRequest("POST", "$absensessionurl/open/$id_jadwal",);

        if ($response["success"]) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "error" => $response["error"] ?? "Gagal membuka sesi absensi.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Open absensi session exception: " . $e->getMessage());
        return [
            "error" => "Terjadi kesalahan saat membuka sesi absensi. Coba lagi nanti.",
        ];
    }
}

function closeAbsensiSession($id_jadwal)
{
    global $absensessionurl;

    try {
        $response = sendRequest("POST", "$absensessionurl/close/$id_jadwal");

        if ($response["success"]) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "error" => $response["error"] ?? "Gagal menutup sesi absensi.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Close absensi session exception: " . $e->getMessage());
        return [
            "error" => "Terjadi kesalahan saat menutup sesi absensi. Coba lagi nanti.",
        ];
    }
}

function getAllAbsensiSession()
{
    global $absensessionurl;

    try {
        $response = sendRequest("GET", "$absensessionurl/");

        if ($response["success"]) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "error" => $response["error"] ?? "Gagal mendapatkan data sesi absensi.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Get all absensi session exception: " . $e->getMessage());
        return [
            "error" => "Terjadi kesalahan saat mendapatkan data sesi absensi. Coba lagi nanti.",
        ];
    }
}

function getAbsensiSessionByIdJadwal($id_jadwal)
{
    global $absensessionurl;

    try {
        $response = sendRequest("GET", "$absensessionurl/$id_jadwal");

        if ($response["success"]) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "error" => $response["error"] ?? "Gagal mendapatkan data sesi absensi.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Get absensi session by ID exception: " . $e->getMessage());
        return [
            "error" => "Terjadi kesalahan saat mendapatkan data sesi absensi. Coba lagi nanti.",
        ];
    }
}
