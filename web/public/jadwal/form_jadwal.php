<?php
session_start();

require_once "../../libs/helper.php";
require_once "../../action/kelas.php";
require_once "../../action/jadwal.php";
require_once "../../action/mata-kuliah.php";
require_once "../auth_check.php";

require_role("dosen");

// === HANDLER: AJAX untuk ambil matkul berdasarkan kelas ===
if ($_GET['ajax'] ?? '' === 'get_matkul' && isset($_GET['kode_kelas'])) {
    header("Content-Type: application/json");

    $kode_kelas = $_GET['kode_kelas'];
    $kelas = getKelasByKodeKelas($kode_kelas);
    $allMatkul = getAllMataKuliah();

    if (!($kelas['success'] ?? false)) {
        logMessage("error", "Gagal ambil data kelas: " . ($kelas['message'] ?? 'Tidak diketahui'));
        echo json_encode([]);
        exit;
    } else {
        logMessage("info", "Data kelas ditemukan: " . json_encode($kelas));
    }

    $idMatkulKelas = $kelas['data']['matakuliah'] ?? [];
    if (!is_array($idMatkulKelas)) $idMatkulKelas = [];

    $filtered = array_filter($allMatkul, fn($m) => in_array($m["id_matkul"], $idMatkulKelas));
    logMessage("info", "Mata kuliah ditemukan: " . count($filtered));

    echo json_encode(array_values($filtered));
    exit;
}

// === AMBIL DATA KELAS ===
$allKelas = getAllKelas();
$allKelas = $allKelas && is_array($allKelas) ? $allKelas : [];

$idJadwal = $_GET["id_jadwal"] ?? null;
$jadwal = $idJadwal ? getJadwalById($idJadwal) : null;

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode_kelas = $_POST["kelas"] ?? null;
    $id_matkul = $_POST["matkul"] ?? null;
    $tanggal = $_POST["tanggal"] ?? null;
    $week = $_POST["week"] ?? null;

    if (!$kode_kelas || !$id_matkul || !$tanggal || !$week) {
        $errorMessage = "Semua field harus diisi.";
    } else {
        if ($idJadwal) {
            $result = updateJadwal($idJadwal, $kode_kelas, $id_matkul, $tanggal, $week);
            if ($result) {
                header("Location: index.php?msg=updated");
                exit();
            } else {
                $errorMessage = "Gagal memperbarui jadwal.";
            }
        } else {
            $result = addJadwal($kode_kelas, $id_matkul, $tanggal, $week);
            if ($result) {
                header("Location: index.php?msg=created");
                exit();
            } else {
                $errorMessage = "Gagal menambahkan jadwal.";
            }
        }
    }
}

$selectedIdMatkul = $jadwal['data']['id_matkul'] ?? null;
?>
<?php include "../../components/header.php"; ?>
<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>
    <div class="content flex-grow-1 p-4">
        <div class="container">
            <h2 class="mb-4">Form Jadwal</h2>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="kelas" class="form-label">Pilih Kelas</label>
                    <select id="kelas" name="kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($allKelas as $kelas): ?>
                            <?php
                            $kode = htmlspecialchars($kelas["kode_kelas"]);
                            $nama = htmlspecialchars($kelas["nama_kelas"]);
                            $selected = ($jadwal && $jadwal['data']["kode_kelas"] === $kode) ? "selected" : "";
                            ?>
                            <option value="<?= $kode ?>" <?= $selected ?>><?= "$kode - $nama" ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Kelas wajib dipilih.</div>
                </div>

                <div class="mb-3">
                    <label for="matkul" class="form-label">Pilih Mata Kuliah</label>
                    <select id="matkul" name="matkul" class="form-select" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                    </select>
                    <div class="invalid-feedback">Mata kuliah wajib dipilih.</div>
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal Pertemuan</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" required
                        value="<?= htmlspecialchars($jadwal['data']["tanggal"] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="week" class="form-label">Minggu Ke-</label>
                    <input type="number" id="week" name="week" class="form-control" min="1" required
                        value="<?= htmlspecialchars($jadwal['data']["week"] ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan Jadwal
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const selectedIdMatkul = <?= json_encode($selectedIdMatkul) ?>;

    function loadMatkulByKelas(kodeKelas) {
        const matkulSelect = document.getElementById('matkul');
        matkulSelect.innerHTML = '<option value="">-- Pilih Mata Kuliah --</option>';

        if (!kodeKelas) return;

        fetch(`form_jadwal.php?ajax=get_matkul&kode_kelas=${encodeURIComponent(kodeKelas)}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(matkul => {
                    const option = document.createElement('option');
                    option.value = matkul.id_matkul;
                    option.textContent = `${matkul.id_matkul} - ${matkul.nama_matkul}`;
                    if (selectedIdMatkul == matkul.id_matkul) {
                        option.selected = true;
                    }
                    matkulSelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Gagal ambil data matkul:', err);
                alert('Terjadi kesalahan saat mengambil data mata kuliah.');
            });
    }

    document.getElementById('kelas').addEventListener('change', function() {
        loadMatkulByKelas(this.value);
    });

    // Auto-load matkul jika form edit
    window.addEventListener("DOMContentLoaded", () => {
        const selectedKelas = document.getElementById('kelas').value;
        if (selectedKelas) {
            loadMatkulByKelas(selectedKelas);
        }
    });
</script>

<?php include "../../components/footer.php"; ?>