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

// Si la table soumissions n'existe pas encore, on gère l'erreur proprement
try {
    $stmt_soumissions = $conn->query("SELECT * FROM soumissions ORDER BY date_soumission DESC");
    $soumissions = $stmt_soumissions->fetchAll();
} catch (Exception $e) {
    $soumissions = [];
    $erreur_table = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Soumissions | RPL Admin</title>
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
            <a href="admin_soumissions.php" class="flex items-center gap-3 p-3 bg-indigo-600 rounded-xl text-white font-medium shadow-lg shadow-indigo-500/20 transition"><i class="fas fa-file-alt w-5"></i> Soumissions</a>
            <a href="admin_articles.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-newspaper w-5"></i> Articles publiés</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Système</p>
            <a href="admin_configuration.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-cog w-5"></i> Configuration</a>
        </nav>
    </aside>

    <main class="ml-72 p-8 lg:p-12 w-full">
        <header class="mb-10">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Soumissions en attente</h2>
            <p class="text-slate-500 font-medium">Gérez les manuscrits soumis par les auteurs.</p>
        </header>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Article & Auteur</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Statut</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if(isset($erreur_table) || empty($soumissions)): ?>
                        <tr><td colspan="4" class="p-10 text-center text-slate-400 italic">Aucune soumission en attente pour le moment.</td></tr>
                    <?php else: ?>
                        <?php foreach ($soumissions as $s): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800"><?php echo htmlspecialchars($s['titre']); ?></div>
                                <div class="text-slate-500 text-xs"><?php echo htmlspecialchars($s['nom_auteur']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs"><?php echo date('d/m/Y', strtotime($s['date_soumission'])); ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">En évaluation</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button class="p-2 text-slate-400 hover:text-emerald-600 transition bg-white border border-slate-200 rounded-lg hover:border-emerald-200 shadow-sm" title="Accepter"><i class="fas fa-check"></i></button>
                                <button class="p-2 text-slate-400 hover:text-red-500 transition bg-white border border-slate-200 rounded-lg hover:border-red-200 shadow-sm" title="Refuser"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>