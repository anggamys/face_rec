<?php

function logError($message)
{
    error_log($message);
}

function sendRequest($method, $url, $data = null)
{
    $token = $_SESSION['token'] ?? ($_COOKIE['token'] ?? null);
    if (!$token) return ['success' => false, 'status' => 401, 'error' => 'Unauthorized'];

    $ch = curl_init($url);

    $headers = [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ];

    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => strtoupper($method)
    ];

    $jsonData = $data !== null ? json_encode($data) : null;
    if ($jsonData) {
        $options[CURLOPT_POSTFIELDS] = $jsonData;
    }

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);

    // DEBUG LOG
    error_log("Method: $method, URL: $url, Data: $jsonData, HTTP Code: $httpCode, Response: $response, cURL Error: $curlError, Decoded: " . json_encode($decoded));

    return [
        'success' => $response !== false && $httpCode < 400,
        'status' => $httpCode,
        'data' => $decoded,
        'raw' => $response,
        'error' => $curlError ?: ($decoded['detail'] ?? json_encode($decoded))
    ];
}
