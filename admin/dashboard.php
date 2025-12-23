<?php
$pageTitle = 'Tableau de bord';
include 'header.php';
?>

<div class="stats-grid">
  <div class="stat-card" id="dbStatusCard">
    <div class="stat-label">Base de données</div>
    <div class="stat-value" id="dbStatusValue">⏳</div>
    <div class="stat-trend" id="dbDetails">Vérification...</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Modules actifs</div>
    <div class="stat-value" id="statsModules">0</div>
    <div class="stat-trend">Modules disponibles</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Commandes</div>
    <div class="stat-value" id="statsOrders">0</div>
    <div class="stat-trend">Total des commandes</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Utilisateurs</div>
    <div class="stat-value" id="statsUsers">0</div>
    <div class="stat-trend">Total des comptes</div>
  </div>
</div>

<div class="dashboard-grid">
  <div class="dashboard-card">
    <h3>📈 Statistiques</h3>
    <div id="statsContent" style="padding: 20px;">
      <p>Chargement des statistiques...</p>
    </div>
  </div>
  <div class="dashboard-card">
    <h3>🔔 Activité récente</h3>
    <div id="recentActivity" style="padding: 20px;">
      <p>Aucune activité récente</p>
    </div>
  </div>
</div>

<script>
// Charger les statistiques
async function loadStats() {
  try {
    const response = await fetch('../server/php/stats.php');
    const data = await response.json();
    
    document.getElementById('statsModules').textContent = data.modules || 0;
    document.getElementById('statsOrders').textContent = data.orders || 0;
    document.getElementById('statsUsers').textContent = data.users || 0;
  } catch (error) {
    console.error('Erreur chargement stats:', error);
  }
}

// Vérifier le statut de la base
async function checkDatabaseStatusDetail() {
  try {
    const response = await fetch('../server/php/db-status.php');
    const data = await response.json();
    const statusValue = document.getElementById('dbStatusValue');
    const dbDetails = document.getElementById('dbDetails');
    const dbCard = document.getElementById('dbStatusCard');
    
    if (data.connected) {
      statusValue.innerHTML = '✅ Connectée';
      dbDetails.textContent = `MySQL ${data.version || ''}`;
      dbCard.style.borderLeftColor = '#10b981';
    } else {
      statusValue.innerHTML = '❌ Déconnectée';
      dbDetails.textContent = data.error || 'Erreur de connexion';
      dbCard.style.borderLeftColor = '#ef4444';
    }
  } catch (error) {
    document.getElementById('dbStatusValue').innerHTML = '⚠️ Erreur';
    document.getElementById('dbDetails').textContent = 'Impossible de vérifier';
  }
}

// Charger au démarrage
loadStats();
checkDatabaseStatusDetail();

// Actualiser toutes les 30 secondes
setInterval(() => {
  loadStats();
  checkDatabaseStatusDetail();
}, 30000);
</script>

<?php include 'footer.php'; ?>
