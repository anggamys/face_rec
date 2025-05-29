<?php
require_once __DIR__ . "/../libs/helper.php";

$userurl = "http://localhost:8000/user";

function getAllMahasiswa()
{
    global $userurl;

    try {
        $response = sendRequest("GET", "$userurl/mahasiswa");

        if (!empty($response["success"])) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response["message"] ?? "Gagal mengambil data mahasiswa.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "getAllMahasiswa exception: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat mengambil data mahasiswa.",
        ];
    }
}

function getUserById($id)
{
    global $userurl;

    try {
        $response = sendRequest("GET", "$userurl/$id");

        if (!empty($response["success"])) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response["message"] ?? "Gagal mengambil data user.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "getUserById exception: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat mengambil data user.",
        ];
    }
}

function getUserByNrp($nrp)
{
    global $userurl;

    try {
        $response = sendRequest("GET", "$userurl/nrp/$nrp");

        if (!empty($response)) {
            return $response;
        }

        return [
            "success" => false,
            "error" => $response["message"] ?? "Gagal mengambil data user.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "getUserByNrp exception: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat mengambil data user.",
        ];
    }
}
