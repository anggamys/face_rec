<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ];

    $ch = curl_init('http://localhost:8000/auth/login'); // Ganti dengan URL API login kamu
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode == 200 && isset($result['access_token'])) {
        // Simpan token dan data user ke session
        $_SESSION['token'] = $result['access_token'];
        $_SESSION['user'] = $result['user'] ?? null;

        // Arahkan ke dashboard berdasarkan role
        if ($_SESSION['user']['role'] == 'dosen') {
            header('Location: dashboard_dosen.php'); // Arahkan ke dashboard dosen
        } else {
            header('Location: dashboard.php'); // Arahkan ke dashboard mahasiswa
        }
        exit();
    } else {
        // Menampilkan error jika login gagal
        $error = $result['detail'] ?? 'Login gagal';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="text-center mb-4">Login</h4>
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          <form method="POST">
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
          </form>
          <div class="mt-3 text-center">
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
