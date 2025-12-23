<?php
$pageTitle = '👥 Utilisateurs';
include 'header.php';
?>

<div class="section-actions">
  <button class="btn-primary" id="btnAddUser">+ Ajouter un utilisateur</button>
  <input type="search" id="searchUsers" placeholder="Rechercher un utilisateur..." class="search-input">
</div>

<div class="table-container">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Date d'inscription</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="usersTableBody"></tbody>
  </table>
</div>

<!-- Modal Utilisateur -->
<div class="modal-overlay" id="modalUser">
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
        <label>Mot de passe *</label>
        <input type="password" id="userPassword" class="form-input">
        <small style="color:var(--muted)">Laissez vide pour ne pas modifier</small>
      </div>
      <div class="form-actions" style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px">
        <button type="button" class="btn-secondary" onclick="closeUserModal()">Annuler</button>
        <button type="submit" class="btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<script>
let usersData = [];

// Charger les utilisateurs (depuis table admins)
async function loadUsers() {
  try {
    const response = await fetch('../server/php/admins.php');
    usersData = await response.json();
    renderUsersTable();
  } catch (error) {
    console.error('Erreur chargement utilisateurs:', error);
  }
}

// Afficher les utilisateurs
function renderUsersTable() {
  const tbody = document.getElementById('usersTableBody');
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

// Ouvrir modal ajout
function openAddUserModal() {
  document.getElementById('modalUserTitle').textContent = 'Ajouter un utilisateur';
  document.getElementById('userForm').reset();
  document.getElementById('userId').value = '';
  document.getElementById('modalUser').classList.add('active');
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
  
  document.getElementById('modalUser').classList.add('active');
}

// Fermer le modal
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
    }
  } catch (error) {
    console.error('Erreur:', error);
    alert('Erreur lors de la suppression');
  }
}

// Formulaire utilisateur
document.getElementById('userForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const id = document.getElementById('userId').value;
  const userData = {
    name: document.getElementById('userName').value,
    email: document.getElementById('userEmail').value,
    password: document.getElementById('userPassword').value
  };
  
  try {
    let response;
    if (id) {
      userData.id = parseInt(id);
      response = await fetch('../server/php/admins.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(userData)
      });
    } else {
      response = await fetch('../server/php/admins.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(userData)
      });
    }
    
    const result = await response.json();
    if (result.success) {
      alert(id ? 'Administrateur modifié avec succès!' : 'Administrateur créé avec succès!');
      closeUserModal();
      await loadUsers();
    } else {
      alert('Erreur: ' + (result.message || 'Échec'));
    }
  } catch (error) {
    console.error('Erreur:', error);
    alert('Erreur lors de l\'enregistrement: ' + error.message);
  }
});

// Bouton ajouter
document.getElementById('btnAddUser').addEventListener('click', openAddUserModal);

// Recherche
document.getElementById('searchUsers').addEventListener('input', (e) => {
  const search = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('#usersTableBody tr');
  rows.forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
  });
});

// Charger au démarrage
loadUsers();
</script>

<?php include 'footer.php'; ?>
