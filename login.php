<?php
// login.php
require_once 'db_connect.php'; // Vérifiez bien si c'est db_connect.php ou db_config.php
session_start();

// Si déjà connecté, redirection selon le rôle
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$erreur = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['motdepasse'] ?? '';

    if (!empty($email) && !empty($mdp)) {
        // On utilise $conn (vérifiez le nom dans votre db_connect.php)
        $stmt = $pdo->prepare("SELECT id, nom, **motdepasse**, role FROM utilisateurs WHERE email = ?");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

       if ($user && password_verify($password, $user['**motdepasse**'])) {
            // On nettoie la valeur de la base de données (minuscules, sans espaces)
            $role_propre = strtolower(trim($user['role']));

            // --- INITIALISATION DES SESSIONS ---
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = $role_propre; // On sauvegarde le rôle nettoyé
            
            // --- REDIRECTION BLINDÉE ---
            // On vérifie "admin" ou "administrateur" au cas où
            if ($role_propre === 'admin' || $role_propre === 'administrateur') {
                header("Location: admin_dashboard.php");
            } else {
                // Si c'est un auteur/utilisateur normal
                header("Location: dashboard_utilisateur.php"); // Modifiez si votre fichier s'appelle autrement
            }
            exit();
        } else {
            $erreur = "<div class='bg-red-100 text-red-700 p-4 rounded mb-4'>Identifiants incorrects.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion | RPL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-2xl shadow-lg max-w-md w-full border border-slate-100">
        <h2 class="text-2xl font-bold text-slate-900 mb-6 text-center">Connexion</h2>
        
        <?php echo $erreur; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Email</label>
                <input type="email" name="email" required class="w-full bg-slate-50 border-none rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-500">
            </div>
            
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Mot de passe</label>
                <div class="relative">
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required class="w-full bg-slate-50 border-none rounded-lg p-3 pr-10 text-sm focus:ring-2 focus:ring-amber-500">
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400">
                        <i class="fas fa-eye-slash" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white py-3 rounded-lg font-bold hover:bg-slate-800 transition">Se connecter</button>
        </form>
        <p class="text-center text-sm text-slate-500 mt-6">
            Pas encore de compte ? <a href="inscription.php" class="text-amber-700 font-bold hover:underline">Créer un compte</a><br><br>
            <a href="index.php" class="text-xs text-slate-400">← Retour au site</a>
        </p>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('mot_de_passe');
        const eyeIcon = document.getElementById('eyeIcon');
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>