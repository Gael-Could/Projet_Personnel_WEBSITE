<?php
/**
 * db_connect.php
 * Ce fichier centralise la connexion à la base de données et démarre les sessions.
 * Il devra être inclus (include) au tout début des fichiers qui en ont besoin.
 */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rpl_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// On démarre la session PHP (obligatoire pour garder un utilisateur connecté)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>