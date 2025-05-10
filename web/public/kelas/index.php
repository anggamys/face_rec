<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/kelas.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

$kelasList = getAllKelas();

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 fw-bold">📚 Daftar Kelas</h2>
                <a href="/kelas/form_kelas.php" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Kelas
                </a>
            </div>

            <?php if (is_array($kelasList) && count($kelasList) > 0): ?>
                <div class="table-responsive shadow rounded-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-primary text-center">
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col">Nama Kelas</th>
                                <th scope="col">Kode Kelas</th>
                                <th scope="col" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kelasList as $index => $kelas): ?>
                                <tr>
                                    <td class="text-center"><?= $index +
                                                                1 ?></td>
                                    <td><?= htmlspecialchars(
                                            $kelas["nama_kelas"] ?? "-"
                                        ) ?></td>
                                    <td><?= htmlspecialchars(
                                            $kelas["kode_kelas"] ?? "-"
                                        ) ?></td>
                                    <td class="text-center">
                                        <?php if (
                                            !empty($kelas["kode_kelas"])
                                        ): ?>
                                            <a href="/kelas/form_kelas.php?kode_kelas=<?= urlencode(
                                                                                            $kelas["kode_kelas"]
                                                                                        ) ?>"
                                                class="btn btn-sm btn-outline-warning me-2" data-bs-toggle="tooltip" title="Edit Kelas" aria-label="Edit Kelas">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="/kelas/delete_kelas.php?kode_kelas=<?= urlencode(
                                                                                            $kelas["kode_kelas"]
                                                                                        ) ?>"
                                                class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Hapus Kelas" aria-label="Hapus Kelas"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus kelas \" <?= htmlspecialchars(
                                                                                                                        $kelas["nama_kelas"]
                                                                                                                    ) ?>\"?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak tersedia</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info shadow-sm mt-3 text-center">
                    <i class="bi bi-info-circle me-1"></i> Belum ada data kelas yang tersedia.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../../components/footer.php"; ?>