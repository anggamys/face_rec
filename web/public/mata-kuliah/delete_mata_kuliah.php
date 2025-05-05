<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

$id = $_GET['id'] ?? null;

if ($id && deleteMataKuliah($id)) {
    header("Location: index.php?msg=deleted");
    exit;
} else {
    header("Location: index.php?msg=error");
    exit;
}
