<?php
/**
 * SCRIPT DE CRÉATION D'ADMINISTRATEUR
 * Exécutez ce fichier une seule fois pour créer l'accès au dashboard.
 */

require_once 'db_config.php';

// Configuration du compte
$email = "admin@test.com";
$password = "admin123"; // Vous pourrez le changer plus tard
$role = "admin";

// On hache le mot de passe (sécurité PHP)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // 1. On vérifie si l'utilisateur existe déjà
    $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $check->execute([$email]);
    
    if ($check->rowCount() > 0) {
        // 2. S'il existe, on met à jour son mot de passe et son rôle
        $sql = "UPDATE utilisateurs SET mot_de_passe = ?, role = ? WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$hashedPassword, $role, $email]);
        echo "<h1>Succès !</h1>";
        echo "<p>Le compte <b>$email</b> a été mis à jour avec le rôle <b>admin</b>.</p>";
    } else {
        // 3. S'il n'existe pas, on le crée
        $sql = "INSERT INTO utilisateurs (email, mot_de_passe, role) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $hashedPassword, $role]);
        echo "<h1>Succès !</h1>";
        echo "<p>Le compte administrateur <b>$email</b> a été créé avec succès.</p>";
    }
    
    echo "<p>Mot de passe : <code>$password</code></p>";
    echo "<p><a href='login.php'>Retourner à la page de connexion</a></p>";

} catch (PDOException $e) {
    echo "<h1>Erreur SQL</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>