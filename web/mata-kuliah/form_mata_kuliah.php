<?php
require_once "../auth_check.php";
require_once "../action/mata-kuliah.php";

require_role("dosen");

// Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_matkul = $_GET['id'] ?? null;
$mataKuliah = null;
$success = false;
$errorMessage = "";

// Jika ID diberikan, ambil data untuk edit
if ($id_matkul) {
    $mataKuliah = getMataKuliahById($id_matkul);
    if (!$mataKuliah) {
        header("Location: mata_kuliah.php?error=notfound");
        exit;
    }
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_matkul = trim($_POST['nama_matkul'] ?? '');

    // Validasi input
    if ($nama_matkul === '') {
        $errorMessage = "Nama mata kuliah tidak boleh kosong.";
    } else {
        $response = $mataKuliah
            ? updateMataKuliah($id_matkul, $nama_matkul)
            : addMataKuliah($nama_matkul);

        if ($response) {
            header("Location: mata_kuliah.php?success=" . ($mataKuliah ? 'updated' : 'created'));
            exit;
        } else {
            $errorMessage = $mataKuliah
                ? "Gagal memperbarui mata kuliah."
                : "Gagal menambahkan mata kuliah.";
        }
    }
}

include "../components/header.php";
?>

<div class="d-flex">
    <?php include "../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <h2 class="mb-4"><?= $mataKuliah ? "Edit Mata Kuliah" : "Tambah Mata Kuliah" ?></h2>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama_matkul" class="form-label">Nama Mata Kuliah</label>
                    <input
                        type="text"
                        id="nama_matkul"
                        name="nama_matkul"
                        class="form-control"
                        value="<?= htmlspecialchars($mataKuliah['nama_matkul'] ?? '') ?>"
                        required
                        autofocus>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="mata_kuliah.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<?php include "../components/footer.php"; ?>