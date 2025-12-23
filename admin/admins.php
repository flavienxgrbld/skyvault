<?php
$pageTitle = 'Administrateurs';
include 'header.php';
?>

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
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="adminsTableBody"></tbody>
  </table>
</div>

<!-- Modal Admin -->
<div class="modal-overlay" id="modalAdmin">
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
        <label>Mot de passe *</label>
        <input type="password" id="adminPassword" class="form-input">
        <small style="color:var(--muted)">Laissez vide pour ne pas modifier</small>
      </div>
      <div class="form-group">
        <label>Statut</label>
        <select id="adminStatus" class="form-input">
          <option value="active">Actif</option>
          <option value="inactive">Inactif</option>
        </select>
      </div>
      <div class="form-actions" style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px">
        <button type="button" class="btn-secondary" onclick="closeAdminModal()">Annuler</button>
        <button type="submit" class="btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<script>
let adminsData = [];

// Charger les administrateurs
async function loadAdmins() {
  try {
    const response = await fetch('../server/php/admins.php');
    adminsData = await response.json();
    renderAdminsTable();
  } catch (error) {
    console.error('Erreur chargement admins:', error);
  }
}

// Afficher les administrateurs
function renderAdminsTable() {
  const tbody = document.getElementById('adminsTableBody');
  tbody.innerHTML = '';
  
  adminsData.forEach((admin) => {
    const lastLogin = admin.last_login 
      ? new Date(admin.last_login).toLocaleDateString('fr-FR') + ' ' + new Date(admin.last_login).toLocaleTimeString('fr-FR')
      : 'Jamais';
    const statusClass = admin.status === 'active' ? 'status-active' : 'status-inactive';
    
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><strong>${admin.name}</strong></td>
      <td>${admin.email}</td>
      <td><span class="status-badge ${statusClass}">${admin.status === 'active' ? 'Actif' : 'Inactif'}</span></td>
      <td>${lastLogin}</td>
      <td class="table-actions">
        <button class="btn-icon" onclick="editAdmin(${admin.id})" title="Modifier">✏️</button>
        <button class="btn-icon" onclick="deleteAdmin(${admin.id})" title="Supprimer">🗑️</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

// Ouvrir modal ajout
function openAddAdminModal() {
  document.getElementById('modalAdminTitle').textContent = 'Ajouter un administrateur';
  document.getElementById('adminForm').reset();
  document.getElementById('adminId').value = '';
  document.getElementById('adminStatus').value = 'active';
  document.getElementById('modalAdmin').classList.add('active');
}

// Modifier un admin
function editAdmin(id) {
  const admin = adminsData.find(a => a.id === id);
  if (!admin) return;
  
  document.getElementById('modalAdminTitle').textContent = 'Modifier l\'administrateur';
  document.getElementById('adminId').value = admin.id;
  document.getElementById('adminName').value = admin.name;
  document.getElementById('adminEmail').value = admin.email;
  document.getElementById('adminPassword').value = '';
  document.getElementById('adminStatus').value = admin.status || 'active';
  
  document.getElementById('modalAdmin').classList.add('active');
}

// Fermer le modal
function closeAdminModal() {
  document.getElementById('modalAdmin').classList.remove('active');
  document.getElementById('adminForm').reset();
}

// Supprimer un admin
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

// Formulaire admin
document.getElementById('adminForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const id = document.getElementById('adminId').value;
  const adminData = {
    name: document.getElementById('adminName').value,
    email: document.getElementById('adminEmail').value,
    password: document.getElementById('adminPassword').value,
    status: document.getElementById('adminStatus').value
  };
  
  try {
    let response;
    if (id) {
      adminData.id = parseInt(id);
      response = await fetch('../server/php/admins.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(adminData)
      });
    } else {
      response = await fetch('../server/php/admins.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(adminData)
      });
    }
    
    const result = await response.json();
    if (result.success) {
      alert(id ? 'Administrateur modifié avec succès!' : 'Administrateur créé avec succès!');
      closeAdminModal();
      await loadAdmins();
    } else {
      alert('Erreur: ' + (result.message || 'Échec'));
    }
  } catch (error) {
    console.error('Erreur:', error);
    alert('Erreur lors de l\'enregistrement: ' + error.message);
  }
});

// Bouton ajouter
document.getElementById('btnAddAdmin').addEventListener('click', openAddAdminModal);

// Recherche
document.getElementById('searchAdmins').addEventListener('input', (e) => {
  const search = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('#adminsTableBody tr');
  rows.forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
  });
});

// Charger au démarrage
loadAdmins();
</script>

<?php include 'footer.php'; ?>
