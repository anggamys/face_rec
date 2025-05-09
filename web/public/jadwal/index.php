<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/jadwal.php";

require_role("dosen");

$allJadwal = getAllJadwal();

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Daftar Jadwal</h2>
                <a href="/jadwal/form_jadwal.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Jadwal
                </a>
            </div>

            <?php if (!empty($allJadwal) && is_array($allJadwal)): ?>
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" style="width: 20%;">Kode Kelas</th>
                                <th scope="col" style="width: 20%;">Minggu ke</th>
                                <th scope="col" style="width: 20%;">Tanggal</th>
                                <th scope="col" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allJadwal as $index => $jadwal): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars(
                                        $jadwal["kode_kelas"] ?? "-"
                                    ) ?></td>
                                    <td><?= htmlspecialchars(
                                        $jadwal["week"] ?? "-"
                                    ) ?></td>
                                    <td><?= htmlspecialchars(
                                        $jadwal["tanggal"] ?? "-"
                                    ) ?></td>
                                    <td>
                                        <a href="/jadwal/form_jadwal.php?id_jadwal=<?= urlencode(
                                            $jadwal["id_jadwal"]
                                        ) ?>" class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="/jadwal/delete_jadwal.php?id_jadwal=<?= urlencode(
                                            $jadwal["id_jadwal"]
                                        ) ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Yakin ingin menghapus jadwal ini?');">
                                           <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">
                    Tidak ada data Jadwal yang tersedia.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../../components/footer.php"; ?>
