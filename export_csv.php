<?php
// export_csv.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// Vérification de sécurité obligatoire (on vérifie bien 'user_role')
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') { 
    exit('Accès refusé'); 
}

// On détermine ce qu'on veut exporter (par défaut, on exporte les logs comme avant)
$type_export = $_GET['type'] ?? 'logs';

if ($type_export === 'logs') {
    $filename = "logs_systeme_" . date('Y-m-d_H-i') . ".csv";
    $query = "SELECT date_action, utilisateur, action_type, cible, details FROM system_logs ORDER BY date_action DESC";
    $headers_csv = ['Date & Heure', 'Utilisateur', 'Type Action', 'Cible', 'Détails'];
} 
elseif ($type_export === 'utilisateurs') {
    $filename = "utilisateurs_" . date('Y-m-d_H-i') . ".csv";
    $query = "SELECT id, nom, email, role, actif, created_at FROM utilisateurs ORDER BY created_at DESC";
    $headers_csv = ['ID', 'Nom', 'Email', 'Rôle', 'Statut (1=Actif, 0=Inactif)', 'Date inscription'];
} 
elseif ($type_export === 'articles') {
    $filename = "articles_publies_" . date('Y-m-d_H-i') . ".csv";
    $query = "SELECT id, titre, auteur, date_publication FROM articles ORDER BY date_publication DESC";
    $headers_csv = ['ID', 'Titre', 'Auteur', 'Date publication'];
}
else {
    exit('Type d\'export invalide');
}

// Entêtes pour forcer le téléchargement du fichier
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Création du pointeur de fichier vers la sortie PHP
$output = fopen('php://output', 'w');

// Ajout de la ligne d'entête CSV (UTF-8 BOM pour Excel pour éviter les soucis d'accents)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, $headers_csv);

// Récupération et écriture des données
try {
    // CORRECTION : On utilise $conn et pas $pdo
    $stmt = $conn->query($query);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    
    // Log de l'action
    $stmt_log = $conn->prepare("INSERT INTO system_logs (utilisateur, action_type, cible, details) VALUES (?, 'BACKUP', 'Export CSV', ?)");
    $stmt_log->execute([$_SESSION['user_nom'], "Export de la table : " . $type_export]);
    
} catch (PDOException $e) {
    // En cas d'erreur
    fputcsv($output, ['Erreur lors de l\'exportation']);
}

fclose($output);
exit;
?>