<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.html');
    exit;
}

$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Administration — SkyVault</title>
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
        <a href="#dashboard" class="admin-nav-item active" data-section="dashboard">
          <span class="nav-icon">📊</span>
          <span>Tableau de bord</span>
        </a>
        <a href="#modules" class="admin-nav-item" data-section="modules">
          <span class="nav-icon">📦</span>
          <span>Modules</span>
        </a>
        <a href="#orders" class="admin-nav-item" data-section="orders">
          <span class="nav-icon">💳</span>
          <span>Commandes</span>
        </a>
        <a href="#users" class="admin-nav-item" data-section="users">
          <span class="nav-icon">👥</span>
          <span>Utilisateurs</span>
        </a>
        <a href="#settings" class="admin-nav-item" data-section="settings">
          <span class="nav-icon">⚙️</span>
          <span>Paramètres</span>
        </a>
      </nav>
      <div class="admin-sidebar-footer">
        <button onclick="logout()" class="btn-logout" style="border:none;cursor:pointer;background:transparent;width:100%">🚪 Déconnexion</button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <header class="admin-header">
        <h2 id="pageTitle">Tableau de bord</h2>
        <div class="admin-user">
          <span><?php echo htmlspecialchars($adminName); ?></span>
          <button class="btn-user">👤</button>
        </div>
      </header>

      <div class="admin-content">
        <!-- Dashboard Section -->
        <section id="section-dashboard" class="admin-section active">
          <div class="stats-grid">
            <div class="stat-card" id="dbStatusCard">
              <div class="stat-label">Base de données</div>
              <div class="stat-value" id="dbStatus">⏳</div>
              <div class="stat-trend" id="dbDetails">Vérification...</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Modules actifs</div>
              <div class="stat-value" id="statsModules">0</div>
              <div class="stat-trend">+0 ce mois</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Commandes</div>
              <div class="stat-value" id="statsOrders">0</div>
              <div class="stat-trend">+0 cette semaine</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Utilisateurs</div>
              <div class="stat-value" id="statsUsers">0</div>
              <div class="stat-trend">+0 ce mois</div>
            </div>
          </div>

          <div class="dashboard-grid">
            <div class="dashboard-card">
              <h3>Dernières commandes</h3>
              <div id="recentOrders" class="recent-list"></div>
            </div>
            <div class="dashboard-card">
              <h3>Activité récente</h3>
              <div id="recentActivity" class="recent-list"></div>
            </div>
          </div>
        </section>

        <!-- Autres sections (modules, orders, users, settings) inchangées -->
        <section id="section-modules" class="admin-section">
          <div class="section-actions">
            <button class="btn-primary" id="btnAddModule">+ Ajouter un module</button>
            <input type="search" id="searchModules" placeholder="Rechercher un module..." class="search-input">
          </div>
          <div class="table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Catégorie</th>
                  <th>Prix HT</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="modulesTableBody"></tbody>
            </table>
          </div>
        </section>

        <section id="section-orders" class="admin-section">
          <p style="padding:20px;color:var(--muted)">Section commandes à implémenter</p>
        </section>

        <section id="section-users" class="admin-section">
          <p style="padding:20px;color:var(--muted)">Section utilisateurs à implémenter</p>
        </section>

        <section id="section-settings" class="admin-section">
          <p style="padding:20px;color:var(--muted)">Section paramètres à implémenter</p>
        </section>
      </div>
    </main>
  </div>

  <!-- Modal Édition Module -->
  <div id="modalModule" class="modal-overlay">
    <div class="modal-content" style="max-width:600px">
      <div class="modal-header">
        <h3 id="modalTitle">Modifier le module</h3>
        <button onclick="closeModal()" class="modal-close">×</button>
      </div>
      <form id="moduleForm" class="modal-body">
        <input type="hidden" id="moduleId">
        <div class="form-group">
          <label>Titre *</label>
          <input type="text" id="moduleTitle" required class="form-input">
        </div>
        <div class="form-group">
          <label>Slug *</label>
          <input type="text" id="moduleSlug" required class="form-input">
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea id="moduleDescription" rows="3" class="form-input"></textarea>
        </div>
        <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div class="form-group">
            <label>Catégorie *</label>
            <select id="moduleCategory" required class="form-input">
              <option value="finance">Finance</option>
              <option value="ventes">Ventes</option>
              <option value="communication">Communication</option>
              <option value="chaine-approvisionnement">Chaîne approvisionnement</option>
              <option value="rh">RH</option>
              <option value="services">Services</option>
              <option value="productivite">Productivité</option>
            </select>
          </div>
          <div class="form-group">
            <label>Prix HT (€) *</label>
            <input type="number" id="modulePrice" required min="0" step="0.01" class="form-input">
          </div>
        </div>
        <div class="form-group">
          <label>Chemin *</label>
          <input type="text" id="modulePath" required class="form-input">
        </div>
        <div class="form-actions" style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px">
          <button type="button" class="btn-secondary" onclick="closeModal()">Annuler</button>
          <button type="submit" class="btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <script>
