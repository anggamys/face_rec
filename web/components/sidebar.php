<?php
$user = $_SESSION["user"] ?? null;

$nav_items = [
    [
        "label" => "Dashboard",
        "icon" => "bi-house-door-fill",
        "href" => "/dashboard.php",
        "active_check" => fn() => basename($_SERVER["PHP_SELF"]) ===
            "dashboard.php",
        "role" => null,
    ],
    [
        "label" => "Mata Kuliah",
        "icon" => "bi-book",
        "href" => "/mata-kuliah",
        "active_check" => fn() => strpos(
            $_SERVER["REQUEST_URI"],
            "/mata-kuliah"
        ) !== false,
        "role" => "dosen",
    ],
    [
        "label" => "Kelas",
        "icon" => "bi-journal-text",
        "href" => "/kelas",
        "active_check" => fn() => strpos($_SERVER["REQUEST_URI"], "/kelas") !==
            false,
        "role" => "dosen",
    ],
    [
        "label" => "Jadwal",
        "icon" => "bi-calendar-check",
        "href" => "/jadwal",
        "active_check" => fn() => strpos($_SERVER["REQUEST_URI"], "/jadwal") !==
            false,
        "role" => "dosen",
    ],
    [
        "label" => "Absensi",
        "icon" => "bi-clipboard-check",
        "href" => "/absensi",
        "active_check" => fn() => strpos($_SERVER["REQUEST_URI"], "/jadwal") !==
            false,
        "role" => "dosen",
    ],
    [
        "label" => "Profil",
        "icon" => "bi-person-lines-fill",
        "href" => "#",
        "active_check" => fn() => false,
        "role" => null,
    ],
];
?>

<div class="sidebar bg-light p-4 shadow-sm d-flex flex-column" style="min-height: 100vh;">
    <div class="text-center mb-5">
        <h4 class="fw-bold">Presensi App</h4>
        <?php if ($user): ?>
            <small class="text-muted">Halo, <?= htmlspecialchars(
                $user["name"] ?? "Pengguna"
            ) ?></small>
        <?php endif; ?>
    </div>

    <ul class="nav flex-column gap-2">
        <?php foreach ($nav_items as $item): ?>
            <?php
            if ($item["role"] && (!$user || $user["role"] !== $item["role"])) {
                continue;
            }
            $isActive = $item["active_check"]();
            ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center <?= $isActive
                    ? "active fw-semibold"
                    : "" ?>" href="<?= $item["href"] ?>">
                    <i class="bi <?= $item["icon"] ?> me-2"></i> <?= $item[
     "label"
 ] ?>
                </a>
            </li>
        <?php endforeach; ?>

        <li class="nav-item mt-auto">
            <a class="nav-link text-danger d-flex align-items-center" href="/logout.php">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>
