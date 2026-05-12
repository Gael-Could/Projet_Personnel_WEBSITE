<?php
// view_user.php
session_start();
// Vérification sécurité (à décommenter en production)
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit; }

require_once 'db_config.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = null;

if ($user_id > 0) {
    try {
        // Remplacer 'utilisateurs' par le nom exact de votre table
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Erreur de base de données.";
    }
}

if (!$user) {
    die("Utilisateur introuvable ou ID invalide.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir Utilisateur - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 p-8 font-sans">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-indigo-600 p-6 text-white flex justify-between items-center">
            <h2 class="text-xl font-bold"><i class="fas fa-user-circle mr-2"></i> Profil Utilisateur</h2>
            <a href="admin_dashboard.php" class="text-indigo-200 hover:text-white transition"><i class="fas fa-times text-xl"></i></a>
        </div>
        
        <div class="p-8">
            <div class="flex items-center gap-6 mb-8 border-b border-gray-100 pb-8">
                <div class="w-24 h-24 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-3xl font-bold">
                    <?= strtoupper(substr($user['nom'] ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($user['nom'] ?? 'Nom Inconnu') ?></h3>
                    <p class="text-gray-500"><?= htmlspecialchars($user['email'] ?? 'email@inconnu.com') ?></p>
                    <span class="mt-2 inline-block text-[10px] font-extrabold bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full uppercase">
                        <?= htmlspecialchars($user['role'] ?? 'Standard') ?>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-400 font-semibold uppercase tracking-wider mb-1">ID Utilisateur</p>
                    <p class="text-gray-800 font-medium">#<?= $user['id'] ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-semibold uppercase tracking-wider mb-1">Date d'inscription</p>
                    <p class="text-gray-800 font-medium"><?= $user['date_inscription'] ?? 'Non définie' ?></p>
                </div>
                </div>

            <div class="mt-10 flex gap-4">
                <a href="edit_user.php?id=<?= $user['id'] ?>" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <a href="admin_dashboard.php" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-lg font-medium hover:bg-gray-200 transition">
                    Retour
                </a>
            </div>
        </div>
    </div>
</body>
</html>