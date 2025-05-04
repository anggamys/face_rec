<?php
$user = $_SESSION['user'] ?? null;
?>

<div class="sidebar bg-light p-4 shadow-sm d-flex flex-column" style="min-height: 100vh;">
    <div class="text-center mb-5">
        <h4 class="fw-bold">Presensi App</h4>
        <?php if ($user): ?>
            <small class="text-muted">Halo, <?= htmlspecialchars($user['name'] ?? 'Pengguna'); ?></small>
        <?php endif; ?>
    </div>

    <ul class="nav flex-column gap-2">
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active fw-semibold' : '' ?>" href="../dashboard.php">
                <i class="bi bi-house-door-fill me-2"></i> Dashboard
            </a>
        </li>

        <?php if ($user && $user['role'] === 'dosen') : ?>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center <?= strpos($_SERVER['REQUEST_URI'], '/mata-kuliah') !== false ? 'active fw-semibold' : '' ?>" href="/mata-kuliah">
                    <i class="bi bi-book me-2"></i> Mata Kuliah
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center <?= strpos($_SERVER['REQUEST_URI'], '/kelas') !== false ? 'active fw-semibold' : '' ?>" href="/kelas">
                    <i class="bi bi-journal-text me-2"></i> Kelas
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="#">
                <i class="bi bi-person-lines-fill me-2"></i> Profil
            </a>
        </li>

        <li class="nav-item mt-auto">
            <a class="nav-link text-danger d-flex align-items-center" href="logout.php">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>