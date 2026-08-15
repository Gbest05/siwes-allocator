<?php
use App\Core\Helper;
$siteName = Helper::getSetting('site_name', 'SIWES Allocation Management System');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' . htmlspecialchars($siteName) : htmlspecialchars($siteName) ?></title>
    
    <!-- Meta Descriptions & Mobile Web App Settings -->
    <meta name="description" content="Digital SIWES Allocation Management System for student industrial work experience registration, company placement, smart allocation, and monitoring.">
    <meta name="author" content="<?= htmlspecialchars(Helper::getSetting('institution_name', 'School of Technology & Applied Sciences')) ?>">
    <meta name="theme-color" content="#166534">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= (require __DIR__ . '/../../../config/app.php')['base_url'] ?>/images/favicon.svg">
    <link rel="alternate icon" href="<?= (require __DIR__ . '/../../../config/app.php')['base_url'] ?>/images/favicon.svg">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Enterprise Theme CSS -->
    <link rel="stylesheet" href="<?= (require __DIR__ . '/../../../config/app.php')['base_url'] ?>/css/style.css">
</head>
<body>
