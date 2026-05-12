<?php
/**
 * dashboard.php
 * Ce fichier gère l'affichage du tableau de bord utilisateur après connexion.
 */

// Démarrage sécurisé de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Vérification de la session : Si l'utilisateur n'est pas connecté, redirection immédiate
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php"); // Redirection vers ta page de connexion
    exit();
}

// 2. Récupération des données de session (CORRIGÉ avec les bonnes clés)
$nom_utilisateur = $_SESSION['user_nom'] ?? 'Utilisateur';
$role_brut = $_SESSION['user_role'] ?? 'utilisateur';

// Formatage du rôle pour l'affichage
$role_utilisateur = ($role_brut === 'admin') ? 'Administrateur' : 'Membre';

// Choix de la couleur du badge selon le rôle
$badge_class = ($role_brut === 'admin') 
    ? 'bg-red-500/10 text-red-400 border border-red-500/20' 
    : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système RPL - Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <div class="flex min-h-screen">
        <!-- BARRE LATÉRALE (Sidebar) -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-xl hidden md:flex">
            <!-- Logo / Titre -->
            <div class="p-6 border-b border-slate-800">
                <h1 class="text-xl font-bold tracking-tight">Système RPL</h1>
            </div>

            <!-- Profil Utilisateur -->
            <div class="p-6">
                <p class="text-xs text-slate-400 uppercase font-semibold mb-2">Connecté en tant que :</p>
                <p class="text-lg font-bold text-white mb-2"><?php echo htmlspecialchars($nom_utilisateur); ?></p>
                
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold <?php echo $badge_class; ?>">
                    <?php echo htmlspecialchars($role_utilisateur); ?>
                </span>
            </div>

            <!-- Navigation -->
            <nav class="flex-grow px-4 mt-4 space-y-2">
                <a href="dashboard.php" class="flex items-center px-4 py-3 bg-blue-600 text-white rounded-lg shadow-sm">
                    <i class="fas fa-home w-6 mr-3"></i>
                    <span>Accueil</span>
                </a>
                <a href="profile.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                    <i class="fas fa-user-circle w-6 mr-3"></i>
                    <span>Profil</span>
                </a>
                <a href="parametres.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                    <i class="fas fa-cog w-6 mr-3"></i>
                    <span>Paramètres</span>
                </a>
            </nav>

            <!-- Pied de la Sidebar (Bouton déconnexion) -->
            <div class="p-4 border-t border-slate-800">
                <a href="logout.php" class="flex items-center px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-lg transition-all font-semibold">
                    <i class="fas fa-sign-out-alt w-6 mr-3"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </aside>

        <!-- CONTENU PRINCIPAL -->
        <main class="flex-grow p-8">
            <header class="mb-10 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900">Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?></h2>
                    <p class="text-slate-500 mt-1">Félicitations, vous êtes maintenant connecté au système de gestion RPL.</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
                    <span class="text-sm font-medium text-gray-500"><?php echo date('d/m/Y'); ?></span>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Carte Statut -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-lg mr-4">
                            <i class="fas fa-shield-alt text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Statut du compte</h3>
                    </div>
                    <p class="text-gray-600">Votre rôle actuel permet d'accéder aux fonctionnalités de <strong><?php echo htmlspecialchars($role_utilisateur); ?></strong>.</p>
                </div>

                <!-- Carte Informations Session -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-purple-100 text-purple-600 rounded-lg mr-4">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Dernière activité</h3>
                    </div>
                    <p class="text-gray-600">Session démarrée aujourd'hui à <?php echo date('H:i'); ?>.</p>
                </div>

                <!-- Carte Actions -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-orange-100 text-orange-600 rounded-lg mr-4">
                            <i class="fas fa-list text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Actions rapides</h3>
                    </div>
                    <a href="parametres.php" class="block w-full py-2 px-4 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-bold transition-colors">
                        Mettre à jour le profil
                    </a>
                </div>
            </div>

            <!-- Zone de contenu vide -->
            <div class="mt-10 bg-white p-10 rounded-2xl shadow-sm border border-gray-200 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-3xl text-gray-300"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800">Aucun projet actif</h4>
                <p class="text-gray-500 max-w-sm mt-2">C'est ici que s'afficheront vos données de gestion une fois que vous aurez commencé à utiliser le système.</p>
            </div>
        </main>
    </div>

</body>
</html>