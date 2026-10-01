<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'Dashboard';
$activePage = $activePage ?? currentPage();
$user = $user ?? currentUser(db());
?><!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f7fb">
    <title><?= e(pageTitle($pageTitle)) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#5b62da">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="KEUANGAN BAGAS">
<link rel="apple-touch-icon" href="assets/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="192x192" href="assets/icons/icon-192.png">
<link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="app-shell">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <div class="app-main">
        <?php require __DIR__ . '/navbar.php'; ?>
        <main class="content-area">
