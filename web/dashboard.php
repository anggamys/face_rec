<?php
require_once "./auth_check.php";

$user = $_SESSION["user"] ?? null;

if (!$user) {
    header("Location: /login.php");
    exit;
}

$role = $user["role"] ?? "guest";

// Konfigurasi berdasarkan role
if ($role === "mahasiswa") {
    $pageTitle = "Dashboard Mahasiswa";
    $currentPage = "dashboard-mahasiswa";
    $metaDescription = "Dashboard untuk mahasiswa";
    $welcomeText = "Selamat datang di Dashboard Mahasiswa";
    $buttonLink = "/presensi";
    $buttonText = "Mulai Presensi";
} elseif ($role === "dosen") {
    $pageTitle = "Dashboard Dosen";
    $currentPage = "dashboard-dosen";
    $metaDescription = "Dashboard untuk dosen";
    $welcomeText = "Selamat datang di Dashboard Dosen";
    $buttonLink = "/mata-kuliah";
    $buttonText = "Kelola Mata Kuliah";
} else {
    // Role tidak dikenali, redirect
    header("Location: /unauthorized.php");
    exit;
}

include_once "./components/header.php";
?>

<div class="d-flex">
    <?php include_once "./components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <h2 class="mb-4"><?= $pageTitle; ?></h2>
            <p><?= $welcomeText; ?></p>
        </div>
    </div>
</div>

<?php include_once "./components/footer.php"; ?>