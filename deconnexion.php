<?php
/**
 * deconnexion.php
 * Alias de logout.php — utilisé dans index.php et about.php
 * Détruit la session et redirige vers la page de connexion
 */
session_start();

// Destruction complète de la session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Redirection vers la page d'accueil
header('Location: connexion.php');
exit;
