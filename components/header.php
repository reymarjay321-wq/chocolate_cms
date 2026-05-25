<?php
/**
 * Shared Header / Layout Component
 * Included at the top of every page
 * $pageTitle and $activePage must be set before including
 */
$pageTitle  = $pageTitle  ?? 'Dashboard';
$activePage = $activePage ?? 'dashboard';

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Chocolate CMS</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon"><i class="fa-solid fa-cookie-bite"></i></span>
        <div class="brand-text">
            <span class="brand-name">ChocoMS</span>
            <span class="brand-sub">Management System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Main Menu</div>

        <a href="index.php" class="nav-item <?= $activePage==='dashboard'?'active':'' ?>">
            <i class="fa-solid fa-chart-pie nav-icon"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-label mt-3">System</div>

        <a href="about.php" class="nav-item <?= $activePage==='about'?'active':'' ?>">
            <i class="fa-solid fa-circle-question nav-icon"></i>
            <span>About System</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="system-status">
            <span class="status-dot"></span>
            <span>System Online</span>
        </div>
    </div>
</aside>

<!-- ═══════════════ MAIN WRAPPER ═══════════════ -->
<div class="main-wrapper" id="mainWrapper">

    <!-- TOPBAR -->
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="topbar-title">
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
        </div>

        <div class="topbar-actions">
            <button class="theme-toggle" id="themeToggle" title="Toggle Theme">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </button>
        </div>
    </header>

    <!-- FLASH TOAST -->
    <?php if ($flash): ?>
    <div id="flashToast" data-type="<?= $flash['type'] ?>" data-msg="<?= htmlspecialchars($flash['msg']) ?>"></div>
    <?php endif; ?>

    <!-- PAGE CONTENT STARTS HERE -->
    <main class="page-content">
