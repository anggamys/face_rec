<?php
session_start();

require_once "../auth_check.php";
require_once "../../libs/helper.php";
require_once "../../action/kelas.php";
require_once "../../action/jadwal.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

$successMessage = "";
$errorMessage = "";
$idJadwal = $_GET["id_jadwal"] ?? null;
$jadwal = null;

logMessage(
    "INFO",
    $idJadwal ? "Form mode edit untuk jadwal ID: $idJadwal" : "Form dalam mode tambah jadwal (create)."
);

$allKelas = getAllKelas();
if (!$allKelas || !is_array($allKelas)) {
    $errorMessage = "Gagal mengambil data kelas.";
    logMessage("ERROR", "Gagal ambil data dari getAllKelas().");
    $allKelas = [];
} else {
    logMessage("INFO", "Berhasil ambil data kelas." . json_encode($allKelas));
}

$allMatkul = getAllMataKuliah();
if (!$allMatkul || !is_array($allMatkul)) {
    $errorMessage = "Gagal mengambil data mata kuliah.";
    logMessage("ERROR", "Gagal ambil data dari getAllMataKuliah().");
    $allMatkul = [];
} else {
    logMessage("INFO", "Berhasil ambil data mata kuliah." . json_encode($allMatkul));
}

if ($idJadwal) {
    $jadwal = getJadwalById($idJadwal);
    if ($jadwal) {
        $successMessage = "Data jadwal berhasil diambil.";
        logMessage("INFO", "Berhasil ambil data jadwal ID $idJadwal." . json_encode($jadwal));
    } else {
        $errorMessage = "Data jadwal tidak ditemukan.";
        logMessage("ERROR", "Jadwal dengan ID $idJadwal tidak ditemukan.");
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode_kelas = $_POST["kelas"] ?? null;
    $id_matkul = $_POST["matkul"] ?? null;
    $tanggal = $_POST["tanggal"] ?? null;
    $week = $_POST["week"] ?? null;

    if (!$kode_kelas || !$id_matkul || !$tanggal || !$week) {
        $errorMessage = "Semua field harus diisi.";
        logMessage("ERROR", "Form tidak valid: " . json_encode($_POST));
    } else {
        if ($idJadwal) {
            $result = updateJadwal($idJadwal, $kode_kelas, $id_matkul, $tanggal, $week);
            if ($result) {
                logMessage("INFO", "Jadwal dengan ID $idJadwal berhasil diperbarui.");
                header("Location: index.php?msg=updated");
                exit();
            } else {
                $errorMessage = "Gagal memperbarui data jadwal.";
                logMessage("ERROR", "Gagal memperbarui jadwal dengan ID $idJadwal.");
            }
        } else {
            $result = addJadwal($kode_kelas, $id_matkul, $tanggal, $week);
            if ($result) {
                logMessage("INFO", "Jadwal baru berhasil ditambahkan.");
                header("Location: index.php?msg=created");
                exit();
            } else {
                $errorMessage = "Gagal menambahkan data jadwal.";
                logMessage("ERROR", "Gagal menambahkan jadwal baru.");
            }
        }
    }
}

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>
    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Form Jadwal</h2>
                <a href="/jadwal/index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php elseif ($errorMessage): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form action="" method="post" class="needs-validation" novalidate>
                <!-- Kelas -->
                <div class="mb-3">
                    <label for="kelas" class="form-label">Pilih Kelas</label>
                    <select id="kelas" name="kelas" class="form-select" required autofocus>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($allKelas as $kelas): ?>
                            <?php
                            $kode = htmlspecialchars($kelas["kode_kelas"]);
                            $nama = htmlspecialchars($kelas["nama_kelas"]);
                            $selected = ($jadwal && $jadwal['data']["kode_kelas"] === $kode) ? "selected" : "";
                            ?>
                            <option value="<?= $kode ?>" <?= $selected ?>><?= "$kode - $nama" ?></option>
                        <?php endforeach; ?>
                        <?php if (empty($allKelas)): ?>
                            <option disabled>Tidak ada data kelas tersedia.</option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">Kelas wajib dipilih.</div>
                </div>

                <!-- Mata Kuliah -->
                <div class="mb-3">
                    <label for="matkul" class="form-label">Pilih Mata Kuliah</label>
                    <select id="matkul" name="matkul" class="form-select" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php foreach ($allMatkul as $matkul): ?>
                            <?php
                            $id = htmlspecialchars($matkul["id_matkul"]);
                            $nama = htmlspecialchars($matkul["nama_matkul"]);
                            $selected = ($jadwal && $jadwal['data']["id_matkul"] == $id) ? "selected" : "";
                            ?>
                            <option value="<?= $id ?>" <?= $selected ?>><?= "$id - $nama" ?></option>
                        <?php endforeach; ?>
                        <?php if (empty($allMatkul)): ?>
                            <option disabled>Tidak ada data mata kuliah tersedia.</option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">Mata kuliah wajib dipilih.</div>
                </div>

                <!-- Tanggal -->
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal Pertemuan</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" required
                        value="<?= htmlspecialchars($jadwal['data']["tanggal"] ?? '') ?>">
                    <div class="invalid-feedback">Tanggal pertemuan harus diisi.</div>
                </div>

                <!-- Week -->
                <div class="mb-3">
                    <label for="week" class="form-label">Minggu Ke-</label>
                    <input type="number" id="week" name="week" class="form-control" min="1" required
                        value="<?= htmlspecialchars($jadwal['data']["week"] ?? '') ?>">
                    <div class="invalid-feedback">Isi minggu ke berapa (minimal 1).</div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan Jadwal
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<?php include "../../components/footer.php"; ?>