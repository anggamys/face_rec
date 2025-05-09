<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

// Ambil semua mata kuliah
$mataKuliahList = getAllMataKuliah();

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 fw-bold">📚 Daftar Mata Kuliah</h2>
                <a href="/mata-kuliah/form_mata_kuliah.php" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Mata Kuliah
                </a>
            </div>

            <?php if (!empty($mataKuliahList) && is_array($mataKuliahList)): ?>
                <div class="table-responsive shadow rounded">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 5%;">#</th>
                                <th class="text-start">Nama Mata Kuliah</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mataKuliahList as $index => $mk): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($mk["nama_matkul"]) ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($mk["id_matkul"])): ?>
                                            <a href="/mata-kuliah/form_mata_kuliah.php?id=<?= urlencode($mk["id_matkul"]) ?>"
                                                class="btn btn-sm btn-outline-warning me-1">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="/mata-kuliah/delete_mata_kuliah.php?id=<?= urlencode($mk["id_matkul"]) ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Yakin ingin menghapus mata kuliah ini?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak tersedia</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4">
                    <i class="bi bi-info-circle-fill me-2"></i> Belum ada mata kuliah yang ditambahkan.<br>
                    Klik <strong>Tambah Mata Kuliah</strong> untuk mulai mengisi.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>