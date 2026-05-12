<?php
/**
 * logout.php
 * Ce fichier détruit la session active et redirige l'utilisateur
 * vers la page de connexion.
 */

session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire le cookie de session si nécessaire
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Redirection vers connexion.php
header("Location: connexion.php");
exit();
?>