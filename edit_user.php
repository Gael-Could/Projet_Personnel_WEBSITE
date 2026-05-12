<?php
// edit_user.php
session_start();
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit; }

require_once 'db_config.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$success_msg = '';
$error_msg = '';

// Traitement du formulaire lors de la soumission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    if (!empty($nom) && !empty($email)) {
        try {
            $update = $pdo->prepare("UPDATE utilisateurs SET nom = ?, email = ?, role = ? WHERE id = ?");
            $update->execute([$nom, $email, $role, $user_id]);
            $success_msg = "Les informations ont été mises à jour avec succès.";
        } catch (PDOException $e) {
            $error_msg = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    } else {
        $error_msg = "Veuillez remplir tous les champs obligatoires.";
    }
}

// Récupération des données actuelles
if ($user_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$user) { die("Utilisateur introuvable."); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 p-8 font-sans">
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Modifier l'utilisateur #<?= $user['id'] ?></h2>
            <a href="admin_dashboard.php" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></a>
        </div>

        <div class="p-6">
            <?php if ($success_msg): ?>
                <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-4 text-sm border border-green-200"><?= $success_msg ?></div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div class="bg-red-50 text-red-700 p-3 rounded-lg mb-4 text-sm border border-red-200"><?= $error_msg ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nom complet</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Adresse Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Rôle</label>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                        <option value="standard" <?= (isset($user['role']) && $user['role'] == 'standard') ? 'selected' : '' ?>>Standard</option>
                        <option value="admin" <?= (isset($user['role']) && $user['role'] == 'admin') ? 'selected' : '' ?>>Administrateur</option>
                    </select>
                </div>

                <div class="flex gap-3 justify-end pt-4 border-t border-gray-100">
                    <a href="admin_dashboard.php" class="px-5 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">Annuler</a>
                    <button type="submit" class="px-5 py-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium transition">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>