<?php
require_once "../auth_check.php";
require_once "../action/kelas.php";
require_once "../action/mata-kuliah.php";

require_role("dosen");

// Ambil semua data kelas
$kelasList = getAllKelas();

include "../components/header.php";
?>

<div class="d-flex">
    <?php include "../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Daftar Kelas</h2>
                <a href="/kelas/form_kelas.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Kelas
                </a>
            </div>

            <?php if (is_array($kelasList) && count($kelasList) > 0): ?>
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
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
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($kelas['nama_kelas'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($kelas['kode_kelas'] ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($kelas['id_kelas'])): ?>
                                            <a href="/kelas/form_kelas.php?kode_kelas=<?= urlencode($kelas['kode_kelas']) ?>" class="btn btn-sm btn-warning me-1">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <a href="/kelas/delete_kelas.php?kode_kelas=<?= urlencode($kelas['kode_kelas']) ?>" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus kelas ini?');">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Belum ada data kelas yang tersedia.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../components/footer.php"; ?>