// Déconnexion
async function logout() {
  if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
    await fetch('../server/php/auth.php?logout=1');
    window.location.href = 'login.html';
  }
}

// Navigation entre sections
const navLinks = document.querySelectorAll('.admin-nav-item');
const sections = document.querySelectorAll('.admin-section');

navLinks.forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const target = link.getAttribute('data-section');
    
    navLinks.forEach(l => l.classList.remove('active'));
    link.classList.add('active');
    
    sections.forEach(s => s.classList.remove('active'));
    document.getElementById('section-' + target).classList.add('active');
    
    const title = link.textContent.trim();
    document.querySelector('.admin-header h2').textContent = title;
  });
});

// Charger les modules depuis modules.json
let modulesData = [];
async function loadModules() {
  try {
    const response = await fetch('../server/php/modules.php');
    modulesData = await response.json();
    renderModulesTable();
    updateDashboardStats();
  } catch (error) {
    console.error('Erreur chargement modules:', error);
  }
}

// Vérifier le statut de la base de données
async function checkDatabaseStatus() {
  const dbStatusCard = document.getElementById('dbStatusCard');
  const dbStatus = document.getElementById('dbStatus');
  const dbDetails = document.getElementById('dbDetails');
  
  try {
    const response = await fetch('../server/php/db-status.php');
    const data = await response.json();
    
    if (data.connected) {
      dbStatus.textContent = '✓ Connecté';
      dbStatusCard.style.borderLeft = '4px solid #10b981';
      dbDetails.textContent = `MySQL ${data.details.version.split('-')[0]} - ${data.details.tables} tables`;
    } else {
      dbStatus.textContent = '✗ Déconnecté';
      dbStatusCard.style.borderLeft = '4px solid #ef4444';
      dbDetails.textContent = data.message.substring(0, 40) + '...';
    }
  } catch (error) {
    dbStatus.textContent = '⚠ Erreur';
    dbStatusCard.style.borderLeft = '4px solid #f59e0b';
    dbDetails.textContent = 'Impossible de vérifier';
    console.error('Erreur statut DB:', error);
  }
}

