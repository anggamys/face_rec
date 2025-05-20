<?php

require_once "../auth_check.php";
require_once "../../action/absensi.php";
require_once "../../action/user.php";
require_once "../../action/jadwal.php";
require_once "../../action/kelas.php";
require_once "../../action/mata-kuliah.php";

require_role("dosen");

// Get all absensi data
$allAbsensi = getAllAbsensi();
$allAbsensi = $allAbsensi["data"] ?? [];

include "../../components/header.php";
?>

<div class="d-flex">
  <?php include "../../components/sidebar.php"; ?>

  <div class="content flex-grow-1 p-4">
    <div class="container">
      <!-- Breadcrumb with icons for clearer navigation -->
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

      <div class="mt-4">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th scope="col">Nama Mahasiswa</th>
                <th scope="col">Kelas</th>
                <th scope="col">Mata Kuliah</th>
                <th scope="col">Tanggal</th>
                <th scope="col">Status</th>
                <!-- <th scope="col">Aksi</th> -->
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($allAbsensi)) : ?>
                <?php foreach ($allAbsensi as $index => $absensi) : ?>
                  <?php
                  $idMahasiswa = $absensi['id_mahasiswa'] ?? null;
                  $dataMahasiswa = $idMahasiswa ? getUserByNrp($idMahasiswa) : null;
                  $namaMahasiswa = 'Tidak Diketahui';

                  if (is_array($dataMahasiswa) && isset($dataMahasiswa['data']['name'])) {
                    $namaMahasiswa = $dataMahasiswa['data']['name'];
                  }

                  $idJadwal = $absensi['id_jadwal'] ?? '-';
                  $dataJadwal = $idJadwal ? getJadwalById($idJadwal) : null;

                  $dataKelas = getKelasByKodeKelas($dataJadwal['data']['kode_kelas'] ?? null);

                  $dataMataKuliah = getMataKuliahById($dataJadwal['data']['id_matkul'] ?? null);

                  $status = ucfirst($absensi['status'] ?? 'Tidak Diketahui');

                  $badgeClass = 'bg-secondary';
                  if (strtolower($status) === 'hadir') {
                    $badgeClass = 'bg-success';
                  } elseif (strtolower($status) === 'alpha') {
                    $badgeClass = 'bg-danger';
                  }
                  ?>
                  <tr>
                    <td><?= htmlspecialchars($namaMahasiswa) ?></td>
                    <td><?= htmlspecialchars($dataKelas['data']['nama_kelas'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($dataMataKuliah['nama_matkul'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($dataJadwal['data']['tanggal'] ?? '-') ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
                    <!-- <td>
                      <a href="#" class="btn btn-sm btn-outline-info">Detail</a>
                    </td> -->
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>

          <?php if (empty($allAbsensi)) : ?>
            <div class="alert alert-warning mt-3" role="alert">
              Belum ada data absensi.
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>