<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$erreur = '';

// Récupérer les infos actuelles
$stmt = $conn->prepare("SELECT nom, email FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nouveau_nom = trim($_POST['nom']);
    $nouvel_email = trim($_POST['email']);
    $nouveau_mdp = $_POST['nouveau_mdp'];

    if (!empty($nouveau_nom) && !empty($nouvel_email)) {
        try {
            // Vérifier si le nouvel email est déjà pris par un autre compte
            $check = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
            $check->execute([$nouvel_email, $user_id]);
            
            if ($check->fetch()) {
                $erreur = "Cette adresse email est déjà utilisée par un autre compte.";
            } else {
                // Mise à jour classique (Nom et Email)
                if (empty($nouveau_mdp)) {
                    $update = $conn->prepare("UPDATE utilisateurs SET nom = ?, email = ? WHERE id = ?");
                    $update->execute([$nouveau_nom, $nouvel_email, $user_id]);
                } else {
                    // Mise à jour avec nouveau mot de passe
                    $hash = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
                    $update = $conn->prepare("UPDATE utilisateurs SET nom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
                    $update->execute([$nouveau_nom, $nouvel_email, $hash, $user_id]);
                }

                // Mettre à jour la session pour que l'affichage change immédiatement
                $_SESSION['user_nom'] = $nouveau_nom;
                
                $message = "Vos paramètres ont été mis à jour avec succès.";
                // Mettre à jour les variables pour l'affichage
                $user_data['nom'] = $nouveau_nom;
                $user_data['email'] = $nouvel_email;
            }
        } catch (PDOException $e) {
            $erreur = "Une erreur est survenue lors de la mise à jour.";
        }
    } else {
        $erreur = "Le nom et l'email sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - RPL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white min-h-screen flex flex-col hidden md:flex">
        <div class="p-6 text-2xl font-bold border-b border-slate-800">Système RPL</div>
        <nav class="flex-grow p-4 space-y-2">
            <a href="dashboard.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                <i class="fas fa-home w-6 mr-3"></i><span>Tableau de bord</span>
            </a>
            <a href="profile.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                <i class="fas fa-user-circle w-6 mr-3"></i><span>Profil</span>
            </a>
            <a href="settings.php" class="flex items-center px-4 py-3 bg-indigo-600 text-white rounded-lg shadow-lg">
                <i class="fas fa-cog w-6 mr-3"></i><span>Paramètres</span>
            </a>
        </nav>
        <div class="p-4 border-t border-slate-800">
            <a href="logout.php" class="flex items-center px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-lg font-semibold">
                <i class="fas fa-sign-out-alt w-6 mr-3"></i><span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow p-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-slate-800 mb-8">Paramètres du compte</h1>
            
            <?php if($message): ?>
                <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-200 font-medium flex items-center gap-3">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if($erreur): ?>
                <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200 font-medium flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($erreur); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-8">
                <form method="POST" action="" class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nom complet</label>
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($user_data['nom']); ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Adresse Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nouveau mot de passe <span class="text-xs text-slate-400 font-normal">(Laisser vide pour ne pas modifier)</span></label>
                        <input type="password" name="nouveau_mdp" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white font-bold px-8 py-3 rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-200">
                            Enregistrer les modifications
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
</body>
</html>