<?php

require_once "../auth_check.php";
require_once "../../action/mata-kuliah.php";
require_once "../../action/jadwal.php";

require_role("dosen");

$response = getAllJadwal();
$jadwalList = $response["success"] ? $response["data"] : [];

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Daftar Jadwal</h2>
                <a href="/jadwal/form_jadwal.php" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
                </a>
            </div>

            <?php if (!empty($jadwalList)): ?>
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col">Kode Kelas</th>
                                <th scope="col">Matakuliah</th>
                                <th scope="col">Minggu Ke</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwalList as $i => $jadwal): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars(
                                            $jadwal["kode_kelas"] ?? "-"
                                        ) ?></td>
                                    <td>
                                        <?php

                                        $matkul = getMataKuliahById(
                                            $jadwal["id_matkul"]
                                        );

                                        if ($matkul !== null) {
                                            echo htmlspecialchars(
                                                $matkul["nama_matkul"]
                                            );
                                        } else {
                                            echo "<span class='text-danger'>Mata kuliah tidak ditemukan</span>";
                                        }
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars(
                                            $jadwal["week"] ?? "-"
                                        ) ?></td>
                                    <td><?= !empty($jadwal["tanggal"])
                                            ? date(
                                                "d M Y",
                                                strtotime($jadwal["tanggal"])
                                            )
                                            : "-" ?></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="/jadwal/form_jadwal.php?id_jadwal=<?= urlencode(
                                                                                            $jadwal["id_jadwal"]
                                                                                        ) ?>"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Edit Jadwal">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="/jadwal/delete_jadwal.php?id_jadwal=<?= urlencode(
                                                                                                $jadwal["id_jadwal"]
                                                                                            ) ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Hapus Jadwal"
                                                onclick="return confirm('Yakin ingin menghapus jadwal ini?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-1"></i>
                    Tidak ada data jadwal yang tersedia.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../../components/footer.php"; ?>