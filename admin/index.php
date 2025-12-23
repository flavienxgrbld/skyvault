<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.html');
    exit;
}

// Rediriger vers le dashboard
header('Location: dashboard.php');
exit;
?>
          </div>
        </section>

        <section id="section-admins" class="admin-section">
          <div class="section-actions">
            <button class="btn-primary" id="btnAddAdmin">+ Ajouter un administrateur</button>
            <input type="search" id="searchAdmins" placeholder="Rechercher un administrateur..." class="search-input">
          </div>
          <div class="table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Email</th>
                  <th>Statut</th>
                  <th>Dernière connexion</th>
                  <th>Date d'inscription</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="adminsTableBody"></tbody>
            </table>
          </div>
        </section>

        <section id="section-settings" class="admin-section">
          <p style="padding:20px;color:var(--muted)">Section paramètres à implémenter</p>
        </section>
      </div>
    </main>
  </div>

  <!-- Modal Édition Administrateur -->
  <div id="modalAdmin" class="modal-overlay">
    <div class="modal-content" style="max-width:500px">
      <div class="modal-header">
        <h3 id="modalAdminTitle">Ajouter un administrateur</h3>
        <button onclick="closeAdminModal()" class="modal-close">×</button>
      </div>
      <form id="adminForm" class="modal-body">
        <input type="hidden" id="adminId">
        <div class="form-group">
          <label>Nom *</label>
          <input type="text" id="adminName" required class="form-input">
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" id="adminEmail" required class="form-input">
        </div>
        <div class="form-group">
          <label>Statut *</label>
          <select id="adminStatus" required class="form-input">
            <option value="active">Actif</option>
            <option value="inactive">Inactif</option>
          </select>
        </div>
        <div class="form-group">
          <label>Mot de passe <span id="adminPasswordOptional" style="color:var(--muted);font-weight:normal">(laisser vide pour ne pas changer)</span></label>
          <input type="password" id="adminPassword" class="form-input">
        </div>
        <div class="form-actions" style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px">
          <button type="button" class="btn-secondary" onclick="closeAdminModal()">Annuler</button>
          <button type="submit" class="btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Édition Utilisateur -->
  <div id="modalUser" class="modal-overlay">
    <div class="modal-content" style="max-width:500px">
      <div class="modal-header">
        <h3 id="modalUserTitle">Ajouter un utilisateur</h3>
        <button onclick="closeUserModal()" class="modal-close">×</button>
      </div>
      <form id="userForm" class="modal-body">
        <input type="hidden" id="userId">
        <div class="form-group">
          <label>Nom *</label>
          <input type="text" id="userName" required class="form-input">
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" id="userEmail" required class="form-input">
        </div>
        <div class="form-group">
          <label>Mot de passe <span id="passwordOptional" style="color:var(--muted);font-weight:normal">(laisser vide pour ne pas changer)</span> *</label>
          <input type="password" id="userPassword" class="form-input">
        </div>
        <div class="form-actions" style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px">
          <button type="button" class="btn-secondary" onclick="closeUserModal()">Annuler</button>
          <button type="submit" class="btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
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

// Charger les administrateurs
let adminsData = [];
async function loadAdmins() {
  try {
    const response = await fetch('../server/php/admins.php');
    adminsData = await response.json();
    renderAdminsTable();
  } catch (error) {
    console.error('Erreur chargement admins:', error);
  }
}

