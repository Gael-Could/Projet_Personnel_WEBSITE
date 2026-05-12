<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = "<div class='bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-200 font-bold'><i class='fas fa-check-circle mr-2'></i>Paramètres sauvegardés avec succès ! (Simulation)</div>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Configuration | RPL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex min-h-screen text-slate-800">

    <aside class="w-72 bg-[#1E2235] text-white hidden md:flex flex-col shadow-xl fixed h-full z-20">
        <div class="p-6 border-b border-slate-800 flex items-center gap-3">
            <div class="bg-indigo-500 p-2 rounded-lg"><i class="fas fa-shield-halved text-white"></i></div>
            <h1 class="text-xl font-bold tracking-tight">RPL Admin</h1>
        </div>
        <nav class="p-4 space-y-2 mt-4 flex-1">
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mb-2 tracking-widest">Tableau de bord</p>
            <a href="admin_dashboard.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-chart-pie w-5"></i> Vue d'ensemble</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Gestion</p>
            <a href="admin_utilisateurs.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-users w-5"></i> Utilisateurs</a>
            <a href="admin_soumissions.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-file-alt w-5"></i> Soumissions</a>
            <a href="admin_articles.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-newspaper w-5"></i> Articles publiés</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Système</p>
            <a href="admin_configuration.php" class="flex items-center gap-3 p-3 bg-indigo-600 rounded-xl text-white font-medium shadow-lg shadow-indigo-500/20 transition"><i class="fas fa-cog w-5"></i> Configuration</a>
        </nav>
    </aside>

    <main class="ml-72 p-8 lg:p-12 w-full">
        <header class="mb-10">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Configuration Globale</h2>
            <p class="text-slate-500 font-medium">Gérez les paramètres généraux de la Revue de Philosophie.</p>
        </header>

        <?php echo $message; ?>

        <form method="POST" class="max-w-2xl bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="font-bold text-lg mb-6 border-b pb-4">Informations du site</h3>
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nom de la revue</label>
                    <input type="text" value="Revue de Philosophie de Libreville (RPL)" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email de contact officiel</label>
                    <input type="email" value="contact@rpl-libreville.org" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100">
                </div>
                <div class="pt-4 border-t mt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" checked class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-slate-700 font-medium">Accepter de nouvelles soumissions d'articles</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="mt-8 w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition">Enregistrer les modifications</button>
        </form>
    </main>
</body>
</html>