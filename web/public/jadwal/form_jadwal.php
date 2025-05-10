<?php
session_start();

// Load dependencies
require_once "../auth_check.php";
require_once "../../libs/helper.php";
require_once "../../action/kelas.php";
require_once "../../action/jadwal.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

// Ambil data awal
$successMessage = "";
$errorMessage = "";
$kodeJadwal = $_GET["id_jadwal"] ?? null;
$jadwal = null;

// Debug log
logMessage(
    "INFO",
    $kodeJadwal
        ? "Form mode edit untuk jadwal ID: $kodeJadwal"
        : "Form dalam mode tambah jadwal (create)."
);

// Ambil semua kelas
$allKelas = getAllKelas();
if (!$allKelas || !is_array($allKelas)) {
    $errorMessage = "Gagal mengambil data kelas. Silakan coba lagi.";
    logMessage("ERROR", "Gagal ambil data dari getAllKelas().");
    $allKelas = [];
}

// Ambil semua mata kuliah
$allMatkul = getAllMataKuliah();
if (!$allMatkul || !is_array($allMatkul)) {
    $errorMessage = "Gagal mengambil data mata kuliah.";
    logMessage("ERROR", "Gagal ambil data dari getAllMataKuliah().");
    $allMatkul = [];
}

// Ambil data jadwal jika ada
if ($kodeJadwal) {
    $jadwal = getJadwalById($kodeJadwal);
    if ($jadwal) {
        $successMessage = "Data jadwal berhasil diambil.";
    } else {
        $errorMessage = "Data jadwal tidak ditemukan.";
        logMessage("ERROR", "Jadwal dengan ID $kodeJadwal tidak ditemukan.");
    }
}

// Handle form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kelas = $_POST["kelas"] ?? null;
    $id_matkul = $_POST["matkul"] ?? null;
    $tanggal = $_POST["tanggal"] ?? null;
    $week = $_POST["week"] ?? null;

    // Validasi input
    if (!$kelas || !$id_matkul || !$tanggal || !$week) {
        $errorMessage = "Semua field harus diisi.";
        logMessage("ERROR", "Form tidak valid: " . json_encode($_POST));
    } else {
        // Simpan data ke database
        if ($kodeJadwal) {
            // Update jadwal
            $result = updateJadwal(
                $kodeJadwal,
                $kelas,
                $id_matkul,
                $tanggal,
                $week
            );
            if ($result) {
                $successMessage = "Data jadwal berhasil diperbarui.";
                logMessage(
                    "INFO",
                    "Jadwal dengan ID $kodeJadwal berhasil diperbarui."
                );
                header("Location: index.php?msg=updated");
                exit();
            } else {
                $errorMessage = "Gagal memperbarui data jadwal.";
                logMessage(
                    "ERROR",
                    "Gagal memperbarui jadwal dengan ID $kodeJadwal."
                );
            }
        } else {
            // Tambah jadwal baru
            $result = addJadwal($kelas, $id_matkul, $tanggal, $week);
            if ($result) {
                $successMessage = "Data jadwal berhasil ditambahkan.";
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
                <div class="alert alert-success"><?= htmlspecialchars(
                                                        $successMessage
                                                    ) ?></div>
            <?php elseif ($errorMessage): ?>
                <div class="alert alert-danger"><?= htmlspecialchars(
                                                    $errorMessage
                                                ) ?></div>
            <?php endif; ?>

            <form action="" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="kelas" class="form-label">Pilih Kelas</label>
                    <select id="kelas" name="kelas" class="form-select" required autofocus>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($allKelas as $kelas):

                            $kode = htmlspecialchars($kelas["kode_kelas"]);
                            $nama = htmlspecialchars($kelas["nama_kelas"]);
                            $selected =
                                isset($jadwal["kode_kelas"]) &&
                                $jadwal["kode_kelas"] === $kode
                                ? "selected"
                                : "";
                        ?>
                            <option value="<?= $kode ?>" <?= $selected ?>><?= "$kode - $nama" ?></option>
                        <?php
                        endforeach; ?>
                        <?php if (empty($allKelas)): ?>
                            <option disabled>Tidak ada data kelas tersedia.</option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">Kelas wajib dipilih.</div>
                </div>

                <div class="mb-3">
                    <label for="matkul" class="form-label">Pilih Mata Kuliah</label>
                    <select id="matkul" name="matkul" class="form-select" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php foreach ($allMatkul as $matkul):

                            $kode = htmlspecialchars($matkul["id_matkul"]);
                            $nama = htmlspecialchars($matkul["nama_matkul"]);
                            $selected =
                                isset($jadwal["kode_matkul"]) &&
                                $jadwal["kode_matkul"] === $kode
                                ? "selected"
                                : "";
                        ?>
                            <option value="<?= $kode ?>" <?= $selected ?>><?= "$kode - $nama" ?></option>
                        <?php
                        endforeach; ?>
                        <?php if (empty($allMatkul)): ?>
                            <option disabled>Tidak ada data mata kuliah tersedia.</option>
                        <?php endif; ?>
                    </select>
                    <div class="invalid-feedback">Mata kuliah wajib dipilih.</div>
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal Pertemuan</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" required
                        value="<?= isset($jadwal["tanggal"])
                                    ? htmlspecialchars($jadwal["tanggal"])
                                    : "" ?>">
                    <div class="invalid-feedback">Tanggal harus diisi.</div>
                </div>

                <div class="mb-3">
                    <label for="week" class="form-label">Minggu Ke-</label>
                    <input type="number" id="week" name="week" class="form-control" min="1" required
                        value="<?= isset($jadwal["week"])
                                    ? htmlspecialchars($jadwal["week"])
                                    : "" ?>">
                    <div class="invalid-feedback">Isi minggu ke berapa (angka minimal 1).</div>
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