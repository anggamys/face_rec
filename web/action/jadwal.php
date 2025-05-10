<?php
require_once __DIR__ . "/../libs/helper.php";

$jadwalurl = "http://localhost:8000/jadwal";

function getAllJadwal()
{
    global $jadwalurl;

    try {
        $response = sendRequest("GET", "$jadwalurl/");

        if (!empty($response["success"])) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response["message"] ?? "Gagal mengambil data jadwal.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "getAllJadwal exception: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat mengambil data jadwal.",
        ];
    }
}

function addJadwal($kode_kelas, $id_matkul, $tanggal, $week)
{
    global $jadwalurl;

    try {
        $data = [
            "kode_kelas" => $kode_kelas,
            "id_matkul" => $id_matkul,
            "tanggal" => $tanggal,
            "week" => $week,
        ];

        $response = sendRequest("POST", "$jadwalurl/", $data);

        if (!empty($response["success"])) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response["message"] ?? "Gagal menambahkan jadwal.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "addJadwal exception: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat menambahkan jadwal.",
        ];
    }
}

function getJadwalById($id_jadwal)
{
    global $jadwalurl;

    try {
        $response = sendRequest("GET", "$jadwalurl/jadwal/$id_jadwal");

        if (!empty($response["success"])) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response["message"] ?? "Gagal mengambil jadwal.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "getJadwalById exception: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat mengambil jadwal.",
        ];
    }
}

function updateJadwal($id_jadwal, $kode_kelas, $id_matkul, $tanggal, $week)
{
    global $jadwalurl;

    try {
        $data = [
            "kode_kelas" => $kode_kelas,
            "tanggal" => $tanggal,
            "week" => $week,
        ];

        $response = sendRequest("PUT", "$jadwalurl/jadwal/$id_jadwal", $data);

        if (!empty($response["success"])) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response["message"] ?? "Gagal memperbarui jadwal.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "updateJadwal exception: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat memperbarui jadwal.",
        ];
    }
}

function deleteJadwal($id_jadwal)
{
    global $jadwalurl;

    try {
        $response = sendRequest("DELETE", "$jadwalurl/$id_jadwal");

        if (!empty($response["success"])) {
            return [
                "success" => true,
            ];
        }

        return [
            "success" => false,
            "error" => $response["message"] ?? "Gagal menghapus jadwal.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "deleteJadwal exception: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat menghapus jadwal.",
        ];
    }
}
