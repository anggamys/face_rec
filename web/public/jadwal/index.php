<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/kelas.php";


require_role("dosen");
