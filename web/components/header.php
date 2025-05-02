<?php
$pageTitle = $pageTitle ?? "Presensi App";
$metaDescription = $metaDescription ?? "";
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
  <title><?= htmlspecialchars($pageTitle) ?> - Presensi App</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <style>
    .navbar-brand {
      font-weight: 700;
    }

    .hero-section {
      background-color: #f8f9fa;
      padding: 100px 0;
    }

    .footer {
      background-color: #212529;
      color: #fff;
      padding: 30px 0;
      margin-top: 50px;
    }

    <?= $customCss ?><?= $pageSpecificCss ?>
  </style>
</head>

<body>