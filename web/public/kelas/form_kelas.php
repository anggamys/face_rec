<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/kelas.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

$kode_kelas = $_GET['kode_kelas'] ?? null;
$kelas = null;
$errorMessage = "";

// Mode Edit
if ($kode_kelas) {
    $kelasResponse = getKelasById($kode_kelas);
    if (!isset($kelasResponse['success']) || !$kelasResponse['success']) {
        header("Location: kelas.php?error=notfound");
        exit;
    }
    $kelas = $kelasResponse['data'] ?? null;
}

// Handle Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_kelas_input = trim($_POST['kode_kelas'] ?? '');
    $nama_kelas = trim($_POST['nama_kelas'] ?? '');
    $id_matkul_array = $_POST['id_matkul'] ?? [];

    if ($kode_kelas_input === '' || $nama_kelas === '' || empty($id_matkul_array)) {
        $errorMessage = "Semua field wajib diisi.";
    } else {
        // Kirim sebagai array atau join jadi string tergantung backend
        $id_matkul_payload = is_array($id_matkul_array) ? $id_matkul_array : [$id_matkul_array];

        if ($kode_kelas && $kelas) {
            $response = updateKelas($kode_kelas, $kode_kelas_input, $nama_kelas, $id_matkul_payload);
        } else {
            $response = addKelas($kode_kelas_input, $nama_kelas, $id_matkul_payload);
        }

        if ((is_array($response) && isset($response['success']) && $response['success']) || $response === true) {
            $redirect_action = $kode_kelas ? "updated" : "created";
            header("Location: kelas.php?success=$redirect_action");
            exit;
        } else {
            $errorMessage = $kode_kelas
                ? "Gagal memperbarui kelas: " . ($response['error'] ?? 'Unknown error')
                : "Gagal menambahkan kelas: " . ($response['error'] ?? 'Unknown error');
        }
    }
}

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <h2 class="mb-4"><?= $kode_kelas ? 'Edit Kelas' : 'Tambah Kelas' ?></h2>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
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

                <div class="mb-3">
                    <label for="id_matkul" class="form-label">Mata Kuliah</label>
                    <div class="mb-3">
                        <div class="form-check">
                            <?php
                            $mataKuliahList = getAllMataKuliah();
                            $selectedMatkul = [];

                            // Ambil yang sudah terpilih kalau mode edit
                            if (isset($kelas['matakuliah']) && is_array($kelas['matakuliah'])) {
                                foreach ($kelas['matakuliah'] as $m) {
                                    $selectedMatkul[] = is_array($m) ? $m['id_matkul'] : $m;
                                }
                            }

                            foreach ($mataKuliahList as $matkul):
                                $isChecked = in_array($matkul['id_matkul'], $selectedMatkul) ? 'checked' : '';
                            ?>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="id_matkul[]"
                                        id="matkul_<?= htmlspecialchars($matkul['id_matkul']) ?>"
                                        value="<?= htmlspecialchars($matkul['id_matkul']) ?>"
                                        <?= $isChecked ?>>
                                    <label class="form-check-label" for="matkul_<?= htmlspecialchars($matkul['id_matkul']) ?>">
                                        <?= htmlspecialchars($matkul['nama_matkul']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php include "../../components/footer.php"; ?>