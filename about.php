<?php
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
    <title>À Propos | Revue Internationale de Philosophie de Libreville</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .serif { font-family: 'Playfair Display', serif; }
        .hero-mini {
            background: linear-gradient(rgba(30, 41, 59, 0.9), rgba(30, 41, 59, 0.82)),
                        url('https://images.unsplash.com/photo-1491843351663-f95982f9b6b6?auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-[#fcfcf9] text-slate-900 selection:bg-amber-100">

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
                        <h1 class="text-xl font-bold leading-none">RIPL</h1>
                        <p class="text-[9px] tracking-widest text-amber-700 font-bold uppercase">Revue Internationale de Philosophie de Libreville</p>
                    </div>
                </a>
                <nav class="hidden md:flex gap-8 items-center text-[10px] font-bold uppercase tracking-widest">
                    <a href="index.php" class="text-slate-600 hover:text-amber-700 transition">Accueil</a>
                    <a href="archives.php" class="text-slate-600 hover:text-amber-700 transition">Archives</a>
                    <a href="about.php" class="text-amber-700 border-b border-amber-700">À propos</a>
                    <a href="contact.html" class="text-slate-600 hover:text-amber-700 transition">Contact</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="user-status text-indigo-600">
                            <?php echo isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : 'CONNECTÉ'; ?>
                        </span>
                    <?php else: ?>
                        <a href="inscription.php" class="bg-slate-900 text-white px-4 py-2 rounded hover:bg-amber-700 transition">S'abonner</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero-mini py-24 text-white text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-bold serif italic mb-4">À propos de la revue</h2>
            <p class="text-amber-500 font-bold uppercase text-[10px] tracking-[0.3em]">Ligne éditoriale • Orientations scientifiques • Comités</p>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 py-20">

        <div class="grid lg:grid-cols-3 gap-16 mb-24">
            <div class="lg:col-span-2 space-y-6 text-slate-600 leading-relaxed">
                <h3 class="serif text-3xl font-bold text-slate-900 italic mb-6">Ligne éditoriale</h3>

                <p>La <strong>Revue Internationale de Philosophie de Libreville</strong> est une revue académique à vocation internationale consacrée à la recherche philosophique dans ses dimensions historiques, théoriques, critiques et contemporaines. Elle entend constituer un espace scientifique structuré, rigoureux et exigeant, dédié à la production, à l'évaluation et à la diffusion de travaux originaux en philosophie, issus de divers horizons intellectuels et institutionnels.</p>

                <p>Elle se donne pour ambition de contribuer à la consolidation d'un champ de recherche philosophique à la fois exigeant et ouvert, favorisant la confrontation des idées, le dialogue entre les traditions de pensée et l'élaboration de problématiques nouvelles. Dans cette perspective, la revue se positionne comme un lieu de circulation internationale des savoirs philosophiques, attentif à la diversité des approches méthodologiques et des traditions intellectuelles.</p>

                <p>La revue s'inscrit pleinement dans une dynamique de dialogue entre les traditions philosophiques classiques et les problématiques contemporaines. Elle accorde une attention particulière à la philosophie antique, considérée comme un socle fondamental de la pensée philosophique occidentale et universelle, ainsi qu'à ses prolongements modernes et contemporains dans les champs de la politique, de l'éthique et des sciences humaines.</p>

                <p>Dans cette perspective, la revue encourage les travaux portant sur les grandes figures de la philosophie antique, notamment Platon, Aristote et Plotin, tout en favorisant des approches critiques, historiques, philologiques et conceptuelles. Elle valorise également les relectures contemporaines de ces traditions, ainsi que leur réinterprétation dans le contexte des débats philosophiques actuels.</p>

                <h4 class="font-bold text-slate-800 mt-8 mb-4">Fondation et inscription académique</h4>
                <p>La revue est fondée par <strong>Roch Foster Boulanga</strong>, chercheur en philosophie ancienne et Assistant à l'Université libre de Bruxelles, en collaboration avec le professeur <strong>Christ Olivier Mpaga</strong>, de l'Université Omar Bongo (Gabon), spécialiste reconnu en philosophie politique et morale. Cette double inscription académique, entre l'espace européen et l'espace africain, confère à la revue une orientation résolument internationale, articulant des perspectives de recherche issues de contextes universitaires diversifiés.</p>

                <p>Par cette configuration, la revue entend renforcer les échanges entre chercheurs africains, européens et internationaux, tout en contribuant à la reconnaissance et à la valorisation de la recherche philosophique produite en Afrique et dans la diaspora académique.</p>

                <p>Ainsi, la Revue Internationale de Philosophie de Libreville se veut un espace de réflexion critique, de dialogue intellectuel et de production scientifique, au service d'une philosophie à la fois ancrée dans son héritage classique et ouverte aux défis du monde contemporain.</p>
            </div>

            <div class="space-y-8">
                <div class="bg-amber-50 p-8 rounded-2xl border border-amber-100">
                    <h4 class="font-bold text-amber-800 uppercase text-[10px] tracking-widest mb-4">Objectifs de la revue</h4>
                    <ul class="space-y-4 text-sm text-amber-900">
                        <li class="flex gap-3"><i class="fas fa-check mt-1"></i><span>Soutenir et valoriser la recherche philosophique gabonaise, africaine et internationale.</span></li>
                        <li class="flex gap-3"><i class="fas fa-check mt-1"></i><span>Développer les études en philosophie antique, politique, morale et théorique.</span></li>
                        <li class="flex gap-3"><i class="fas fa-check mt-1"></i><span>Créer un espace international de dialogue, de confrontation critique et de diffusion scientifique des savoirs philosophiques.</span></li>
                    </ul>
                </div>

                <div class="bg-slate-900 text-white p-8 rounded-2xl">
                    <h4 class="font-bold text-amber-500 uppercase text-[10px] tracking-widest mb-4">Invitation aux chercheurs</h4>
                    <p class="text-sm text-slate-300 mb-4">La revue invite les chercheurs, enseignants-chercheurs, doctorants et post-doctorants, issus des universités africaines, européennes et internationales, à soumettre leurs travaux originaux.</p>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li>• La rigueur méthodologique et argumentative</li>
                        <li>• L'originalité conceptuelle et interprétative</li>
                        <li>• La critique rationnelle et structurée</li>
                        <li>• L'ouverture aux débats internationaux et interdisciplinaires</li>
                    </ul>
                    <a href="soumission.php" class="block text-center mt-6 py-2 px-4 bg-amber-700 hover:bg-amber-600 rounded text-xs font-bold uppercase tracking-wider transition">Soumettre un article</a>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-800 uppercase text-[10px] tracking-widest mb-4">Originalité et valeur ajoutée</h4>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex gap-3"><i class="fas fa-globe-africa mt-1 text-amber-700"></i><span>Valorisation et visibilité internationale des recherches menées par des chercheurs africains et de la diaspora académique.</span></li>
                        <li class="flex gap-3"><i class="fas fa-book-open mt-1 text-amber-700"></i><span>Diffusion de la pensée philosophique africaine dans un cadre scientifique international exigeant.</span></li>
                        <li class="flex gap-3"><i class="fas fa-people-arrows mt-1 text-amber-700"></i><span>Mise en dialogue critique entre philosophie antique, pensée africaine et problématiques contemporaines.</span></li>
                        <li class="flex gap-3"><i class="fas fa-layer-group mt-1 text-amber-700"></i><span>Ouverture à des approches interdisciplinaires en lien avec les sciences humaines et sociales.</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mb-24">
            <h3 class="text-center serif text-3xl font-bold text-slate-900 italic mb-12">Orientations scientifiques</h3>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-10 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-landmark text-xl"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-4 serif">Philosophie antique</h4>
                    <p class="text-slate-600 text-sm mb-4">La revue accorde une place centrale aux recherches portant sur la philosophie antique, entendue comme l'un des fondements historiques et conceptuels de la tradition philosophique occidentale et universelle.</p>
                    <ul class="text-sm text-slate-500 space-y-2 list-disc pl-4">
                        <li>Commentaires philosophiques rigoureux, originaux et argumentés des textes antiques.</li>
                        <li>Analyses critiques des doctrines philosophiques dans leurs contextes historiques et conceptuels.</li>
                        <li>Relectures contemporaines de la philosophie ancienne intégrant les débats philosophiques actuels.</li>
                        <li>Études comparatives entre la pensée antique et les problématiques philosophiques modernes et contemporaines.</li>
                    </ul>
                    <p class="text-sm text-slate-500 mt-4">Cette orientation vise à renouveler la compréhension des textes fondateurs de la philosophie et à en proposer des actualisations pertinentes dans les débats contemporains, notamment en métaphysique, en épistémologie et en philosophie de la connaissance.</p>
                </div>

                <div class="bg-white p-10 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-slate-100 text-slate-700 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-balance-scale text-xl"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-4 serif">Philosophie politique et morale</h4>
                    <p class="text-slate-600 text-sm mb-4">La revue accueille également des travaux consacrés à la philosophie politique et morale, envisagée dans une perspective à la fois historique, théorique et contemporaine.</p>
                    <ul class="text-sm text-slate-500 space-y-2 list-disc pl-4">
                        <li>Le bien commun, la justice et les fondements normatifs de la vie collective.</li>
                        <li>Les formes de gouvernement, de souveraineté et d'organisation de la cité.</li>
                        <li>Les rapports entre éthique individuelle et structuration politique des sociétés.</li>
                        <li>Les enjeux contemporains de la philosophie morale : responsabilité, liberté et normativité.</li>
                        <li>La démocratie, ses fondements philosophiques et ses transformations actuelles.</li>
                        <li>La philosophie africaine dans ses expressions classiques, modernes et contemporaines.</li>
                        <li>Les problématiques liées au pouvoir, à la légitimité et aux institutions politiques.</li>
                    </ul>
                    <p class="text-sm text-slate-500 mt-4">Cette section vise à favoriser une réflexion critique sur les conditions de possibilité du vivre-ensemble, en articulant héritages philosophiques et défis politiques contemporains.</p>
                </div>
            </div>
        </div>

        <div class="mb-24">
            <div class="text-center mb-12">
                <h3 class="serif text-3xl font-bold text-slate-900 italic mb-4">Les rubriques de la revue</h3>
                <p class="text-slate-500 text-sm">Une structure éditoriale plurielle pour couvrir les principaux espaces de publication et de discussion philosophique.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 border border-slate-200 rounded-xl">
                    <i class="fas fa-folder-open text-amber-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-slate-900 mb-2">Dossier thématique</h5>
                    <p class="text-xs text-slate-500">Chaque numéro est structuré autour d'un thème central permettant une exploration approfondie, problématisée et plurielle d'un objet philosophique.</p>
                </div>
                <div class="bg-white p-6 border border-slate-200 rounded-xl">
                    <i class="fas fa-book-reader text-amber-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-slate-900 mb-2">Études et commentaires de textes</h5>
                    <p class="text-xs text-slate-500">Analyses philologiques, historiques et philosophiques des textes classiques, avec des interprétations nouvelles des œuvres majeures de l'Antiquité.</p>
                </div>
                <div class="bg-white p-6 border border-slate-200 rounded-xl">
                    <i class="fas fa-language text-amber-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-slate-900 mb-2">Traductions philosophiques</h5>
                    <p class="text-xs text-slate-500">Publication de textes rares, inédits ou difficilement accessibles, accompagnés de traductions critiques, de notes et de commentaires.</p>
                </div>
                <div class="bg-white p-6 border border-slate-200 rounded-xl">
                    <i class="fas fa-pen-nib text-amber-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-slate-900 mb-2">Notes de recherche</h5>
                    <p class="text-xs text-slate-500">Contributions brèves présentant des résultats intermédiaires, des hypothèses de travail ou des réflexions ponctuelles issues de recherches en cours.</p>
                </div>
                <div class="bg-white p-6 border border-slate-200 rounded-xl">
                    <i class="fas fa-comments text-amber-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-slate-900 mb-2">Débats philosophiques</h5>
                    <p class="text-xs text-slate-500">Espace de discussion critique pour répondre à des articles publiés, formuler des objections argumentées et proposer de nouvelles pistes de réflexion.</p>
                </div>
                <div class="bg-white p-6 border border-slate-200 rounded-xl">
                    <i class="fas fa-star-half-alt text-amber-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-slate-900 mb-2">Recensions d'ouvrages</h5>
                    <p class="text-xs text-slate-500">Analyses critiques d'ouvrages récents en philosophie antique, politique et morale, contribuant à la veille scientifique.</p>
                </div>
                <div class="bg-white p-6 border border-slate-200 rounded-xl">
                    <i class="fas fa-microphone-alt text-amber-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-slate-900 mb-2">Entretiens avec philosophes</h5>
                    <p class="text-xs text-slate-500">Entretiens avec des chercheurs et enseignants-chercheurs pour mettre en lumière leurs travaux, méthodes et perspectives philosophiques.</p>
                </div>
                <div class="bg-white p-6 border border-slate-200 rounded-xl">
                    <i class="fas fa-newspaper text-amber-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-slate-900 mb-2">Actualités académiques</h5>
                    <p class="text-xs text-slate-500">Suivi des activités scientifiques et universitaires de l'UOB, de l'ENS, de l'IRSH et de l'ULB, notamment autour du séminaire de philosophie ancienne.</p>
                </div>
            </div>

            <div class="mt-10 bg-slate-50 border border-slate-200 rounded-2xl p-8">
                <h4 class="font-bold text-slate-800 uppercase text-[10px] tracking-widest mb-4">Exemples de thématiques</h4>
                <div class="grid md:grid-cols-2 gap-4 text-sm text-slate-600">
                    <div>• Le Bien dans la philosophie antique</div>
                    <div>• Justice, pouvoir et gouvernance</div>
                    <div>• Éthique, responsabilité et modernité</div>
                    <div>• Philosophie antique et enjeux contemporains</div>
                    <div>• Démocratie et fondements philosophiques du politique</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 lg:p-12 shadow-sm border border-slate-200 mb-24">
            <h3 class="text-center serif text-3xl font-bold text-slate-900 italic mb-12">Organisation scientifique</h3>

            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-12">
                <div>
                    <h4 class="font-bold text-amber-700 uppercase tracking-widest text-[11px] mb-6 border-b border-amber-100 pb-2">Direction de publication</h4>
                    <ul class="space-y-4">
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Prof. Christ Olivier Mpaga</p>
                            <p class="text-xs text-slate-500">Université Omar Bongo (UOB)</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Roch Foster Boulanga</p>
                            <p class="text-xs text-slate-500">Université libre de Bruxelles (ULB)</p>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-400 uppercase tracking-widest text-[11px] mb-6 border-b border-slate-100 pb-2">Comité de rédaction</h4>
                    <ul class="space-y-4">
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Jean-Rodrigue Elisée EYENE MBE</p>
                            <p class="text-xs text-slate-500">Université Omar Bongo (UOB)</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Blanchard MAKANGA</p>
                            <p class="text-xs text-slate-500">Institut de Recherche en Sciences Humaines (IRSH) - CENAREST - Gabon</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Charles Philippe ASSEMBE ELA</p>
                            <p class="text-xs text-slate-500">École Normale Supérieure, Gabon</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Aaron Septine NZENGUI</p>
                            <p class="text-xs text-slate-500">Institut de Recherche en Sciences Humaines (IRSH) - CENAREST - Gabon</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Sylvain Delcomminette</p>
                            <p class="text-xs text-slate-500">Université libre de Bruxelles</p>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-400 uppercase tracking-widest text-[11px] mb-6 border-b border-slate-100 pb-2">Comité de lecture</h4>
                    <ul class="space-y-4">
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Irma Julienne ANGUE MEDOUX</p>
                            <p class="text-xs text-slate-500">Université Omar Bongo (UOB)</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Symphorien NGUEMA EZEMA</p>
                            <p class="text-xs text-slate-500">École Normale Supérieure, Libreville / Gabon</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Rodrigue MAKAYA MAKAYA</p>
                            <p class="text-xs text-slate-500">Université Omar Bongo (UOB)</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Rodolphe EDOU NKOGHE</p>
                            <p class="text-xs text-slate-500">Université Omar Bongo (UOB)</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Nestor ENGONE EllOUE</p>
                            <p class="text-xs text-slate-500">Université Omar Bongo (UOB)</p>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-400 uppercase tracking-widest text-[11px] mb-6 border-b border-slate-100 pb-2">Secrétariat de rédaction</h4>
                    <ul class="space-y-4">
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Irma Julienne ANGUE MEDOUX</p>
                            <p class="text-xs text-slate-500">Université Omar Bongo (UOB)</p>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 text-sm">Symphorien NGUEMA EZEMA</p>
                            <p class="text-xs text-slate-500">École Normale Supérieure, Gabon</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center">
            <h4 class="font-bold uppercase text-[10px] tracking-[0.3em] text-slate-400 mb-10 italic">Institutions partenaires et évaluation</h4>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-60">
                <span class="font-bold text-xl text-slate-900 serif">UOB</span>
                <span class="font-bold text-xl text-slate-900 serif">ENS Gabon</span>
                <span class="font-bold text-xl text-slate-900 serif">IRSH</span>
                <span class="font-bold text-xl text-slate-900 serif">ULB Belgique</span>
                <span class="font-bold text-xl text-slate-900 serif">CAMES</span>
            </div>
        </div>
    </main>

    <footer class="bg-slate-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] text-slate-500 uppercase tracking-widest font-bold">
                <p>© 2026 Revue Internationale de Philosophie Antique et Contemporaine</p>
                <div class="flex gap-8">
                    <a href="index.php" class="hover:text-white transition">Accueil</a>
                    <a href="archives.php" class="hover:text-white transition">Archives</a>
                    <a href="ethique.html" class="hover:text-white transition">Éthique</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>