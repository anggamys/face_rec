<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/jadwal.php";
require_once "../../action/kelas.php";
require_once "../../action/absen-session.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

$successMessage = $_SESSION['successMessage'] ?? '';
$errorMessage = $_SESSION['errorMessage'] ?? '';
unset($_SESSION['successMessage'], $_SESSION['errorMessage']);

// Ambil semua jadwal
$fetchResult = getAllJadwal();
$allJadwal = $fetchResult["data"] ?? [];

include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Daftar Jadwal</h2>
                <a href="/absensi/index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php elseif ($errorMessage): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <?php if (empty($allJadwal)): ?>
                <div class="alert alert-warning">Tidak ada jadwal tersedia.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nama Matkul</th>
                                <th>Kelas</th>
                                <th>Tanggal</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allJadwal as $index => $jadwal): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td>
                                        <?php
                                        $matkul = getMataKuliahById($jadwal['id_matkul']);
                                        echo $matkul
                                            ? htmlspecialchars($matkul['id_matkul'] . " - " . $matkul['nama_matkul'])
                                            : "<span class='text-danger'>Matkul tidak ditemukan</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $kelas = getKelasByKodeKelas($jadwal['kode_kelas']);
                                        echo $kelas && isset($kelas['data'])
                                            ? htmlspecialchars($kelas['data']['kode_kelas'] . " - " . $kelas['data']['nama_kelas'])
                                            : "<span class='text-danger'>Kelas tidak ditemukan</span>";
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($jadwal['tanggal'] ?? '-') ?></td>

                                    <?php
                                    $absensiSession = getAbsensiSessionByIdJadwal($jadwal['id_jadwal']);
                                    $sessionData = $absensiSession['data'] ?? null;
                                    ?>

                                    <td>
                                        <?= $sessionData && isset($sessionData['waktu_mulai'])
                                            ? formatTanggalIndonesia($sessionData['waktu_mulai'])
                                            : '-' ?>
                                    </td>
                                    <td>
                                        <?= $sessionData && isset($sessionData['waktu_berakhir'])
                                            ? formatTanggalIndonesia($sessionData['waktu_berakhir'])
                                            : '-' ?>
                                    </td>
                                    <td>
                                        <?php if ($sessionData && $sessionData['is_active']): ?>
                                            <!-- Tombol Tutup Sesi -->
                                            <form method="POST" action="open-session.php" class="d-inline">
                                                <input type="hidden" name="id_session" value="<?= htmlspecialchars($sessionData['id_session']) ?>">
                                                <input type="hidden" name="action" value="close">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Tutup Sesi Absensi
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- Tombol Buka Sesi -->
                                            <form method="POST" action="open-session.php" class="d-inline">
                                                <input type="hidden" name="id_jadwal" value="<?= htmlspecialchars($jadwal['id_jadwal']) ?>">
                                                <input type="hidden" name="action" value="open">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    Buka Sesi Absensi
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../../components/footer.php"; ?>
`