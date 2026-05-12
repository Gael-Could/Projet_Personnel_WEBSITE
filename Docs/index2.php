<?php

// login.php

session_start();

require_once 'db_config.php'; // Assurez-vous que ce fichier contient vos accès PDO



$error = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    $password = $_POST['password'];



    if (!empty($email) && !empty($password)) {

        // Préparation de la requête pour éviter les injections SQL

        $stmt = $pdo->prepare("SELECT id, nom, password, role FROM utilisateurs WHERE email = ?");

        $stmt->execute([$email]);

        $user = $stmt->fetch();



        if ($user && password_verify($password, $user['password'])) {

            // Régénération de l'ID de session pour la sécurité (prévention fixation de session)

            session_regenerate_id(true);



            // Initialisation des variables de session indispensables

            $_SESSION['user_id'] = $user['id'];

            $_SESSION['user_nom'] = $user['nom'];

            $_SESSION['role'] = $user['role'];



            // Redirection selon le rôle

            if ($user['role'] === 'admin') {

                header("Location: admin_dashboard.php");

            } else {

                header("Location: dashboard_utilisateur.php");

            }

            exit();

        } else {

            $error = "Identifiants incorrects. Veuillez réessayer.";

        }

    } else {

        $error = "Veuillez remplir tous les champs.";

    }

}

?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>Connexion | RPL</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-slate-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-md w-96">

        <h2 class="text-2xl font-bold mb-6 text-center text-slate-800">Connexion</h2>

       

        <?php if ($error): ?>

            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm">

                <?php echo $error; ?>

            </div>

        <?php endif; ?>



        <form method="POST" action="login.php">

            <div class="mb-4">

                <label class="block text-sm font-semibold text-slate-700">Email</label>

                <input type="email" name="email" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">

            </div>

            <div class="mb-6">

                <label class="block text-sm font-semibold text-slate-700">Mot de passe</label>

                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">

            </div>

            <button type="submit" class="w-full bg-amber-700 text-white font-bold py-2 rounded-md hover:bg-amber-800 transition duration-200">

                Se connecter

            </button>

        </form>

        <p class="mt-4 text-center text-xs text-slate-500">

            <a href="index.php" class="hover:underline">Retour à l'accueil</a>

        </p>

    </div>

</body>

</html>

