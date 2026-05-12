<?php
/**
 * reset_password.php
 * Vérifie le token envoyé par email et permet à l'utilisateur de changer son mot de passe.
 */

require_once 'db_connect.php';

$message = '';
$success = false;
$token_valide = false;
$user_id = null;

// 1. Vérification du token (via GET)
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = trim($_GET['token']);
    
    try {
        // Rechercher le token dans la base, vérifier s'il n'est pas expiré et n'a pas été utilisé
        $stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = ? AND used = 0 LIMIT 1");
        $stmt->execute([$token]);
        $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset_data) {
            // Vérifier la date d'expiration
            if (strtotime($reset_data['expires_at']) > time()) {
                $token_valide = true;
                $user_id = $reset_data['user_id'];
            } else {
                $message = "Ce lien de réinitialisation a expiré (validité de 1 heure). Veuillez faire une nouvelle demande.";
            }
        } else {
            $message = "Ce lien est invalide ou a déjà été utilisé.";
        }
    } catch (PDOException $e) {
        $message = "Erreur de connexion à la base de données.";
    }
} else {
    // Si aucun token n'est fourni, on a probablement juste soumis le formulaire (POST)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $message = "Aucun jeton de sécurité fourni. Accès refusé.";
    }
}

// 2. Traitement du formulaire de réinitialisation (via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'], $_POST['nouveau_mdp'], $_POST['confirmer_mdp'])) {
    $token = trim($_POST['token']);
    $mdp1 = $_POST['nouveau_mdp'];
    $mdp2 = $_POST['confirmer_mdp'];
    
    // Revérifier le token (sécurité)
    try {
        $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW() LIMIT 1");
        $stmt->execute([$token]);
        $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset_data) {
            $user_id = $reset_data['user_id'];
            
            // Vérifier que les mots de passe correspondent
            if ($mdp1 !== $mdp2) {
                $message = "Les mots de passe ne correspondent pas.";
                $token_valide = true; // On garde le formulaire affiché
            } elseif (strlen($mdp1) < 8) {
                $message = "Le mot de passe doit contenir au moins 8 caractères.";
                $token_valide = true;
            } else {
                // Tout est bon : on met à jour !
                $hash = password_hash($mdp1, PASSWORD_BCRYPT);
                
                $conn->beginTransaction();
                
                // Maj du mot de passe
                $update_user = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
                $update_user->execute([$hash, $user_id]);
                
                // Invalider le token
                $update_token = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
                $update_token->execute([$token]);
                
                $conn->commit();
                
                $success = true;
                $message = "Votre mot de passe a été réinitialisé avec succès !";
            }
        } else {
            $message = "Le jeton est expiré ou a déjà été utilisé.";
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $message = "Une erreur technique est survenue.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe – RIPAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-10 rounded-2xl shadow-lg max-w-md w-full border border-slate-100">
        
        <div class="text-center mb-8">
            <div class="inline-flex bg-slate-900 p-3 rounded-xl mb-4 shadow-sm">
                <i class="fas fa-lock text-white text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Nouveau mot de passe</h2>
            <?php if (!$success && $token_valide): ?>
                <p class="text-sm text-slate-500">Veuillez saisir votre nouveau mot de passe sécurisé.</p>
            <?php endif; ?>
        </div>

        <?php if ($message): ?>
            <div class="<?php echo $success ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'; ?> border rounded-xl p-4 mb-6 text-sm flex items-start gap-3 shadow-sm">
                <i class="fas <?php echo $success ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-red-500'; ?> mt-0.5"></i>
                <span class="font-medium"><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <!-- SI SUCCÈS : BOUTON DE CONNEXION -->
        <?php if ($success): ?>
            <a href="connexion.php" class="block w-full text-center bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition mt-6 shadow-md">
                Se connecter maintenant
            </a>

        <!-- SI TOKEN VALIDE : FORMULAIRE -->
        <?php elseif ($token_valide): ?>
            <form method="POST" action="reset_password.php" class="space-y-5">
                
                <!-- Champ caché pour garder le token lors du POST -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Nouveau mot de passe</label>
                    <div class="relative">
                        <input type="password" id="nouveau_mdp" name="nouveau_mdp" required minlength="8" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pr-10 text-sm focus:ring-2 focus:ring-amber-500 outline-none transition shadow-sm">
                        <button type="button" onclick="toggleVis('nouveau_mdp', 'eye1')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-amber-700 transition focus:outline-none">
                            <i class="far fa-eye" id="eye1"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5"><i class="fas fa-info-circle mr-1"></i> 8 caractères minimum.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Confirmer le mot de passe</label>
                    <div class="relative">
                        <input type="password" id="confirmer_mdp" name="confirmer_mdp" required minlength="8" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 pr-10 text-sm focus:ring-2 focus:ring-amber-500 outline-none transition shadow-sm">
                        <button type="button" onclick="toggleVis('confirmer_mdp', 'eye2')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-amber-700 transition focus:outline-none">
                            <i class="far fa-eye" id="eye2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-amber-700 text-white py-3.5 rounded-xl font-bold hover:bg-amber-800 transition text-sm uppercase tracking-widest shadow-md mt-4">
                    Réinitialiser
                </button>
            </form>

        <!-- SI ERREUR OU EXIPIRÉ : BOUTON RETOUR -->
        <?php else: ?>
            <a href="motdepasse_oublie.php" class="block w-full text-center bg-slate-100 text-slate-700 py-3 rounded-xl font-bold hover:bg-slate-200 transition mt-6 border border-slate-200">
                Faire une nouvelle demande
            </a>
        <?php endif; ?>

        <div class="mt-8 text-center">
            <a href="index.php" class="text-xs font-semibold text-slate-400 hover:text-slate-600 transition">← Retour à l'accueil</a>
        </div>

    </div>

    <!-- Script pour afficher/masquer les mots de passe -->
    <script>
        function toggleVis(inputId, iconId) {
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