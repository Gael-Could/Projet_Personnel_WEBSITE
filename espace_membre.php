<?php
require_once 'db_connect.php';

// SECURITE : Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

// Variables de l'utilisateur connecté
$nom_user = $_SESSION['user_nom'];
$role_user = $_SESSION['user_role'];

// Si c'est un administrateur, on peut chercher des statistiques
$stats_html = "";
if ($role_user === 'admin') {
    // Compter les messages
    $stmt1 = $conn->query("SELECT COUNT(*) FROM contacts");
    $total_messages = $stmt1->fetchColumn();

    // Compter les articles
    $stmt2 = $conn->query("SELECT COUNT(*) FROM articles");
    $total_articles = $stmt2->fetchColumn();

    $stats_html = "
        <div class='grid grid-cols-2 gap-4 mt-8'>
            <div class='bg-white p-6 rounded-xl border border-slate-200 shadow-sm'>
                <p class='text-xs font-bold uppercase text-slate-500 mb-1'>Messages reçus</p>
                <p class='text-4xl font-bold text-slate-900'>{$total_messages}</p>
            </div>
            <div class='bg-white p-6 rounded-xl border border-slate-200 shadow-sm'>
                <p class='text-xs font-bold uppercase text-slate-500 mb-1'>Articles publiés</p>
                <p class='text-4xl font-bold text-slate-900'>{$total_articles}</p>
            </div>
        </div>
    ";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace | RPL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <nav class="bg-slate-900 text-white p-4 flex justify-between items-center">
        <div class="font-bold tracking-widest uppercase text-sm">RPL Dashboard</div>
        <div class="flex items-center gap-6 text-sm">
            <span>Bonjour, <strong><?php echo htmlspecialchars($nom_user); ?></strong></span>
            <a href="deconnexion.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white font-bold transition">Déconnexion</a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold mb-2">Tableau de bord</h1>
        
        <?php if ($role_user === 'admin'): ?>
            <span class="bg-amber-700 text-white text-[10px] uppercase tracking-widest px-2 py-1 rounded font-bold">Compte Administrateur</span>
            <?php echo $stats_html; ?>
            <div class="mt-8 p-6 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-amber-800 font-bold">Mode Administration</p>
                <p class="text-sm text-amber-700 mt-2">Vous avez accès à la gestion globale du site. Bientôt, vous pourrez ajouter des articles directement depuis cette page.</p>
            </div>
        <?php else: ?>
            <span class="bg-slate-200 text-slate-700 text-[10px] uppercase tracking-widest px-2 py-1 rounded font-bold">Compte Auteur/Lecteur</span>
            <div class="mt-8 p-6 bg-white border border-slate-200 rounded-xl shadow-sm">
                <p class="text-slate-800 font-bold mb-2">Bienvenue dans votre espace</p>
                <p class="text-sm text-slate-600">Vous pouvez désormais soumettre des articles ou suivre l'état de vos publications (fonctionnalité à venir).</p>
            </div>
        <?php endif; ?>

        <div class="mt-12 text-center border-t border-slate-200 pt-8">
            <a href="index.html" class="text-slate-500 hover:text-slate-900 text-sm font-bold uppercase tracking-widest">Retourner sur le site public</a>
        </div>
    </main>
</body>
</html>