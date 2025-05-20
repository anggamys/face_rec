<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/absen-session.php";
require_once "../../action/jadwal.php";

require_role("mahasiswa");

// Ambil semua sesi absensi
$response = getAllAbsensiSession();
$sessionData = $response["data"] ?? [];

// Filter hanya sesi yang aktif
$openSessions = array_filter($sessionData, function ($session) {
    return isset($session['is_active']) && $session['is_active'] === true;
});

// Reset index array
$openSessions = array_values($openSessions);

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Daftar Sesi Absensi</h2>
                <a href="/absensi/index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (empty($openSessions)): ?>
                <div class="alert alert-warning">Tidak ada sesi absensi yang tersedia.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nama Matkul</th>
                                <th>Kelas</th>
                                <th>Jam Mulai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($openSessions as $index => $session): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($session["id_jadwal"] ?? "-") ?></td>
                                    <td><?= htmlspecialchars($session["tanggal"] ?? "-") ?></td>
                                    <td><?= htmlspecialchars($session["waktu_mulai"] ?? "-") ?></td>
                                    <td class="text-center">
                                        <a href="/absensi/absen.php?id_session=<?= htmlspecialchars($session["id_session"] ?? "-") ?>"
                                            class="btn btn-primary btn-sm">
                                            Absen
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>