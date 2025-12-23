<?php
$pageTitle = 'Paramètres';
include 'header.php';
?>

<div class="settings-container" style="max-width: 800px;">
  <div class="settings-section">
    <h3>⚙️ Paramètres généraux</h3>
    <form id="settingsForm" class="form-vertical">
      <div class="form-group">
        <label>Nom du site</label>
        <input type="text" value="SkyVault" class="form-input">
      </div>
      <div class="form-group">
        <label>Email administrateur</label>
        <input type="email" value="admin@skyvault.com" class="form-input">
      </div>
      <div class="form-group">
        <label>Fuseau horaire</label>
        <select class="form-input">
          <option>Europe/Paris</option>
          <option>America/New_York</option>
          <option>Asia/Tokyo</option>
        </select>
      </div>
      <button type="submit" class="btn-primary">Enregistrer</button>
    </form>
  </div>

  <div class="settings-section" style="margin-top: 32px;">
    <h3>🔐 Sécurité</h3>
    <div class="info-box">
      <p><strong>Base de données :</strong> Connectée</p>
      <p><strong>Version PHP :</strong> <?php echo phpversion(); ?></p>
      <p><strong>Serveur :</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
    </div>
  </div>

  <div class="settings-section" style="margin-top: 32px;">
    <h3>🗑️ Zone dangereuse</h3>
    <div class="danger-box" style="border: 2px solid #ef4444; padding: 20px; border-radius: 8px; background: #fef2f2;">
      <p style="color: #991b1b; margin-bottom: 12px;">Actions irréversibles</p>
      <button class="btn-danger" onclick="alert('Fonctionnalité à implémenter')">Réinitialiser la base de données</button>
    </div>
  </div>
</div>

<script>
document.getElementById('settingsForm').addEventListener('submit', (e) => {
  e.preventDefault();
  alert('Paramètres enregistrés avec succès !');
});
</script>

<?php include 'footer.php'; ?>
