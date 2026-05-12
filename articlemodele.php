<?php
/**
 * articlemodele.php
 * Affiche un article complet depuis la base de données.
 * Appelé depuis archives.php avec ?id=XX
 */

require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Récupération et validation de l'ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: archives.php');
    exit;
}

// Récupération de l'article
try {
    $stmt = $conn->prepare("SELECT * FROM articles WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $article = null;
}

if (!$article) {
    // Article introuvable → redirection
    header('Location: archives.php');
    exit;
}

// Articles liés (même rubrique, hors article courant)
$articlesLies = [];
try {
    $stmt2 = $conn->prepare("SELECT id, titre, auteur, annee FROM articles WHERE id != ? ORDER BY date_publication DESC LIMIT 4");
    $stmt2->execute([$id]);
    $articlesLies = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { /* silencieux */ }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($article['titre']); ?> – RPL</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600&display=swap');
    body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
    .serif { font-family: 'Playfair Display', serif; }
    .article-body p { margin-bottom: 1.25rem; line-height: 1.85; color: #334155; }
    .article-body h2 { font-size: 1.25rem; font-weight: 700; margin: 2rem 0 1rem; color: #0f172a; }
    .article-body blockquote { border-left: 3px solid #b45309; padding-left: 1.5rem; margin: 1.5rem 0; font-style: italic; color: #64748b; }
  </style>
</head>
<body class="bg-slate-50 text-slate-900">

  <!-- NAVIGATION -->
  <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        <a href="index.php" class="flex items-center gap-3 group">
          <div class="bg-slate-900 p-1.5 rounded group-hover:bg-amber-700 transition-colors">
            <svg width="30" height="30" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 5L5 12L20 19L35 12L20 5Z" stroke="white" stroke-width="2"/>
              <path d="M5 20V28C5 28 10 32 20 32C30 32 35 28 35 28V20" stroke="white" stroke-width="2"/>
              <path d="M20 19V32" stroke="white" stroke-width="2"/>
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-bold leading-none">RPL</h1>
            <p class="text-[9px] tracking-widest text-amber-700 font-bold uppercase">Libreville</p>
          </div>
        </a>
        <nav class="hidden md:flex gap-8 items-center text-[10px] font-bold uppercase tracking-[0.15em]">
          <a href="index.php" class="text-slate-600 hover:text-amber-700 transition">Accueil</a>
          <a href="archives.php" class="text-amber-700 border-b border-amber-700">Archives</a>
          <a href="about.php" class="text-slate-600 hover:text-amber-700 transition">La Revue</a>
          <a href="contact.html" class="text-slate-600 hover:text-amber-700 transition">Contact</a>
          <?php if (isset($_SESSION['userid'])): ?>
            <a href="dashboard.php" class="text-amber-700"><?php echo htmlspecialchars($_SESSION['usernom'] ?? 'Mon Espace'); ?></a>
          <?php else: ?>
            <a href="connexion.php" class="text-slate-500 hover:text-slate-900 transition">Connexion</a>
          <?php endif; ?>
        </nav>
      </div>
    </div>
  </header>

  <!-- FIL D'ARIANE -->
  <div class="bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-6 py-3 text-xs text-slate-400 flex items-center gap-2">
      <a href="index.php" class="hover:text-amber-700 transition">Accueil</a>
      <i class="fas fa-chevron-right text-[8px]"></i>
      <a href="archives.php" class="hover:text-amber-700 transition">Archives</a>
      <i class="fas fa-chevron-right text-[8px]"></i>
      <span class="text-slate-600 font-semibold truncate max-w-xs"><?php echo htmlspecialchars($article['titre']); ?></span>
    </div>
  </div>

  <!-- CONTENU PRINCIPAL -->
  <main class="max-w-7xl mx-auto px-4 py-16">
    <div class="grid lg:grid-cols-3 gap-12">

      <!-- ARTICLE -->
      <article class="lg:col-span-2">
        <div class="bg-white p-10 rounded-2xl shadow-sm border border-slate-100">

          <!-- En-tête article -->
          <div class="mb-8 pb-8 border-b border-slate-100">
            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-widest mb-3 block">
              <?php echo htmlspecialchars($article['rubrique'] ?? 'Article Scientifique'); ?>
            </span>
            <h1 class="text-3xl md:text-4xl font-bold serif italic text-slate-900 mb-6 leading-tight">
              <?php echo htmlspecialchars($article['titre']); ?>
            </h1>
            <div class="flex flex-wrap gap-6 text-sm text-slate-500">
              <div class="flex items-center gap-2">
                <i class="fas fa-user-circle text-amber-700"></i>
                <span class="font-semibold text-slate-700"><?php echo htmlspecialchars($article['auteur'] ?? 'Auteur inconnu'); ?></span>
              </div>
              <?php if (!empty($article['affiliation'])): ?>
              <div class="flex items-center gap-2">
                <i class="fas fa-university text-slate-400"></i>
                <span><?php echo htmlspecialchars($article['affiliation']); ?></span>
              </div>
              <?php endif; ?>
              <?php if (!empty($article['annee'])): ?>
              <div class="flex items-center gap-2">
                <i class="fas fa-calendar-alt text-slate-400"></i>
                <span><?php echo htmlspecialchars($article['annee']); ?></span>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Résumé -->
          <?php if (!empty($article['resume'])): ?>
          <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-8">
            <h2 class="text-xs font-bold uppercase tracking-widest text-amber-700 mb-3">Résumé / Abstract</h2>
            <p class="text-sm text-slate-600 leading-relaxed italic"><?php echo nl2br(htmlspecialchars($article['resume'])); ?></p>
          </div>
          <?php endif; ?>

          <!-- Corps de l'article -->
          <div class="article-body prose max-w-none">
            <?php
            if (!empty($article['contenu'])) {
                echo nl2br(htmlspecialchars($article['contenu']));
            } else {
                echo '<p class="text-slate-400 italic text-center py-12">
                        <i class="fas fa-file-alt text-3xl mb-4 block"></i>
                        Le contenu complet de cet article est disponible en téléchargement ci-dessous.
                      </p>';
            }
            ?>
          </div>

          <!-- Téléchargement PDF -->
          <?php if (!empty($article['fichier_path'])): ?>
          <div class="mt-10 pt-8 border-t border-slate-100">
            <a href="<?php echo htmlspecialchars($article['fichier_path']); ?>"
               download
               class="inline-flex items-center gap-3 bg-slate-900 text-white px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-amber-700 transition shadow-lg">
              <i class="fas fa-file-pdf text-lg"></i>
              Télécharger le PDF
            </a>
          </div>
          <?php endif; ?>

          <!-- Métadonnées / Citation -->
          <div class="mt-10 pt-8 border-t border-slate-100">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Comment citer cet article</h3>
            <div class="bg-slate-50 rounded-lg p-4 text-sm text-slate-600 italic font-light border border-slate-200">
              <?php
                $auteur = htmlspecialchars($article['auteur'] ?? '');
                $titre  = htmlspecialchars($article['titre'] ?? '');
                $annee  = htmlspecialchars($article['annee'] ?? date('Y'));
              ?>
              <?php echo "$auteur ($annee). « $titre ». <em>Revue de Philosophie de Libreville (RPL)</em>. Université Omar Bongo, Libreville, Gabon."; ?>
            </div>
          </div>

        </div>
      </article>

      <!-- SIDEBAR -->
      <aside class="lg:col-span-1 space-y-8">

        <!-- Retour archives -->
        <a href="archives.php" class="flex items-center gap-3 text-sm font-bold text-slate-700 hover:text-amber-700 transition bg-white px-6 py-4 rounded-xl border border-slate-200 shadow-sm">
          <i class="fas fa-arrow-left text-amber-700"></i>
          Retour aux archives
        </a>

        <!-- Articles liés -->
        <?php if (!empty($articlesLies)): ?>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
          <h3 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-6">Autres articles</h3>
          <div class="space-y-5">
            <?php foreach ($articlesLies as $lie): ?>
            <a href="articlemodele.php?id=<?php echo $lie['id']; ?>" class="block group">
              <p class="text-sm font-bold text-slate-800 group-hover:text-amber-700 transition leading-snug serif italic">
                <?php echo htmlspecialchars($lie['titre']); ?>
              </p>
              <p class="text-xs text-slate-400 mt-1"><?php echo htmlspecialchars($lie['auteur'] ?? ''); ?> — <?php echo htmlspecialchars($lie['annee'] ?? ''); ?></p>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Soumettre -->
        <div class="bg-slate-900 text-white p-8 rounded-2xl">
          <i class="fas fa-pen-fancy text-amber-500 text-2xl mb-4 block"></i>
          <h3 class="font-bold text-lg serif italic mb-3">Contribuer à la RPL</h3>
          <p class="text-sm text-slate-300 font-light mb-6 leading-relaxed">Nous accueillons les contributions originales en philosophie et sciences humaines.</p>
          <a href="soumission.php" class="inline-block bg-amber-700 text-white px-6 py-3 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-amber-800 transition">
            Soumettre un manuscrit
          </a>
        </div>

      </aside>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-slate-900 text-white py-16 mt-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
        <p>&copy; 2026 Revue de Philosophie de Libreville – ISSN en attente</p>
        <div class="flex gap-8">
          <a href="ethique.html" class="hover:text-white transition">Éthique &amp; Plagiat</a>
          <a href="mentionslegales.html" class="hover:text-white transition">Mentions Légales</a>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
