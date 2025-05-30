<?php
$user = $_SESSION["user"] ?? null;

// Utility untuk cek aktif berdasarkan URL
function is_active_path(string $pathSegment, bool $exact = false): bool
{
    $uri = $_SERVER["REQUEST_URI"];
    return $exact ? basename(parse_url($uri, PHP_URL_PATH)) === $pathSegment : strpos($uri, $pathSegment) !== false;
}

$nav_items = [
    [
        "label" => "Dashboard",
        "icon" => "bi-house-door-fill",
        "href" => "/dashboard.php",
        "active_check" => fn() => is_active_path("dashboard.php", true),
        "role" => null,
    ],
    [
        "label" => "Mata Kuliah",
        "icon" => "bi-book",
        "href" => "/mata-kuliah",
        "active_check" => fn() => is_active_path("/mata-kuliah"),
        "role" => "dosen",
    ],
    [
        "label" => "Kelas",
        "icon" => "bi-journal-text",
        "href" => "/kelas",
        "active_check" => fn() => is_active_path("/kelas"),
        "role" => "dosen",
    ],
    [
        "label" => "Jadwal",
        "icon" => "bi-calendar-check",
        "href" => "/jadwal",
        "active_check" => fn() => is_active_path("/jadwal"),
        "role" => "dosen",
    ],
    [
        "label" => "Absensi",
        "icon" => "bi-clipboard-check",
        "href" => "/absensi",
        "active_check" => fn() => is_active_path("/absensi") && !is_active_path("riwayat-absensi.php") && !is_active_path("absen-available.php"),
        "role" => "dosen",
    ],
    [
        "label" => "Riwayat Absensi",
        "icon" => "bi-clock-history",
        "href" => "/absensi/riwayat-absensi.php",
        "active_check" => fn() => is_active_path("riwayat-absensi.php", true),
        "role" => "mahasiswa",
    ],
    [
        "label" => "Presensi",
        "icon" => "bi-camera-video-fill",
        "href" => "/absensi/absen-available.php",
        "active_check" => fn() => is_active_path("absen-available.php", true),
        "role" => "dosen",
    ],
    [
        "label" => "Profil",
        "icon" => "bi-person-lines-fill",
        "href" => "/profil.php",
        "active_check" => fn() => is_active_path("profil.php", true),
        "role" => null,
    ]
];
?>

<div class="sidebar bg-white border-end p-4 shadow-sm d-flex flex-column" style="min-height: 100vh;">
    <div class="text-center mb-5">
        <h4 class="fw-bold">Presensi App</h4>
        <?php if ($user): ?>
            <small class="text-muted">👋 Halo, <?= htmlspecialchars($user["name"] ?? "Pengguna") ?></small>
        <?php endif; ?>
    </div>

    <ul class="nav flex-column gap-2">
        <?php foreach ($nav_items as $item): ?>
            <?php
            if ($item["role"] && (!$user || $user["role"] !== $item["role"])) continue;
            $isActive = $item["active_check"]();
            ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded <?= $isActive ? "active fw-semibold bg-primary text-white shadow-sm" : "text-dark" ?>"
                    href="<?= $item["href"] ?>"
                    style="transition: all 0.2s ease-in-out;">
                    <i class="bi <?= $item["icon"] ?> <?= $isActive ? "text-white" : "text-secondary" ?>"></i>
                    <span><?= $item["label"] ?></span>
                </a>
            </li>
        <?php endforeach; ?>

        <li class="nav-item mt-auto pt-3 border-top">
            <a class="nav-link d-flex align-items-center text-danger gap-2 px-3 py-2" href="/logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>