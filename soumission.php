<?php
session_start();
require_once 'db_config.php';

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom_complet'] ?? '';
    $email = $_POST['email'] ?? '';
    $affiliation = $_POST['affiliation'] ?? '';
    $titre = $_POST['titre'] ?? '';
    $rubrique = $_POST['rubrique'] ?? '';
    $resume = $_POST['resume'] ?? '';

    // Gestion de l'upload du fichier
    if (isset($_FILES['manuscrit']) && $_FILES['manuscrit']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['manuscrit']['tmp_name'];
        $file_name = $_FILES['manuscrit']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Extensions autorisées
        $allowed_extensions = ['pdf', 'doc', 'docx'];

        if (in_array($file_extension, $allowed_extensions)) {
            // Création du dossier d'upload s'il n'existe pas
            $upload_dir = 'uploads/soumissions/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Nouveau nom de fichier sécurisé (timestamp + nom original nettoyé)
            $new_file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
            $destination = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_path, $destination)) {
                try {
                    // Enregistrement dans la base de données (Table 'soumissions')
                    $sql = "INSERT INTO soumissions (nom_auteur, email, affiliation, titre, rubrique, resume, fichier_path) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nom, $email, $affiliation, $titre, $rubrique, $resume, $destination]);
                    
                    $success = true;
                    $message = "Votre manuscrit a été soumis avec succès. Notre comité de lecture vous contactera prochainement.";
                } catch (PDOException $e) {
                    // Si la table 'soumissions' n'existe pas encore, on prévient gentiment
                    $message = "Le fichier a été téléchargé, mais une erreur de base de données est survenue. (Vérifiez la table 'soumissions').";
                }
            } else {
                $message = "Erreur lors du déplacement du fichier sur le serveur.";
            }
        } else {
            $message = "Format de fichier non autorisé. Veuillez envoyer un PDF ou un document Word (.doc, .docx).";
        }
    } else {
        $message = "Veuillez joindre votre manuscrit.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumettre un Article | RPL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
        .serif { font-family: 'Playfair Display', serif; }
        .hero-bg { background-color: #1e293b; background-image: url('https://images.unsplash.com/photo-1455390582262-044cdead27d8?auto=format&fit=crop&w=1500&q=80'); background-size: cover; background-position: center; background-blend-mode: overlay; }
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
                            <path d="M20 5L5 12L20 19L35 12L20 5Z" stroke="white" stroke-width="2" />
                            <path d="M5 20V28C5 28 10 32 20 32C30 32 35 28 35 28V20" stroke="white" stroke-width="2" />
                            <path d="M20 19V32" stroke="white" stroke-width="2" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold leading-none">RPL</h1>
                        <p class="text-[9px] tracking-widest text-amber-700 font-bold uppercase">Libreville</p>
                    </div>
                </a>
                <nav class="hidden md:flex gap-8 items-center text-[10px] font-bold uppercase tracking-[0.15em]">
                    <a href="index.php" class="text-slate-600 hover:text-amber-700 transition">Accueil</a>
                    <a href="archives.php" class="text-slate-600 hover:text-amber-700 transition">Archives</a>
                    <a href="about.php" class="text-slate-600 hover:text-amber-700 transition">A propos</a>
                    <a href="contact.html" class="text-slate-600 hover:text-amber-700 transition">Contact</a>
                    <div class="h-4 w-px bg-slate-200 mx-2"></div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="text-amber-700">Mon Espace</a>
                    <?php else: ?>
                        <a href="login.php" class="text-slate-500 hover:text-slate-900 transition">Connexion</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- HEADER PAGE -->
    <section class="hero-bg py-24 text-center text-white">
        <div class="max-w-3xl mx-auto px-6">
            <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-amber-500 mb-4 block">Comité de lecture</span>
            <h1 class="text-4xl md:text-5xl font-bold serif italic mb-6">Soumettre un manuscrit</h1>
            <p class="text-lg text-slate-300 font-light">
                Nous accueillons les contributions originales en philosophie et sciences humaines. Tous les manuscrits sont soumis à une évaluation en double aveugle.
            </p>
        </div>
    </section>

    <!-- CONTENU -->
    <main class="max-w-6xl mx-auto px-6 py-16">
        
        <?php if ($message): ?>
            <div class="<?php echo $success ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'; ?> border p-6 rounded-2xl mb-10 text-center shadow-sm">
                <i class="fas <?php echo $success ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-triangle text-red-500'; ?> text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2"><?php echo $success ? 'Soumission réussie !' : 'Oups, une erreur est survenue'; ?></h3>
                <p><?php echo $message; ?></p>
                <?php if ($success): ?>
                    <div class="mt-6">
                        <a href="index.php" class="inline-block bg-slate-900 text-white px-6 py-3 rounded-lg font-bold text-sm hover:bg-slate-800 transition">Retour à l'accueil</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Instructions Auteurs -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-slate-900 text-white p-8 rounded-2xl shadow-lg">
                    <h3 class="text-xl font-bold serif italic mb-6">Directives aux auteurs</h3>
                    <ul class="space-y-4 text-sm text-slate-300">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-file-word text-amber-500 mt-1"></i>
                            <span>Le manuscrit doit être envoyé au format Word (.docx).</span>
                        </li>
                        
                        <li class="flex items-start gap-3">
                            <i class="fas fa-file-word text-amber-500 mt-1"></i>
                            <span>Police : Times New Roman, 12 points, Interligne 1,5.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-align-left text-amber-500 mt-1"></i>
                            <span>Longueur recommandée : entre 6 000 et 10 000 mots.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-list text-amber-500 mt-1"></i>
                            <span>Inclure un résumé (français et anglais) entre 150 et 300 mots maximum.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-list text-amber-500 mt-1"></i>
                            <span>Inclure 5 mots clés obligatoires.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-list text-amber-500 mt-1"></i>
                            <span>Les citations, les notes de page et la bibliographie doivent suivre un style académique (CAMES).</span>
                        </li>
                    
                    </ul> <br>

                    <h3 class="text-xl font-bold serif italic mb-6">Structure de l'article</h3>
                    <ul class="space-y-4 text-sm text-slate-300">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-file-word text-amber-500 mt-1"></i>
                            <span>Introduction.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-file-word text-amber-500 mt-1"></i>
                            <span>Développement.</span>
                        </li>

                        <li class="flex items-start gap-3">
                            <i class="fas fa-file-word text-amber-500 mt-1"></i>
                            <span>Conclusion.</span>
                        </li>
                    </ul></br>

                    <h3 class="text-xl font-bold serif italic mb-6">Evaluation scientifique</h3>
                    <ul class="space-y-4 text-sm text-slate-300">
                        <li class="flex items-start gap-3">
                            <i class="fa-mask"></i>
                            <span>Double aveugle.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-calendar-check"></i>
                            <span>Délai moyen : 4  à 8 semaines.</span>
                        </li>
                        
    
    
                    </ul>
                    <hr class="border-slate-700 my-6">
                    <a href="ethique.html" class="text-amber-500 text-xs font-bold uppercase tracking-widest hover:text-white transition flex items-center gap-2">
                        Lire la charte éthique <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="lg:col-span-2 bg-white p-10 rounded-2xl shadow-sm border border-slate-200">
                <h2 class="text-2xl font-bold text-slate-800 mb-8 border-b pb-4">Formulaire de Soumission</h2>
                
                <form action="soumission.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <!-- Infos Auteur -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Nom et Prénom</label>
                            <input type="text" name="nom_complet" value="<?php echo isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : ''; ?>" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Email </label>
                            <input type="email" name="email" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Affiliation (Université, Labo...)</label>
                        <input type="text" name="affiliation" placeholder="Ex: Université Omar Bongo" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none transition">
                    </div>

                    <hr class="border-slate-100 my-8">

                    <!-- Infos Article -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Titre de l'article</label>
                        <input type="text" name="titre" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none transition font-bold text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Rubrique souhaitée</label>
                        <select name="rubrique" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none transition">
                            <option value="Article de recherche">Article de recherche</option>
                            <option value="Commentaire">Commentaire ou Exégèse</option>
                            <option value="Recension">Recension d'ouvrage</option>
                            <option value="Traduction">Traduction inédite</option>
                            <option value="Traduction">Numéro thématique</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Résumé (Abstract)</label>
                        <textarea name="resume" rows="5" required placeholder="Saisissez ici le résumé de votre article..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none transition"></textarea>
                    </div>

                   <!-- Upload Fichier -->
<div class="mt-8">
    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">
        Manuscrit (Fichier)
    </label>

    <!-- Input réellement utilisé, caché -->
    <input 
        type="file" 
        id="manuscrit"
        name="manuscrit"
        accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        required
        class="hidden"
    >

    <!-- Zone 100% cliquable -->
    <label 
        for="manuscrit"
        id="uploadZone"
        class="block border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center bg-slate-50 hover:bg-slate-100 transition cursor-pointer"
    >
        <i class="fas fa-cloud-upload-alt text-4xl text-amber-600 mb-4"></i>
        <p class="text-sm text-slate-600 mb-2 font-bold">Cliquez ici pour choisir un fichier</p>
        <p class="text-xs text-slate-400 mb-4">DOC ou DOCX uniquement (Max 10 Mo)</p>
        <p id="fileName" class="text-sm text-emerald-700 font-semibold hidden"></p>
    </label>
</div>

<script>
    const fileInput = document.getElementById('manuscrit');
    const fileName = document.getElementById('fileName');
    const uploadZone = document.getElementById('uploadZone');

    fileInput.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            const fichier = this.files[0];
            fileName.textContent = "Fichier sélectionné : " + fichier.name;
            fileName.classList.remove('hidden');
            uploadZone.classList.remove('border-slate-300', 'bg-slate-50');
            uploadZone.classList.add('border-emerald-400', 'bg-emerald-50');
        } else {
            fileName.textContent = '';
            fileName.classList.add('hidden');
            uploadZone.classList.remove('border-emerald-400', 'bg-emerald-50');
            uploadZone.classList.add('border-slate-300', 'bg-slate-50');
        }
    });
