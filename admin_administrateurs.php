<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// --- Gestion des notifications (ex: soumissions en attente) ---
try {
    $stmt_notifs = $conn->query("SELECT COUNT(*) FROM soumissions"); 
    $count_notifs = $stmt_notifs->fetchColumn();
} catch (Exception $e) {
    $count_notifs = 0;
}

// Vérification de sécurité
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

$nom_admin = $_SESSION['user_nom'] ?? 'Administrateur';
$message = "";
$erreur = "";

// 1. TRAITEMENT : CRÉATION D'UN NOUVEL ADMINISTRATEUR
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_admin'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];

    if (!empty($nom) && !empty($email) && !empty($mdp)) {
        // Vérifier si l'email existe déjà
        $stmt_check = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt_check->execute([$email]);
        
        if ($stmt_check->fetch()) {
            $erreur = "Cette adresse email est déjà utilisée par un autre compte.";
        } else {
            // Création directe avec le rôle 'admin'
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            $stmt_insert = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif, created_at) VALUES (?, ?, ?, 'admin', 1, NOW())");
            
            if ($stmt_insert->execute([$nom, $email, $hash])) {
                $message = "Le nouvel administrateur a été créé avec succès.";
            } else {
                $erreur = "Erreur lors de la création du compte.";
            }
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

// 2. RÉCUPÉRATION DE LA LISTE DES ADMINISTRATEURS
$stmt_admins = $conn->query("SELECT * FROM utilisateurs WHERE role = 'admin' ORDER BY created_at DESC");
$admins = $stmt_admins->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administrateurs | RPL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap'); 
        body { font-family: 'Inter', sans-serif; }

        /* -- NOUVELLES ANIMATIONS CSS -- */
        .animate-fade-in-up {
            animation: fadeInUp 0.7s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-slate-50 flex min-h-screen text-slate-800">

    <!-- Sidebar -->
    <aside class="w-72 bg-[#1E2235] text-white hidden md:flex flex-col shadow-xl fixed h-full z-20">
        <div class="p-6 border-b border-slate-800 flex items-center gap-3">
            <div class="bg-indigo-500 p-2 rounded-lg"><i class="fas fa-shield-halved text-white"></i></div>
            <h1 class="text-xl font-bold tracking-tight">RPL Admin</h1>
        </div>
        <nav class="p-4 space-y-2 mt-4 flex-1">
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mb-2 tracking-widest">Tableau de bord</p>
            <a href="admin_dashboard.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-chart-pie w-5"></i> Vue d'ensemble</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Gestion</p>
            <a href="admin_administrateurs.php" class="flex items-center gap-3 p-3 bg-indigo-600 rounded-xl text-white font-medium shadow-lg shadow-indigo-500/20 transition"><i class="fas fa-user-shield w-5"></i> Équipe Admin</a>
            <a href="admin_utilisateurs.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-users w-5"></i> Utilisateurs</a>
            <a href="admin_soumissions.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-file-alt w-5"></i> Soumissions</a>
            <a href="admin_articles.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-newspaper w-5"></i> Articles publiés</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Système</p>
            <a href="admin_configuration.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-cog w-5"></i> Configuration</a>
        </nav>
        <div class="p-6 border-t border-slate-800">
            <a href="logout.php" class="flex items-center gap-3 text-red-400 hover:text-red-300 font-semibold transition"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </aside>

    <main class="ml-72 p-8 lg:p-12 w-full">
        
        <!-- HEADER DYNAMIQUE INTÉGRÉ ICI -->
        <header class="flex justify-between items-start md:items-center mb-10 flex-col md:flex-row gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Équipe d'Administration</h2>
                <p class="text-slate-500 font-medium">Gérez les accès privilégiés et ajoutez de nouveaux collaborateurs.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Cloche dynamique -->
                <a href="admin_soumissions.php" class="relative p-2 text-slate-400 hover:text-slate-600 transition" title="<?php echo $count_notifs; ?> nouvelle(s) soumission(s)">
                    <i class="fas fa-bell text-xl"></i>
                    <?php if ($count_notifs > 0): ?>
                        <span class="absolute top-1 right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-slate-50"></span>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Date du jour dynamique -->
                <div class="px-4 py-2 bg-white rounded-xl border border-slate-200 text-sm font-bold text-slate-700 flex items-center gap-3 shadow-sm">
                    <i class="fas fa-calendar-alt text-indigo-500"></i>
                    <?php echo date('d/m/Y'); ?>
                </div>
            </div>
        </header>

        <!-- Messages d'alerte -->
        <?php if($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-200 font-medium flex items-center gap-3 alert-box"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if($erreur): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200 font-medium flex items-center gap-3 alert-box"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($erreur); ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <!-- Colonne 1 : Formulaire de création -->
            <div class="xl:col-span-1">
                <section class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 form-section transition-all duration-300">
                    <h3 class="font-bold text-lg mb-6 flex items-center gap-2 text-slate-800"><i class="fas fa-user-plus text-indigo-500"></i> Nouvel Admin</h3>
                    
                    <form method="POST" action="" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nom complet</label>
                            <input type="text" name="nom" required class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Mot de passe</label>
                            <div class="relative">
                                <input type="password" id="mot_de_passe" name="mot_de_passe" required class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                                <!-- Bouton avec icône oeil FontAwesome -->
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-600 transition focus:outline-none">
                                    <i class="far fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" name="add_admin" class="w-full mt-2 bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-200 transform hover:-translate-y-0.5 active:translate-y-0">
                            Créer le compte
                        </button>
                    </form>
                </section>
            </div>

            <!-- Colonne 2 : Liste des admins -->
            <div class="xl:col-span-2">
                <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Administrateur</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date de création</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 table-body-animated">
                            <?php foreach ($admins as $admin): ?>
                            <tr class="hover:bg-slate-50 transition cursor-default">
                                <td class="px-6 py-4 font-medium text-slate-800"><?php echo htmlspecialchars($admin['nom']); ?></td>
                                <td class="px-6 py-4 text-slate-500"><?php echo htmlspecialchars($admin['email']); ?></td>
                                <td class="px-6 py-4 text-slate-500"><?php echo date('d/m/Y', strtotime($admin['created_at'])); ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($admin['actif']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Actif</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            </div>
            
        </div>
    </main>

    <!-- NOUVEAUX SCRIPTS JS (Effets visuels + Oeil) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // 1. Animation Globale de la page (Fade-in et Slide-up)
            const mainContent = document.querySelector('main');
            mainContent.classList.add('animate-fade-in-up');

            // 2. Auto-disparition des messages d'alerte après 5 secondes
            const alerts = document.querySelectorAll('.alert-box');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 500); // Retrait du DOM
                }, 5000);
            });

            // 3. Animation d'apparition en cascade (Stagger) pour le tableau
            const tableRows = document.querySelectorAll('.table-body-animated tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(15px)';
                row.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                
                // Le délai augmente à chaque ligne (index * 80ms)
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 150 + (index * 80)); 
            });

            // 4. Effet de focus dynamique (3D/Soulèvement) sur le formulaire
            const formInputs = document.querySelectorAll('form input');
            const formSection = document.querySelector('.form-section');
            
            formInputs.forEach(input => {
                input.addEventListener('focus', () => {
                    formSection.style.transform = 'translateY(-4px)';
                    formSection.style.boxShadow = '0 20px 25px -5px rgba(99, 102, 241, 0.1), 0 8px 10px -6px rgba(99, 102, 241, 0.1)';
                    formSection.style.borderColor = 'rgba(99, 102, 241, 0.3)';
                });
                
                input.addEventListener('blur', () => {
                    formSection.style.transform = 'translateY(0)';
                    formSection.style.boxShadow = ''; // Retour au style par défaut Tailwind
                    formSection.style.borderColor = '';
                });
            });

            // 5. Afficher/Masquer le mot de passe (Existant)
            const togglePassword = document.querySelector('#togglePassword');
            const passwordInput = document.querySelector('#mot_de_passe');
            const eyeIcon = document.querySelector('#eyeIcon');

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });

        });
    </script>
</body>
</html>