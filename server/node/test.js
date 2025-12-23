// Test de connexion MySQL avec Node.js
const { getConnection } = require('./db');

async function testConnection() {
  console.log('Test de connexion MySQL...\n');
  
  try {
    const conn = await getConnection();
    console.log('✓ Connexion réussie !');
    
    // Test version
    const [version] = await conn.query('SELECT VERSION() as version');
    console.log('Version MySQL :', version[0].version, '\n');
    
    // Test des données
    const [users] = await conn.query('SELECT * FROM users LIMIT 1');
    if (users[0]) {
      console.log('Premier utilisateur :', users[0].name, `(${users[0].email})`);
    }
    
    await conn.end();
  } catch (error) {
    console.error('✗ Erreur :', error.message);
  }
}

testConnection();
