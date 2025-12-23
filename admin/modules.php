<?php
$pageTitle = '📦 Modules';
include 'header.php';
?>

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

<!-- Modal Module -->
<div class="modal-overlay" id="modalModule">
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
let modulesData = [];

// Charger les modules
async function loadModules() {
  try {
    const response = await fetch('../server/php/modules.php');
    modulesData = await response.json();
    renderModulesTable();
  } catch (error) {
    console.error('Erreur chargement modules:', error);
  }
}

// Afficher les modules
function renderModulesTable() {
  const tbody = document.getElementById('modulesTableBody');
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
        <button class="btn-icon" onclick="deleteModule(${module.id})" title="Supprimer">🗑️</button>
      </td>
    `;
    tbody.appendChild(row);
  });
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

// Formulaire module
document.getElementById('moduleForm').addEventListener('submit', async (e) => {
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
      alert('Erreur: ' + (result.message || 'Échec'));
    }
  } catch (error) {
    console.error('Erreur:', error);
    alert('Erreur lors de la modification');
  }
});

// Recherche
document.getElementById('searchModules').addEventListener('input', (e) => {
  const search = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('#modulesTableBody tr');
  rows.forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
  });
});

// Charger au démarrage
loadModules();
</script>

<?php include 'footer.php'; ?>
