<?php
session_start();

$datauser = $_SESSION['user'] ?? null;

if (!$datauser) {
    header("Location: login.php");
    exit();
}

include "../components/header.php";
?>

<div class="d-flex">
    <?php include "../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 fw-bold">Data Pengguna</h2>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Informasi Pengguna</h5>
                    <p class="card-text">
                        <strong>Nama:</strong> <?= htmlspecialchars($datauser['name']) ?><br>
                        <strong>Email:</strong> <?= htmlspecialchars($datauser['email']) ?><br>
                        <strong>Role:</strong> <?= htmlspecialchars($datauser['role']) ?><br>
                        <?php if ($datauser['role'] === 'mahasiswa'): ?>
                            <strong>NRP:</strong> <?= htmlspecialchars($datauser['nrp']) ?><br>
                        <?php elseif ($datauser['role'] === 'dosen'): ?>
                            <strong>NIP:</strong> <?= htmlspecialchars($datauser['nip']) ?><br>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>