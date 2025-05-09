<?php
require_once __DIR__ . "/../libs/helper.php";

$kelasurl = "http://localhost:8000/kelas";

function getAllKelas()
{
    global $kelasurl;
    $response = sendRequest("GET", "$kelasurl/");

    if ($response['success']) {
        return $response['data'];
    }

    logMessage("ERROR", "Failed to fetch all kelas. Response: " . json_encode($response));
}

function getKelasById($id)
{
    global $kelasurl;
    $response = sendRequest("GET", "$kelasurl/$id");

    if (isset($response['success']) && $response['success']) {
        return [
            'success' => true,
            'data' => $response['data']
        ];
    }

    $errorMessage = $response['message'] ?? 'Unknown error';
    logMessage("ERROR", "Failed to fetch kelas with ID {$id}. Error: {$errorMessage}");

    return [
        'success' => false,
        'error' => $errorMessage
    ];
}

function getKelasByMatkul($id_matkul)
{
    global $kelasurl;
    $response = sendRequest("GET", "$kelasurl/matkul/$id_matkul");

    if (isset($response['success']) && $response['success']) {
        return $response['data'];
    }

    logMessage("ERROR", "Failed to fetch kelas by matkul ID {$id_matkul}. Response: " . json_encode($response));
    return [];
}

function addKelas($kode_kelas, $nama_kelas, $id_matkul)
{
    global $kelasurl;
    $data = [
        'kode_kelas' => $kode_kelas,
        'nama_kelas' => $nama_kelas,
        'mahasiswa' => [],
        'matakuliah' => is_array($id_matkul) ? $id_matkul : [$id_matkul]
    ];
    $response = sendRequest("POST", "$kelasurl/", $data);

    if ($response['success']) {
        return true;
    }

    logMessage("ERROR", "Failed to add kelas. Response: " . json_encode($response));
    return false;
}

function updateKelas($kode_kelas, $kode_kelas_input, $nama_kelas, $id_matkul)
{
    global $kelasurl;

    $matakuliahArray = is_array($id_matkul) ? $id_matkul : [$id_matkul];

    $data = [
        'kode_kelas' => $kode_kelas_input,
        'nama_kelas' => $nama_kelas,
        'matakuliah' => $matakuliahArray
    ];

    $response = sendRequest("PUT", "$kelasurl/$kode_kelas", $data);

    if (isset($response['success']) && $response['success']) {
        return $response;
    }

    logMessage("ERROR", "Failed to update kelas with kode_kelas {$kode_kelas}. Response: " . json_encode($response));
    return $response;
}

function deleteKelas($id)
{
    global $kelasurl;
    $response = sendRequest("DELETE", "$kelasurl/$id");

    if (isset($response['success']) && $response['success']) {
        return true;
    }

    logMessage("ERROR", "Failed to delete kelas with ID {$id}. Response: " . json_encode($response));
    return false;
}
