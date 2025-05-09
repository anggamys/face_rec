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
        "active_check" => fn() => strpos($_SERVER["REQUEST_URI"], "/absensi") !==
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