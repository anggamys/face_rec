<?php
require_once __DIR__ . "/../libs/helper.php";

$jadwalurl = "http://localhost:8000/jadwal";

function getAllJadwal()
{
    global $jadwalurl;
    $response = sendRequest("GET", "$jadwalurl/");

    if ($response['success']) {
        return $response['data'];
    }

    logMessage("ERROR", "Failed to fetch all jadwal. Response: " . json_encode($response));
    return [];
}

function addJadwal($kode_kelas, $tanggal, $week)
{
    global $jadwalurl;
    $data = [
        'kode_kelas' => $kode_kelas,
        'tanggal' => $tanggal,
        'week' => $week
    ];

    $response = sendRequest("POST", "$jadwalurl/", $data);

    if ($response['success']) {
        return $response['data'];
    }

    logMessage("ERROR", "Failed to add jadwal. Response: " . json_encode($response));
    return null;
}

function getJadwalById($id_jadwal)
{
    global $jadwalurl;
    $response = sendRequest("GET", "$jadwalurl/jadwal/$id_jadwal");

    if ($response['success']) {
        return $response['data'];
    }

    logMessage("ERROR", "Failed to fetch jadwal by ID. Response: " . json_encode($response));
    return null;
}

function updateJadwal($id_jadwal, $kode_kelas, $tanggal, $week)
{
    global $jadwalurl;
    $data = [
        'kode_kelas' => $kode_kelas,
        'tanggal' => $tanggal,
        'week' => $week
    ];

    $response = sendRequest("PUT", "$jadwalurl/jadwal/$id_jadwal", $data);

    if ($response['success']) {
        return $response['data'];
    }

    logMessage("ERROR", "Failed to update jadwal. Response: " . json_encode($response));
    return null;
}

function deleteJadwal($id_jadwal)
{
    global $jadwalurl;
    $response = sendRequest("DELETE", "$jadwalurl/$id_jadwal");

    if ($response['success']) {
        return true;
    }

    logMessage("ERROR", "Failed to delete jadwal. Response: " . json_encode($response));
    return false;
}
