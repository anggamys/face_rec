<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/kelas.php";

require_role("dosen");

$id = $_GET['kode_kelas'] ?? null;
if ($id && deleteKelas($id)) {
    header("Location: index.php?msg=deleted");
    exit;
} else {
    header("Location: index.php?msg=error");
    exit;
}
