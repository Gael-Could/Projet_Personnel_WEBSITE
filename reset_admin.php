<?php
require_once 'db_connect.php';

$email_admin = 'admin@rpl-libreville.org'; // Remplace par l'email de ton compte admin
$nouveau_mdp = 'admin123'; // Le mot de passe que tu veux définir
$hash = password_hash($nouveau_mdp, PASSWORD_BCRYPT);

try {
    // On met à jour le mot de passe pour cet email
    $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ?, role = 'admin', actif = 1 WHERE email = ?");
    $stmt->execute([$hash, $email_admin]);
    
    // Si l'email n'existait pas, on le crée
    if ($stmt->rowCount() === 0) {
        $stmt_insert = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif, created_at) VALUES ('Super Admin', ?, ?, 'admin', 1, NOW())");
        $stmt_insert->execute([$email_admin, $hash]);
        echo "Le compte admin a été CRÉÉ avec succès.<br>";
    } else {
        echo "Le mot de passe a été RÉINITIALISÉ avec succès.<br>";
    }
    
    echo "<b>Ton email :</b> " . htmlspecialchars($email_admin) . "<br>";
    echo "<b>Ton nouveau mot de passe :</b> " . htmlspecialchars($nouveau_mdp) . "<br><br>";
    echo "<a href='connexion.php'>Aller à la page de connexion</a>";

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
