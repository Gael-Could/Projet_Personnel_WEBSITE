<?php
/**
 * Configuration de la connexion à la base de données MySQL
 * Serveur: localhost | Base: rpldb
 */

$host = "localhost";
$dbname = "rpldb";
$username = "root";
$password = "";

try {
    // Connexion via PDO avec charset utf8mb4 pour éviter les problèmes d'encodage
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active l'affichage complet des erreurs SQL
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retourne les résultats sous forme de tableau associatif
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    // Stoppe l'exécution du site si la BDD est inaccessible
    die("Échec de la connexion à la base de données : " . $e->getMessage());
}

