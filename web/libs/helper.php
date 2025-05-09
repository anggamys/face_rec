<?php

function logMessage(string $level, string $message): void
{
    $timestamp = date("Y-m-d H:i:s");
    error_log("[{$timestamp}] [$level] $message");
}

function sendRequest(string $method, string $url, ?array $data = null): array
{
    $token = $_SESSION['token'] ?? ($_COOKIE['token'] ?? null);
    if (!$token) {
        return [
            'success' => false,
            'status' => 401,
            'error' => 'Unauthorized (Token not found)'
        ];
    }

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

    if ($data !== null) {
        $jsonData = json_encode($data);
        $options[CURLOPT_POSTFIELDS] = $jsonData;
    } else {
        $jsonData = null;
    }

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);
    $isJson = is_array($decoded);

    $logLevel = $curlError ? "ERROR" : "INFO";
    logMessage($logLevel, "Method: $method | URL: $url | Payload: $jsonData | HTTP Code: $httpCode | Raw Response: $response | Decoded: " . ($isJson ? json_encode($decoded) : 'Invalid JSON') . " | cURL Error: $curlError");

    return [
        'success' => !$curlError && $httpCode < 400,
        'status' => $httpCode,
        'data' => $isJson ? $decoded : null,
        'raw' => $response,
        'error' => $curlError ?: ($decoded['detail'] ?? ($isJson ? json_encode($decoded) : 'Unknown error'))
    ];
}
