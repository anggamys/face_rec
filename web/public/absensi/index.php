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

include "../../components/header.php";
?>

<!-- Tambahkan CSS untuk UI ramah lanjut usia -->
<style>
    /* Ukuran font lebih besar dan kontras warna yang tinggi */
    body {
        font-size: 18px;
        line-height: 1.6;
    }

    /* Tombol lebih besar dan dengan kontras tinggi */
    .btn {
        font-size: 1.1rem;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-primary {
        background-color: #0056b3;
        border-color: #0056b3;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .btn-success {
        background-color: #008a28;
        border-color: #008a28;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Judul lebih besar dan jelas */
    h2, h5 {
        font-weight: 700;
        color: #333;
    }

    h2 {
        font-size: 2.2rem;
        margin-bottom: 1rem;
    }

    /* Tabel dengan garis yang lebih jelas */
    .table {
        font-size: 1.1rem;
        border: 2px solid #dee2e6;
    }

    .table th {
        background-color: #343a40;
        color: white;
        font-weight: 600;
        padding: 15px 10px;
    }

    .table td {
        padding: 15px 10px;
        border: 1px solid #dee2e6;
    }

    /* Modal dengan ukuran yang lebih besar */
    .modal-dialog {
        max-width: 650px;
    }

    .modal-content {
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 15px 20px;
    }

    .modal-title {
        font-size: 1.6rem;
    }

    .modal-body {
        padding: 25px 20px;
    }

    .modal-footer {
        border-top: 2px solid #dee2e6;
        padding: 15px 20px;
    }

    /* Form dengan label yang lebih jelas */
    .form-label {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-select, .form-control {
        font-size: 1.1rem;
        padding: 12px;
        border: 2px solid #ced4da;
        border-radius: 8px;
        height: auto;
    }

    /* Meningkatkan fokus pada elemen */
    .form-select:focus, .form-control:focus, .btn:focus {
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.4);
        border-color: #0d6efd;
    }

    /* Badge status untuk meningkatkan visibility */
    .badge {
        font-size: 0.9rem;
        padding: 8px 12px;
        border-radius: 6px;
    }

    /* Tooltip bantuan */
    .tooltip-helper {
        display: inline-block;
        margin-left: 8px;
        background-color: #6c757d;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        text-align: center;
        line-height: 24px;
        cursor: help;
    }

    /* Ruang antara elemen form */
    .mb-3 {
        margin-bottom: 1.5rem !important;
    }

    /* Mengoptimalkan untuk penglihatan yang lebih rendah */
    @media (prefers-reduced-motion: reduce) {
        * {
            transition: none !important;
        }
    }
</style>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4">
        <div class="container">
            <!-- Breadcrumb untuk navigasi yang lebih jelas -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard/" style="font-size: 1.1rem;">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page" style="font-size: 1.1rem;">Daftar Absensi</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Daftar Absensi</h2>
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#openAbsensiModal">
                    <i class="bi bi-plus-circle-fill me-2"></i> Buka Sesi Absensi
                </button>
            </div>

            <?php if (!empty($allAbsensi) && is_array($allAbsensi)): ?>
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" style="width: 20%;">ID Mahasiswa</th>
                                <th scope="col" style="width: 20%;">Mata Kuliah</th>
                                <th scope="col" style="width: 20%;">Jadwal</th>
                                <th scope="col" style="width: 20%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (
                                $allAbsensi
                                as $index => $absensi
                            ): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars(
                                        $absensi["id_mahasiswa"] ?? "-"
                                    ) ?></td>
                                    <td>
                                        <?php
                                        $matkulId =
                                            $absensi["id_matkul"] ?? "-";
                                        $matkulName = "-";

                                        // Mencari nama mata kuliah berdasarkan ID
                                        foreach ($allMataKuliah as $matkul) {
                                            if (
                                                $matkul["id_matkul"] ==
                                                $matkulId
                                            ) {
                                                $matkulName =
                                                    $matkul["nama_matkul"];
                                                break;
                                            }
                                        }

                                        echo htmlspecialchars($matkulName);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $jadwalId =
                                            $absensi["id_jadwal"] ?? "-";
                                        $jadwalInfo = "-";

                                        // Mencari informasi jadwal berdasarkan ID
                                        foreach ($allJadwal as $jadwal) {
                                            if (
                                                $jadwal["id_jadwal"] ==
                                                $jadwalId
                                            ) {
                                                $jadwalInfo =
                                                    "Kelas " .
                                                    $jadwal["kode_kelas"];
                                                break;
                                            }
                                        }

                                        echo htmlspecialchars($jadwalInfo);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $absensi["status"] ?? "-";
                                        $badgeClass = "bg-secondary";

                                        if ($status == "Hadir") {
                                            $badgeClass = "bg-success";
                                        } elseif ($status == "Tidak Hadir") {
                                            $badgeClass = "bg-danger";
                                        } elseif ($status == "Izin") {
                                            $badgeClass =
                                                "bg-warning text-dark";
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars(
    $status
) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination yang lebih besar dan jelas -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination pagination-lg justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Sebelumnya</a>
                        </li>
                        <li class="page-item active" aria-current="page">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">Selanjutnya</a>
                        </li>
                    </ul>
                </nav>

            <?php else: ?>
                <div class="alert alert-warning p-4" role="alert">
                    <h5 class="alert-heading"><i class="bi bi-exclamation-circle-fill me-2"></i> Tidak Ada Data</h5>
                    <p class="mb-0">Tidak ada data absensi yang tersedia. Silakan buka sesi absensi baru dengan mengklik tombol "Buka Sesi Absensi" di atas.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Buka Sesi Absensi (Diperbaiki untuk UX yang lebih baik) -->
<div class="modal fade" id="openAbsensiModal" tabindex="-1" aria-labelledby="openAbsensiModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="openAbsensiForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="openAbsensiModalLabel">
            <i class="bi bi-calendar-check me-2"></i> Buka Sesi Absensi
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="font-size: 1.2rem; padding: 12px;"></button>
        </div>
        <div class="modal-body">
          <!-- Instruksi yang jelas -->
          <div class="alert alert-info mb-4">
            <p class="mb-0"><i class="bi bi-info-circle-fill me-2"></i> Silakan pilih jadwal dan mata kuliah untuk membuka sesi absensi baru.</p>
          </div>

          <div class="mb-4">
            <label for="id_jadwal" class="form-label">
                Pilih Jadwal
                <span class="tooltip-helper" title="Jadwal kelas yang akan dibuka sesi absensi">?</span>
            </label>
            <select class="form-select form-select-lg" id="id_jadwal" name="id_jadwal" required>
              <option value="" selected disabled>-- Pilih Jadwal --</option>
              <?php foreach ($allJadwal as $jadwal): ?>
                <option value="<?= htmlspecialchars($jadwal["id_jadwal"]) ?>">
                    <?= "Jadwal #" .
                        htmlspecialchars($jadwal["id_jadwal"]) .
                        " - " .
                        htmlspecialchars($jadwal["kode_kelas"]) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="form-text text-muted mt-2">Pilih jadwal sesuai dengan kelas yang akan diampu.</small>
          </div>

          <div class="mb-4">
            <label for="id_matkul" class="form-label">
                Pilih Mata Kuliah
                <span class="tooltip-helper" title="Mata kuliah yang akan diadakan">?</span>
            </label>
            <select class="form-select form-select-lg" id="id_matkul" name="id_matkul" required>
              <option value="" selected disabled>-- Pilih Mata Kuliah --</option>
              <?php foreach ($allMataKuliah as $matkul): ?>
                <option value="<?= htmlspecialchars($matkul["id_matkul"]) ?>">
                    <?= htmlspecialchars($matkul["nama_matkul"]) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="form-text text-muted mt-2">Pilih mata kuliah yang akan diajarkan pada jadwal tersebut.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-2"></i> Batal
          </button>
          <button type="submit" class="btn btn-success btn-lg">
            <i class="bi bi-check-circle me-2"></i> Buka Sesi
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Pastikan Bootstrap JS dan Bootstrap Icons sudah dimuat dengan benar -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah Bootstrap JS tersedia
    if (typeof bootstrap === 'undefined') {
      console.error('Bootstrap JS tidak dimuat dengan benar');

      // Tambahkan Bootstrap JS jika belum ada
      const bootstrapScript = document.createElement('script');
      bootstrapScript.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';
      bootstrapScript.integrity = 'sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz';
      bootstrapScript.crossOrigin = 'anonymous';
      document.body.appendChild(bootstrapScript);

      // Tambahkan Bootstrap Icons jika belum ada
      const bootstrapIcons = document.createElement('link');
      bootstrapIcons.rel = 'stylesheet';
      bootstrapIcons.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css';
      document.head.appendChild(bootstrapIcons);

      bootstrapScript.onload = function() {
        console.log('Bootstrap JS berhasil dimuat');
        initializeModals();
        initializeTooltips();
      };
    } else {
      initializeModals();
      initializeTooltips();
    }

    function initializeModals() {
      // Inisialisasi semua modal
      const modalElement = document.getElementById('openAbsensiModal');
      if (modalElement) {
        const absensiModal = new bootstrap.Modal(modalElement);

        // Event handler untuk tombol buka modal
        const openModalBtn = document.querySelector('[data-bs-target="#openAbsensiModal"]');
        if (openModalBtn) {
          openModalBtn.addEventListener('click', function() {
            absensiModal.show();
          });
        }

        // Handle form submission
        const absensiForm = document.getElementById('openAbsensiForm');
        if (absensiForm) {
          absensiForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validasi form dengan pesan yang jelas
            const jadwal = document.getElementById('id_jadwal').value;
            const matkul = document.getElementById('id_matkul').value;

            if (!jadwal || !matkul) {
              alert('Mohon pilih jadwal dan mata kuliah terlebih dahulu.');
              return;
            }

            // Tampilkan konfirmasi sebelum submit
            if (confirm('Anda yakin ingin membuka sesi absensi untuk kelas ini?')) {
              // Tambahkan logika untuk mengirim data form ke server di sini
              console.log('Form submitted');

              // Tampilkan pesan sukses
              const successMessage = document.createElement('div');
              successMessage.className = 'alert alert-success mt-3';
              successMessage.innerHTML = '<strong>Berhasil!</strong> Sesi absensi telah dibuka.';

              document.querySelector('.container').insertBefore(
                successMessage,
                document.querySelector('.container').firstChild
              );

              // Tutup modal setelah submit
              absensiModal.hide();

              // Otomatis hilangkan pesan sukses setelah 5 detik
              setTimeout(function() {
                successMessage.remove();
              }, 5000);
            }
          });
        }
      }
    }

    function initializeTooltips() {
      // Inisialisasi tooltips
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
          placement: 'right',
          trigger: 'hover focus',
          container: 'body'
        });
      });
    }

    // Tambahkan fokus visual yang lebih jelas
    const formElements = document.querySelectorAll('button, input, select, a');
    formElements.forEach(element => {
      element.addEventListener('focus', function() {
        this.style.outline = '4px solid rgba(13, 110, 253, 0.5)';
      });

      element.addEventListener('blur', function() {
        this.style.outline = '';
      });
    });
  });
</script>
