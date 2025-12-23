<?php
$pageTitle = 'Commandes';
include 'header.php';
?>

<div class="section-actions">
  <input type="search" id="searchOrders" placeholder="Rechercher une commande..." class="search-input">
</div>

<div class="table-container">
  <table class="admin-table">
    <thead>
      <tr>
        <th>N° Commande</th>
        <th>Client</th>
        <th>Modules</th>
        <th>Montant TTC</th>
        <th>Date</th>
        <th>Statut</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="ordersTableBody">
      <tr>
        <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
          Aucune commande pour le moment
        </td>
      </tr>
    </tbody>
  </table>
</div>

<script>
// TODO: Implémenter la gestion des commandes
console.log('Page commandes - À implémenter');
</script>

<?php include 'footer.php'; ?>
