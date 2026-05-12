<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Logique simplifiée pour traiter les formulaires (POST)
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ici, vous ajouteriez la logique de mise à jour SQL
    $message = "Paramètres mis à jour avec succès !";
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
<body class="bg-slate-50 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white min-h-screen flex flex-col hidden md:flex">
        <div class="p-6 text-2xl font-bold border-b border-slate-800">RPL System</div>
        <nav class="flex-grow p-4 space-y-2">
            <a href="dashboard.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                <i class="fas fa-home w-6"></i><span>Tableau de bord</span>
            </a>
            <a href="profile.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                <i class="fas fa-user-circle w-6"></i><span>Profil</span>
            </a>
            <a href="settings.php" class="flex items-center px-4 py-3 bg-indigo-600 text-white rounded-lg shadow-lg">
                <i class="fas fa-cog w-6"></i><span>Paramètres</span>
            </a>
        </nav>
        <div class="p-4 border-t border-slate-800 text-rose-400">
            <a href="logout.php" class="flex items-center px-4 py-3 hover:bg-rose-500/10 rounded-lg">
                <i class="fas fa-sign-out-alt w-6"></i><span>Quitter</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow p-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-slate-800 mb-8">Paramètres</h1>

            <?php if ($message): ?>
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <div class="space-y-6">
                <!-- Section Sécurité -->
                <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                        <i class="fas fa-shield-alt text-indigo-500"></i> Sécurité du compte
                    </h2>
                    
                    <form action="settings.php" method="POST" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Mot de passe actuel</label>
                                <input type="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Nouveau mot de passe</label>
                                <input type="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                            </div>
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                            Mettre à jour le mot de passe
                        </button>
                    </form>
                </section>

                <!-- Section Notifications -->
                <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                        <i class="fas fa-bell text-amber-500"></i> Notifications
                    </h2>
                    
                    <div class="space-y-4">
                        <label class="flex items-center justify-between p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                            <div>
                                <p class="font-bold text-slate-700">Alertes par Email</p>
                                <p class="text-sm text-slate-500">Recevoir un mail en cas d'activité suspecte.</p>
                            </div>
                            <input type="checkbox" checked class="w-6 h-6 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </label>
                        
                        <label class="flex items-center justify-between p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                            <div>
                                <p class="font-bold text-slate-700">Rapports Hebdomadaires</p>
                                <p class="text-sm text-slate-500">Recevoir un résumé de vos statistiques par semaine.</p>
                            </div>
                            <input type="checkbox" class="w-6 h-6 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </label>
                    </div>
                </section>

                <!-- Danger Zone -->
                <section class="bg-rose-50 rounded-2xl border border-rose-100 p-8">
                    <h2 class="text-xl font-bold text-rose-800 mb-2">Zone de danger</h2>
                    <p class="text-rose-600 mb-6 text-sm">Une fois supprimé, votre compte et toutes ses données ne pourront plus être récupérés.</p>
                    <button class="bg-rose-100 text-rose-700 px-6 py-3 rounded-xl font-bold hover:bg-rose-200 transition-colors">
                        Supprimer mon compte définitivement
                    </button>
                </section>
            </div>
        </div>
    </main>
</body>
</html>