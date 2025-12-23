<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - SkyVault</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <h2>⚡ SkyVault</h2>
        <p>Administration</p>
      </div>
      
      <nav class="nav-menu">
        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
          <span class="icon">📊</span>
          <span>Tableau de bord</span>
        </a>
        <a href="modules.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'modules.php' ? 'active' : ''; ?>">
          <span class="icon">📦</span>
          <span>Modules</span>
        </a>
        <a href="orders.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
          <span class="icon">💳</span>
          <span>Commandes</span>
        </a>
        <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
          <span class="icon">👥</span>
          <span>Utilisateurs</span>
        </a>
        <a href="admins.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admins.php' ? 'active' : ''; ?>">
          <span class="icon">🔐</span>
          <span>Administrateurs</span>
        </a>
        <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
          <span class="icon">⚙️</span>
          <span>Paramètres</span>
        </a>
      </nav>
      
      <div class="sidebar-footer">
        <p>👤 <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
        <button onclick="logout()" class="btn-logout">Déconnexion</button>
      </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
      <header class="top-bar">
        <h1 class="page-title"><?php echo $pageTitle ?? 'Administration'; ?></h1>
        <div class="db-status" id="dbStatus">
          <span class="status-indicator"></span>
          <span>Vérification...</span>
        </div>
      </header>
      
      <div class="content-wrapper">
