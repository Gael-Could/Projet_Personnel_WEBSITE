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

// 1. TRAITEMENT DE L'AJOUT
if (isset($_POST['add_article'])) {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $resume = $_POST['resume'];
    
    $target_dir = "uploads/pdf/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_name = time() . '_' . basename($_FILES["pdf_file"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO articles (titre, auteur, resume, fichier_path, date_publication) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt->execute([$titre, $auteur, $resume, $target_file])) {
            $message = "Article publié avec succès !";
        }
    } else {
        $message = "Erreur lors de l'upload du PDF.";
    }
}

// 2. RÉCUPÉRATION
$stmt_articles = $conn->query("SELECT * FROM articles ORDER BY date_publication DESC");
$articles = $stmt_articles->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Articles Publiés | RPL Admin</title>
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
            <a href="admin_articles.php" class="flex items-center gap-3 p-3 bg-indigo-600 rounded-xl text-white font-medium shadow-lg shadow-indigo-500/20 transition"><i class="fas fa-newspaper w-5"></i> Articles publiés</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Système</p>
            <a href="admin_configuration.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-cog w-5"></i> Configuration</a>
        </nav>
    </aside>

    <main class="ml-72 p-8 lg:p-12 w-full">
        <header class="mb-10">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Articles Publiés</h2>
            <p class="text-slate-500 font-medium">Ajoutez ou gérez les articles visibles par le public.</p>
        </header>

        <?php if($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-200 font-bold"><i class="fas fa-check-circle mr-2"></i><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Formulaire caché dans un accordéon ou direct -->
        <section class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 mb-10">
            <h3 class="font-bold text-lg mb-6 flex items-center gap-2"><i class="fas fa-plus-circle text-indigo-500"></i> Ajouter un article manuellement</h3>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <input type="text" name="titre" placeholder="Titre de l'article" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-100 outline-none">
                    <input type="text" name="auteur" placeholder="Auteur" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-100 outline-none">
                    <textarea name="resume" placeholder="Résumé" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-100 outline-none"></textarea>
                </div>
                <div class="flex flex-col justify-between">
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center bg-slate-50">
                        <i class="fas fa-file-pdf text-3xl text-slate-400 mb-2"></i>
                        <p class="text-xs text-slate-500 mb-4">Sélectionner un PDF</p>
                        <input type="file" name="pdf_file" accept=".pdf" required class="text-xs w-full">
                    </div>
                    <button type="submit" name="add_article" class="mt-4 bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition">Publier</button>
                </div>
            </form>
        </section>

        <!-- Liste -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Titre</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Auteur</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach($articles as $art): ?>
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-bold text-slate-800"><?php echo htmlspecialchars($art['titre']); ?></td>
                        <td class="px-6 py-4 text-slate-500"><?php echo htmlspecialchars($art['auteur']); ?></td>
                        <td class="px-6 py-4 text-right">
                            <a href="<?php echo htmlspecialchars($art['fichier_path']); ?>" target="_blank" class="text-indigo-600 hover:underline text-xs font-bold mr-4"><i class="fas fa-eye"></i> PDF</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>