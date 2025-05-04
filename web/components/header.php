<?php
$pageTitle = $pageTitle ?? "Presensi App";
$metaDescription = $metaDescription ?? "Your attendance management solution";
$customCss = $customCss ?? "";
$pageSpecificCss = $pageSpecificCss ?? "";
$currentPage = $currentPage ?? "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>" />
  <meta name="author" content="Your Name" />
  <meta name="robots" content="index, follow" />
  <title><?= htmlspecialchars($pageTitle) ?> - Presensi App</title>

  <!-- Open Graph Meta Tags for social media -->
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>" />
  <meta property="og:image" content="path_to_image.jpg" /> <!-- Replace with actual image URL -->
  <meta property="og:url" content="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <!-- Custom CSS -->
  <style>
    body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
    }

    .footer {
      background-color: #212529;
      color: #fff;
      padding: 10px 0;
      font-size: 14px;
    }
  </style>


  <?= $customCss ?><?= $pageSpecificCss ?>
  </style>
</head>

<body>