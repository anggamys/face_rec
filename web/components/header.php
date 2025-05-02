<?php
session_start();
$user = $_SESSION["user"] ?? null;

// Default title & meta
$pageTitle = $pageTitle ?? "Entry System";
$metaDescription =
    $metaDescription ?? "A secure entry management system for your facility";
$customCss = $customCss ?? "";
$pageSpecificCss = $pageSpecificCss ?? "";
$currentPage = $currentPage ?? "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?= htmlspecialchars(
      $metaDescription
  ) ?>" />
  <title><?= htmlspecialchars($pageTitle) ?> - Entry System</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />

  <style>
    .navbar-brand { font-weight: 700; }
    .hero-section { background-color: #f8f9fa; padding: 100px 0; }
    .footer { background-color: #212529; color: #fff; padding: 30px 0; margin-top: 50px; }

    <?= $customCss ?>
    <?= $pageSpecificCss ?>
  </style>
</head>
<body>

<?php if (!$user): ?>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="index.php">Entry System</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link <?= $currentPage === "home"
              ? "active"
              : "" ?>" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?= $currentPage ===
          "features"
              ? "active"
              : "" ?>" href="features.php">Features</a></li>
          <li class="nav-item"><a class="nav-link <?= $currentPage === "about"
              ? "active"
              : "" ?>" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link <?= $currentPage === "contact"
              ? "active"
              : "" ?>" href="contact.php">Contact</a></li>
        </ul>
        <a href="login.php" class="btn btn-primary">Login</a>
      </div>
    </div>
  </nav>
<?php endif; ?>
