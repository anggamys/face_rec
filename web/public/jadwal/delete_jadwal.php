<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/jadwal.php";

require_role("dosen");

$id = $_GET["id_jadwal"] ?? null;
if ($id) {
    $response = deleteJadwal($id);
    if ($response) {
        header("Location: index.php?msg=deleted");
        exit();
    } else {
        header("Location: index.php?msg=error");
        exit();
    }
} else {
    header("Location: index.php?msg=invalid_id");
    exit();
}
