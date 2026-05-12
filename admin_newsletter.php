<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// Vérification de sécurité Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

$nom_admin = $_SESSION['user_nom'] ?? 'Administrateur';
$message = "";

// 1. EXPORT CSV DES ABONNÉS
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=abonnes_newsletter_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    
    // En-têtes du CSV
    fputcsv($output, ['ID', 'Email', 'Statut', 'Date Inscription']);
    
    // Données
    $rows = $conn->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC")->fetchAll();
    foreach ($rows as $row) {
        $statut = $row['actif'] ? 'Actif' : 'Désabonné';
        fputcsv($output, [$row['id'], $row['email'], $statut, $row['subscribed_at']]);
    }
    exit();
}

// 2. SUPPRESSION D'UN ABONNÉ
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id_target = (int)$_GET['id'];
    $stmt_delete = $conn->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
    if ($stmt_delete->execute([$id_target])) {
        $message = "L'abonné a été supprimé de la liste.";
    }
}

// 3. RÉCUPÉRATION DE LA LISTE
try {
    $stmt_subs = $conn->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
    $abonnes = $stmt_subs->fetchAll();
    $total_abonnes = count($abonnes);
} catch (Exception $e) {
    // Si la table n'a pas encore été créée par le script d'inscription
    $abonnes = [];
    $total_abonnes = 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Newsletter | RPL Admin</title>
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
            <a href="admin_utilisateurs.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-users w-5"></i> Utilisateurs</a>
            <a href="admin_soumissions.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-file-alt w-5"></i> Soumissions</a>
            <a href="admin_articles.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-newspaper w-5"></i> Articles publiés</a>
            
            <!-- Lien Actif Newsletter -->
            <a href="admin_newsletter.php" class="flex items-center gap-3 p-3 bg-indigo-600 rounded-xl text-white font-medium shadow-lg shadow-indigo-500/20 transition"><i class="fas fa-envelope-open-text w-5"></i> Newsletter</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Système</p>
            <a href="admin_configuration.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-cog w-5"></i> Configuration</a>
        </nav>
    </aside>

    <main class="ml-72 p-8 lg:p-12 w-full">
        <header class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Abonnés Newsletter</h2>
                <p class="text-slate-500 font-medium">Gérez la liste de diffusion de la Revue (<?php echo $total_abonnes; ?> abonnés).</p>
            </div>
            
            <!-- NOUVEAU : Boutons d'action regroupés -->
            <div class="flex gap-3">
                <?php if($total_abonnes > 0): ?>
                <a href="admin_send_newsletter.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-md shadow-indigo-200 transition flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Rédiger un message
                </a>
                <a href="?export=csv" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-6 py-2.5 rounded-xl font-bold shadow-sm transition flex items-center gap-2">
                    <i class="fas fa-file-csv"></i> Exporter (CSV)
                </a>
                <?php endif; ?>
            </div>
        </header>

        <?php if($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-200 font-medium flex items-center gap-3"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Liste des abonnés -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date d'inscription</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Statut</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($abonnes)): ?>
                        <tr>
                            <td colspan="4" class="p-10 text-center text-slate-400 italic">Aucun abonné à la newsletter pour le moment.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($abonnes as $a): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 flex items-center gap-2">
                                    <i class="fas fa-at text-slate-400"></i> <?php echo htmlspecialchars($a['email']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                <?php echo date('d/m/Y à H:i', strtotime($a['subscribed_at'])); ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($a['actif']): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Actif</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">Désabonné</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="?delete=1&id=<?php echo $a['id']; ?>" onclick="return confirm('Supprimer cet email de la liste de diffusion ?');" class="inline-block p-2 text-slate-400 hover:text-red-500 transition bg-white border border-slate-200 rounded-lg hover:border-red-200 shadow-sm" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
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