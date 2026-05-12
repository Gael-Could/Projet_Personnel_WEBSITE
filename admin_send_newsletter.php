<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// Vérification de sécurité Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

$message_succes = "";
$erreur = "";

// TRAITEMENT DE L'ENVOI
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_newsletter'])) {
    $sujet = trim($_POST['sujet']);
    $contenu = trim($_POST['contenu']);
    
    if (!empty($sujet) && !empty($contenu)) {
        // Récupérer uniquement les abonnés actifs
        $stmt_subs = $conn->query("SELECT email FROM newsletter_subscribers WHERE actif = 1");
        $abonnes = $stmt_subs->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($abonnes) > 0) {
            $envois_reussis = 0;
            
            // Préparation des en-têtes de l'email
            $headers = "From: noreply@rpl-libreville.org\r\n"; // À MODIFIER avec votre vrai domaine
            $headers .= "Reply-To: contact@rpl-libreville.org\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            // Formatage HTML basique du message
            $message_html = "<html><body>";
            $message_html .= nl2br(htmlspecialchars($contenu));
            $message_html .= "<br><br><hr><small>Vous recevez cet email car vous êtes abonné à la Revue de Philosophie de Libreville.</small>";
            $message_html .= "</body></html>";
            
            // Boucle d'envoi
            foreach ($abonnes as $abonne) {
                $to = $abonne['email'];
                // L'envoi réel dépend de la configuration SMTP de votre serveur
                if (mail($to, $sujet, $message_html, $headers)) {
                    $envois_reussis++;
                }
            }
            
            $message_succes = "Newsletter envoyée avec succès à $envois_reussis abonné(s).";
        } else {
            $erreur = "Aucun abonné actif trouvé pour l'envoi.";
        }
    } else {
        $erreur = "Le sujet et le contenu sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Envoyer Newsletter | RPL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex min-h-screen text-slate-800">

    <!-- Sidebar (Identique) -->
    <aside class="w-72 bg-[#1E2235] text-white hidden md:flex flex-col shadow-xl fixed h-full z-20">
        <div class="p-6 border-b border-slate-800 flex items-center gap-3">
            <div class="bg-indigo-500 p-2 rounded-lg"><i class="fas fa-shield-halved text-white"></i></div>
            <h1 class="text-xl font-bold tracking-tight">RPL Admin</h1>
        </div>
        <nav class="p-4 space-y-2 mt-4 flex-1">
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mb-2 tracking-widest">Tableau de bord</p>
            <a href="admin_dashboard.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-chart-pie w-5"></i> Vue d'ensemble</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Gestion</p>
            <a href="admin_administrateurs.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-user-shield w-5"></i> Équipe Admin</a>
            <a href="admin_utilisateurs.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-users w-5"></i> Utilisateurs</a>
            <a href="admin_soumissions.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-file-alt w-5"></i> Soumissions</a>
            <a href="admin_articles.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-newspaper w-5"></i> Articles publiés</a>
            
            <!-- Lien Actif Newsletter -->
            <a href="admin_newsletter.php" class="flex items-center gap-3 p-3 bg-indigo-600 rounded-xl text-white font-medium shadow-lg shadow-indigo-500/20 transition"><i class="fas fa-envelope-open-text w-5"></i> Newsletter</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Système</p>
            <a href="admin_configuration.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-cog w-5"></i> Configuration</a>
        </nav>
    </aside>

    <main class="ml-72 p-8 lg:p-12 w-full">
        <header class="flex justify-between items-center mb-10">
            <div class="flex items-center gap-4">
                <a href="admin_newsletter.php" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-full text-slate-500 hover:text-indigo-600 hover:border-indigo-200 shadow-sm transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Nouvelle diffusion</h2>
                    <p class="text-slate-500 font-medium">Envoyer un email à tous les abonnés actifs.</p>
                </div>
            </div>
        </header>

        <?php if($message_succes): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-200 font-medium flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i> <?php echo htmlspecialchars($message_succes); ?>
            </div>
        <?php endif; ?>
        
        <?php if($erreur): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200 font-medium flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-xl"></i> <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire de création -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 max-w-4xl">
            <form method="POST" action="">
                
                <div class="mb-6">
                    <label for="sujet" class="block text-sm font-bold text-slate-700 mb-2">Sujet de l'email <span class="text-red-500">*</span></label>
                    <input type="text" id="sujet" name="sujet" required placeholder="Ex: Appel à contributions - Volume 3" 
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                </div>

                <div class="mb-8">
                    <label for="contenu" class="block text-sm font-bold text-slate-700 mb-2">Contenu du message <span class="text-red-500">*</span></label>
                    <textarea id="contenu" name="contenu" rows="12" required placeholder="Saisissez votre message ici..." 
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition resize-y"></textarea>
                    <p class="text-xs text-slate-500 mt-2"><i class="fas fa-info-circle"></i> Le message sera formaté automatiquement (les retours à la ligne seront conservés).</p>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="admin_newsletter.php" class="px-6 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition">
                        Annuler
                    </a>
                    <button type="submit" name="send_newsletter" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-md shadow-indigo-200 transition flex items-center gap-2" onclick="return confirm('Êtes-vous sûr de vouloir envoyer cet email à tous les abonnés ? Cette action est irréversible.');">
                        <i class="fas fa-paper-plane"></i> Envoyer la newsletter
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>