<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

$nom_admin = $_SESSION['user_nom'] ?? 'Administrateur';
$message = "";
$erreur = "";

// 1. TRAITEMENT : BASCULER LE STATUT (ACTIF/INACTIF)
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id_target = (int)$_GET['id'];
    
    // Empêcher l'admin de se désactiver lui-même
    if ($id_target === $_SESSION['user_id']) {
        $erreur = "Vous ne pouvez pas suspendre votre propre compte.";
    } else {
        // Récupérer le statut actuel
        $stmt_status = $conn->prepare("SELECT actif FROM utilisateurs WHERE id = ?");
        $stmt_status->execute([$id_target]);
        $user_status = $stmt_status->fetch();

        if ($user_status) {
            $nouveau_statut = $user_status['actif'] ? 0 : 1;
            $stmt_update = $conn->prepare("UPDATE utilisateurs SET actif = ? WHERE id = ?");
            if ($stmt_update->execute([$nouveau_statut, $id_target])) {
                $action_msg = $nouveau_statut ? "réactivé" : "suspendu";
                $message = "Le compte utilisateur a été $action_msg avec succès.";
            }
        }
    }
}

// 2. TRAITEMENT : SUPPRESSION DÉFINITIVE
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id_target = (int)$_GET['id'];

    if ($id_target === $_SESSION['user_id']) {
        $erreur = "Vous ne pouvez pas supprimer votre propre compte.";
    } else {
        $stmt_delete = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        if ($stmt_delete->execute([$id_target])) {
            $message = "L'utilisateur a été supprimé définitivement.";
        }
    }
}

// 3. TRAITEMENT : CRÉATION D'UN UTILISATEUR
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_user'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];

    if (!empty($nom) && !empty($email) && !empty($mdp)) {
        $stmt_check = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt_check->execute([$email]);
        
        if ($stmt_check->fetch()) {
            $erreur = "Cette adresse email est déjà utilisée.";
        } else {
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            // On crée un utilisateur standard (role = utilisateur)
            $stmt_insert = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif, created_at) VALUES (?, ?, ?, 'utilisateur', 1, NOW())");
            
            if ($stmt_insert->execute([$nom, $email, $hash])) {
                $message = "Le nouvel utilisateur a été créé avec succès.";
            } else {
                $erreur = "Erreur lors de la création du compte.";
            }
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

// 4. RÉCUPÉRATION DE LA LISTE DES UTILISATEURS (Généralement on exclut les admins pour la clarté)
$stmt_users = $conn->query("SELECT * FROM utilisateurs ORDER BY created_at DESC");
$utilisateurs = $stmt_users->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Utilisateurs | RPL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex min-h-screen text-slate-800">

    <!-- Sidebar -->
    <aside class="w-72 bg-[#1E2235] text-white hidden md:flex flex-col shadow-xl fixed h-full z-20">
        <div class="p-6 border-b border-slate-800 flex items-center gap-3">
            <div class="bg-indigo-500 p-2 rounded-lg"><i class="fas fa-shield-halved text-white"></i></div>
            <h1 class="text-xl font-bold tracking-tight">RPL Admin</h1>
        </div>
        <nav class="p-4 space-y-2 mt-4 flex-1">
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mb-2 tracking-widest">Tableau de bord</p>
            <a href="admin_dashboard.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-chart-pie w-5"></i> Vue d'ensemble</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Gestion</p>
            <a href="admin_administrateurs.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-user-shield w-5"></i> Équipe Admin</a>
            <a href="admin_utilisateurs.php" class="flex items-center gap-3 p-3 bg-indigo-600 rounded-xl text-white font-medium shadow-lg shadow-indigo-500/20 transition"><i class="fas fa-users w-5"></i> Utilisateurs</a>
            <a href="admin_soumissions.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-file-alt w-5"></i> Soumissions</a>
            <a href="admin_articles.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-newspaper w-5"></i> Articles publiés</a>
            <a href="admin_newsletter.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-envelope-open-text w-5"></i> Newsletter</a>

            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Système</p>
            <a href="admin_configuration.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-cog w-5"></i> Configuration</a>
        </nav>
    </aside>

    <main class="ml-72 p-8 lg:p-12 w-full">
        <header class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Gestion des Utilisateurs</h2>
                <p class="text-slate-500 font-medium">Liste de tous les comptes enregistrés sur la plateforme.</p>
            </div>
            <!-- Bouton pour afficher le formulaire de création -->
            <button onclick="document.getElementById('createUserForm').classList.toggle('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-md shadow-indigo-200 transition flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Nouvel Utilisateur
            </button>
        </header>

        <!-- Alertes -->
        <?php if($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-200 font-medium flex items-center gap-3"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if($erreur): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200 font-medium flex items-center gap-3"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($erreur); ?></div>
        <?php endif; ?>

        <!-- Formulaire de création (masqué par défaut) -->
        <div id="createUserForm" class="hidden bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-8">
            <h3 class="font-bold text-lg mb-4 text-slate-800"><i class="fas fa-plus text-indigo-500 mr-2"></i>Ajouter un nouvel utilisateur</h3>
            <form method="POST" action="" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nom complet</label>
                    <input type="text" name="nom" required class="w-full px-4 py-2 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Mot de passe</label>
                    <input type="password" name="mot_de_passe" required placeholder="8 caractères min" class="w-full px-4 py-2 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100">
                </div>
                <button type="submit" name="add_user" class="bg-indigo-600 text-white font-bold py-2 px-6 rounded-xl hover:bg-indigo-700 transition h-[42px]">
                    Créer
                </button>
            </form>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Utilisateur</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rôle</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Statut</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach ($utilisateurs as $u): ?>
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800"><?php echo htmlspecialchars($u['nom']); ?></div>
                            <div class="text-slate-500 text-xs"><?php echo htmlspecialchars($u['email']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($u['role'] === 'admin'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100"><i class="fas fa-crown text-[10px]"></i> Admin</span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">Membre</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($u['actif']): ?>
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Actif</span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-500"><span class="w-2 h-2 rounded-full bg-red-500"></span> Suspendu</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-lg">Vous</span>
                            <?php else: ?>
                                <!-- Bouton Suspendre / Réactiver -->
                                <a href="?toggle_status=1&id=<?php echo $u['id']; ?>" class="inline-block p-2 text-slate-400 hover:text-amber-600 transition bg-white border border-slate-200 rounded-lg hover:border-amber-200 shadow-sm" title="<?php echo $u['actif'] ? 'Suspendre' : 'Réactiver'; ?>">
                                    <i class="fas <?php echo $u['actif'] ? 'fa-ban' : 'fa-check'; ?>"></i>
                                </a>
                                <!-- Bouton Supprimer -->
                                <a href="?delete=1&id=<?php echo $u['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ? Cette action est irréversible.');" class="inline-block p-2 text-slate-400 hover:text-red-500 transition bg-white border border-slate-200 rounded-lg hover:border-red-200 shadow-sm" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>