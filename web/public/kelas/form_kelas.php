<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/kelas.php";
require_once "../../action/mata-kuliah.php";
require_once "../../action/user.php";

require_role("dosen");

$kode_kelas = $_GET['kode_kelas'] ?? null;
$errorMessage = "";
$kelas = null;
$selectedMatkul = [];
$selectedMahasiswa = [];

// Ambil data awal
$mataKuliahList = getAllMataKuliah();
$fetchMahasiswa = getAllMahasiswa();
$allMahasiswa = $fetchMahasiswa['data'] ?? [];

// Jika mode edit
if ($kode_kelas) {
    $kelasResponse = getKelasByKodeKelas($kode_kelas);
    if (!isset($kelasResponse['success']) || !$kelasResponse['success']) {
        header("Location: kelas.php?error=notfound");
        exit;
    }
    $kelas = $kelasResponse['data'] ?? null;

    // Isi selected jika data kelas berhasil ditemukan
    $selectedMatkul = array_map('strval', $kelas['matakuliah'] ?? []);
    $selectedMahasiswa = array_map('strval', $kelas['mahasiswa'] ?? []);
}

// Handle Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_kelas_input = trim($_POST['kode_kelas'] ?? '');
    $nama_kelas = trim($_POST['nama_kelas'] ?? '');
    $id_matkul_array = $_POST['id_matkul'] ?? [];
    $nrp_mahasiswa_array = $_POST['nrp_mahasiswa'] ?? [];

    if ($kode_kelas_input === '' || $nama_kelas === '' || empty($id_matkul_array)) {
        $errorMessage = "Semua field wajib diisi, termasuk minimal satu mata kuliah.";
    } else {
        $id_matkul_payload = array_map('intval', (array) $id_matkul_array);
        $nrp_mahasiswa_payload = array_map('intval', (array) $nrp_mahasiswa_array);

        if ($kode_kelas && $kelas) {
            $response = updateKelas($kode_kelas, $kode_kelas_input, $nama_kelas, $id_matkul_payload, $nrp_mahasiswa_payload);
        } else {
            $response = addKelas($kode_kelas_input, $nama_kelas, $id_matkul_payload, $nrp_mahasiswa_payload);
        }

        if (!empty($response['success'])) {
            $action = $kode_kelas ? "updated" : "created";
            header("Location: kelas.php?success=$action");
            exit;
        } else {
            $errorMessage = "Gagal menyimpan data kelas: " . ($response['error'] ?? 'Unknown error');
        }
    }
}
?>
<?php include "../../components/header.php"; ?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>
    <div class="content flex-grow-1 p-4">
        <div class="container">
            <h2 class="mb-4"><?= $kode_kelas ? 'Edit Kelas' : 'Tambah Kelas' ?></h2>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form method="POST">
                <?php if (!$kode_kelas): ?>
                    <div class="mb-3">
                        <label for="kode_kelas" class="form-label">Kode Kelas</label>
                        <input type="text" class="form-control" id="kode_kelas" name="kode_kelas"
                            value="<?= htmlspecialchars($kelas['kode_kelas'] ?? '') ?>" required>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="kode_kelas" value="<?= htmlspecialchars($kelas['kode_kelas']) ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="nama_kelas" class="form-label">Nama Kelas</label>
                    <input type="text" class="form-control" id="nama_kelas" name="nama_kelas"
                        value="<?= htmlspecialchars($kelas['nama_kelas'] ?? '') ?>" required>
                </div>

                <!-- Mata Kuliah -->
                <div class="mb-3">
                    <label class="form-label">Mata Kuliah</label>
                    <?php foreach ($mataKuliahList as $matkul):
                        $isChecked = in_array((string) $matkul['id_matkul'], $selectedMatkul) ? 'checked' : '';
                    ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="id_matkul[]"
                                id="matkul_<?= $matkul['id_matkul'] ?>"
                                value="<?= $matkul['id_matkul'] ?>"
                                <?= $isChecked ?>>
                            <label class="form-check-label" for="matkul_<?= $matkul['id_matkul'] ?>">
                                <?= htmlspecialchars($matkul['nama_matkul']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Mahasiswa -->
                <div class="mb-3">
                    <label class="form-label">Mahasiswa</label>
                    <?php foreach ($allMahasiswa as $mahasiswa):
                        $isChecked = in_array((string) $mahasiswa['nrp'], $selectedMahasiswa) ? 'checked' : '';
                    ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="nrp_mahasiswa[]"
                                id="mhs_<?= htmlspecialchars($mahasiswa['nrp']) ?>"
                                value="<?= htmlspecialchars($mahasiswa['nrp']) ?>"
                                <?= $isChecked ?>>
                            <label class="form-check-label" for="mhs_<?= htmlspecialchars($mahasiswa['nrp']) ?>">
                                <?= htmlspecialchars($mahasiswa['name']) ?> (<?= htmlspecialchars($mahasiswa['nrp']) ?>)
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php include "../../components/footer.php"; ?>