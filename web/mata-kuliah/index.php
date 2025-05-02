<?php
require_once "../auth_check.php";
require_once "../action/mata-kuliah.php";

require_role("dosen");

// Ambil semua mata kuliah
$mataKuliahList = getAllMataKuliah();

include "../components/header.php";
?>

<div class="d-flex">
    <?php include "../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Daftar Mata Kuliah</h2>
                <a href="/mata-kuliah/form_mata_kuliah.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Mata Kuliah
                </a>
            </div>

            <?php if (!empty($mataKuliahList) && is_array($mataKuliahList)): ?>
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Nama Mata Kuliah</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mataKuliahList as $index => $mk): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= htmlspecialchars($mk["nama_matkul"]); ?></td>
                                    <td>
                                        <?php if (!empty($mk["id_matkul"])): ?>
                                            <a href="/mata-kuliah/form_mata_kuliah.php?id=<?= urlencode($mk["id_matkul"]); ?>" class="btn btn-sm btn-warning me-1">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <a href="/mata-kuliah/delete_mata_kuliah.php?id=<?= urlencode($mk["id_matkul"]); ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus mata kuliah ini?');">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada aksi</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Belum ada data mata kuliah yang tersedia.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../components/footer.php"; ?>