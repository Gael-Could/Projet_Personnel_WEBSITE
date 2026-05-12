<?php
require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirection si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$erreur = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    if (!empty($email) && !empty($mdp)) {
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = :email AND actif = 1 LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_role'] = $user['role']; 
            
            // Redirection spécifique selon le rôle
            if ($_SESSION['user_role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $erreur = "<div class='bg-red-100 text-red-700 p-4 rounded mb-4'>Identifiants incorrects ou compte désactivé.</div>";
        }
    } else {
        $erreur = "<div class='bg-red-100 text-red-700 p-4 rounded mb-4'>Veuillez remplir tous les champs.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion | RIPAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Ajout de Font Awesome pour l'icône de l'œil -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-2xl shadow-lg max-w-md w-full border border-slate-100">
        
        <!-- Logo (optionnel, pour l'esthétique) -->
        <div class="text-center mb-6">
            <div class="inline-block bg-slate-900 p-2 rounded-lg mb-2">
                <i class="fas fa-user-circle text-white text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 text-center">Connexion</h2>
        </div>
        
        <?php echo $erreur; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Email</label>
                <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none transition shadow-sm">
            </div>
            
            <div>
                <!-- Modification ici : Label + Lien Oublié -->
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-xs font-bold uppercase text-slate-500">Mot de passe</label>
                    <a href="motdepasse_oublie.php" class="text-xs font-bold text-amber-700 hover:text-amber-800 hover:underline transition">Oublié ?</a>
                </div>
                
                <div class="relative">
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 pr-10 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none transition shadow-sm">
                    <!-- Bouton pour afficher/masquer le mot de passe -->
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-amber-700 transition focus:outline-none">
                        <i class="far fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white py-3 rounded-lg font-bold hover:bg-slate-800 transition mt-6 shadow-md shadow-slate-200">
                Se connecter
            </button>
        </form>
        
        <div class="text-center text-sm text-slate-500 mt-6 space-y-2">
            <p>Pas encore de compte ? <a href="inscription.php" class="text-amber-700 font-bold hover:underline">Créer un compte</a></p>
            <p><a href="index.php" class="text-xs text-slate-400 hover:text-slate-600 transition font-medium">← Retour au site</a></p>
        </div>
    </div>

    <!-- Script pour gérer l'affichage du mot de passe -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#mot_de_passe');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            // Basculer le type de l'input entre 'password' et 'text'
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Basculer l'icône (œil ouvert / œil barré)
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>