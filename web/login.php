<?php
ob_start();

$pageTitle = "Login";
$currentPage = "login";
include "./components/header.php";

// Inisialisasi error
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = [
        "email" => $_POST["email"],
        "password" => $_POST["password"],
    ];

    $ch = curl_init("http://localhost:8000/auth/login"); // Ganti sesuai URL backend
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result["access_token"])) {
        $_SESSION["token"] = $result["access_token"];
        $_SESSION["user"] = $result["user"] ?? null;

        if ($_SESSION["user"]["role"] === "dosen") {
            header("Location: dashboard_dosen.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $error =
            $result["detail"] ??
            "Login gagal. Cek kembali email dan password Anda.";
    }
}
?>

<!-- Login Form -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="text-center mb-4">Login</h4>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars(
                $error
            ) ?></div>
          <?php endif; ?>

          <form method="POST" novalidate>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" id="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
          </form>

          <div class="mt-3 text-center">
            <small>Belum punya akun? <a href="register.php">Daftar di sini</a></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "./components/footer.php"; ?>