</script>

                    <!-- Submit -->
                    <div class="pt-6">
                        <button type="submit" class="w-full bg-amber-700 text-white py-4 rounded-xl font-bold uppercase tracking-widest text-sm hover:bg-amber-800 transition shadow-lg shadow-amber-700/30">
                            Soumettre au comité de lecture
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12 border-b border-slate-800 pb-12">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <svg width="30" height="30" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 5L5 12L20 19L35 12L20 5Z" stroke="#b45309" stroke-width="2" />
                            <path d="M5 20V28C5 28 10 32 20 32C30 32 35 28 35 28V20" stroke="#b45309" stroke-width="2" />
                        </svg>
                        <h5 class="text-xl font-bold italic serif">RIPAC</h5>
                    </div>
                    <p class="text-slate-400 text-sm font-light">Favoriser la diffusion de la pensée philosophique rigoureuse dans le contexte gabonais et international.</p>
                </div>
                <div>
                    <h6 class="font-bold uppercase text-[10px] tracking-[0.2em] text-amber-500 mb-6">Navigation</h6>
                    <ul class="space-y-3 text-slate-400 text-sm">
                        <li><a href="index.php" class="hover:text-white transition">Accueil</a></li>
                        <li><a href="archives.php" class="hover:text-white transition">Numéros & Archives</a></li>
                        <li><a href="soumission.php" class="hover:text-white transition">Soumettre un article</a></li>
                    </ul>
                </div>
                <div>
                    <h6 class="font-bold uppercase text-[10px] tracking-[0.2em] text-amber-500 mb-6">Partenaires</h6>
                    <ul class="space-y-3 text-slate-400 text-sm font-light">
                        <li>Université Omar Bongo (UOB)</li>
                        <li>École Normale Supérieure (ENS)</li>
                        <li>IRSH, Gabon</li>
                    </ul>
                </div>
                <div>
                    <h6 class="font-bold uppercase text-[10px] tracking-[0.2em] text-amber-500 mb-6">Abonnement</h6>
                    <p class="text-xs text-slate-400 mb-4 font-light">Inscrivez-vous à la newsletter scientifique.</p>
                    <!--<div class="flex">
                        <input type="email" placeholder="Votre email" class="bg-slate-800 border-none rounded-l px-4 py-2 text-xs w-full focus:ring-1 focus:ring-amber-500">
                        <button class="bg-amber-700 px-4 py-2 rounded-r hover:bg-amber-800 transition"><i class="fas fa-paper-plane"></i></button>
                    </div>-->

                    <form action="newsletter_subscribe.php" method="POST" 
      onsubmit="
        event.preventDefault();
        fetch('newsletter_subscribe.php', {method:'POST', body: new FormData(this)})
        .then(r => r.json())
        .then(d => {
          this.innerHTML = '<p class=\'text-emerald-400 text-xs font-bold\'><i class=\'fas fa-check mr-1\'></i>' + d.message + '</p>';
        })
        .catch(() => {});
      ">
    <div class="flex">
        <input type="email" name="email" placeholder="Votre email"
               class="bg-slate-800 border-none rounded-l px-4 py-2 text-xs w-full focus:ring-1 focus:ring-amber-500"
               required>
        <button type="submit" class="bg-amber-700 px-4 py-2 rounded-r hover:bg-amber-800 transition">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</form>

                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
                <p>© 2026 Revue Internationale de Philosophie Antique et Contemporaine</p>
                <div class="flex gap-8">
                    <a href="ethique.html" class="hover:text-white transition">Éthique & Plagiat</a>
                    <a href="mentions_legales.html" class="hover:text-white transition">Mentions Légales</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>