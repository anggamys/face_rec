<?php
require_once __DIR__ . "/../libs/helper.php";

$absensiurl = "http://localhost:8000/absen";

function getAllAbsensi()
{
    global $absensiurl;

    try {
        $response = sendRequest("GET", "$absensiurl/");

        if ($response["success"]) {
            return $response["data"];
        }

        return [
            "error" => $response["error"] ?? "Gagal mengambil data absensi.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "getAllAbsensi exception: " . $e->getMessage());
        return [
            "error" => "Terjadi kesalahan saat mengambil data absensi. Coba lagi nanti.",
        ];
    }
}

// For mahasiswa

$rekapabsensiurl = "http://localhost:8000/mahasiswa";

function getRekapAbsensi()
{
    global $rekapabsensiurl;

    try {
        $response = sendRequest("GET", "$rekapabsensiurl/rekap-absen");

        if ($response["success"]) {
            return $response["data"];
        }

        return [
            "error" => $response["error"] ?? "Gagal mengambil data rekap absensi.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "getRekapAbsensi exception: " . $e->getMessage());
        return [
            "error" => "Terjadi kesalahan saat mengambil data rekap absensi. Coba lagi nanti.",
        ];
    }
}
