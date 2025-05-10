<?php
require_once __DIR__ . "/../libs/helper.php";

$authurl = "http://localhost:8000/auth";

function login($email, $password)
{
    global $authurl;

    try {
        $response = sendRequest(
            "POST",
            "$authurl/login",
            [
                "email" => $email,
                "password" => $password,
            ],
            false
        ); // false jika endpoint login tidak butuh token

        if ($response["success"]) {
            return $response["data"]; // clean return
        }

        return [
            "error" =>
                $response["error"] ??
                "Login gagal. Cek email dan password Anda.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Login exception: " . $e->getMessage());
        return [
            "error" => "Terjadi kesalahan saat login. Coba lagi nanti.",
        ];
    }
}

function register($name, $email, $password, $role, $nrp, $nip)
{
    global $authurl;

    try {
        $payload = [
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "role" => $role,
            "nrp" => $role === "mahasiswa" ? $nrp : null,
            "nip" => $role === "dosen" ? $nip : null,
        ];

        $response = sendRequest("POST", "$authurl/register", $payload, false); // false = tanpa token

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
                "Pendaftaran gagal. Email mungkin sudah digunakan.",
        ];
    } catch (Exception $e) {
        logMessage("ERROR", "Registration exception: " . $e->getMessage());
        return [
            "success" => false,
            "error" =>
                "Terjadi kesalahan saat registrasi. Silakan coba lagi nanti.",
        ];
    }
}
