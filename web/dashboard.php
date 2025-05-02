<?php
require_once "./auth_check.php";
require_role("mahasiswa");

$user = $_SESSION["user"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h4 class="text-center mt-3">Presensi App</h4>
  <a href="dashboard.php"><i class="bi bi-house-door-fill"></i> <span>Dashboard</span></a>
  <a href="#"><i class="bi bi-calendar-check-fill"></i> <span>Presensi</span></a>
  <a href="#"><i class="bi bi-person-lines-fill"></i> <span>Profil</span></a>
  <a href="logout.php"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a>
</div>

<!-- Main Content -->
<div class="content">
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4>Selamat datang, <?= htmlspecialchars($user["name"]) ?></h4>
        <p><strong>Email:</strong> <?= htmlspecialchars($user["email"]) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user["role"]) ?></p>
        <?php if ($user["role"] === "mahasiswa"): ?>
          <p><strong>NRP:</strong> <?= htmlspecialchars($user["nrp"]) ?></p>
        <?php elseif ($user["role"] === "dosen"): ?>
          <p><strong>NIP:</strong> <?= htmlspecialchars($user["nip"]) ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