// Afficher les administrateurs dans le tableau
function renderAdminsTable() {
  const tbody = document.getElementById('adminsTableBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  
  adminsData.forEach((admin) => {
    const date = new Date(admin.created_at).toLocaleDateString('fr-FR');
    const lastLogin = admin.last_login ? new Date(admin.last_login).toLocaleDateString('fr-FR') : 'Jamais';
    const statusClass = admin.status === 'active' ? 'status-active' : 'status-inactive';
    const statusLabels = { active: 'Actif', inactive: 'Inactif' };
    
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><strong>${admin.name}</strong></td>
      <td>${admin.email}</td>
      <td><span class="status-badge ${statusClass}">${statusLabels[admin.status] || admin.status}</span></td>
      <td>${lastLogin}</td>
      <td>${date}</td>
      <td class="table-actions">
        <button class="btn-icon" onclick="editAdmin(${admin.id})" title="Modifier">✏️</button>
        <button class="btn-icon" onclick="deleteAdmin(${admin.id})" title="Supprimer">🗑️</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

// Modifier un administrateur
function editAdmin(id) {
  const admin = adminsData.find(a => a.id === id);
  if (!admin) return;
  
  document.getElementById('modalAdminTitle').textContent = 'Modifier l\'administrateur';
  document.getElementById('adminId').value = admin.id;
  document.getElementById('adminName').value = admin.name;
  document.getElementById('adminEmail').value = admin.email;
  document.getElementById('adminStatus').value = admin.status || 'active';
  document.getElementById('adminPassword').value = '';
  document.getElementById('adminPasswordOptional').style.display = '';
  
  document.getElementById('modalAdmin').classList.add('active');
}

// Ouvrir modal ajout administrateur
function openAddAdminModal() {
  document.getElementById('modalAdminTitle').textContent = 'Ajouter un administrateur';
  document.getElementById('adminForm').reset();
  document.getElementById('adminId').value = '';
  document.getElementById('adminPasswordOptional').style.display = 'none';
  document.getElementById('modalAdmin').classList.add('active');
}

// Fermer le modal administrateur
function closeAdminModal() {
  document.getElementById('modalAdmin').classList.remove('active');
  document.getElementById('adminForm').reset();
}

// Supprimer un administrateur
async function deleteAdmin(id) {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?')) return;
  
  try {
    const response = await fetch(`../server/php/admins.php?id=${id}`, {
      method: 'DELETE'
    });
    
    const result = await response.json();
    if (result.success) {
      alert('Administrateur supprimé');
      loadAdmins();
    }
  } catch (error) {
    console.error('Erreur:', error);
    alert('Erreur lors de la suppression');
  }
}

// Gestion du formulaire administrateur
const adminForm = document.getElementById('adminForm');
if (adminForm) {
  adminForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('adminId').value;
    const adminData = {
      name: document.getElementById('adminName').value,
      email: document.getElementById('adminEmail').value,
      status: document.getElementById('adminStatus').value,
      password: document.getElementById('adminPassword').value
    };
    
    try {
      let response;
      if (id) {
        // Modifier
        adminData.id = parseInt(id);
        response = await fetch('../server/php/admins.php', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(adminData)
        });
      } else {
        // Créer
        response = await fetch('../server/php/admins.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(adminData)
        });
      }
      
      const result = await response.json();
      if (result.success) {
        alert(id ? 'Administrateur modifié' : 'Administrateur ajouté');
        closeAdminModal();
        loadAdmins();
      } else {
        alert('Erreur: ' + (result.message || 'Échec'));
      }
    } catch (error) {
      console.error('Erreur:', error);
      alert('Erreur lors de l\'enregistrement');
    }
  });
}

// Bouton ajouter administrateur
const btnAddAdmin = document.getElementById('btnAddAdmin');
if (btnAddAdmin) {
  btnAddAdmin.addEventListener('click', openAddAdminModal);
}

// Recherche administrateurs
const searchAdmins = document.getElementById('searchAdmins');
if (searchAdmins) {
  searchAdmins.addEventListener('input', (e) => {
    const search = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#adminsTableBody tr');
    rows.forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
  });
}

// Charger les utilisateurs
let usersData = [];
async function loadUsers() {
  try {
    const response = await fetch('../server/php/admins.php');
    usersData = await response.json();
    renderUsersTable();
  } catch (error) {
    console.error('Erreur chargement utilisateurs:', error);
  }
}

