<?php
/**
 * admin_database.php
 * Interface d'administration pour la santé de la base de données.
 */
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$nom_admin = $_SESSION['nom'] ?? 'Administrateur';
$message = "";

// Traitement (Simulation de sauvegarde pour l'UX)
if (isset($_GET['action']) && $_GET['action'] === 'backup') {
    $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-xl border border-emerald-100 font-bold mb-8'><i class='fas fa-check-circle mr-2'></i> Sauvegarde de la base de données initiée avec succès (Simulation).</div>";
    @$pdo->prepare("INSERT INTO system_logs (action, details, user_id) VALUES (?, ?, ?)")->execute(['Sauvegarde BDD', "Génération d'un backup", $_SESSION['user_id']]);
}

// Récupération des tables et du nombre de lignes
$tables_info = [];
try {
    $tables_query = $pdo->query("SHOW TABLES");
    $tables = $tables_query->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $count_query = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $count_query->fetchColumn();
        $tables_info[] = [
            'name' => $table,
            'rows' => $count
        ];
    }
} catch (Exception $e) {
    $message = "Erreur SQL : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Données | RPL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen font-sans text-slate-800">

    <!-- MENU LATÉRAL -->
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
            <a href="manage_users.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
                <i class="fas fa-users w-8"></i> <span class="font-medium">Utilisateurs</span>
            </a>
            
            <p class="px-4 text-[10px] font-semibold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Système</p>
            <a href="admin_database.php" class="flex items-center px-4 py-3 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-900/20 transition-all">
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
        <header class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tight mb-2">Base de Données</h2>
                <p class="text-slate-500 text-lg">Santé du système et gestion des tables de données.</p>
            </div>
            <a href="export_csv.php?type=logs" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 transition">
                <i class="fas fa-download mr-2"></i> Exporter la Base de données (CSV)
            </a>
        </header>

        <?php echo $message; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php foreach($tables_info as $t): ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:-translate-y-1 transition-transform">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-indigo-50 p-3 rounded-lg text-indigo-600">
                            <i class="fas fa-table"></i>
                        </div>
                        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">
                            <?php echo $t['rows']; ?> entrées
                        </span>
                    </div>
                    <h3 class="font-black text-slate-800 text-lg uppercase tracking-wider"><?php echo $t['name']; ?></h3>
                    <p class="text-sm text-slate-400 mt-2">Table active du système.</p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

</body>
</html>