<?php
require_once __DIR__ . "/../libs/helper.php";

$matakuliahurl = "http://localhost:8000/matakuliah";

function getAllMataKuliah()
{
    global $matakuliahurl;

    try {
        $response = sendRequest("GET", "$matakuliahurl/");

        if ($response["success"]) {
            return $response["data"];
        }

        return [
            "error" =>
            $response["error"] ?? "Gagal mengambil data mata kuliah.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Exception getAllMataKuliah: " . $e->getMessage());
        return ["error" => "Terjadi kesalahan saat mengambil data."];
    }
}

function addMataKuliah($nama_matkul)
{
    global $matakuliahurl;

    try {
        $response = sendRequest("POST", "$matakuliahurl/", [
            "nama_matkul" => $nama_matkul,
        ]);

        if ($response["success"]) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response["error"] ?? "Gagal menambahkan mata kuliah.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Exception addMataKuliah: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat menambahkan data.",
        ];
    }
}

function getMataKuliahById($id)
{
    global $matakuliahurl;

    try {
        $response = sendRequest("GET", "$matakuliahurl/$id");

        if ($response["success"]) {
            return $response["data"];
        }

        return [
            "error" =>
            $response["error"] ?? "Data mata kuliah tidak ditemukan.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Exception getMataKuliahById: " . $e->getMessage());
        return ["error" => "Terjadi kesalahan saat mengambil data."];
    }
}

function updateMataKuliah($id, $nama_matkul)
{
    global $matakuliahurl;

    try {
        $response = sendRequest("PUT", "$matakuliahurl/$id", [
            "nama_matkul" => $nama_matkul,
        ]);

        if ($response["success"]) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response["error"] ?? "Gagal memperbarui mata kuliah.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Exception updateMataKuliah: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat memperbarui data.",
        ];
    }
}

function deleteMataKuliah($id)
{
    global $matakuliahurl;

    try {
        $response = sendRequest("DELETE", "$matakuliahurl/$id");

        if ($response["success"]) {
            return ["success" => true];
        }

        return [
            "success" => false,
            "error" => $response["error"] ?? "Gagal menghapus mata kuliah.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Exception deleteMataKuliah: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat menghapus data.",
        ];
    }
}
