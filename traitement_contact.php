<?php
// On inclut la connexion à la base de données
require_once('db_connect.php');

// On initialise une variable pour l'affichage du succès
$success = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération sécurisée des données du formulaire (évite l'erreur "Undefined array key")
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $objet = $_POST['objet'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!empty($nom) && !empty($email) && !empty($message)) {
        try {
            // Préparation de la requête SQL (on utilise $pdo du fichier db_connect.php)
            $sql = "INSERT INTO contacts (nom, email, objet, message) VALUES (:nom, :email, :objet, :message)";
            $stmt = $pdo->prepare($sql);
            
            // Exécution avec les paramètres
            $stmt->execute([
                'nom' => $nom,
                'email' => $email,
                'objet' => $objet,
                'message' => $message
            ]);

            $success = true;
        } catch (PDOException $e) {
            $error_message = "Erreur SQL : " . $e->getMessage();
        }
    } else {
        $error_message = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statut de l'envoi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f8fafc; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <?php if ($success): ?>
        <!-- Le bloc de succès (correspondant à votre image_75c045.png) -->
        <div class="bg-white p-10 rounded-3xl shadow-xl max-w-md w-full text-center border border-gray-100">
            <div class="flex justify-center mb-6">
                <!-- Icône Check verte stylisée -->
                <div class="bg-[#4ade80] p-4 rounded-2xl shadow-lg shadow-green-200">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-[#1e293b] mb-4">Message reçu !</h1>
            <p class="text-gray-500 italic mb-10 leading-relaxed">
                Merci, votre message a bien été transmis au secrétariat de la revue.
            </p>
            
            <a href="index.php" class="inline-block w-full bg-[#0f172a] text-white font-bold py-4 px-6 rounded-xl hover:bg-slate-800 transition-all uppercase tracking-widest text-sm">
                Retour à l'accueil
            </a>
        </div>

    <?php else: ?>
        <!-- Bloc d'erreur si l'accès est direct ou s'il y a un souci -->
        <div class="bg-white p-10 rounded-3xl shadow-xl max-w-md w-full text-center border border-red-50">
            <div class="text-red-500 mb-6 text-6xl">⚠️</div>
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Oups !</h1>
            <p class="text-gray-600 mb-8">
                <?php echo $error_message ?: "Accès non autorisé au script."; ?>
            </p>
            <a href="index.php" class="text-blue-600 font-semibold hover:underline">
                Retourner au formulaire
            </a>
        </div>
    <?php endif; ?>

</body>
</html>