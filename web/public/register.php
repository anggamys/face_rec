<?php
ob_start(); // Mulai output buffering untuk mencegah output sebelum header()

require "../action/auth.php";

$error = "";

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Ambil dan sanitasi input
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? 'mahasiswa';
  $nrp = trim($_POST['nrp'] ?? '');
  $nip = trim($_POST['nip'] ?? '');

  // Validasi input dasar
  if (empty($name) || empty($email) || empty($password)) {
    $error = "Semua field wajib diisi.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Format email tidak valid.";
  } elseif ($role === "mahasiswa" && empty($nrp)) {
    $error = "NRP wajib diisi untuk mahasiswa.";
  } elseif ($role === "dosen" && empty($nip)) {
    $error = "NIP wajib diisi untuk dosen.";
  } else {
    // Lakukan registrasi lewat API
    $response = register($name, $email, $password, $role, $nrp, $nip);

    if ($response && $response['success']) {
      header("Location: login.php?msg=registered");
      exit();
    } else {
      $error = $response['error'] ?? "Pendaftaran gagal. Silakan coba lagi.";
    }
  }
}

ob_end_flush(); // Akhiri buffering & kirim output

include "../components/header.php";
include "../components/navbar.php";
?>


<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="text-center mb-4">Register</h4>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" id="registerForm">
            <div class="mb-3">
              <label for="name">Nama</label>
              <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                required
                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label for="email">Email</label>
              <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                required
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label for="password">Password</label>
              <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                required>
            </div>

            <div class="mb-3">
              <label for="role">Role</label>
              <select name="role" id="role" class="form-control" required>
                <option value="mahasiswa" <?= ($_POST['role'] ?? '') === 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                <option value="dosen" <?= ($_POST['role'] ?? '') === 'dosen' ? 'selected' : '' ?>>Dosen</option>
              </select>
            </div>

            <div class="mb-3" id="nrpField" style="display: none;">
              <label for="nrp">NRP (Mahasiswa)</label>
              <input
                type="text"
                id="nrp"
                name="nrp"
                class="form-control"
                value="<?= htmlspecialchars($_POST['nrp'] ?? '') ?>">
            </div>

            <div class="mb-3" id="nipField" style="display: none;">
              <label for="nip">NIP (Dosen)</label>
              <input
                type="text"
                id="nip"
                name="nip"
                class="form-control"
                value="<?= htmlspecialchars($_POST['nip'] ?? '') ?>">
            </div>

            <button class="btn btn-primary w-100" type="submit" name="register">Register</button>
          </form>

          <div class="mt-3 text-center">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Tampilkan NRP/NIP sesuai role
  const roleSelect = document.getElementById("role");
  const nrpField = document.getElementById("nrpField");
  const nipField = document.getElementById("nipField");

  function toggleFields() {
    const role = roleSelect.value;
    nrpField.style.display = role === "mahasiswa" ? "block" : "none";
    nipField.style.display = role === "dosen" ? "block" : "none";
  }

  roleSelect.addEventListener("change", toggleFields);
  window.addEventListener("DOMContentLoaded", toggleFields);
</script>

<?php include "../components/footer.php"; ?>