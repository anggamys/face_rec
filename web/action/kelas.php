<?php
require_once __DIR__ . "/../libs/helper.php";

$kelasurl = "http://localhost:8000/kelas";

function getAllKelas()
{
    global $kelasurl;

    try {
        $response = sendRequest("GET", "$kelasurl/");

        if ($response["success"]) {
            return $response["data"];
        }

        return ["error" => $response["error"] ?? "Gagal mengambil data kelas."];
    } catch (Exception $e) {
        logMessage("ERROR", "Exception getAllKelas: " . $e->getMessage());
        return ["error" => "Terjadi kesalahan saat mengambil data kelas."];
    }
}

function getKelasByKodeKelas($kode_kelas)
{
    global $kelasurl;

    try {
        $response = sendRequest("GET", "$kelasurl/$kode_kelas");

        if ($response["success"]) {
            return [
                "success" => true,
                "data" => $response["data"],
            ];
        }

        return [
            "success" => false,
            "error" =>
            $response["error"] ??
                "Gagal mengambil data kelas dengan ID $kode_kelas.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Exception getKelasByKodeKelas($kode_kelas): " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat mengambil data kelas.",
        ];
    }
}

function getKelasByMatkul($id_matkul)
{
    global $kelasurl;

    try {
        $response = sendRequest("GET", "$kelasurl/matkul/$id_matkul");

        if ($response["success"]) {
            return $response["data"];
        }

        return [
            "error" =>
            $response["error"] ??
                "Gagal mengambil kelas berdasarkan ID matkul $id_matkul.",
        ];
    } catch (Exception $e) {
        logMessage(
            "ERROR",
            "Exception getKelasByMatkul($id_matkul): " . $e->getMessage()
        );
        return ["error" => "Terjadi kesalahan saat mengambil data."];
    }
}

function addKelas($kode_kelas, $nama_kelas, $id_matkul, $nrp_mahasiswa)
{
    global $kelasurl;

    try {
        $payload = [
            "kode_kelas" => $kode_kelas,
            "nama_kelas" => $nama_kelas,
            "mahasiswa" => (array) $nrp_mahasiswa,
            "matakuliah" => (array) $id_matkul,
        ];

        $response = sendRequest("POST", "$kelasurl/", $payload);

        if ($response["success"]) {
            return ["success" => true, "data" => $response["data"]];
        }

        return [
            "success" => false,
            "error" => $response["error"] ?? "Gagal menambahkan kelas.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Exception addKelas: " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat menambahkan kelas.",
        ];
    }
}

function updateKelas($kode_kelas, $kode_kelas_input, $nama_kelas, $id_matkul, $nrp_mahasiswa)
{
    global $kelasurl;

    try {
        $payload = [
            "kode_kelas" => $kode_kelas_input,
            "nama_kelas" => $nama_kelas,
            "mahasiswa" => (array) $nrp_mahasiswa,
            "matakuliah" => (array) $id_matkul,
        ];

        $response = sendRequest("PUT", "$kelasurl/$kode_kelas", $payload);

        if ($response["success"]) {
            return ["success" => true, "data" => $response["data"]];
        }

        return [
            "success" => false,
            "error" => $response["error"] ?? "Gagal memperbarui kelas.",
        ];
    } catch (Exception $e) {
        logMessage(
            "ERROR",
            "Exception updateKelas($kode_kelas): " . $e->getMessage()
        );
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat memperbarui kelas.",
        ];
    }
}

function deleteKelas($id)
{
    global $kelasurl;

    try {
        $response = sendRequest("DELETE", "$kelasurl/$id");

        if ($response["success"]) {
            return ["success" => true];
        }

        return [
            "success" => false,
            "error" => $response["error"] ?? "Gagal menghapus kelas.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Exception deleteKelas($id): " . $e->getMessage());
        return [
            "success" => false,
            "error" => "Terjadi kesalahan saat menghapus kelas.",
        ];
    }
}
