<?php
// On inclut le fichier de connexion et on démarre la session
require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revue Internationale de Philosophie Antique et Contemporaine| RIPAC</title>    
    
</div>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600&display=swap');

        :root {
            --primary: #1e293b;
            --accent: #b45309;
        }

        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
        .serif { font-family: 'Playfair Display', serif; }
        
        .nav-link {
            position: relative;
            transition: color 0.3s;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0; height: 1px;
            bottom: -2px; left: 0;
            background-color: #b45309;
            transition: width 0.3s;
        }
        .nav-link:hover::after { width: 100%; }

        .hero-gradient {
            background: linear-gradient(rgba(30, 41, 59, 0.85), rgba(30, 41, 59, 0.75));
        }
    </style>
</head>
<body class="bg-[#fcfcf9] text-slate-900 selection:bg-amber-100">

    <!-- NAVIGATION DYNAMIQUE -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo Section -->
                <a href="index.php" class="flex items-center gap-3 group">
                    <div class="bg-slate-900 p-1.5 rounded group-hover:bg-amber-700 transition-colors">
                        <svg width="30" height="30" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 5L5 12L20 19L35 12L20 5Z" stroke="white" stroke-width="2" />
                            <path d="M5 20V28C5 28 10 32 20 32C30 32 35 28 35 28V20" stroke="white" stroke-width="2" />
                            <path d="M20 19V32" stroke="white" stroke-width="2" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold leading-none">RIPAC</h1>
                        <p class="text-[9px] tracking-widest text-amber-700 font-bold uppercase">Revue internationale de philosophie antique et contemporaine</p>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex gap-8 items-center text-[10px] font-bold uppercase tracking-[0.15em]">
                    <a href="index.php" class="text-amber-700">Accueil</a>
                    <a href="archives.php" class="text-slate-600 hover:text-amber-700 transition nav-link">Archives</a>
                    <a href="about.php" class="text-slate-600 hover:text-amber-700 transition nav-link">A propos</a>
                    <a href="contact.html" class="text-slate-600 hover:text-amber-700 transition nav-link">Contact</a>
                    
                    <div class="h-4 w-px bg-slate-200 mx-2"></div>

                    <!-- BLOC DYNAMIQUE PHP -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center gap-4">
                            <a href="dashboard.php" class="text-amber-700 hover:text-amber-800">
                               <span class="user-status">
                            <?php echo isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : 'INVITÉ'; ?>
                            </span>
                            </a>
                            <a href="deconnexion.php" class="border border-slate-200 px-4 py-2 rounded hover:bg-slate-50 transition">
                                Déconnexion
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="connexion.php" class="text-slate-500 hover:text-slate-900 transition">Connexion</a>
                        <a href="inscription.php" class="bg-slate-900 text-white px-5 py-2.5 rounded hover:bg-slate-800 transition shadow-md">
                            S'abonner
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- HERO SECTION (Design Premium) -->
    <!--<section class="relative bg-slate-900 py-32 text-white overflow-hidden">
        <div class="absolute inset-0 opacity-40 bg-[url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1500&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 hero-gradient"></div>
        
        <div class="relative max-w-5xl mx-auto px-4 text-center">
            <span class="inline-block bg-amber-700 text-[10px] font-bold uppercase tracking-[0.3em] px-4 py-1.5 rounded-full mb-8">Université Omar Bongo</span>
            <h2 class="text-5xl md:text-7xl font-bold serif italic mb-8 leading-tight">Revue internationale de philosophie antique et contemporaine</h2>
            <h1>Entre héritage antique et enjeux contemporains</h1>
            <p>Penser la cité, éclairer l'humain</p>
            
            <p class="text-xl text-slate-300 mb-12 font-light italic leading-relaxed max-w-3xl mx-auto">
                Revue internationale de philosophie de Libreville (RPL). Un espace de rigueur scientifique dédié à l'excellence académique sous la direction du Prof. Christ Olivier Mpaga.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="archives.php" class="bg-amber-700 hover:bg-amber-800 px-10 py-4 rounded font-bold transition flex items-center justify-center gap-3 text-sm uppercase tracking-widest shadow-xl">
                    <i class="fas fa-book-open"></i> Explorer les Archives
                </a>
                <a href="contact.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-md px-10 py-4 rounded font-bold transition flex items-center justify-center gap-3 text-sm uppercase tracking-widest border border-white/20">
                    Soumettre un manuscrit
                </a>
            </div>
        </div>
    </section>-->

    <section class="relative bg-slate-900 py-32 text-white overflow-hidden">
        <div
            class="absolute inset-0 opacity-30 bg-[url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1500&q=80')] bg-cover bg-center">
        </div>
        <div class="relative max-w-5xl mx-auto px-4 text-center">
            <!--<span
                class="inline-block bg-amber-700 text-[10px] font-bold uppercase tracking-[0.3em] px-4 py-1.5 rounded-full mb-8">Université
                Omar Bongo</span>-->
            <h2 class="text-5xl md:text-5xl font-bold serif italic mb-8 leading-tight">Revue internationale de philosophie Antique et Contemporaine
            </h2>
            <h1 class="text-5xl md:text-4xl font-bold serif italic mb-3 leading-tight">Entre héritage Antique et enjeux Contemporains</h1>
                        <p class="text-xl text-slate-300 mb-12 font-light italic leading-relaxed max-w-3xl mx-auto">
                "Comprendre, Interpréter et Problématiser"
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="archives.php"
                    class="bg-amber-700 hover:bg-amber-800 px-10 py-4 rounded font-bold transition flex items-center justify-center gap-3 text-sm uppercase tracking-widest">
                    <i class="fas fa-book-open"></i> Explorer les Archives
                </a>
                <a href="soumission.php"
                    class="bg-white/10 hover:bg-white/20 backdrop-blur-md px-10 py-4 rounded font-bold transition flex items-center justify-center gap-3 text-sm uppercase tracking-widest border border-white/20">
                    Soumettre un manuscrit
                </a>
            </div>
        </div>
    </section>

    <!-- MOT DU DIRECTEUR -->
    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="aspect-[4/5] bg-slate-200 rounded-2xl overflow-hidden shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=800&q=80" alt="Directeur de publication" class="w-full h-full object-cover grayscale hover:grayscale-0 transition duration-700">
                    </div>
                    <div class="absolute -bottom-6 -right-6 bg-amber-700 text-white p-8 rounded-xl hidden md:block shadow-xl">
                        <p class="serif italic text-2xl font-bold">"La philosophie n'est pas un luxe, c'est une nécessité."</p>
                    </div>
                </div>
                <div>
                    <h3 class="text-amber-700 text-xs font-bold uppercase tracking-widest mb-4">Direction Scientifique</h3>
                    <h2 class="text-4xl font-bold serif mb-6 italic">Le mot du Directeur de Publication</h2>
                    <div class="prose prose-slate text-slate-600 space-y-4 leading-relaxed italic">
                        <p>La <strong>Revue Internationale de Philosophie Antique et Contempaire </strong> s'est donnée pour mission d'être le carrefour des pensées critiques qui interrogent notre contemporanéité. Dans un monde en mutation, le Département de Philosophie de l'UOB réaffirme son engagement pour une recherche d'excellence.</p>
                        <p>Nous accueillons des contributions originales qui, par leur rigueur méthodologique et leur profondeur spéculative, contribuent à l'avancement du savoir universel.</p>
                        <p class="font-bold text-slate-900 not-italic mt-6">— Professeur Christ Olivier Mpaga</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- AXES DE RECHERCHE -->
    <section class="py-24 bg-slate-50 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold serif mb-4 italic text-slate-900">Axes de Recherche & Rubriques</h3>
                <div class="w-24 h-1 bg-amber-700 mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <a href="archives.php?cat=recherche" class="bg-white p-10 rounded-2xl border border-slate-100 shadow-sm hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-slate-900 text-white flex items-center justify-center rounded-lg mb-6 group-hover:bg-amber-700 transition-colors">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <h4 class="font-bold mb-4 uppercase text-sm tracking-widest text-slate-900">Articles de Recherche</h4>
                    <p class="text-sm text-slate-500 font-light leading-relaxed italic">Études approfondies et contributions originales aux différents domaines de la philosophie.</p>
                </a>

                <a href="archives.php?cat=commentaires" class="bg-white p-10 rounded-2xl border border-slate-100 shadow-sm hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-slate-900 text-white flex items-center justify-center rounded-lg mb-6 group-hover:bg-amber-700 transition-colors">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <h4 class="font-bold mb-4 uppercase text-sm tracking-widest text-slate-900">Commentaires & Exégèse</h4>
                    <p class="text-sm text-slate-500 font-light leading-relaxed italic">Analyses critiques et commentaires de textes classiques ou contemporains.</p>
                </a>

                <a href="archives.php?cat=recensions" class="bg-white p-10 rounded-2xl border border-slate-100 shadow-sm hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-slate-900 text-white flex items-center justify-center rounded-lg mb-6 group-hover:bg-amber-700 transition-colors">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4 class="font-bold mb-4 uppercase text-sm tracking-widest text-slate-900">Recensions d'Ouvrages</h4>
                    <p class="text-sm text-slate-500 font-light leading-relaxed italic">Notes de lecture et comptes rendus critiques des parutions récentes.</p>
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER COMPLET -->
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
<!--form action="newsletter_subscribe.php" method="POST" 
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
</form>-->

