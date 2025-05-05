<?php
$pageTitle = "Home";
$currentPage = "home";
include "../components/header.php";
?>

<?php include "../components/navbar.php"; ?>

<!-- Hero Section -->
<section class="hero-section text-center bg-primary text-white d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="container text-center">
        <h1 class="display-4 mb-3">Welcome to Presensi App</h1>
        <p class="lead mb-4">Your solution for easy and efficient attendance tracking. Manage your classes and attendance seamlessly.</p>
        <a href="/login.php" class="btn btn-light btn-lg">Start Presensi</a>
    </div>
</section>

<?php include "../components/footer.php"; ?>