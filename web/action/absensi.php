<?php
require_once __DIR__ . "/../libs/helper.php";

$absensiurl = "http://localhost:8000/absensi";

function getAllAbsensi()
{
    global $absensiurl;
    $response = sendRequest("GET", "$absensiurl/");

    if ($response["success"]) {
        return $response["data"];
    }

    logMessage("Error", "Failed to fetch all absensi");
}