// Afficher les utilisateurs dans le tableau
function renderUsersTable() {
  const tbody = document.getElementById('usersTableBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  
  usersData.forEach((user) => {
    const date = new Date(user.created_at).toLocaleDateString('fr-FR');
    
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><strong>${user.name}</strong></td>
      <td>${user.email}</td>
      <td>${date}</td>
      <td class="table-actions">
        <button class="btn-icon" onclick="editUser(${user.id})" title="Modifier">✏️</button>
        <button class="btn-icon" onclick="deleteUser(${user.id})" title="Supprimer">🗑️</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

// Modifier un utilisateur
function editUser(id) {
  const user = usersData.find(u => u.id === id);
  if (!user) return;
  
  document.getElementById('modalUserTitle').textContent = 'Modifier l\'utilisateur';
  document.getElementById('userId').value = user.id;
  document.getElementById('userName').value = user.name;
  document.getElementById('userEmail').value = user.email;
  document.getElementById('userPassword').value = '';
  document.getElementById('userPassword').required = false;
  document.getElementById('passwordOptional').style.display = '';
  
  document.getElementById('modalUser').classList.add('active');
}

// Ouvrir modal ajout utilisateur
function openAddUserModal() {
  document.getElementById('modalUserTitle').textContent = 'Ajouter un utilisateur';
  document.getElementById('userForm').reset();
  document.getElementById('userId').value = '';
  document.getElementById('userPassword').required = true;
  document.getElementById('passwordOptional').style.display = 'none';
  document.getElementById('modalUser').classList.add('active');
}

// Fermer le modal utilisateur
function closeUserModal() {
  document.getElementById('modalUser').classList.remove('active');
  document.getElementById('userForm').reset();
}

// Supprimer un utilisateur
async function deleteUser(id) {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?')) return;
  
  try {
    const response = await fetch(`../server/php/admins.php?id=${id}`, {
      method: 'DELETE'
    });
    
    const result = await response.json();
    if (result.success) {
      alert('Administrateur supprimé');
      loadUsers();
      updateDashboardStats();
    }
  } catch (error) {
    console.error('Erreur:', error);
    alert('Erreur lors de la suppression');
  }
}

// Gestion du formulaire utilisateur
const userForm = document.getElementById('userForm');
if (userForm) {
  userForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('userId').value;
    const userData = {
      name: document.getElementById('userName').value,
      email: document.getElementById('userEmail').value,
      password: document.getElementById('userPassword').value
    };
    
    console.log('Envoi des données:', userData);
    
    try {
      let response;
      if (id) {
        // Modifier
        userData.id = parseInt(id);
        response = await fetch('../server/php/admins.php', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(userData)
        });
      } else {
        // Créer
        response = await fetch('../server/php/admins.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(userData)
        });
      }
      
      console.log('Réponse HTTP:', response.status);
      const result = await response.json();
      console.log('Résultat:', result);
      
      if (result.success) {
        alert(id ? 'Administrateur modifié avec succès!' : 'Administrateur créé avec succès!');
        closeUserModal();
        await loadUsers(); // Recharger depuis la base
        updateDashboardStats();
      } else {
        alert('Erreur: ' + (result.message || 'Échec'));
      }
    } catch (error) {
      console.error('Erreur complète:', error);
      alert('Erreur lors de l\'enregistrement: ' + error.message);
    }
  });
}

// Bouton ajouter utilisateur
const btnAddUser = document.getElementById('btnAddUser');
if (btnAddUser) {
  btnAddUser.addEventListener('click', openAddUserModal);
}

// Recherche utilisateurs
const searchUsers = document.getElementById('searchUsers');
if (searchUsers) {
  searchUsers.addEventListener('input', (e) => {
    const search = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTableBody tr');
    rows.forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
  });
}

// Initialisation
loadModules();
loadUsers();
loadAdmins();
checkDatabaseStatus();
setInterval(checkDatabaseStatus, 30000);
  </script>
</body>
</html>
