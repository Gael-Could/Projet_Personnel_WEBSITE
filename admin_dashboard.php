<?php
/**
 * admin_dashboard.php - Version Corrigée et Enrichie
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. VERIFICATION DE SECURITE
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Si l'utilisateur essaie de forcer l'accès, on détruit tout
    session_destroy();
    header("Location: connexion.php");
    exit();
}

require_once 'db_connect.php';

//  Récupération du nom pour l'affichage
$nom_admin = $_SESSION['user_nom'] ?? 'Administrateur';

// 3. STATISTIQUES DYNAMIQUES (depuis la BDD)
// Nombre total d'utilisateurs
$stmt_users = $conn->query("SELECT COUNT(*) as total FROM utilisateurs");
$total_users = $stmt_users->fetch()['total'];

// Nombre de soumissions en attente (si la table existe, sinon on met 0 par défaut pour éviter l'erreur)
try {
    $stmt_soumissions = $conn->query("SELECT COUNT(*) as total FROM soumissions WHERE statut = 'recu'");
    $total_soumissions = $stmt_soumissions->fetch()['total'];
} catch (Exception $e) {
    $total_soumissions = 0; // Sécurité si la table est vide ou non créée
}

// Récupération des 5 derniers utilisateurs inscrits
$stmt_recent_users = $conn->query("SELECT nom, email, role, created_at, actif FROM utilisateurs ORDER BY created_at DESC LIMIT 5");
$recent_users = $stmt_recent_users->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPL Admin - Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B]">

    <div class="flex min-h-screen">
        <!-- SIDEBAR GAUCHE -->
        <aside class="w-72 bg-[#1E2235] text-white p-6 hidden lg:flex lg:flex-col shadow-xl">
            <div class="flex items-center gap-3 mb-10 px-2">
                <div class="bg-indigo-500 p-2 rounded-lg">
                    <i class="fas fa-shield-halved text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold tracking-tight">RPL Admin</h2>
            </div>

            <!-- Profil Admin -->
            <div class="flex items-center gap-4 mb-10 p-4 bg-white/5 rounded-xl border border-white/10">
                <div class="w-12 h-12 rounded-full bg-indigo-500 flex items-center justify-center font-bold text-lg shadow-inner">
                    <?php echo strtoupper(substr($nom_admin, 0, 1)); ?>
                </div>
                <div>
                    <p class="font-semibold text-sm truncate w-32"><?php echo htmlspecialchars($nom_admin); ?></p>
                    <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-wider">Super Administrateur</p>
                </div>
            </div>

            <nav class="space-y-1 flex-1">
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mb-4 px-4">Tableau de bord</p>
                <a href="admin_dashboard.php" class="flex items-center gap-3 p-3 bg-indigo-600 rounded-xl transition shadow-lg shadow-indigo-500/20"><i class="fas fa-chart-pie w-5"></i> Vue d'ensemble</a>
                
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-8 mb-4 px-4">Gestion</p>
                <a href="admin_utilisateurs.php" class="flex items-center justify-between p-3 hover:bg-white/5 rounded-xl transition text-gray-400 hover:text-white group">
                    <div class="flex items-center gap-3"><i class="fas fa-users w-5"></i> Utilisateurs</div>
                    <span class="bg-indigo-500/20 text-indigo-400 text-xs font-bold px-2 py-0.5 rounded-full group-hover:bg-indigo-500 group-hover:text-white transition"><?php echo $total_users; ?></span>
                </a>
                <a href="admin_soumissions.php" class="flex items-center justify-between p-3 hover:bg-white/5 rounded-xl transition text-gray-400 hover:text-white group">
                    <div class="flex items-center gap-3"><i class="fas fa-file-alt w-5"></i> Soumissions</div>
                    <?php if($total_soumissions > 0): ?>
                        <span class="bg-red-500/20 text-red-400 text-xs font-bold px-2 py-0.5 rounded-full group-hover:bg-red-500 group-hover:text-white transition"><?php echo $total_soumissions; ?></span>
                    <?php endif; ?>
                </a>
                <a href="admin_articles.php" class="flex items-center gap-3 p-3 hover:bg-white/5 rounded-xl transition text-gray-400 hover:text-white"><i class="fas fa-newspaper w-5"></i> Articles publiés</a>
                <a href="admin_newsletter.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-envelope-open-text w-5"></i> Newsletter</a>

                
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-8 mb-4 px-4">Système</p>
                <a href="admin_configuration.php" class="flex items-center gap-3 p-3 hover:bg-white/5 rounded-xl transition text-gray-400 hover:text-white"><i class="fas fa-cog w-5"></i> Configuration</a>
            </nav>
            
            <!-- Bouton Déconnexion en bas -->
            <div class="mt-auto pt-6 border-t border-white/10">
                <a href="logout.php" class="flex items-center gap-3 p-3 hover:bg-red-500/20 rounded-xl transition text-red-400 group">
                    <i class="fas fa-sign-out-alt w-5 group-hover:translate-x-1 transition-transform"></i> Déconnexion
                </a>
            </div>
        </aside>

        <!-- CONTENU PRINCIPAL -->
        <main class="flex-1 lg:max-h-screen lg:overflow-y-auto bg-slate-50/50">
            <!-- Header supérieur -->
            <header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 border-b border-gray-200 p-4 lg:px-8 lg:py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Console Admin</h1>
                </div>
                <div class="flex items-center gap-4">
                    <button class="relative p-2 text-gray-400 hover:text-indigo-600 transition">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                    <div class="hidden md:flex bg-white border border-gray-200 px-4 py-2 rounded-xl items-center gap-2 shadow-sm">
                        <i class="far fa-calendar-alt text-indigo-500"></i>
                        <span class="text-sm font-semibold text-gray-700"><?php echo date('d/m/Y'); ?></span>
                    </div>
                </div>
            </header>

            <div class="p-4 lg:p-8 max-w-7xl mx-auto space-y-8">
                
                <!-- Bannières d'actions rapides -->
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="admin_administrateurs.php" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-2xl shadow-lg shadow-indigo-200 transition flex items-center justify-between group cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="bg-white/20 p-3 rounded-xl"><i class="fas fa-user-plus text-xl"></i></div>
                            <div>
                                <h3 class="font-bold">Créer un Administrateur</h3>
                                <p class="text-indigo-100 text-sm">Ajouter un membre à l'équipe</p>
                            </div>
                        </div>
                        <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 group-hover:translate-x-2 transition-all"></i>
                    </a>
                    <a href="export_csv.php?type=logs" class="flex-1 bg-white border border-gray-200 hover:border-indigo-300 p-4 rounded-2xl shadow-sm transition flex items-center justify-between group cursor-pointer block">
    <div class="flex items-center gap-4">
        <div class="bg-blue-50 text-blue-600 p-3 rounded-xl"><i class="fas fa-file-export text-xl"></i></div>
        <div>
            <h3 class="font-bold text-slate-800">Exporter les données</h3>
            <p class="text-slate-500 text-sm">Télécharger les logs (CSV)</p>
        </div>
    </div>
    <i class="fas fa-download text-gray-300 group-hover:text-blue-600 transition-colors"></i>
</a>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl"><i class="fas fa-users text-xl"></i></div>
                            <span class="text-xs font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">+12% ce mois</span>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Membres Inscrits</p>
                        <h3 class="text-3xl font-extrabold text-slate-800"><?php echo $total_users; ?></h3>
                    </div>
                    
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl"><i class="fas fa-file-signature text-xl"></i></div>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Soumissions en attente</p>
                        <h3 class="text-3xl font-extrabold text-slate-800"><?php echo $total_soumissions; ?></h3>
                    </div>
                    
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl"><i class="fas fa-check-circle text-xl"></i></div>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Articles Publiés</p>
                        <h3 class="text-3xl font-extrabold text-slate-800">1</h3> <!-- Statique pour l'exemple -->
                    </div>
                </div>

                <!-- Tableau des derniers inscrits -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden mt-8">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h2 class="text-lg font-bold text-slate-800"><i class="fas fa-user-clock mr-2 text-indigo-500"></i> Derniers utilisateurs inscrits</h2>
                        <button class="text-sm text-indigo-600 font-semibold hover:underline">Voir tout</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs text-gray-400 uppercase tracking-wider bg-white">
                                    <th class="p-4 font-semibold border-b border-gray-100">Utilisateur</th>
                                    <th class="p-4 font-semibold border-b border-gray-100">Rôle</th>
                                    <th class="p-4 font-semibold border-b border-gray-100">Statut</th>
                                    <th class="p-4 font-semibold border-b border-gray-100 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50">
                                <?php if (count($recent_users) > 0): ?>
                                    <?php foreach ($recent_users as $u): ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="p-4">
                                            <p class="font-bold text-slate-800"><?php echo htmlspecialchars($u['nom']); ?></p>
                                            <p class="text-gray-500 text-xs"><?php echo htmlspecialchars($u['email']); ?></p>
                                        </td>
                                        <td class="p-4">
                                            <?php if($u['role'] === 'admin'): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100"><i class="fas fa-crown text-[10px]"></i> Admin</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">Membre</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-4">
                                            <?php if($u['actif']): ?>
                                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Actif</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-500"><span class="w-2 h-2 rounded-full bg-red-500"></span> Suspendu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-4 text-right">
                                            <button class="p-2 text-gray-400 hover:text-indigo-600 transition" title="Éditer"><i class="fas fa-pen"></i></button>
                                            <button class="p-2 text-gray-400 hover:text-red-500 transition" title="Bannir"><i class="fas fa-ban"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-gray-500">Aucun utilisateur trouvé.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>