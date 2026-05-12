<?php
require_once 'db_connect.php';

// Démarrage de session s'il n'y en a pas encore
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l'utilisateur est déjà connecté, redirection vers son dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$erreur = '';
$succes = '';

// Traitement du formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';
    $mdp_confirm = $_POST['mot_de_passe_confirm'] ?? '';

    // Validation des champs
    if (empty($nom) || empty($email) || empty($mdp) || empty($mdp_confirm)) {
        $erreur = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } elseif (strlen($mdp) < 8) {
        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($mdp !== $mdp_confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérification si l'email existe déjà
        $stmt_check = $conn->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $stmt_check->execute(['email' => $email]);
        
        if ($stmt_check->fetch()) {
            $erreur = "Cette adresse email est déjà utilisée.";
        } else {
            // Hashage du mot de passe en bcrypt
            $mdp_hash = password_hash($mdp, PASSWORD_BCRYPT);
            
            // Insertion dans la base de données
            $stmt_insert = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif, created_at) VALUES (:nom, :email, :mdp_hash, 'utilisateur', 1, NOW())");
            
            $result = $stmt_insert->execute([
                'nom' => $nom,
                'email' => $email,
                'mdp_hash' => $mdp_hash
            ]);

            // ... ton code d'insertion existant ...

            if ($result) {
                // ====================================================
                // NOUVEAU : ENVOI DE L'EMAIL AUTOMATIQUE DE BIENVENUE
                // ====================================================
                $sujet = "Bienvenue sur la Revue de Philosophie de Libreville";
                
                // Préparation des en-têtes pour un format HTML correct
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: RPL Admin <admin@rpl-libreville.org>" . "\r\n";
                
                // Corps du message en HTML
                $message_bienvenue = "
                <html>
                <head>
                    <title>Bienvenue sur RPL</title>
                </head>
                <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                    <h2 style='color: #4f46e5;'>Bonjour " . htmlspecialchars($nom) . ",</h2>
                    <p>Votre compte a été créé avec succès sur le site de la <strong>Revue de Philosophie de Libreville (RPL)</strong>.</p>
                    <p>Vous pouvez dès à présent vous connecter pour soumettre vos articles, consulter les archives ou modifier votre profil.</p>
                    
                    <p style='margin: 30px 0;'>
                        <a href='https://rpl-libreville.org/connexion.php' style='background-color: #0f172a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>
                            Accéder à mon espace
                        </a>
                    </p>
                    
                    <p>Si vous avez des questions ou rencontrez des difficultés, n'hésitez pas à nous contacter.</p>
                    <br>
                    <p>Cordialement,<br><strong>L'équipe Éditoriale de la RPL</strong></p>
                </body>
                </html>
                ";
                
                // Envoi de l'email (silencieux, s'il échoue, ça ne bloque pas l'inscription)
                @mail($email, $sujet, $message_bienvenue, $headers);
                // ====================================================

                // Le message de succès qui s'affiche sur la page d'inscription
                $succes = "Votre compte a été créé avec succès ! Un email de confirmation vous a été envoyé. Vous pouvez maintenant vous connecter.";
                
                // Réinitialisation des champs du formulaire
                $nom = $email = ''; 
                
            } else {
                $erreur = "Une erreur est survenue lors de la création de votre compte.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte | RPL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen py-10">
    <div class="bg-white p-10 rounded-2xl shadow-lg max-w-md w-full border border-slate-100">
        <h2 class="text-2xl font-bold text-slate-900 mb-2 text-center">Créer un compte</h2>
        <p class="text-center text-slate-500 mb-6 text-sm">Rejoignez le Système RPL</p>
        
        <!-- Affichage des messages d'erreur ou de succès -->
        <?php if (!empty($erreur)): ?>
            <div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm font-medium'>
                <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($succes)): ?>
            <div class='bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded mb-6 text-sm font-medium'>
                <?php echo htmlspecialchars($succes); ?>
                <div class="mt-3">
                    <a href="connexion.php" class="text-emerald-800 font-bold underline hover:text-emerald-900">Aller à la page de connexion →</a>
                </div>
            </div>
        <?php else: ?>

            <form method="POST" action="inscription.php" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="nom">Nom complet</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom ?? ''); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow">
                </div>
                
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow">
                </div>
                
                <!-- Champ Mot de passe avec oeil -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="mot_de_passe">Mot de passe</label>
                    <div class="relative">
                        <input type="password" id="mot_de_passe" name="mot_de_passe" required placeholder="8 caractères minimum" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 pr-10 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow">
                        <button type="button" onclick="toggleVisibility('mot_de_passe', 'eyeIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i class="far fa-eye" id="eyeIcon1"></i>
                        </button>
                    </div>
                </div>

                <!-- Champ Confirmer Mot de passe avec oeil -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="mot_de_passe_confirm">Confirmez le mot de passe</label>
                    <div class="relative">
                        <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 pr-10 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none transition-shadow">
                        <button type="button" onclick="toggleVisibility('mot_de_passe_confirm', 'eyeIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i class="far fa-eye" id="eyeIcon2"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-slate-900 text-white py-3 rounded-lg font-bold hover:bg-slate-800 transition-colors shadow-md">
                        Créer mon compte
                    </button>
                </div>
            </form>

        <?php endif; ?>

        <p class="text-center text-sm text-slate-500 mt-6">
            Déjà un compte ? <a href="connexion.php" class="text-amber-700 font-bold hover:underline">Se connecter</a>
        </p>
    </div>

    <!-- Script générique pour gérer plusieurs boutons "Oeil" -->
    <script>
        function toggleVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>