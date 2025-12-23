# Ajout d'une base MySQL locale pour SkyVault

Ce fichier décrit rapidement comment créer la base de données MySQL et tester les connexions (Laragon / PHP / Node).

Étapes rapides :

- 1) Importer le schéma

  - Avec la ligne de commande MySQL :

    ```bash
    mysql -u root < db/schema.sql
    ```

  - Ou via phpMyAdmin / Adminer : importer `db/schema.sql`.

- 2) Tester la connexion PHP

  - Placer `server/php/db.php` dans un script PHP accessible par le serveur web (ex: `server/php/test.php`), inclure `db.php` et effectuer une requête de test.

- 3) Tester la connexion Node.js

  - Installer la dépendance :

    ```bash
    npm init -y
    npm install mysql2
    ```

  - Lancer un fichier de test qui utilise `server/node/db.js` (voir commentaire d'exemple dans le fichier).

Notes :

- Si vous utilisez Laragon, ouvrez son panneau et démarrez MySQL, puis importez le fichier `db/schema.sql` via `Menu > Database > Import` ou `phpMyAdmin`.
- Les fichiers de connexion utilisent la configuration Laragon par défaut (root sans mot de passe). Modifiez directement `server/php/db.php` ou `server/node/db.js` si vos paramètres diffèrent.
