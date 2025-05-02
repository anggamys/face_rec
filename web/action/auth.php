<?php

$backend_url = 'http://localhost:8000';

function login($email, $password)
{
    global $backend_url;

    $url = $backend_url . '/auth/login';
    $data = [
        'email' => $email,
        'password' => $password,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode >= 400) {
        return false;
    }

    return json_decode($response, true);
}

function register($name, $email, $password, $role, $nrp = null, $nip = null)
{
    global $backend_url;

    $url = $backend_url . '/auth/register';
    $data = [
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => $role,
        'nrp' => $role === 'mahasiswa' ? (int)$nrp : null,
        'nip' => $role === 'dosen' ? (int)$nip : null,
    ];

    $payload = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode >= 400) {
        error_log("Register gagal | CURL Error: $error | HTTP Code: $httpCode | Response: $response");
        return false;
    }

    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON parse error (register): " . json_last_error_msg());
        return false;
    }

    return $result;
}
