<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.html');
    exit;
}
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - SkyVault</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="admin-brand">
        <h1>SkyVault</h1>
        <span class="admin-badge">Admin</span>
      </div>
      
      <nav class="admin-nav">
        <a href="dashboard.php" class="admin-nav-item <?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
          <span class="nav-icon">📊</span>
          <span>Tableau de bord</span>
        </a>
        <a href="modules.php" class="admin-nav-item <?php echo $currentPage == 'modules' ? 'active' : ''; ?>">
          <span class="nav-icon">📦</span>
          <span>Modules</span>
        </a>
        <a href="orders.php" class="admin-nav-item <?php echo $currentPage == 'orders' ? 'active' : ''; ?>">
          <span class="nav-icon">💳</span>
          <span>Commandes</span>
        </a>
        <a href="users.php" class="admin-nav-item <?php echo $currentPage == 'users' ? 'active' : ''; ?>">
          <span class="nav-icon">👥</span>
          <span>Utilisateurs</span>
        </a>
        <a href="admins.php" class="admin-nav-item <?php echo $currentPage == 'admins' ? 'active' : ''; ?>">
          <span class="nav-icon">🔐</span>
          <span>Administrateurs</span>
        </a>
        <a href="settings.php" class="admin-nav-item <?php echo $currentPage == 'settings' ? 'active' : ''; ?>">
          <span class="nav-icon">⚙️</span>
          <span>Paramètres</span>
        </a>
      </nav>
      
      <div class="admin-sidebar-footer">
        <p style="margin:0 0 12px;font-size:13px;color:var(--muted)">👤 <?php echo htmlspecialchars($adminName); ?></p>
        <button onclick="logout()" class="btn-logout">🚪 Déconnexion</button>
      </div>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-main">
      <header class="admin-header">
        <h2><?php echo $pageTitle ?? 'Administration'; ?></h2>
        <div class="db-status" id="dbStatus">
          <span class="status-indicator"></span>
          <span>Vérification...</span>
        </div>
      </header>
      
      <div class="admin-content">
