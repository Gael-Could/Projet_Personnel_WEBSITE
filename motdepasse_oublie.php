<?php
/**
 * motdepasse_oublie.php
 * Permet à un utilisateur de demander la réinitialisation de son mot de passe.
 * Envoie un email avec un lien contenant un token unique.
 */
require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Si déjà connecté, rediriger
if (isset($_SESSION['userid'])) {
    header('Location: dashboard.php');
    exit;
}

$message  = '';
$success  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Veuillez saisir une adresse email valide.';
    } else {
        try {
            // Vérifier si l'email existe
            $stmt = $conn->prepare("SELECT id, nom FROM utilisateurs WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Générer un token sécurisé
                $token   = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600); // valide 1 heure

                // Créer la table si elle n'existe pas encore
                $conn->exec("CREATE TABLE IF NOT EXISTS password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    token VARCHAR(64) NOT NULL UNIQUE,
                    expires_at DATETIME NOT NULL,
                    used TINYINT(1) DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )");

                // Supprimer les anciens tokens de cet utilisateur
                $conn->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$user['id']]);

                // Insérer le nouveau token
                $ins = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
                $ins->execute([$user['id'], $token, $expires]);

                // Construire l'URL de réinitialisation
                $protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host       = $_SERVER['HTTP_HOST'];
                $resetLink  = "$protocol://$host/reset_password.php?token=$token";

                // Envoi de l'email (nécessite un serveur SMTP configuré)
                $sujet   = '[RPL] Réinitialisation de votre mot de passe';
                $corps   = "Bonjour {$user['nom']},\n\n"
                         . "Vous avez demandé la réinitialisation de votre mot de passe sur la Revue Internationale de Philosophie Antique et Contemporaine.\n\n"
                         . "Cliquez sur ce lien pour choisir un nouveau mot de passe (valide 1 heure) :\n"
                         . $resetLink . "\n\n"
                         . "Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.\n\n"
                         . "Cordialement,\nL'équipe RPL";
                $headers = "From: noreply@ripac-libreville.org\r\nContent-Type: text/plain; charset=UTF-8";

                @mail($email, $sujet, $corps, $headers);

                $success = true;
                $message = 'Si un compte est associé à cette adresse, vous recevrez un email de réinitialisation dans quelques instants.';
            } else {
                // Ne pas révéler si l'email existe ou non (sécurité)
                $success = true;
                $message = 'Si un compte est associé à cette adresse, vous recevrez un email de réinitialisation dans quelques instants.';
            }
        } catch (PDOException $e) {
            $message = 'Une erreur est survenue. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mot de passe oublié – RPL</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">

  <div class="bg-white p-10 rounded-2xl shadow-lg max-w-md w-full border border-slate-100">

    <!-- Logo -->
    <div class="text-center mb-8">
      <a href="index.php" class="inline-flex flex-col items-center gap-1">
        <div class="bg-slate-900 p-2 rounded-lg">
          <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 5L5 12L20 19L35 12L20 5Z" stroke="white" stroke-width="2"/>
            <path d="M5 20V28C5 28 10 32 20 32C30 32 35 28 35 28V20" stroke="white" stroke-width="2"/>
            <path d="M20 19V32" stroke="white" stroke-width="2"/>
          </svg>
        </div>
        <span class="text-xs text-amber-700 font-bold uppercase tracking-widest">RIPAC</span>
      </a>
    </div>

    <h2 class="text-2xl font-bold text-slate-900 mb-2 text-center">Mot de passe oublié</h2>
    <p class="text-sm text-slate-500 text-center mb-8">Saisissez votre email pour recevoir un lien de réinitialisation.</p>

    <?php if ($message): ?>
      <div class="<?php echo $success ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'; ?> border rounded-xl p-4 mb-6 text-sm flex items-start gap-3">
        <i class="fas <?php echo $success ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-red-500'; ?> mt-0.5"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
      </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" action="motdepasse_oublie.php" class="space-y-5">
      <div>
        <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Adresse email</label>
        <input type="email" name="email" required placeholder="votre@email.com"
               class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-500 outline-none transition"
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
      </div>
      <button type="submit"
              class="w-full bg-amber-700 text-white py-3 rounded-lg font-bold hover:bg-amber-800 transition text-sm uppercase tracking-widest">
        <i class="fas fa-paper-plane mr-2"></i>Envoyer le lien
      </button>
    </form>
    <?php endif; ?>

    <div class="mt-6 text-center space-y-2 text-sm text-slate-400">
      <p><a href="connexion.php" class="text-slate-600 font-bold hover:text-amber-700 transition">← Retour à la connexion</a></p>
      <p>Pas encore de compte ? <a href="inscription.php" class="text-amber-700 font-bold hover:underline">Créer un compte</a></p>
    </div>

  </div>
</body>
</html>
