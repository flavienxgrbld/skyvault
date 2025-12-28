// Connexion MySQL pour Node.js (mysql2/promise)
// Installer : npm install mysql2
const mysql = require('mysql2/promise');

// Configuration Laragon par défaut
const DB_HOST = '192.168.1.13';
const DB_USER = 'admin';
const DB_PASS = 'MotDePasseSolide';
const DB_NAME = 'skyvault';

async function getConnection() {
  return await mysql.createConnection({
    host: DB_HOST,
    user: DB_USER,
    password: DB_PASS,
    database: DB_NAME,
  });
}

module.exports = { getConnection };

// Usage rapide :
// (async () => {
//   const { getConnection } = require('./db');
//   const conn = await getConnection();
//   const [rows] = await conn.query('SELECT 1 AS ok');
//   console.log(rows);
//   await conn.end();
// })();
