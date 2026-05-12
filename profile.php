<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

// Récupérer les VRAIES infos depuis la base de données
$stmt = $conn->prepare("SELECT nom, email, role, created_at FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
    // Si l'utilisateur n'existe plus en base
    session_destroy();
    header("Location: connexion.php");
    exit();
}

// Formatage des données
$nom = $user_data['nom'];
$email = $user_data['email'];
$role = ($user_data['role'] === 'admin') ? 'Administrateur' : 'Membre';
$date_inscription = date("d/m/Y", strtotime($user_data['created_at']));
$avatar_color = ($user_data['role'] === 'admin') ? 'bg-red-500' : 'bg-indigo-500';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - RPL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white min-h-screen flex flex-col hidden md:flex">
        <div class="p-6 text-2xl font-bold border-b border-slate-800">Système RPL</div>
        <nav class="flex-grow p-4 space-y-2">
            <a href="dashboard.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                <i class="fas fa-home w-6 mr-3"></i><span>Tableau de bord</span>
            </a>
            <a href="profile.php" class="flex items-center px-4 py-3 bg-indigo-600 text-white rounded-lg shadow-lg">
                <i class="fas fa-user-circle w-6 mr-3"></i><span>Profil</span>
            </a>
            <a href="settings.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
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
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-slate-800 mb-8">Mon Profil</h1>
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Header Profil -->
                <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                <div class="px-8 pb-8">
                    <div class="relative flex justify-between items-end -mt-12 mb-6">
                        <div class="w-24 h-24 rounded-2xl <?php echo $avatar_color; ?> border-4 border-white shadow-lg flex items-center justify-center text-white text-3xl font-bold uppercase">
                            <?php echo substr($nom, 0, 1); ?>
                        </div>
                        <a href="parametres.php" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg font-semibold hover:bg-slate-200 transition-colors">
                            Modifier
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800"><?php echo htmlspecialchars($nom); ?></h2>
                            <p class="text-slate-500"><?php echo htmlspecialchars($role); ?></p>
                            
                            <div class="mt-8 space-y-4">
                                <div class="flex items-center gap-3 text-slate-600">
                                    <i class="fas fa-envelope w-5"></i>
                                    <span><?php echo htmlspecialchars($email); ?></span>
                                </div>
                                <div class="flex items-center gap-3 text-slate-600">
                                    <i class="fas fa-calendar-alt w-5"></i>
                                    <span>Inscrit le <?php echo $date_inscription; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-6 rounded-xl border border-slate-100">
                            <h3 class="font-bold text-slate-800 mb-4">Statistiques du compte</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white p-4 rounded-lg border border-slate-200 text-center">
                                    <p class="text-2xl font-bold text-indigo-600">0</p>
                                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Projets</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-slate-200 text-center">
                                    <p class="text-2xl font-bold text-emerald-600">100%</p>
                                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Score RPL</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>