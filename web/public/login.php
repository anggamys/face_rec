<?php
require_once "../action/auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $response = login($email, $password);

    if ($response && isset($response['access_token'])) {
        $_SESSION['token'] = $response['access_token'];
        $_SESSION['user'] = $response['user'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Email atau password salah.";
    }
}

include "../components/header.php";
include "../components/navbar.php";
?>

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
                        <button class="btn btn-primary w-100" type="submit" name="login">Login</button>
                    </form>
                    <div class="mt-3 text-center">
                        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../components/footer.php"; ?>