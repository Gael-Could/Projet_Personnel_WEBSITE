<?php
/**
 * manage_users.php
 * Interface d'administration pour gérer les comptes utilisateurs.
 */
session_start();
require_once 'db_config.php';

// Sécurité : Seul un admin a le droit d'être ici
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialisation de la variable (pour le menu)
$nom_admin = $_SESSION['nom'] ?? 'Administrateur';
$message = "";

// Traitement des actions (Supprimer ou changer le rôle)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_id = intval($_GET['id']);
    
    // Protection : on s'empêche de modifier son propre compte par erreur
    if ($target_id === $_SESSION['user_id']) {
        $message = "<div class='bg-rose-50 text-rose-600 p-4 rounded-xl border border-rose-100 font-bold mb-8'><i class='fas fa-exclamation-triangle mr-2'></i> Vous ne pouvez pas modifier ou supprimer votre propre compte ici.</div>";
    } else {
        if ($_GET['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$target_id]);
            $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-xl border border-emerald-100 font-bold mb-8'><i class='fas fa-check-circle mr-2'></i> Utilisateur supprimé avec succès.</div>";
            
            // Log de l'action
            @$pdo->prepare("INSERT INTO system_logs (action, details, user_id) VALUES (?, ?, ?)")->execute(['Suppression', "Compte ID $target_id supprimé", $_SESSION['user_id']]);
        } 
        elseif ($_GET['action'] === 'promote') {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET role = 'admin' WHERE id = ?");
            $stmt->execute([$target_id]);
            $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-xl border border-emerald-100 font-bold mb-8'><i class='fas fa-check-circle mr-2'></i> Utilisateur promu Administrateur.</div>";
        }
        elseif ($_GET['action'] === 'demote') {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET role = 'abonne' WHERE id = ?");
            $stmt->execute([$target_id]);
            $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-xl border border-emerald-100 font-bold mb-8'><i class='fas fa-check-circle mr-2'></i> Utilisateur rétrogradé en simple abonné.</div>";
        }
    }
}

// Récupération de tous les utilisateurs
$stmt = $pdo->query("SELECT * FROM utilisateurs ORDER BY id DESC");
$utilisateurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs | RPL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen font-sans text-slate-800">

    <!-- MENU LATÉRAL (Identique à la maquette) -->
    <aside class="w-72 bg-[#0f172a] text-white flex flex-col fixed h-full shadow-xl z-20">
        <div class="p-6 border-b border-slate-800 flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-500 rounded flex items-center justify-center">
                <i class="fas fa-lock text-white text-xs"></i>
            </div>
            <h1 class="text-xl font-bold tracking-tight">RPL Admin</h1>
        </div>

        <div class="p-6 bg-slate-800/50">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                    <?php echo strtoupper(substr($nom_admin, 0, 1)); ?>
                </div>
                <div>
                    <p class="text-sm font-bold"><?php echo htmlspecialchars($nom_admin); ?></p>
                    <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest">Super Administrateur</p>
                </div>
            </div>
        </div>

        <nav class="flex-grow px-4 mt-6 space-y-1">
            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase mb-2 tracking-widest">Principal</p>
            <a href="admin_dashboard.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-chart-pie w-8"></i> <span class="font-medium">Vue d'ensemble</span>
            </a>
            <a href="admin_soumissions.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-inbox w-8"></i> <span class="font-medium">Soumissions</span>
            </a>
            <a href="manage_users.php" class="flex items-center px-4 py-3 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-900/20 transition-all">
                <i class="fas fa-users w-8"></i> <span class="font-medium">Utilisateurs</span>
            </a>
            
            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Système</p>
            <a href="admin_database.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-database w-8"></i> <span class="font-medium">Base de données</span>
            </a>
            <a href="admin_logs.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-history w-8"></i> <span class="font-medium">Logs d'activité</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800">
            <a href="logout.php" class="flex items-center px-4 py-3 text-rose-400 hover:bg-rose-500/10 rounded-xl transition-all font-semibold">
                <i class="fas fa-power-off w-8"></i> <span>Déconnexion</span>
            </a>
        </div>
    </aside>

    <main class="ml-72 p-12 w-full">
        <header class="mb-10">
            <h2 class="text-4xl font-black text-slate-900 tracking-tight mb-2">Comptes Utilisateurs</h2>
            <p class="text-slate-500 text-lg">Gérez les accès, nommez des administrateurs ou supprimez des comptes.</p>
        </header>

        <?php echo $message; ?>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-bold text-slate-800">Liste des inscrits</h3>
                <span class="bg-indigo-100 text-indigo-700 font-bold py-1 px-3 rounded-full text-xs">
                    <?php echo count($utilisateurs); ?> compte(s)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">ID</th>
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Email</th>
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Rôle</th>
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($utilisateurs as $user): ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-8 py-4 text-sm text-slate-500 font-mono">
                                #<?php echo $user['id']; ?>
                            </td>
                            
                            <td class="px-8 py-4">
                                <p class="font-bold text-slate-900"><?php echo htmlspecialchars($user['email']); ?></p>
                            </td>

                            <td class="px-8 py-4">
                                <?php if($user['role'] === 'admin'): ?>
                                    <span class="inline-block px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase">
                                        <i class="fas fa-crown mr-1"></i> Admin
                                    </span>
                                <?php else: ?>
                                    <span class="inline-block px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-black uppercase">
                                        Abonné
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="px-8 py-4 text-right">
                                <?php if($user['id'] !== $_SESSION['user_id']): // On cache les boutons pour son propre compte ?>
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        
                                        <?php if($user['role'] === 'admin'): ?>
                                            <a href="?action=demote&id=<?php echo $user['id']; ?>" class="bg-slate-200 text-slate-700 px-3 py-1.5 rounded text-xs font-bold hover:bg-slate-300 transition" title="Retirer les droits admin">
                                                <i class="fas fa-arrow-down"></i> Rétrograder
                                            </a>
                                        <?php else: ?>
                                            <a href="?action=promote&id=<?php echo $user['id']; ?>" class="bg-amber-100 text-amber-700 px-3 py-1.5 rounded text-xs font-bold hover:bg-amber-200 transition" title="Nommer Administrateur">
                                                <i class="fas fa-arrow-up"></i> Promouvoir
                                            </a>
                                        <?php endif; ?>

                                        <a href="?action=delete&id=<?php echo $user['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte définitivement ?');" class="bg-rose-100 text-rose-700 px-3 py-1.5 rounded text-xs font-bold hover:bg-rose-600 hover:text-white transition" title="Supprimer le compte">
                                            <i class="fas fa-trash"></i>
                                        </a>

                                    </div>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 italic">C'est vous</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>