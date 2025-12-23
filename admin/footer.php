      </div>
    </main>
  </div>

  <script>
    // Fonction de déconnexion
    function logout() {
      if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        fetch('../server/php/auth.php?logout=1')
          .then(() => window.location.href = 'login.html');
      }
    }

    // Vérifier le statut de la base de données
    async function checkDatabaseStatus() {
      try {
        const response = await fetch('../server/php/db-status.php');
        const data = await response.json();
        const statusEl = document.getElementById('dbStatus');
        
        if (data.connected) {
          statusEl.innerHTML = '<span class="status-indicator connected"></span><span>Base de données connectée</span>';
        } else {
          statusEl.innerHTML = '<span class="status-indicator"></span><span>Base de données déconnectée</span>';
        }
      } catch (error) {
        const statusEl = document.getElementById('dbStatus');
        statusEl.innerHTML = '<span class="status-indicator"></span><span>Erreur de connexion</span>';
      }
    }

    // Vérifier au chargement
    checkDatabaseStatus();
    // Vérifier toutes les 30 secondes
    setInterval(checkDatabaseStatus, 30000);
  </script>
</body>
</html>
