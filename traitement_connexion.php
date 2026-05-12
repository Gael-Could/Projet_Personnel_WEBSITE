<?php
// On démarre la session pour pouvoir mémoriser l'utilisateur
session_start();

// Inclusion du fichier de connexion à la BDD que nous venons de créer
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et nettoyage des données du formulaire
    // Assurez-vous que les 'name' dans votre HTML sont bien "email" et "password"
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        try {
            // Recherche de l'utilisateur par son email
            $stmt = $pdo->prepare("SELECT id, nom, mot_de_passe FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Vérification si l'utilisateur existe et si le mot de passe est correct
            // Note : On utilise password_verify() car les mots de passe doivent être hachés en BDD
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                
                // Connexion réussie : on stocke les infos en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];

                // Redirection vers l'espace membre ou l'accueil
                header("Location: espace_membre.php");
                exit();
            } else {
                // Identifiants incorrects
                $error = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $error = "Une erreur est survenue lors de l'authentification.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// Si on arrive ici, c'est qu'il y a eu une erreur
// On peut rediriger vers la page de login avec un message d'erreur
if (isset($error)) {
    header("Location: login.php?error=" . urlencode($error));
    exit();
}
?>