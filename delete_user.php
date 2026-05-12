<?php
// delete_user.php
session_start();
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit; }

require_once 'db_config.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id > 0) {
    try {
        // Facultatif : Empêcher l'admin de se supprimer lui-même
        // if ($user_id == $_SESSION['user_id']) { die("Vous ne pouvez pas supprimer votre propre compte."); }

        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$user_id]);
        
        // Redirection avec un paramètre de succès pour afficher une notification sur le dashboard
        header("Location: admin_dashboard.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    die("ID invalide.");
}
?>