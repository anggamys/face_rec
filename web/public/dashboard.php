<?php
session_start();

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
    $welcomeText = "Selamat datang di Dashboard Mahasiswa 🎓";
    $buttonLink = "/presensi";
    $buttonText = "Mulai Presensi";
    $icon = "bi-person-check-fill";
    $description = "Akses presensi dan lihat riwayat kehadiranmu.";
} elseif ($role === "dosen") {
    $pageTitle = "Dashboard Dosen";
    $currentPage = "dashboard-dosen";
    $metaDescription = "Dashboard untuk dosen";
    $welcomeText = "Selamat datang di Dashboard Dosen 🧑‍🏫";
    $buttonLink = "/mata-kuliah";
    $buttonText = "Kelola Mata Kuliah";
    $icon = "bi-book-half";
    $description = "Atur mata kuliah, jadwal, dan sesi absensi.";
} else {
    header("Location: /unauthorized.php");
    exit;
}

include_once "../components/header.php";
?>

<div class="d-flex">
    <?php include_once "../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>
                <?= $welcomeText; ?>
            </div>

            <div class="card shadow-sm border-0 rounded p-4 mt-4">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <i class="bi <?= $icon ?> text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1"><?= $pageTitle; ?></h5>
                        <p class="text-muted mb-2"><?= $description; ?></p>
                        <a href="<?= $buttonLink; ?>" class="btn btn-primary"><?= $buttonText; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "../components/footer.php"; ?>