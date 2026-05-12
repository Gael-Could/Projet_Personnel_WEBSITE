<?php
/**
 * Page d'Archives Dynamique (PHP)
 * Récupère la liste des articles depuis la base de données rpldb
 */

// 1. Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rpldb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Récupération des paramètres de l'URL
    $cat = isset($_GET['cat']) ? $_GET['cat'] : 'all';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // 3. Construction de la requête SQL dynamique
    if (!empty($search)) {
        // Recherche active : on filtre sur le titre ou l'auteur
        $query = "SELECT * FROM articles WHERE titre LIKE :search OR auteur LIKE :search ORDER BY date_publication DESC";
        $stmt = $conn->prepare($query);
        // Les % permettent de chercher le mot n'importe où dans la phrase
        $stmt->execute(['search' => '%' . $search . '%']);
    } else {
        // Aucune recherche : on affiche tout
        $query = "SELECT * FROM articles ORDER BY date_publication DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
    }
    
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives & Rubriques | RIPAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700&family=Inter:wght@400;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
        .serif {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">

    <!-- NAVIGATION -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex justify-between items-center">
            <a href="index.php" class="font-bold serif italic text-xl">RIPAC <span class="text-xs font-sans not-italic text-amber-700 uppercase tracking-widest ml-2">Archives</span></a>
            <a href="index.php" class="text-xs font-bold uppercase tracking-widest text-slate-500 hover:text-amber-700 transition">← Accueil</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-12 px-6 flex-grow w-full">
        <div class="mb-10">
            <h2 class="text-4xl font-bold serif italic mb-4">Consultation des publications</h2>
            <p class="text-slate-500 italic mb-8">Tous les articles indexés dans la base de données de la revue.</p>

            <!-- BARRE DE RECHERCHE -->
            <div class="max-w-2xl">
                <form action="archives.php" method="GET" class="flex gap-3 flex-col sm:flex-row">
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            value="<?php echo htmlspecialchars($search); ?>" 
                            placeholder="Rechercher un titre, un auteur..." 
                            class="w-full pl-12 pr-4 py-3 rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-sm"
                        >
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-lg font-bold hover:bg-slate-800 transition flex-grow sm:flex-none whitespace-nowrap shadow-sm">
                            Chercher
                        </button>
                        
                        <!-- Bouton pour réinitialiser la recherche si une recherche est active -->
                        <?php if(!empty($search)): ?>
                            <a href="archives.php" class="bg-slate-200 text-slate-600 px-4 py-3 rounded-lg font-bold hover:bg-slate-300 transition flex items-center justify-center shadow-sm" title="Effacer la recherche">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Message de résultat de recherche -->
            <?php if(!empty($search)): ?>
                <div class="mt-4 text-sm text-slate-600">
                    <?php echo count($articles); ?> résultat(s) pour "<strong><?php echo htmlspecialchars($search); ?></strong>"
                </div>
            <?php endif; ?>
        </div>

        <!-- Liste des Articles (Générée par PHP) -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (count($articles) > 0): ?>
                <?php foreach($articles as $article): ?>
                <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm hover:shadow-md transition-shadow flex flex-col">
                    <span class="text-[10px] font-bold text-amber-700 uppercase mb-2 block tracking-widest">
                        Article Scientifique
                    </span>
                    <h4 class="font-bold text-lg mb-2 italic flex-grow">
                        <?php echo htmlspecialchars($article['titre']); ?>
                    </h4>
                    <p class="text-sm text-slate-500 mb-4">
                        Par <?php echo htmlspecialchars($article['auteur']); ?> — <?php echo htmlspecialchars($article['annee']); ?>
                    </p>
                    <a href="article_modele.php?id=<?php echo $article['id']; ?>" class="text-slate-900 font-bold text-xs hover:text-amber-700 transition-colors inline-flex items-center w-fit">
                        Lire l'article <i class="fas fa-arrow-right ml-2 text-[10px]"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full bg-white rounded-lg border border-slate-200 p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-2xl text-slate-300"></i>
                    </div>
                    <p class="text-slate-500 italic text-lg mb-4">Aucun article ne correspond à votre recherche.</p>
                    <a href="archives.php" class="text-amber-700 font-bold hover:underline">Voir tous les articles</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-white py-12 mt-12 w-full">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">© 2026 Revue de Philosophie de Libreville</p>
        </div>
    </footer>

</body>
</html>