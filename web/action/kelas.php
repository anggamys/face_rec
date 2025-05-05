<?php
require_once __DIR__ . "/../libs/helper.php";

$global_url = "http://localhost:8000/kelas";

function getAllKelas()
{
    global $global_url;
    $response = sendRequest("GET", "$global_url/");

    if ($response['success']) {
        return $response['data'];
    }

    logError("Failed to fetch all kelas. Response: " . json_encode($response));
    return [];
}

function getKelasById($id)
{
    global $global_url;
    $response = sendRequest("GET", "$global_url/$id");

    if (isset($response['success']) && $response['success']) {
        return [
            'success' => true,
            'data' => $response['data']
        ];
    }

    $errorMessage = isset($response['message']) ? $response['message'] : 'Unknown error';
    logError("Failed to fetch kelas with ID {$id}. Error: {$errorMessage}");

    return [
        'success' => false,
        'error' => $errorMessage
    ];
}

function getKelasByMatkul($id_matkul)
{
    global $global_url;
    $response = sendRequest("GET", "$global_url/matkul/$id_matkul");

    if (isset($response['success']) && $response['success']) {
        return $response['data'];
    }

    logError("Failed to fetch kelas by matkul ID {$id_matkul}. Response: " . json_encode($response));
    return [];
}

function addKelas($kode_kelas, $nama_kelas, $id_matkul)
{
    global $global_url;
    $data = [
        'kode_kelas' => $kode_kelas,
        'nama_kelas' => $nama_kelas,
        'mahasiswa' => [],
        'matakuliah' => [
            'id_matkul' => $id_matkul
        ]
    ];
    $response = sendRequest("POST", "$global_url/", $data);

    if ($response['success']) {
        return true;
    }

    logError("Failed to add kelas. Response: " . json_encode($response));
    return false;
}

function updateKelas($kode_kelas, $kode_kelas_input, $nama_kelas, $id_matkul)
{
    global $global_url;

    // Pastikan $id_matkul dalam bentuk array
    $matakuliahArray = is_array($id_matkul) ? $id_matkul : [$id_matkul];

    $data = [
        'kode_kelas' => $kode_kelas_input,
        'nama_kelas' => $nama_kelas,
        'matakuliah' => $matakuliahArray
    ];

    $response = sendRequest("PUT", "$global_url/$kode_kelas", $data);

    if (isset($response['success']) && $response['success']) {
        return $response; // Tetap kembalikan response utuh
    }

    // Logging ke terminal untuk debugging
    error_log("❌ Failed to update kelas with kode_kelas {$kode_kelas}. Response: " . json_encode($response));

    return $response;
}

function deleteKelas($id)
{
    global $global_url;
    $response = sendRequest("DELETE", "$global_url/$id");

    if (isset($response['success']) && $response['success']) {
        return true;
    }

    logError("Failed to delete kelas with ID {$id}. Response: " . json_encode($response));
    return false;
}
