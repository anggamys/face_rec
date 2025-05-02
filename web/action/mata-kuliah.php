<?php

$backend_url = "http://localhost:8000";

function getAllMataKuliah()
{
    global $backend_url;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token = $_SESSION['token'] ?? null;
    if (!$token) {
        return false;
    }

    $url = $backend_url . "/matakuliah/";

    // Gunakan cURL untuk kontrol error yang lebih baik
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Cek jika terjadi error pada cURL
    if ($response === false) {
        return false;
    }

    // Cek jika HTTP response buruk (>=400)
    if ($httpCode >= 400) {
        return false;
    }

    // Return data dalam format array
    return json_decode($response, true);
}

function addMataKuliah($nama_matkul)
{
    global $backend_url;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token = $_SESSION['token'] ?? null;
    if (!$token) {
        return false;
    }

    $url = $backend_url . "/matakuliah/";
    $data = ['nama_matkul' => $nama_matkul];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Cek jika response gagal
    if ($response === false || $httpCode >= 400) {
        return false;
    }

    // Kembalikan response yang sudah di-decode
    return json_decode($response, true);
}

function getMataKuliahById($id)
{
    global $backend_url;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token = $_SESSION['token'] ?? null;
    if (!$token) {
        return false;
    }

    $url = $backend_url . "/matakuliah/$id";

    // Gunakan cURL untuk kontrol error yang lebih baik
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Cek jika terjadi error pada cURL
    if ($response === false) {
        return false;
    }

    // Cek jika HTTP response buruk (>=400)
    if ($httpCode >= 400) {
        return false;
    }

    // Return data dalam format array
    return json_decode($response, true);
}

function updateMataKuliah($id, $nama_matkul)
{
    global $backend_url;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token = $_SESSION['token'] ?? null;
    if (!$token) {
        return false;
    }

    $url = $backend_url . "/matakuliah/$id";
    $data = ['nama_matkul' => $nama_matkul];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Cek jika response gagal
    if ($response === false || $httpCode >= 400) {
        return false;
    }

    // Kembalikan response yang sudah di-decode
    return json_decode($response, true);
}

function deleteMataKuliah($id)
{
    global $backend_url;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token = $_SESSION['token'] ?? null;
    if (!$token) {
        return false;
    }

    $url = $backend_url . "/matakuliah/$id";

    // Gunakan cURL untuk kontrol error yang lebih baik
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Cek jika terjadi error pada cURL
    if ($response === false) {
        return false;
    }

    // Cek jika HTTP response buruk (>=400)
    if ($httpCode >= 400) {
        return false;
    }

    // Return data dalam format array
    return json_decode($response, true);
}
