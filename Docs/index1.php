<?php
// On inclut le fichier de connexion pour vérifier si l'utilisateur est connecté
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPL - Revue de Pensée Libre</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600&display=swap');
        
        body { font-family: 'Inter', sans-serif; }
        .serif { font-family: 'Playfair Display', serif; }
        
        .nav-link {
            position: relative;
            transition: color 0.3s;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: #78350f;
            transition: width 0.3s;
        }
        .nav-link:hover::after { width: 100%; }
    </style>
</head>
<body class="bg-[#fcfcf9] text-slate-900 selection:bg-amber-100">

    <!-- NAVIGATION MISE À JOUR -->
    <nav class="sticky top-0 z-50 bg-[#fcfcf9]/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="index.php" class="serif text-2xl font-bold tracking-tighter">RPL.</a>
                <div class="hidden md:flex items-center gap-6 text-[10px] uppercase tracking-[0.2em] font-bold text-slate-500">
                    <a href="#" class="nav-link hover:text-slate-900">Articles</a>
                    <a href="#" class="nav-link hover:text-slate-900">Archives</a>
                    <a href="#" class="nav-link hover:text-slate-900">À propos</a>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- BLOC DYNAMIQUE : CONNEXION / DECONNEXION -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-4">
                        <a href="dashboard.php" class="text-[10px] uppercase tracking-widest font-bold text-amber-700 hover:text-amber-800">
                            Mon Espace (<?php echo htmlspecialchars($_SESSION['user_nom']); ?>)
                        </a>
                        <a href="deconnexion.php" class="border border-slate-200 px-4 py-2 text-[10px] uppercase tracking-widest font-bold hover:bg-slate-50 transition">
                            Déconnexion
                        </a>
                    </div>
                <?php else: ?>
                    <a href="connexion.php" class="text-[10px] uppercase tracking-widest font-bold text-slate-500 hover:text-slate-900">
                        Connexion
                    </a>
                    <a href="inscription.php" class="bg-slate-900 text-white px-5 py-2.5 text-[10px] uppercase tracking-widest font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                        S'abonner
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <header class="max-w-7xl mx-auto px-6 pt-20 pb-16 border-b border-slate-100">
        <div class="max-w-3xl">
            <span class="text-[10px] uppercase tracking-[0.3em] font-bold text-amber-700 mb-4 block">Édition de Printemps 2024</span>
            <h1 class="serif text-6xl md:text-8xl font-bold leading-[0.9] mb-8 italic">Penser la liberté dans l'ère numérique.</h1>
            <p class="text-lg text-slate-600 leading-relaxed mb-8 max-w-xl">
                Une exploration trimestrielle des idées qui façonnent notre avenir, entre philosophie classique et révolutions technologiques.
            </p>
        </div>
    </header>

    <!-- SECTION ARTICLES (Exemple) -->
    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <!-- Article 1 -->
            <div class="group cursor-pointer">
                <div class="aspect-[4/5] bg-slate-100 mb-6 overflow-hidden">
                    <div class="w-full h-full bg-slate-200 group-hover:scale-105 transition duration-700"></div>
                </div>
                <span class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Philosophie</span>
                <h3 class="serif text-2xl font-bold mt-2 group-hover:text-amber-700 transition">Le mythe de l'algorithme souverain.</h3>
            </div>
            <!-- Article 2 -->
            <div class="group cursor-pointer">
                <div class="aspect-[4/5] bg-slate-100 mb-6 overflow-hidden">
                    <div class="w-full h-full bg-slate-300 group-hover:scale-105 transition duration-700"></div>
                </div>
                <span class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Société</span>
                <h3 class="serif text-2xl font-bold mt-2 group-hover:text-amber-700 transition">L'art de la conversation au XXIème siècle.</h3>
            </div>
            <!-- Article 3 -->
            <div class="group cursor-pointer">
                <div class="aspect-[4/5] bg-slate-100 mb-6 overflow-hidden">
                    <div class="w-full h-full bg-slate-400 group-hover:scale-105 transition duration-700"></div>
                </div>
                <span class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Politique</span>
                <h3 class="serif text-2xl font-bold mt-2 group-hover:text-amber-700 transition">Démocratie liquide : l'utopie réalisée ?</h3>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12 border-b border-slate-800 pb-12">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <svg width="30" height="30" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 5L5 12L20 19L35 12L20 5Z" stroke="#b45309" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5 20V28C5 28 10 32 20 32C30 32 35 28 35 28V20" stroke="#b45309" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <h5 class="text-xl font-bold italic serif">RPL</h5>
                    </div>
                    <p class="text-slate-400 text-sm font-light">
                        Favoriser la diffusion de la pensée philosophique rigoureuse dans le contexte gabonais et
                        international.
                    </p>
                </div>
                <div>
                    <h6 class="font-bold uppercase text-xs tracking-[0.2em] text-amber-500 mb-6">Navigation</h6>
                    <ul class="space-y-3 text-slate-400 text-sm">
                        <li><button onclick="showPage('home')" class="hover:text-white transition">Accueil</button></li>
                        <li><button onclick="showPage('about')" class="hover:text-white transition">La Revue</button>
                        </li>
                        <li><button onclick="showPage('archive')" class="hover:text-white transition">Numéros &
                                Archives</button></li>
                        <li><button onclick="showPage('submission')" class="hover:text-white transition">Soumettre un
                                article</button></li>
                    </ul>
                </div>
                <div>
                    <h6 class="font-bold uppercase text-xs tracking-[0.2em] text-amber-500 mb-6">Partenaires</h6>
                    <ul class="space-y-3 text-slate-400 text-sm font-light">
                        <li>Université Omar Bongo (UOB)</li>
                        <li>École Normale Supérieure (ENS)</li>
                        <li>IRSH, Gabon</li>
                        <li>ULB, Belgique</li>
                    </ul>
                </div>
                <div>
                    <h6 class="font-bold uppercase text-xs tracking-[0.2em] text-amber-500 mb-6">Abonnement</h6>
                    <p class="text-sm text-slate-400 mb-4">Recevez les alertes pour chaque nouveau numéro.</p>
                    <div class="flex">
                        <input type="email" placeholder="Votre email"
                            class="bg-slate-800 border-none rounded-l px-4 py-2 text-sm w-full focus:ring-1 focus:ring-amber-500">
                        <button class="bg-amber-700 px-4 py-2 rounded-r hover:bg-amber-800"><i
                                class="fas fa-paper-plane"></i></button>
                    </div>


                </div>
            </div>
            <div
                class="flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] md:text-xs text-slate-500 uppercase tracking-widest font-bold">
                <p>© 2026 Revue de Philosophie de Libreville - ISSN en attente</p>

                <div class="flex gap-8">
                    <a href="ethique.html" class="hover:text-white transition">Éthique & Plagiat</a>
                    <a href="mentions-legales.html" class="hover:text-white transition">Mentions Légales</a>
                    <a href="plan.html" class="hover:text-white transition">Plan du site</a>
                </div>

            </div>
        </div>
    </footer>

</body>
</html>