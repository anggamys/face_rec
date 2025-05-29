<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/absensi.php";
require_once "../../action/user.php";
require_once "../../action/jadwal.php";
require_once "../../action/kelas.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

$absensiList = getAllAbsensi();
$absensiList = $absensiList['data'] ?? [];
?>

<?php include "../../components/header.php"; ?>

<div class="d-flex">
  <?php include "../../components/sidebar.php"; ?>

  <div class="content flex-grow-1 p-4">
    <div class="container">
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="../dashboard/" class="text-decoration-none fw-semibold">Dashboard</a>
          </li>
          <li class="breadcrumb-item active fw-semibold" aria-current="page">Daftar Absensi</li>
        </ol>
      </nav>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark mb-0">📋 Daftar Absensi</h2>
        <a href="/absensi/sesi-absensi.php" class="btn btn-primary shadow-sm">
          <i class="bi bi-plus-circle me-1"></i> Buka Sesi Absensi
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>Nama Mahasiswa</th>
              <th>Kelas</th>
              <th>Mata Kuliah</th>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($absensiList)) : ?>
              <?php foreach ($absensiList as $absensi) : ?>
                <?php
                // Ambil data mahasiswa
                $namaMahasiswa = 'Tidak Diketahui';
                if (!empty($absensi['id_mahasiswa'])) {
                  $mahasiswaResp = getUserByNrp($absensi['id_mahasiswa']);
                  if (!empty($mahasiswaResp['data']['name'])) {
                    $namaMahasiswa = $mahasiswaResp['data']['name'];
                  }
                }

                // Ambil data jadwal, kelas, matkul
                $tanggal = '-';
                $namaKelas = '-';
                $namaMatkul = '-';

                if (!empty($absensi['id_jadwal'])) {
                  $jadwalResp = getJadwalById($absensi['id_jadwal']);
                  $jadwalData = $jadwalResp['data'] ?? [];

                  $tanggal = $jadwalData['tanggal'] ?? '-';

                  if (!empty($jadwalData['kode_kelas'])) {
                    $kelasResp = getKelasByKodeKelas($jadwalData['kode_kelas']);
                    $namaKelas = $kelasResp['data']['nama_kelas'] ?? '-';
                  }

                  if (!empty($jadwalData['id_matkul'])) {
                    $matkulResp = getMataKuliahById($jadwalData['id_matkul']);
                    $namaMatkul = $matkulResp['nama_matkul'] ?? '-';
                  }
                }

                // Status dan badge
                $status = ucfirst(strtolower($absensi['status'] ?? 'Tidak Diketahui'));
                $badgeClass = match (strtolower($status)) {
                  'hadir' => 'bg-success',
                  'alpha' => 'bg-danger',
                  default => 'bg-secondary',
                };
                ?>
                <tr>
                  <td><?= htmlspecialchars($namaMahasiswa) ?></td>
                  <td><?= htmlspecialchars($namaKelas) ?></td>
                  <td><?= htmlspecialchars($namaMatkul) ?></td>
                  <td><?= htmlspecialchars($tanggal) ?></td>
                  <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada data absensi.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include "../../components/footer.php"; ?>