// Afficher les modules dans le tableau
function renderModulesTable() {
  const tbody = document.getElementById('modulesTableBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  
  modulesData.forEach((module) => {
    const statusClass = module.active ? 'status-active' : 'status-inactive';
    const categoryMap = {
      'rh': 'RH',
      'finance': 'Finance',
      'ventes': 'Ventes',
      'services': 'Services',
      'productivite': 'Productivité',
      'communication': 'Communication',
      'chaine-approvisionnement': 'Chaîne approvisionnement'
    };
    
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><strong>${module.title}</strong></td>
      <td>${categoryMap[module.category] || 'Autre'}</td>
      <td>${parseFloat(module.price).toFixed(2)} €</td>
      <td><span class="status-badge ${statusClass}">${module.active ? 'Actif' : 'Inactif'}</span></td>
      <td class="table-actions">
        <button class="btn-icon" onclick="editModule(${module.id})" title="Modifier">✏️</button>
        <button class="btn-icon" onclick="toggleModuleStatus(${module.id}, ${module.active})" title="${module.active ? 'Désactiver' : 'Activer'}">${module.active ? '🔴' : '🟢'}</button>
        <button class="btn-icon" onclick="deleteModule(${module.id})" title="Supprimer">🗑️</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

// Activer/désactiver un module
async function toggleModuleStatus(id, currentStatus) {
  const module = modulesData.find(m => m.id === id);
  if (!module) return;
  
  module.active = currentStatus ? 0 : 1;
  
  try {
    const response = await fetch('../server/php/modules.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(module)
    });
    
    const result = await response.json();
    if (result.success) {
      loadModules();
    }
  } catch (error) {
    console.error('Erreur:', error);
  }
}

// Modifier un module
function editModule(id) {
  const module = modulesData.find(m => m.id === id);
  if (!module) return;
  
  document.getElementById('modalTitle').textContent = 'Modifier le module';
  document.getElementById('moduleId').value = module.id;
  document.getElementById('moduleTitle').value = module.title;
  document.getElementById('moduleSlug').value = module.slug;
  document.getElementById('moduleDescription').value = module.description || '';
  document.getElementById('moduleCategory').value = module.category;
  document.getElementById('modulePrice').value = module.price;
  document.getElementById('modulePath').value = module.path;
  
  document.getElementById('modalModule').classList.add('active');
}

// Fermer le modal
function closeModal() {
  document.getElementById('modalModule').classList.remove('active');
  document.getElementById('moduleForm').reset();
}

// Supprimer un module
async function deleteModule(id) {
  if (!confirm('Êtes-vous sûr de vouloir supprimer ce module ?')) return;
  
  try {
    const response = await fetch(`../server/php/modules.php?id=${id}`, {
      method: 'DELETE'
    });
    
    const result = await response.json();
    if (result.success) {
      alert('Module supprimé');
      loadModules();
    }
  } catch (error) {
    console.error('Erreur:', error);
    alert('Erreur lors de la suppression');
  }
}

// Stats dashboard
async function updateDashboardStats() {
  try {
    const response = await fetch('../server/php/stats.php');
    const stats = await response.json();
    
    const statsModules = document.getElementById('statsModules');
    const statsRevenue = document.getElementById('statsRevenue');
    const statsOrders = document.getElementById('statsOrders');
    const statsUsers = document.getElementById('statsUsers');
    
    if (statsModules) statsModules.textContent = stats.modules || modulesData.length;
    if (statsRevenue) statsRevenue.textContent = stats.revenue.toFixed(2) + '€';
    if (statsOrders) statsOrders.textContent = stats.orders;
    if (statsUsers) statsUsers.textContent = stats.users;
  } catch (error) {
    console.error('Erreur stats:', error);
  }
}

// Gestion du formulaire module
const moduleForm = document.getElementById('moduleForm');
if (moduleForm) {
  moduleForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('moduleId').value;
    const moduleData = {
      id: parseInt(id),
      slug: document.getElementById('moduleSlug').value,
      title: document.getElementById('moduleTitle').value,
      description: document.getElementById('moduleDescription').value,
      price: parseFloat(document.getElementById('modulePrice').value),
      path: document.getElementById('modulePath').value,
      category: document.getElementById('moduleCategory').value,
      active: modulesData.find(m => m.id == id)?.active ?? 1
    };
    
    try {
      const response = await fetch('../server/php/modules.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(moduleData)
      });
      
      const result = await response.json();
      if (result.success) {
        alert('Module modifié avec succès');
        closeModal();
        loadModules();
      } else {
        alert('Erreur: ' + (result.message || 'Échec de la modification'));
      }
    } catch (error) {
      console.error('Erreur:', error);
      alert('Erreur lors de la modification');
    }
  });
}

// Recherche modules
const searchModules = document.getElementById('searchModules');
if (searchModules) {
  searchModules.addEventListener('input', (e) => {
    const search = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#modulesTableBody tr');
    rows.forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
  });
}

// Initialisation
loadModules();
checkDatabaseStatus();
setInterval(checkDatabaseStatus, 30000);
  </script>
</body>
</html>
