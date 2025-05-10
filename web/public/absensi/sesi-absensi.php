<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/jadwal.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

$allJadwal = getAllJadwal();
$allMataKuliah = getAllMataKuliah();

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <!-- Breadcrumb untuk navigasi -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="../dashboard/" style="font-size: 1.1rem;">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="daftar_absensi.php" style="font-size: 1.1rem;">Daftar Absensi</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="font-size: 1.1rem;">Buka Sesi Absensi</li>
                </ol>
            </nav>

            <h2 class="mb-4">Buka Sesi Absensi</h2>

            <div class="alert alert-info mb-4">
                <p class="mb-0"><i class="bi bi-info-circle-fill me-2"></i> Pilih jadwal dan mata kuliah untuk membuka sesi absensi baru.</p>
            </div>

            <form id="openAbsensiForm" action="process_buka_sesi_absensi.php" method="POST">
                <div class="mb-4">
                    <label for="id_jadwal" class="form-label">Pilih Jadwal</label>
                    <select class="form-select form-select-lg" id="id_jadwal" name="id_jadwal" required>
                        <option value="" selected disabled>-- Pilih Jadwal --</option>
                        <?php foreach ($allJadwal as $jadwal): ?>
                            <option value="<?= htmlspecialchars($jadwal["id_jadwal"]) ?>">
                                <?= "Jadwal #" . htmlspecialchars($jadwal["id_jadwal"]) . " - " . htmlspecialchars($jadwal["kode_kelas"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted mt-2">Pilih jadwal sesuai dengan kelas yang akan diampu.</small>
                </div>

                <div class="mb-4">
                    <label for="id_matkul" class="form-label">Pilih Mata Kuliah</label>
                    <select class="form-select form-select-lg" id="id_matkul" name="id_matkul" required>
                        <option value="" selected disabled>-- Pilih Mata Kuliah --</option>
                        <?php foreach ($allMataKuliah as $matkul): ?>
                            <option value="<?= htmlspecialchars($matkul["id_matkul"]) ?>">
                                <?= htmlspecialchars($matkul["nama_matkul"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted mt-2">Pilih mata kuliah yang akan diajarkan pada jadwal tersebut.</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="daftar_absensi.php" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-circle me-2"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle me-2"></i> Buka Sesi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../../components/footer.php"; ?>