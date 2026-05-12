<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$email = trim($_POST["email"] ?? "");

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Adresse email invalide.'); window.history.back();</script>";
    exit();
}

try {
    $conn->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        actif TINYINT(1) DEFAULT 1,
        subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $check = $conn->prepare("SELECT id, actif FROM newsletter_subscribers WHERE email = ? LIMIT 1");
    $check->execute([$email]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        if ((int)$existing['actif'] === 1) {
            $message = "Vous êtes déjà inscrit(e) à la newsletter.";
        } else {
            $stmt = $conn->prepare("UPDATE newsletter_subscribers SET actif = 1 WHERE email = ?");
            $stmt->execute([$email]);
            $message = "Votre abonnement a été réactivé.";
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
        $stmt->execute([$email]);
        $message = "Inscription réussie ! Vous recevrez nos prochaines actualités.";
    }

} catch (PDOException $e) {
    $message = "Une erreur est survenue. Veuillez réessayer plus tard.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Newsletter | RIPACL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-md w-full border border-slate-200 text-center">
        <h2 class="text-2xl font-bold text-slate-800 mb-4">Newsletter RIPACL</h2>
        <p class="text-slate-600 mb-6"><?php echo htmlspecialchars($message); ?></p>
        <a href="index.php" class="inline-block bg-slate-900 text-white px-6 py-3 rounded-lg font-bold hover:bg-slate-800 transition">
            Retour à l'accueil
        </a>
    </div>
</body>
</html>
