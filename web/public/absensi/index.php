<?php
session_start();

require_once "../auth_check.php";
require_once "../../action/absensi.php";
require_once "../../action/jadwal.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

$allAbsensi = getAllAbsensi();
$allMataKuliah = getAllMataKuliah();
$allJadwal = getAllJadwal();

// Membuat array cepat untuk akses nama mata kuliah dan jadwal
$mataKuliahArray = array_column($allMataKuliah, 'nama_matkul', 'id_matkul');
$jadwalArray = array_column($allJadwal, 'kode_kelas', 'id_jadwal');

include "../../components/header.php";
?>

<div class="d-flex">
  <?php include "../../components/sidebar.php"; ?>

  <div class="content flex-grow-1 p-4">
    <div class="container">
      <!-- Breadcrumb dengan ikon untuk navigasi yang lebih jelas -->
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="../dashboard/" style="font-size: 1.1rem;">Dashboard</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page" style="font-size: 1.1rem;">Daftar Absensi</li>
        </ol>
      </nav>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark">📋 Daftar Absensi</h2>
        <a href="/absensi/sesi-absensi.php" class="btn btn-primary shadow-sm">
          <i class="bi bi-plus-circle me-1"></i> Buka Sesi Absensi
        </a>
      </div>

      <?php if (!empty($allAbsensi) && is_array($allAbsensi)): ?>
        <div class="table-responsive shadow-sm rounded">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 5%;">#</th>
                <th scope="col" style="width: 20%;">ID Mahasiswa</th>
                <th scope="col" style="width: 20%;">Mata Kuliah</th>
                <th scope="col" style="width: 20%;">Jadwal</th>
                <th scope="col" style="width: 20%;">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($allAbsensi as $index => $absensi): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($absensi["id_mahasiswa"] ?? "-") ?></td>
                  <td><?= htmlspecialchars($mataKuliahArray[$absensi["id_matkul"]] ?? '-') ?></td>
                  <td><?= htmlspecialchars($jadwalArray[$absensi["id_jadwal"]] ?? '-') ?></td>
                  <td>
                    <?php
                    $status = $absensi["status"] ?? "-";
                    $badgeClass = "bg-secondary";
                    if ($status == "Hadir") {
                      $badgeClass = "bg-success";
                    } elseif ($status == "Tidak Hadir") {
                      $badgeClass = "bg-danger";
                    } elseif ($status == "Izin") {
                      $badgeClass = "bg-warning text-dark";
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-warning p-4" role="alert">
          <h5 class="alert-heading"><i class="bi bi-exclamation-circle-fill me-2"></i> Tidak Ada Absensi</h5>
          <p class="mb-0">Saat ini tidak ada data absensi. Untuk memulai, klik tombol "Buka Sesi Absensi" di atas untuk membuka sesi absensi baru.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>