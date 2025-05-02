<?php
require "./action/auth.php";

if (isset($_POST['register'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  $role = $_POST['role'] ?? 'mahasiswa'; // Default role if not provided
  $response = register($name, $email, $password, $role);

  if ($response && isset($response['access_token'])) {
    session_start();
    $_SESSION['token'] = $response['access_token'];
    $_SESSION['user'] = $response['user'];

    if ($_SESSION['user']['role'] == 'dosen') {
      header("Location: /dashboard_dosen.php");
    } else {
      header("Location: /dashboard.php");
    }

    exit();
  } else {
    $error = "Registration failed. Please try again.";
  }
}
include "./components/header.php";
include "./components/navbar.php";
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
          <form method="POST">
            <div class="mb-3">
              <label>Name</label>
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
            <button class="btn btn-primary w-100" type="submit">Register</button>
          </form>
          <div class="mt-3 text-center">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
include "./components/footer.php";
?>