<form action="newsletter_subscribe.php" method="POST" id="newsletter-form" class="space-y-2">
    <div class="flex">
        <input 
            type="email" 
            name="email" 
            placeholder="Votre email"
            class="bg-slate-800 border-none rounded-l px-4 py-2 text-xs w-full focus:ring-1 focus:ring-amber-500"
            required
        >
        <button 
            type="submit" 
            class="bg-amber-700 px-4 py-2 rounded-r hover:bg-amber-800 transition text-white"
        >
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
    <div id="newsletter-message" class="text-xs font-bold"></div>
</form>

<script>
document.getElementById('newsletter-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = this;
    const button = form.querySelector('button[type="submit"]');
    const input = form.querySelector('input[name="email"]');
    const messageBox = document.getElementById('newsletter-message');

    messageBox.textContent = '';
    messageBox.className = 'text-xs font-bold';

    button.disabled = true;
    button.classList.add('opacity-60', 'cursor-not-allowed');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        });

        const data = await response.json();

        if (data.success) {
            messageBox.innerHTML = "<span class='text-emerald-400'><i class='fas fa-check mr-1'></i>" + data.message + "</span>";
            form.reset();
        } else {
            messageBox.innerHTML = "<span class='text-red-400'><i class='fas fa-triangle-exclamation mr-1'></i>" + data.message + "</span>";
        }
    } catch (error) {
        messageBox.innerHTML = "<span class='text-red-400'><i class='fas fa-wifi mr-1'></i>Erreur réseau, veuillez réessayer.</span>";
    } finally {
        button.disabled = false;
        button.classList.remove('opacity-60', 'cursor-not-allowed');
    }
});
</script>

                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
                <p>© 2026 Revue Internationale de Philosophie Antique et Contemporaine </p>
                <div class="flex gap-8">
                    <a href="ethique.html" class="hover:text-white transition">Éthique & Plagiat</a>
                    <a href="mentions_legales.html" class="hover:text-white transition">Mentions Légales</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>