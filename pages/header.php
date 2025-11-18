<?php
// Simple HTML header shared across pages
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo htmlspecialchars($settings['site_title'] ?? 'Site'); ?></title>
  <link rel="stylesheet" href="assets/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="?page=dashboard"><?php echo htmlspecialchars($settings['site_title']); ?></a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link <?php echo ($_GET['page'] ?? 'dashboard') === 'dashboard' ? 'active' : ''; ?>" href="?page=dashboard">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?php echo ($_GET['page'] ?? '') === 'settings' ? 'active' : ''; ?>" href="?page=settings">Settings</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
