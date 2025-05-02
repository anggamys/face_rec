<?php
ob_start();
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        "name" => $_POST["name"],
        "email" => $_POST["email"],
        "password" => $_POST["password"],
        "role" => $_POST["role"],
        "nrp" => $_POST["role"] === "mahasiswa" ? (int) $_POST["nrp"] : null, // Mengubah NRP menjadi null jika role bukan mahasiswa
        "nip" => $_POST["role"] === "dosen" ? (int) $_POST["nip"] : null, // Mengubah NIP menjadi null jika role bukan dosen
    ];

    $ch = curl_init("http://localhost:8000/auth/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 201) {
        $success = "Registrasi berhasil. Silakan login.";
    } else {
        $res = json_decode($response, true);
        $error = $res["detail"] ?? "Registrasi gagal. Coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-body">
          <h3 class="mb-4">Register Akun</h3>

          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars(
                $error
            ) ?></div>
          <?php endif; ?>
          <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars(
                $success
            ) ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label>Nama</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Role</label>
              <select name="role" class="form-select" required onchange="toggleRoleField(this.value)">
                <option value="">-- Pilih Role --</option>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="dosen">Dosen</option>
              </select>
            </div>
            <div class="mb-3" id="nrp-field" style="display: none;">
              <label>NRP</label>
              <input type="number" name="nrp" class="form-control">
            </div>
            <div class="mb-3" id="nip-field" style="display: none;">
              <label>NIP</label>
              <input type="number" name="nip" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleRoleField(role) {
    document.getElementById("nrp-field").style.display = role === 'mahasiswa' ? 'block' : 'none';
    document.getElementById("nip-field").style.display = role === 'dosen' ? 'block' : 'none';
  }
</script>
</body>
</html>
