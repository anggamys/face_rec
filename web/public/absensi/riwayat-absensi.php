<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/absensi.php";

require_role("mahasiswa");

$fetchdata = getRekapAbsensi();
$absensi = $fetchdata["data"] ?? [];

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <!-- Breadcrumb dengan ikon untuk navigasi yang lebih jelas -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="../dashboard.php" style="font-size: 1.1rem;">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="font-size: 1.1rem;">Riwayat Absensi</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 fw-bold text-dark">📋 Riwayat Absensi</h2>
            </div>

            <?php if (!empty($absensi) && is_array($absensi)): ?>
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" style="width: 20%;">ID Mahasiswa</th>
                                <th scope="col" style="width: 20%;">Mata Kuliah</th>
                                <th scope="col" style="width: 20%;">Jadwal</th>
                                <th scope="col" style="width: 20%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absensi as $index => $data): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($data["id_mahasiswa"] ?? "-") ?></td>
                                    <td><?= htmlspecialchars($data["nama_matkul"] ?? "-") ?></td>
                                    <td><?= htmlspecialchars($data["kode_kelas"] ?? "-") ?></td>
                                    <td><?= htmlspecialchars($data["status"] ?? "-") ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    Tidak ada data absensi yang ditemukan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>