<?php

function logMessage(string $level, string $message): void
{
    $timestamp = date("Y-m-d H:i:s");
    error_log("[{$timestamp}] [$level] $message");
}

function sendRequest(
    string $method,
    string $url,
    ?array $data = null,
    bool $requireAuth = true
): array {
    $token = $_SESSION["token"] ?? ($_COOKIE["token"] ?? null);

    if ($requireAuth && !$token) {
        return [
            "success" => false,
            "status" => 401,
            "error" => "Unauthorized: token tidak tersedia",
        ];
    }

    $headers = ["Content-Type: application/json"];
    if ($requireAuth && $token) {
        $headers[] = "Authorization: Bearer $token";
    }

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $data ? json_encode($data) : null,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);

    $isSuccess = !$curlError && $httpCode < 400;
    $errorMsg =
        $curlError ?: $decoded["detail"] ?? ($decoded["message"] ?? null);

    // Logging
    logMessage(
        $isSuccess ? "INFO" : "ERROR",
        sprintf(
            "Method: %s | URL: %s | Payload: %s | Status: %d | Error: %s",
            strtoupper($method),
            $url,
            $data ? json_encode($data) : "null",
            $httpCode,
            $errorMsg ?: "None"
        )
    );

    return [
        "success" => $isSuccess,
        "status" => $httpCode,
        "data" => $decoded,
        "error" => $isSuccess ? null : $errorMsg,
    ];
}

function formatTanggalIndonesia($tanggalIso)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    try {
        // Ubah ke zona waktu Indonesia
        $date = new DateTime($tanggalIso, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Asia/Jakarta'));

        $day = $date->format('d');
        $month = $bulan[(int)$date->format('m')];
        $year = $date->format('Y');
        $time = $date->format('H:i');

        return "$day $month $year, $time WIB";
    } catch (Exception $e) {
        return "Format tanggal tidak valid";
    }
}
