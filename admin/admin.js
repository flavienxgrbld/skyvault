// Navigation entre sections
const navLinks = document.querySelectorAll('.admin-nav-item');
const sections = document.querySelectorAll('.admin-section');

navLinks.forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const target = link.getAttribute('data-section');
    
    // Update active nav
    navLinks.forEach(l => l.classList.remove('active'));
    link.classList.add('active');
    
    // Update active section
    sections.forEach(s => s.classList.remove('active'));
    document.getElementById(target).classList.add('active');
    
    // Update header title
    const title = link.textContent.trim();
    document.querySelector('.admin-header h2').textContent = title;
  });
});

// Charger les modules depuis modules.json
let modulesData = [];
async function loadModules() {
  try {
    const response = await fetch('../modules/modules.json');
    modulesData = await response.json();
    renderModulesTable();
    updateDashboardStats();
  } catch (error) {
    console.error('Erreur chargement modules:', error);
  }
}

// Afficher les modules dans le tableau
function renderModulesTable() {
  const tbody = document.getElementById('modulesTableBody');
  tbody.innerHTML = '';
  
  modulesData.forEach((module, index) => {
    const status = index % 3 === 0 ? 'active' : 'inactive';
    const statusClass = status === 'active' ? 'status-active' : 'status-inactive';
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
      <td>${categoryMap[module.slug.split('/')[0]] || 'Autre'}</td>
      <td>${module.price.toFixed(2)} €</td>
      <td><span class="status-badge ${statusClass}">${status === 'active' ? 'Actif' : 'Inactif'}</span></td>
      <td class="table-actions">
        <button class="btn-icon" onclick="editModule(${index})" title="Modifier">✏️</button>
        <button class="btn-icon" onclick="deleteModule(${index})" title="Supprimer">🗑️</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

// Modal gestion
const modal = document.getElementById('moduleModal');
const modalTitle = document.getElementById('modalTitle');
const moduleForm = document.getElementById('moduleForm');
let editingIndex = -1;

function openAddModal() {
  editingIndex = -1;
  modalTitle.textContent = 'Ajouter un module';
  moduleForm.reset();
  modal.classList.add('active');
}

function editModule(index) {
  editingIndex = index;
  const module = modulesData[index];
  modalTitle.textContent = 'Modifier le module';
  
  document.getElementById('moduleName').value = module.title;
  document.getElementById('moduleSlug').value = module.slug;
  document.getElementById('moduleDescription').value = module.description;
  document.getElementById('moduleCategory').value = module.slug.split('/')[0];
  document.getElementById('modulePrice').value = module.price;
  document.getElementById('modulePath').value = module.path;
  
  modal.classList.add('active');
}

function closeModal() {
  modal.classList.remove('active');
  moduleForm.reset();
  editingIndex = -1;
}

function deleteModule(index) {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce module ?')) {
    modulesData.splice(index, 1);
    renderModulesTable();
    updateDashboardStats();
    alert('Module supprimé (simulation)');
  }
}

// Sauvegarder module
moduleForm.addEventListener('submit', (e) => {
  e.preventDefault();
  
  const moduleData = {
    title: document.getElementById('moduleName').value,
    slug: document.getElementById('moduleSlug').value,
    description: document.getElementById('moduleDescription').value,
    price: parseFloat(document.getElementById('modulePrice').value),
    path: document.getElementById('modulePath').value
  };
  
  if (editingIndex === -1) {
    modulesData.push(moduleData);
    alert('Module ajouté (simulation)');
  } else {
    modulesData[editingIndex] = moduleData;
    alert('Module modifié (simulation)');
  }
  
  renderModulesTable();
  updateDashboardStats();
  closeModal();
});

// Fermer modal au clic sur overlay
modal.addEventListener('click', (e) => {
  if (e.target === modal) {
    closeModal();
  }
});

// Stats dashboard
function updateDashboardStats() {
  document.querySelector('.stat-value[data-stat="modules"]').textContent = modulesData.length;
  
  // Revenus simulés
  const totalRevenue = modulesData.reduce((sum, m) => sum + m.price, 0) * 12.5;
  document.querySelector('.stat-value[data-stat="revenue"]').textContent = totalRevenue.toFixed(0) + ' €';
  
  // Commandes simulées
  const orders = Math.floor(modulesData.length * 3.2);
  document.querySelector('.stat-value[data-stat="orders"]').textContent = orders;
  
  // Utilisateurs simulés
  const users = Math.floor(modulesData.length * 8.7);
  document.querySelector('.stat-value[data-stat="users"]').textContent = users;
}

// Recherche modules
const searchInput = document.getElementById('searchModules');
searchInput.addEventListener('input', (e) => {
  const search = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('#modulesTableBody tr');
  
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(search) ? '' : 'none';
  });
});

// Filtres commandes
const filterStatus = document.getElementById('filterStatus');
const filterDate = document.getElementById('filterDate');

function filterOrders() {
  const status = filterStatus.value;
  const date = filterDate.value;
  
  const rows = document.querySelectorAll('#ordersTableBody tr');
  rows.forEach(row => {
    let show = true;
    
    if (status) {
      const rowStatus = row.querySelector('.status-badge').textContent.toLowerCase();
      show = show && rowStatus.includes(status);
    }
    
    if (date) {
      const rowDate = row.querySelector('td:first-child').textContent;
      show = show && rowDate.includes(date);
    }
    
    row.style.display = show ? '' : 'none';
  });
}

filterStatus.addEventListener('change', filterOrders);
filterDate.addEventListener('input', filterOrders);

// Recherche utilisateurs
const searchUsers = document.getElementById('searchUsers');
searchUsers.addEventListener('input', (e) => {
  const search = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('#usersTableBody tr');
  
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(search) ? '' : 'none';
  });
});

// Formulaires paramètres
const generalForm = document.getElementById('generalForm');
const paymentForm = document.getElementById('paymentForm');
const notifForm = document.getElementById('notifForm');

generalForm.addEventListener('submit', (e) => {
  e.preventDefault();
  alert('Paramètres généraux sauvegardés (simulation)');
});

paymentForm.addEventListener('submit', (e) => {
  e.preventDefault();
  alert('Paramètres de paiement sauvegardés (simulation)');
});

notifForm.addEventListener('submit', (e) => {
  e.preventDefault();
  alert('Paramètres de notifications sauvegardés (simulation)');
});

// Initialisation
loadModules();

// Sidebar mobile toggle (si implémenté)
const sidebarToggle = document.querySelector('.sidebar-toggle');
if (sidebarToggle) {
  sidebarToggle.addEventListener('click', () => {
    document.querySelector('.admin-sidebar').classList.toggle('open');
  });
}
