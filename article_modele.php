<?php
$servername = "localhost"; $username = "root"; $password = ""; $dbname = "rpldb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // On récupère l'ID de l'article depuis l'URL (ex: article_modele.php?id=1)
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $stmt = $conn->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    if (!$article) { die("Article non trouvé."); }
} catch(PDOException $e) { die("Erreur : " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($article['titre']); ?> | RPL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Inter:wght@400;600&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .serif { font-family: 'Playfair Display', serif; }
        .article-content p { margin-bottom: 1.5rem; line-height: 1.8; }
    </style>
</head>
<body class="bg-white text-slate-900">
    <header class="border-b py-6 mb-12">
        <div class="max-w-4xl mx-auto px-6 flex justify-between items-center">
            <a href="archives.php" class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-amber-700">← Retour aux archives</a>
            <span class="serif italic font-bold text-xl">RPL</span>
        </div>
    </header>

    <article class="max-w-3xl mx-auto px-6 pb-24">
        <header class="mb-12">
            <h1 class="text-4xl md:text-5xl font-bold serif italic mb-6"><?php echo htmlspecialchars($article['titre']); ?></h1>
            <div class="flex flex-col gap-2 text-slate-500 border-l-2 border-amber-700 pl-4">
                <span class="font-bold text-slate-900"><?php echo htmlspecialchars($article['auteur']); ?></span>
                <span class="text-sm italic"><?php echo htmlspecialchars($article['affiliation']); ?></span>
                <span class="text-xs uppercase tracking-widest mt-2">Volume <?php echo $article['volume']; ?>, N°<?php echo $article['numero']; ?> (<?php echo $article['annee']; ?>)</span>
            </div>
        </header>

        <div class="bg-slate-50 p-8 mb-12 rounded-xl italic text-slate-700 leading-relaxed shadow-inner">
            <h2 class="not-italic font-bold uppercase text-[10px] tracking-widest mb-4 text-amber-700">Résumé</h2>
            <?php echo nl2br(htmlspecialchars($article['resume'])); ?>
        </div>

        <div class="article-content text-lg text-slate-800">
            <?php echo $article['contenu']; // Ici on ne met pas htmlspecialchars car le contenu peut contenir du HTML (gras, titres) ?>
        </div>
    </article>
</body>
</html>