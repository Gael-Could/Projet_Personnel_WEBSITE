<?php
session_start();
require_once 'db_config.php'; // Votre fichier de connexion PDO

// --- CONFIGURATION DE LA PAGINATION ---
$resultats_par_page = 10;
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
if ($page <= 0) $page = 1;
$debut = ($page - 1) * $resultats_par_page;

// --- GESTION DES FILTRES ---
$search_user = isset($_GET['user']) ? trim($_GET['user']) : '';
$filter_action = isset($_GET['action_type']) ? $_GET['action_type'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

// Construction dynamique de la requête SQL
$conditions = [];
$params = [];

if (!empty($search_user)) {
    $conditions[] = "(utilisateur LIKE ? OR details LIKE ?)";
    $params[] = "%$search_user%";
    $params[] = "%$search_user%";
}

if (!empty($filter_action)) {
    $conditions[] = "action_type = ?";
    $params[] = $filter_action;
}

if (!empty($filter_date)) {
    $conditions[] = "DATE(date_action) = ?";
    $params[] = $filter_date;
}

$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// --- RÉCUPÉRATION DES LOGS ---
$sql = "SELECT * FROM system_logs $where_clause ORDER BY date_action DESC LIMIT $debut, $resultats_par_page";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// --- CALCUL POUR LA PAGINATION ---
$sql_count = "SELECT COUNT(*) FROM system_logs $where_clause";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_resultats = $stmt_count->fetchColumn();
$total_pages = ceil($total_resultats / $resultats_par_page);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Logs d'activité</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 p-4 lg:p-8">

    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Logs d'activité</h1>
                <p class="text-gray-500">Historique des actions effectuées sur le système</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="bg-white border px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-gray-50">
                    <i class="fas fa-print"></i> Imprimer
                </button>
                <a href="export_csv.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-indigo-700">
                    <i class="fas fa-file-export"></i> Exporter CSV
                </a>
            </div>
        </div>

        <form method="GET" class="bg-white p-6 rounded-xl shadow-sm border mb-6 flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Utilisateur</label>
                <input type="text" name="user" value="<?= htmlspecialchars($search_user) ?>" placeholder="Nom ou ID..." class="w-full border rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Type d'action</label>
                <select name="action_type" class="w-full border rounded-lg px-4 py-2 text-sm outline-none bg-white">
                    <option value="">Toutes les actions</option>
                    <option value="UPDATE" <?= $filter_action == 'UPDATE' ? 'selected' : '' ?>>UPDATE</option>
                    <option value="BACKUP" <?= $filter_action == 'BACKUP' ? 'selected' : '' ?>>BACKUP</option>
                    <option value="DELETE" <?= $filter_action == 'DELETE' ? 'selected' : '' ?>>DELETE</option>
                    <option value="LOGIN" <?= $filter_action == 'LOGIN' ? 'selected' : '' ?>>LOGIN</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Période</label>
                <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>" class="w-full border rounded-lg px-4 py-2 text-sm outline-none">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-8 py-2 rounded-lg text-sm font-bold hover:bg-slate-900 transition flex items-center gap-2">
                <i class="fas fa-filter"></i> Filtrer
            </button>
        </form>

        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4 text-[10px] font-bold text-gray-400 uppercase">Date & Heure</th>
                        <th class="p-4 text-[10px] font-bold text-gray-400 uppercase">Utilisateur</th>
                        <th class="p-4 text-[10px] font-bold text-gray-400 uppercase">Action</th>
                        <th class="p-4 text-[10px] font-bold text-gray-400 uppercase">Cible</th>
                        <th class="p-4 text-[10px] font-bold text-gray-400 uppercase">Détails</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($logs as $log): 
                        $badge_color = match($log['action_type']) {
                            'UPDATE' => 'bg-blue-100 text-blue-600',
                            'DELETE' => 'bg-red-100 text-red-600',
                            'BACKUP' => 'bg-amber-100 text-amber-600',
                            'LOGIN'  => 'bg-green-100 text-green-600',
                            default  => 'bg-gray-100 text-gray-600'
                        };
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-xs text-gray-500"><?= $log['date_action'] ?></td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] font-bold">A</span>
                                <span class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($log['utilisateur']) ?></span>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-[10px] font-bold <?= $badge_color ?>"><?= $log['action_type'] ?></span>
                        </td>
                        <td class="p-4 text-xs italic text-gray-400"><?= htmlspecialchars($log['cible']) ?></td>
                        <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($log['details']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="p-4 border-t bg-gray-50 flex justify-between items-center">
                <p class="text-xs text-gray-500">Affichage de <?= count($logs) ?> sur <?= $total_resultats ?> résultats</p>
                <div class="flex gap-1">
                    <?php if ($page > 1): ?>
                        <a href="?p=<?= $page - 1 ?>&user=<?= $search_user ?>&action_type=<?= $filter_action ?>&date=<?= $filter_date ?>" class="w-8 h-8 border rounded flex items-center justify-center hover:bg-white"><i class="fas fa-chevron-left text-xs"></i></a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?p=<?= $i ?>&user=<?= $search_user ?>&action_type=<?= $filter_action ?>&date=<?= $filter_date ?>" class="w-8 h-8 border rounded flex items-center justify-center <?= $i == $page ? 'bg-indigo-600 text-white border-indigo-600' : 'hover:bg-white' ?> text-xs font-bold"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?p=<?= $page + 1 ?>&user=<?= $search_user ?>&action_type=<?= $filter_action ?>&date=<?= $filter_date ?>" class="w-8 h-8 border rounded flex items-center justify-center hover:bg-white"><i class="fas fa-chevron-right text-xs"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>