<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// Vérification de sécurité
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

// Notifications
try {
    $stmt_notifs = $conn->query("SELECT COUNT(*) FROM soumissions"); 
    $count_notifs = $stmt_notifs->fetchColumn();
} catch (Exception $e) { $count_notifs = 0; }

$message_succes = "";
$erreur = "";

// TRAITEMENT DE L'ENVOI D'EMAIL
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_mail'])) {
    $sujet = trim($_POST['sujet']);
    $contenu_html = $_POST['message'];
    $cible = $_POST['cible']; // 'tous' ou 'newsletter'

    if (!empty($sujet) && !empty($contenu_html)) {
        try {
            // Déterminer qui reçoit l'email
            if ($cible === 'newsletter') {
                $stmt = $conn->query("SELECT email, 'Abonné' as nom FROM newsletter_subscribers WHERE actif = 1");
            } else {
                $stmt = $conn->query("SELECT email, nom FROM utilisateurs WHERE actif = 1");
            }
            
            $destinataires = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $compteur = 0;

            // En-têtes pour un email au format HTML
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: Revue de Philosophie de Libreville <admin@rpl-libreville.org>" . "\r\n";

            // Boucle d'envoi
            foreach ($destinataires as $dest) {
                // Personnalisation du message (remplace {NOM} par le vrai nom)
                $message_perso = str_replace('{NOM}', htmlspecialchars($dest['nom']), $contenu_html);
                
                // Envoi de l'email (utilise la fonction mail native de PHP)
                if (mail($dest['email'], $sujet, $message_perso, $headers)) {
                    $compteur++;
                }
            }

            $message_succes = "L'email a été envoyé avec succès à $compteur destinataire(s).";

        } catch (Exception $e) {
            $erreur = "Erreur lors de la récupération des destinataires.";
        }
    } else {
        $erreur = "Veuillez remplir le sujet et le message.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mailing | RPL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex min-h-screen text-slate-800">

    <!-- Sidebar Admin -->
    <aside class="w-72 bg-[#1E2235] text-white hidden md:flex flex-col shadow-xl fixed h-full z-20">
        <div class="p-6 border-b border-slate-800 flex items-center gap-3">
            <div class="bg-indigo-500 p-2 rounded-lg"><i class="fas fa-shield-halved text-white"></i></div>
            <h1 class="text-xl font-bold tracking-tight">RPL Admin</h1>
        </div>
        <nav class="p-4 space-y-2 mt-4 flex-1">
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mb-2 tracking-widest">Tableau de bord</p>
            <a href="admin_dashboard.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-chart-pie w-5"></i> Vue d'ensemble</a>
            
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase mt-8 mb-2 tracking-widest">Communication</p>
            <a href="admin_mailing.php" class="flex items-center gap-3 p-3 bg-indigo-600 rounded-xl text-white font-medium shadow-lg shadow-indigo-500/20 transition"><i class="fas fa-paper-plane w-5"></i> Mailing & Newsletters</a>
            
            <!-- Ajoute les autres liens ici... -->
            <a href="admin_administrateurs.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-white/5 hover:text-white rounded-xl transition"><i class="fas fa-user-shield w-5"></i> Équipe Admin</a>
        </nav>
        <div class="p-6 border-t border-slate-800">
            <a href="logout.php" class="flex items-center gap-3 text-red-400 hover:text-red-300 font-semibold transition"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </aside>

    <main class="ml-72 p-8 lg:p-12 w-full">
        <!-- Header Dynamique -->
        <header class="flex justify-between items-start md:items-center mb-10 flex-col md:flex-row gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Campagnes d'emails</h2>
                <p class="text-slate-500 font-medium">Envoyez des messages groupés à vos membres et abonnés.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="px-4 py-2 bg-white rounded-xl border border-slate-200 text-sm font-bold text-slate-700 flex items-center gap-3 shadow-sm">
                    <i class="fas fa-calendar-alt text-indigo-500"></i> <?php echo date('d/m/Y'); ?>
                </div>
            </div>
        </header>

        <?php if($message_succes): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-200 font-medium flex items-center gap-3"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message_succes); ?></div>
        <?php endif; ?>
        <?php if($erreur): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200 font-medium flex items-center gap-3"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($erreur); ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-8 max-w-4xl">
            <form method="POST" action="" class="space-y-6">
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Destinataires</label>
                        <select name="cible" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                            <option value="tous">Tous les utilisateurs inscrits</option>
                            <option value="newsletter">Uniquement les abonnés Newsletter</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Sujet de l'email</label>
                        <input type="text" name="sujet" required placeholder="Ex: Nouvel article disponible sur RPL" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 flex justify-between">
                        <span>Message (HTML autorisé)</span>
                        <span class="text-xs text-indigo-500 font-normal">Astuce : Utilisez <b>{NOM}</b> pour afficher le nom du membre.</span>
                    </label>
                    <textarea name="message" rows="8" required placeholder="Bonjour {NOM},&#10;&#10;Nous avons le plaisir de vous annoncer..." class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition"></textarea>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" name="send_mail" class="bg-indigo-600 text-white font-bold px-8 py-3 rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-200 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Envoyer la campagne
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>